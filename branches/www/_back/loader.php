<?php
/*
 * qWikiOffice Desktop 0.8.1
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

require_once("system/os/os.php");	
if(class_exists('os')){
	$os = new os();
	
	$module_id = (isset($_GET["moduleId"])) ? $_GET["moduleId"] : $_POST["moduleId"];
	$success = "false";
	
	if($module_id != ""){
		$module = $os->load_module($module_id);
		if($module != ""){
		    $success = "true";
			print $module;
		}
		
		// Uncomment the line below if you use the 'script' method in loadModule() of App.js
		// print "QoDesk.loadModuleComplete(".$success.", '".$module_id."');";
	}
}
?>