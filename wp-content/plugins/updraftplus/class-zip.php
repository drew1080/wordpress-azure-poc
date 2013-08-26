<?php

if (!defined ('ABSPATH')) die('No direct access allowed');

if (class_exists('ZipArchive')):
# We just add a last_error variable for comaptibility with our UpdraftPlus_PclZip object
class UpdraftPlus_ZipArchive extends ZipArchive {
	public $last_error = '(Unknown: ZipArchive does not return error messages)';
}
endif;

class UpdraftPlus_BinZip extends UpdraftPlus_PclZip {

	private $binzip;

	function __construct() {
		global $updraftplus_backup;
		$this->binzip = $updraftplus_backup->binzip;
		if (!is_string($this->binzip)) {
			$this->last_error = "No binary zip was found";
			return false;
		}
		$this->debug = UpdraftPlus_Options::get_updraft_option('updraft_debug_mode');
		return parent::__construct();
	}

	public function addFile($file, $add_as) {

		global $updraftplus;
		$base = $updraftplus->str_lreplace($add_as, '', $file);

		if ($file == $base) {
			// Shouldn't happen
		} else {
			$rdirname = untrailingslashit($base);
			# Note: $file equals $rdirname/$add_as
			$this->addfiles[$rdirname][] = $add_as;
		}

	}

	# The standard zip binary cannot list; so we use PclZip for that
	# Do the actual write-out - it is assumed that close() is where this is done. Needs to return true/false
	public function close() {

		if (empty($this->pclzip)) {
			$this->last_error = 'Zip file was not opened';
			return false;
		}

		global $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();

		$activity = false;

		# BinZip does not like zero-sized zip files
		if (file_exists($this->path) && 0 == filesize($this->path)) @unlink($this->path);

		$descriptorspec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);
		$exec = $this->binzip." -v -@ ".escapeshellarg($this->path);
		$last_recorded_alive = time();
		$something_useful_happened = $updraftplus->something_useful_happened;
		$orig_size = file_exists($destination) ? filesize($destination) : 0;
		$last_size = $orig_size;
		clearstatcache();

		$added_dirs_yet = false;

		// Loop over each destination directory name
		foreach ($this->addfiles as $rdirname => $files) {

			$process = proc_open($exec, $descriptorspec, $pipes, $rdirname);

			if (!is_resource($process)) {
				$updraftplus->log('BinZip error: proc_open failed');
				$this->last_error = 'BinZip error: proc_open failed';
				return false;
			}

			if (!$added_dirs_yet) {
				# Add the directories - (in fact, with binzip, non-empty directories automatically have their entries added; but it doesn't hurt to add them explicitly)
				foreach ($this->adddirs as $dir) {
					fwrite($pipes[0], $dir."/\n");
				}
				$added_dirs_yet=true;
			}

			foreach ($files as $file) {
				// Send the list of files on stdin
				fwrite($pipes[0], $file."\n");
			}
			fclose($pipes[0]);

			while (!feof($pipes[1])) {
				$w = fgets($pipes[1], 1024);
				// Logging all this really slows things down; use debug to mitigate
				if ($w && $this->debug) $updraftplus->log("Output from zip: ".trim($w), 'debug');
				if (time() > $last_recorded_alive + 5) {
					$updraftplus->record_still_alive();
					$last_recorded_alive = time();
				}
				if (file_exists($this->path)) {
					$new_size = @filesize($this->path);
					if (!$something_useful_happened && $new_size > $orig_size + 20) {
						$updraftplus->something_useful_happened();
						$something_useful_happened = true;
					}
					clearstatcache();
					# Log when 20% bigger or at least every 50Mb
					if ($new_size > $last_size*1.2 || $new_size > $last_size + 52428800) {
						$updraftplus->log($this->path.sprintf(": size is now: %.2f Mb", round($new_size/1048576,1)));
						$last_size = $new_size;
					}
				}
			}

			fclose($pipes[1]);

			while (!feof($pipes[2])) {
				$last_error = fgets($pipes[2]);
				if (!empty($last_error)) $this->last_error = $last_error;
			}
			fclose($pipes[2]);

			$ret = proc_close($process);

			if ($ret != 0 && $ret != 12) {
				$updraftplus->log("Binary zip: error (code: $ret)");
				if (!empty($w) && !$this->debug) $updraftplus->log("Last output from zip: ".trim($w), 'debug');
				return false;
			}

			unset($this->addfiles[$rdirname]);
		}

		return true;
	}

}

# A ZipArchive compatibility layer, with behaviour sufficient for our usage of ZipArchive
class UpdraftPlus_PclZip {

	protected $pclzip;
	protected $path;
	protected $addfiles;
	protected $adddirs;
	private $statindex;
	public $last_error;

	function __construct() {
		$this->addfiles = array();
		$this->adddirs = array();
	}

	public function __get($name) {
		if ($name != 'numFiles') return null;

		if (empty($this->pclzip)) return false;

		$statindex = $this->pclzip->listContent();

		if (empty($statindex)) {
			$this->statindex=array();
			return 0;
		}

		$result = array();
		foreach ($statindex as $i => $file) {
			if (!isset($statindex[$i]['folder']) || 0 == $statindex[$i]['folder']) {
				$result[] = $file;
			}
			unset($statindex[$i]);
		}

		$this->statindex=$result;

		return count($this->statindex);

	}

	public function statIndex($i) {

		if (empty($this->statindex[$i])) return array('name' => null, 'size' => 0);

		return array('name' => $this->statindex[$i]['filename'], 'size' => $this->statindex[$i]['size']);

	}

	public function open($path, $flags = 0) {
		if(!class_exists('PclZip')) require_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
		if(!class_exists('PclZip')) {
			$this->last_error = "No PclZip class was found";
			return false;
		}

		$ziparchive_create_match = (defined('ZIPARCHIVE::CREATE')) ? ZIPARCHIVE::CREATE : 1;

		if ($flags == $ziparchive_create_match && file_exists($path)) @unlink($path);

		$this->pclzip = new PclZip($path);
		if (empty($this->pclzip)) {
			$this->last_error = 'Could not get a PclZip object';
			return false;
		}

		# Make the empty directory we need to implement addEmptyDir()
		global $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();
		if (!is_dir($updraft_dir.'/emptydir') && !mkdir($updraft_dir.'/emptydir')) {
			$this->last_error = "Could not create empty directory ($updraft_dir/emptydir)";
			return false;
		}

		$this->path = $path;

		return true;

	}

	# Do the actual write-out - it is assumed that close() is where this is done. Needs to return true/false
	public function close() {
		if (empty($this->pclzip)) {
			$this->last_error = 'Zip file was not opened';
			return false;
		}

		global $updraftplus;
		$updraft_dir = $updraftplus->backups_dir_location();

		$activity = false;

		# Add the empty directories
		foreach ($this->adddirs as $dir) {
			if (false == $this->pclzip->add($updraft_dir.'/emptydir', PCLZIP_OPT_REMOVE_PATH, $updraft_dir.'/emptydir', PCLZIP_OPT_ADD_PATH, $dir)) {
				$this->last_error = $this->pclzip->errorInfo(true);
				return false;
			}
			$activity = true;
		}

		foreach ($this->addfiles as $rdirname => $adirnames) {
			foreach ($adirnames as $adirname => $files) {
				if (false == $this->pclzip->add($files, PCLZIP_OPT_REMOVE_PATH, $rdirname, PCLZIP_OPT_ADD_PATH, $adirname)) {
					$this->last_error = $this->pclzip->errorInfo(true);
					return false;
				}
				$activity = true;
			}
			unset($this->addfiles[$rdirname]);
		}

		$this->pclzip = false;
		$this->addfiles = array();
		$this->adddirs = array();

		clearstatcache();
		if ($activity && filesize($this->path) < 50) {
			$this->last_error = "Write failed - unknown cause (check your file permissions)";
			return false;
		}

		return true;
	}

	# Note: basename($add_as) is irrelevant; that is, it is actually basename($file) that will be used. But these are always identical in our usage.
	public function addFile($file, $add_as) {
		# Add the files. PclZip appears to do the whole (copy zip to temporary file, add file, move file) cycle for each file - so batch them as much as possible. We have to batch by dirname(). On a test with 1000 files of 25Kb each in the same directory, this reduced the time needed on that directory from 120s to 15s (or 5s with primed caches).
		$rdirname = dirname($file);
		$adirname = dirname($add_as);
		$this->addfiles[$rdirname][$adirname][] = $file;
	}

	# PclZip doesn't have a direct way to do this
	public function addEmptyDir($dir) {
		$this->adddirs[] = $dir;
	}

}