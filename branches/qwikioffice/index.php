<?php
require_once("system/os/os.php");
if(!class_exists('os')){
	header("Location: ".config::getInstance()->LOGIN_URL);
}else{
	$os = new os();
	if(!$os->session->exists()){
		header("Location: ".config::getInstance()->LOGIN_URL);
	}else{
		$os->init();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="PRAGMA" content="NO-CACHE">
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
<meta http-equiv="EXPIRES" content="-1">

<title>Desktop Demo</title>

<!-- EXT JS LIBRARY -->
<?php
	echo config::loadExtJs();
?>

<!-- DESKTOP CSS -->
<link rel="stylesheet" type="text/css" href="resources/css/desktop.css" />
<link rel="stylesheet" type="text/css" href="system/dialogs/colorpicker/colorpicker.css" />

<!-- THEME CSS -->
<?php print $os->theme->get(); ?>
<!-- MODULES CSS -->
<?php print $os->module->get_css(); ?>

<!-- SYSTEM DIALOGS AND CORE -->
<!-- In a production environment these should be minified into one file -->
<script src="system/dialogs/colorpicker/ColorPicker.js"></script>
<script src="system/core/App.js"></script>
<script src="system/core/Desktop.js"></script>
<script src="system/core/HexField.js"></script>
<script src="system/core/Module.js"></script>
<script src="system/core/Notification.js"></script>
<script src="system/core/Shortcut.js"></script>
<script src="system/core/StartMenu.js"></script>
<script src="system/core/TaskBar.js"></script>

<!-- QoDesk -->
<script src="QoDesk.php"></script>
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