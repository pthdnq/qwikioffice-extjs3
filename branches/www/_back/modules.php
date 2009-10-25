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
	print $os->preload_modules();
}
?>