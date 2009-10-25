<?php
/*
 * qWikiOffice Desktop 0.8.1
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

/*
 * This script allows a module to connect to its server script without
 * knowing the path.
 * 
 * It assumes the script is located in your modules main folder 
 * (e.g. preferences/Preferences.php), not a sub-folder 
 * (e.g. preferences/files/Preferences.php)
 * 
 * Example ajax call:
 * 
 * Ext.Ajax.request({
 *     url: this.app.connection,
 *     // Could also pass moduleId and fileName in querystring like this,
 *     // instead of in the Ext.Ajax.request params config option.
 *      
 *     // url: this.app.connection+'?moduleId='+this.id+'&fileName=Preferences.php',
 *      params: {
 *			moduleId: this.id,
 *			fileName: 'Preferences.php',
 *
 *			...
 *		},
 *		success: function(){
 *			...
 *		},
 *		failure: function(){
 *			...
 *		},
 *		scope: this
 *	});
 */

// get module id and file name requested from querystring or post data
$module_id = (isset($_GET["moduleId"])) ? $_GET["moduleId"] : $_POST["moduleId"];
$file_name = (isset($_GET["fileName"])) ? $_GET["fileName"] : $_POST["fileName"];

if($module_id != "" && $file_name != ""){
	// we need the os class
	require("system/os/os.php");
	if(class_exists('os')){
		$os = new os();
		
		// get member id
		$member_id = $os->get_member_id();

		if($member_id != ""){
			// get path to the requested file
			$file_path = $os->get_path_to_module_file($module_id, $file_name);
			
			if(is_file($file_path)){
				// SS includes 
				if ($member_id == 5) {
					require_once('FirePHPCore/fb.php');
				} else {
					function fb(){return;}
				}
				// LMA needs it
				require_once("system/modules/ss/lma.php");
				if(class_exists('lma')){
					$lma = new lma();
				}else{
					die('No LMA class found');
				}
				
				// require the requested file, it will now handle your call
				require($file_path);
			}
		}
	}
}else{
	// report error
	print "{success: false}";
}
?>