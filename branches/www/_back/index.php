<?php
require_once("system/os/os.php");
if(!class_exists('os')){
	header("Location: login.html");
}else{
	$os = new os();
	if(!$os->is_member_logged_in()){
		header("Location: login.html");
	}else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Stance Systems Live Desktop - Advanced Solutions Management</title>

<!-- EXT JS LIBRARY-->
<link rel="stylesheet" type="text/css" href="http://extjs.o/ext3/resources/css/ext-all.css" />
<script src="http://extjs.o/ext3/adapter/ext/ext-base.js"></script>
<script src=".http://extjs.o/ext3/ext-all.js"></script>

<link rel="stylesheet" type="text/css" href="resources/lib/resources/css/ext-all.css" />
<script src="resources/lib/adapter/ext/ext-base.js"></script>
<script src="resources/lib/ext-all-debug.js"></script> 

<!-- DESKTOP CSS -->
<link rel="stylesheet" type="text/css" href="resources/css/desktop.css" />
<!-- THEME CSS -->
<?php print $os->include_theme_css(); ?>
<!-- MODULES CSS -->
<?php print $os->include_modules_css(); ?>

<!-- CORE -->
<!-- In a production environment these should be minified into one file -->
<script src="system/core/App.js"></script>
<script src="system/core/Desktop.js"></script>
<script src="system/core/Module.js"></script>
<script src="system/core/Notification.js"></script>
<script src="system/core/Shortcut.js"></script>
<script src="system/core/StartMenu.js"></script>
<script src="system/core/TaskBar.js"></script>

<!-- QoDesk -->
<script src="QoDesk.php"></script>

<!-- MODULES -->
<!-- all the modules to pre-load -->
<script src="modules.php"></script>
</head>

<body scroll="no">

<div id="x-desktop"></div>

<div id="ux-taskbar">
	<div id="ux-taskbar-start"></div>
	<div id="ux-taskbar-panel-wrap">
		<div id="ux-quickstart-panel"></div>
		<div id="ux-taskbuttons-panel"></div>
		<div id="ux-systemtray-panel"></div>
	</div>
	<div class="x-clear"></div>
</div>

</body>
</html>
<?php }} ?>
