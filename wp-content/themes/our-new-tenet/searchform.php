<!-- search -->
<map name="searchbutton">
  <area shape="circle" coords="165,10,8" alt="Search" href="<?php echo home_url( '/' ); ?>">
</map>

<form role="search" class="search-form" method="get" action="<?php echo home_url( '/' ); ?>" role="search">
	<input class="search-input-box" type="search" name="s" placeholder="Search" usemap="#searchbutton">
  <!-- <input type="submit" id="searchsubmit" class="search-button"> -->
</form>
<!-- /search -->
