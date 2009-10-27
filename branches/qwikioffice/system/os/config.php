<?php
/*
 * qWikiOffice Desktop 0.8.1
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

// If you want all Error Reporting on, use this:
//ini_set('display_errors',1);
//error_reporting(E_ALL|E_STRICT);

// If you want to see Warning Messages and not Notice Messages, use this:
//ini_set('display_errors',1);
//error_reporting(E_ALL);

// If you want all Error Reporting off, use this:
error_reporting(255);

include_once('D:/serwisy/pocms5.o/libs/Pocms/Tools.php');

class config {
	// document root
	public $DOCUMENT_ROOT = '';
	
	// directories
	public $MODULES_DIR = 'system/modules/';
	public $THEMES_DIR = 'resources/themes/';
	public $WALLPAPERS_DIR = 'resources/wallpapers/';
	
	// login url
	public $LOGIN_URL = 'login.html';
	
	// local database
	public $DB_HOST = 'localhost';
	public $DB_USERNAME = 'qouser';
	public $DB_PASSWORD = 'qopass';
	public $DB_NAME = 'qwikioffice-distro';
	
	public function __construct(){
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', getcwd());
		$this->DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'].'/';
	}
	
	/**
	 * ExtJs loader scripts
	 *
	 */
	public static function loadExtJs(){
		$ext[]='<link rel="stylesheet" type="text/css" href="http://extjs.w.interia.pl/v3/resources/css/ext-all.css" />';
		$ext[]='<script src="http://extjs.w.interia.pl/v3/adapter/ext/ext-base-debug.js"></script>';
		$ext[]='<script src="http://extjs.w.interia.pl/v3/ext-all-debug.js"></script>';
		$ext[]='<script src="/ext3/ext-fix.js"></script>';
		return join("\n",$ext);
	}
}
