<?php
$path = "http://wordpresspoc.azurewebsites.net/wp-content/themes/our-new-tenet/images/OurNewTenet-EmployeeGuide.pdf";
$filename = "OurNewTenet-EmployeeGuide.pdf";
header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
header('Accept-Ranges: bytes');  // For download resume
header('Content-Length: ' . filesize($path));  // File size
header('Content-Encoding: none');
header('Content-Type: application/pdf');  // Change this mime type if the file is not PDF
header('Content-Disposition: attachment; filename=' . $filename);  // Make the browser display the Save As dialog
readfile($path);  //this is necessary in order to get it to actually download the file, otherwise it will be 0Kb

?>