<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */
if( (  function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()  )
     || (  ini_get('magic_quotes_sybase') && ( strtolower(ini_get('magic_quotes_sybase')) != "off" )  )
   ){
    foreach($_GET as $k => $v) $_GET[$k] = stripslashes($v);
    foreach($_POST as $k => $v) $_POST[$k] = stripslashes($v);
    foreach($_COOKIE as $k => $v) $_COOKIE[$k] = stripslashes($v);
}
$service = isset($_POST['service']) ? $_POST['service'] : '';

if(isset($service) && $service != ''){

   /**
    * Load.
    * Provide Module on Demand loading.
    */
   if($service == 'load'){
      $module_id = isset($_GET['moduleId']) ? $_GET['moduleId'] : $_POST['moduleId'];
      if(isset($module_id) && $module_id != ''){
         require_once('server/os.php');
         if(class_exists('os')){
            $os = new os();
            $os->load_module($module_id);
         }
      }
	}

   /**
    * Login.
    */
   else if($service == 'login'){
      require_once('server/os.php');
      $os = new os();
      print $os->login($_POST['user'], $_POST['pass'], $_POST['group']);
   }

   /**
    * Forgot Password.
    */
   else if($service == 'forgotPassword'){
      require_once('server/os.php');
      $os = new os();
      $os->load('member');
      print $os->forgot_password($_POST['user']);
   }

   /**
    * Signup.
    */
   else if($service == 'signup'){
      require_once('server/os.php');
      $os = new os();
      $os->load('member');
      print $os->signup($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['email_verify'], $_POST['comments']);
   }

}else{

   /**
    * Make request.
    * Allow the module's client code to make a request of its server code (class).
    *
    * Example ajax call:
    *
    * Ext.Ajax.request({
    *    url: this.app.connection,
    *    // Could also pass the module id and action in querystring like this,
    *    // instead of in the Ext.Ajax.request params config option.
    *
    *    // url: this.app.connection+'?method=myMethodName&moduleId='+this.id,
    *    params: {
    *			method: 'myMethodName',
    *			moduleId: this.id,
    *			...
    *		},
    *		success: this.mySuccessFn,
    *		failure: this.myFailureFn,
    *		scope: this
    *	});
    */
   $method_name = isset($_GET['method']) ? $_GET['method'] : $_POST['method'];
   $module_id = isset($_GET['moduleId']) ? $_GET['moduleId'] : $_POST['moduleId'];

   if(isset($module_id, $method_name) && $module_id != '' && $method_name != ''){
      require('server/os.php');
      if(class_exists('os')){
         $os = new os();
         $os->make_request($module_id, $method_name);
      }
   }

}
?>