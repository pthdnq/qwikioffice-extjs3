<?php
require_once('server/os.php');

if(!class_exists('os')){
	die('Server os class is missing!');
}else{
	$os = new os();

	if(!$os->session_exists()){
		header("Location: ".config::getInstance()->LOGIN_URL);
	}else{
		$os->init();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="PRAGMA" content="NO-CACHE">
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
<meta http-equiv="EXPIRES" content="-1">

<title>A qWikiOffice Desktop</title>

<!-- EXT JS LIBRARY -->
<?php
	echo config::loadExtJs();
?>

<!-- DESKTOP CSS -->
<link rel="stylesheet" type="text/css" href="resources/css/desktop.css" />

<!-- THEME CSS -->
<?php $os->print_theme_css(); ?>

<!-- MODULES CSS -->
<?php $os->print_module_css(); ?>

<!-- CORE -->
<!-- In a production environment these would be minified into one file -->
<script src="client/core/App.js"></script>
<script src="client/core/Desktop.js"></script>
<script src="client/core/Module.js"></script>
<script src="client/core/Notification.js"></script>
<script src="client/core/Shortcut.js"></script>
<script src="client/core/StartMenu.js"></script>
<script src="client/core/TaskBar.js"></script>
<script src="client/overrides/BorderLayout.js"></script>
<script src="client/overrides/TreeNodeUI.js"></script>

<!-- QoDesk -->
<!-- This dynamic file will load all the modules the member has access to and setup the desktop -->
<script src="QoDesk.php"></script>
</head>
<body scroll="no"></body>
</html>
<?php }} ?>