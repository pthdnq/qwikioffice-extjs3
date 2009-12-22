<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class config {

   /**
    * Begin editable code.
    * Update the following with your information.
    */

   /**
    * Domain url
    */
   public $DOMAIN = 'qwiki.o';

   /**
    * Email address
    */
   public $EMAIL = 'info@qwiki.o';

   /**
    * Database connection
    * Using PHP Data Objects (PDO)
    */
   public $DB_CONN_STRING = 'mysql:dbname=qwikioffice3;host=localhost';
   public $DB_USERNAME = 'localuser';
   public $DB_PASSWORD = 'localpass';

   public $LOGS_DIR = 'server/logs/';

   /**
    * Login url
    */
   public $LOGIN_URL = 'login.php';

   /**
    * PDO error mode
    */
   public $PDO_ERROR_MODE = PDO::ERRMODE_WARNING; // development environment
   //public $PDO_ERROR_MODE = PDO::ERRMODE_SILENT; // production environment

   /**
    * PHP error reporting
    * Options are:
    * 1. show all
    * 2. show only warnings
    * 3. show no errors
    */
   private $error_reporting = 'show only warnings';

   // End editable code

   /**
    * Directories
    */
   public $LIBRARIES_DIR = 'modules/common/libraries/';
   public $MODULES_DIR = 'modules/';
   public $THEMES_DIR = 'resources/themes/';
   public $WALLPAPERS_DIR = 'resources/wallpapers/';

   /**
    * Document root
    */
   public $DOCUMENT_ROOT = '';

   /**
    * __construct()
    *
    * @access public
    */
   public function __construct(){
      // set error reporting
      switch($this->error_reporting){
         case 'show all':
            ini_set('display_errors',1);
            error_reporting(E_ALL|E_STRICT);
            break;
         case 'show only warnings':
            ini_set('display_errors',1);
            error_reporting(E_ALL);
            break;
         case 'show no errors':
            error_reporting(0);
            break;
      }

      // set the document root
      $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', getcwd());
      $this->DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'].'/';
   } // end __construct()

  /**
	 * ExtJs loader scripts
	 *
	 */
	public static function loadExtJs(){
//		$ext[]='<link rel="stylesheet" type="text/css" href="http://extjs.w.interia.pl/v3/resources/css/ext-all.css" />';
//		$ext[]='<script src="http://extjs303.googlecode.com/svn/trunk/ext-3.0.3/adapter/ext/ext-base-debug.js"></script>';
//		$ext[]='<script src="http://extjs303.googlecode.com/svn/trunk/ext-3.0.3/ext-all-debug.js"></script>';
//		$ext[]='<script src="http://extjs303.googlecode.com/svn/trunk/ext-3.0.3/examples/ux/statusbar/StatusBar.js"></script>';

		$ext[]='<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-3.1.0/resources/css/ext-all.css" />';
		$ext[]='<script type="text/javascript" src="http://extjs.cachefly.net/ext-3.1.0/adapter/ext/ext-base.js"></script>';
		$ext[]='<script type="text/javascript" src="http://extjs.cachefly.net/ext-3.1.0/ext-all-debug.js"></script>';
		$ext[]='<script type="text/javascript" src="http://extjs.cachefly.net/ext-3.1.0/examples/ux/statusbar/StatusBar.js"></script>';

		$ext[]='<script src="/ext3/ext-fix.js"></script>';
		return join("\n",$ext);
	}

	public function getInstance(){
			static $instance;
			if(!isset($instance)) {
			   $instance = new self();
			}
			return $instance;
   }
}
