<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class kernal {

	private $os = null;

   private $module_dir = null;
   private $library_dir = null;

   /** __construct() The constructor.
     *
     * @access public
     * @param {class} $os An instance of the os class.
     **/
	public function __construct($os){
      $os->load('session');

      if(!$os->session->exists()){
         die('Session does not exist!');
      }

      $document_root = $os->get_document_root();
      $this->module_dir = $document_root.$os->get_module_dir();
		$this->library_dir = $document_root.$os->get_library_dir();

      $this->os = $os;
	} // end __construct()

	/** init() Initial page load or refresh has occured.
	  * Called from init() of os.php
     *
     * @access public
	  **/
	public function init(){
      $this->prepare_registry();
	} // end init()

   /** prepare_registry() Prepare the registry.
     *
     * @access private
     **/
   private function prepare_registry(){
      // 1. register any member definitions
      $this->register_member();

      // 2. register any privilege definitions
      $this->register_privileges();

      // 3. register any module or library definitions
      $this->register_definitions();
   } // end prepare_registry()

   /** register_member()
     *
     **/
   private function register_member(){
      $member = new stdClass();

      $this->os->load('member');
      $member_id = $this->os->member->get_id();

      $this->os->load('group');
      $group_id = $this->os->group->get_id();

      if(isset($member_id, $group_id)){
         $member->locale = $this->os->member->get_locale($member_id);
         $member->name = $this->os->member->get_name($member_id);
      }

      // add to the registry
      $this->os->load('registry');
      $this->os->registry->set($this->os->REG_KEY_MEMBER, $member);
   } // end register_member()

   /** register_privileges()
    *
    */
   private function register_privileges(){
      $this->os->load('privilege');
      $privileges = $this->os->privilege->get();

      // privileges?
      if(count($privileges) > 0){
         $this->os->load('registry');
         $this->os->registry->set($this->os->REG_KEY_PRIVILEGE, $privileges);
      }
   } // end register_privileges()

   /** register_definitions() Get any definitions to add to the registry.
     *
     * @access private
     **/
   private function register_definitions(){
      $this->os->load('definition');

      // get definitions from files
      // filter for 'module' or 'library' definitions
      //$filters = array( array('key' => 'defines', 'values' => array('module', 'library')) );
      //$definitions = $this->os->definition->get_from_files($filters);

      // get definitions from database
      $definitions = $this->os->definition->get_from_db("WHERE active = 1 AND defines = 'module' OR defines = 'library'");

      // definitions?
      if(!count($definitions) > 0){
         return false;
      }

      $this->os->load('module');
      $modules = array();
      $libraries = array();

      foreach($definitions as $key => $definition){

         // module?
         if($definition->defines == 'module'){
            if($this->is_valid_module($definition)){
               // privilege ( allowed to load the module )?
               if($this->os->module->is_allowed_to_load($definition->id)){
                  $modules[$definition->id] = $definition;
               }
            }
         }

         // library?
         if($definition->defines == 'library'){
            if($this->is_valid_library($definition)){
               $libraries[$definition->id] = $definition;
            }
         }

      }

      // add any modules to the registry
      if(count($modules) > 0){
         $this->os->load('registry');
         $this->os->registry->set($this->os->REG_KEY_MODULE, $modules);
      }

      // add any libraries to the registry
      if(count($libraries) > 0){
         $this->os->load('registry');
         $this->os->registry->set($this->os->REG_KEY_LIBRARY, $libraries);
      }

      // module(s) are required for success
      return count($modules) > 0 ? true : false;
   } // end register_definitions()

   /** register_modules() Get and add module definitions into the registry
     *
     * @access private
     **
   private function register_modules(){
      $this->os->load('definition');

      // filter for 'module' definitions
      $filters = array( array('key' => 'defines', 'values' => array('module')) );
      // use the definition's id as the index for the return associative array
      $definitions = $this->os->definition->get_from_files($filters, 'id');

      // if any module definitions were found
      if(count($definitions) > 0){
         $this->os->load('module');
         $modules = array();

         foreach($definitions as $key => $definition){
            // allow the module class to validate/prepare/modify each module for the registry
            $module = $this->os->module->prepare($definition);
            // if returned, the module definition was succesfully prepared
            if($module){
               $modules[$key] = $module;
            }
         }

         // add any modules to the registry
         if(count($modules) > 0){
            $this->os->load('registry');
            $this->os->registry->set($this->os->REG_KEY_MODULE, $modules);
         }
      }
   } // end register_modules() */

   /** register_libraries() Get and add library definitions into the registry
     *
     * @access private
     **
   private function register_libraries(){
      $this->os->load('definition');

      // filter for 'library' definitions
      $filters = array( array('key' => 'defines', 'values' => array('library')) );
      // use the definition's Id as the index for the return associative array
      $definitions = $this->os->definition->get_from_files($filters, 'id');

      // if any library definitions were found
      if(count($definitions) > 0){
         $this->os->load('library');
         $libraries = array();

         foreach($definitions as $key => $definition){
            // allow the library class to validate/prepare/modify each library for the registry
            $library = $this->os->library->prepare($definition);
            // if returned, the library definition was succesfully prepared
            if($library){
               $libraries[$key] = $library;
            }
         }

         // add any libraries to the registry
         if(count($libraries) > 0){
            $this->os->load('registry');
            $this->os->registry->set($this->os->REG_KEY_LIBRARY, $libraries);
         }
      }
   } // end register_libraries() */

   /** is_valid_module() Check if a member is allowed to load the module and validate the definition.
     *
     * @access private
     * @param {stdClass} $m The module definition.
     * @return {boolean}
     **/
   private function is_valid_module($m){
      $response = true;

      // Todo: localization

      // module definition?
      if(!is_object($m) || $m->defines != 'module'){
         // log error
         $response = false;
      }

      // client class?
      if(!isset($m->clientClass) || $m->clientClass == ''){
         // log error
         $response = false;
      }

      // at least one client file
      if(!isset($m->clientFiles) || !is_array($m->clientFiles) || !count($m->clientFiles) > 0){
         // log error
         $response = false;
      }

      // valid client files?
      if(isset($m->clientFiles)){
         $files = $this->get_files($m, 'clientFiles');

         if($files){
            foreach($files as $file){
               if(!is_file($this->module_dir.$file)){
                  // log error with module
                  $response = false;
               }
            }
         }
      }

      // valid stylesheets?
      if(isset($m->stylesheets)){
         $files = $this->get_files($m, 'stylesheets');

         if($files){
            foreach($files as $file){
               if(!is_file($this->module_dir.$file)){
                  // log error
                  $response = false;
               }
            }
         }
      }

      // valid server file?
      if(isset($m->serverFile) && $m->serverFile != ''){
         if(!is_file($this->module_dir.$m->serverFile)){
            // log error
            $response = false;
         }

         // server class?
         if(!isset($m->serverClass) || $m->serverClass == ''){
            // log error
            $response = false;
         }
      }

      return $response;
   } // end is_valid_module()

   /** is_valid_library() Is the library definition valid.
     *
     * @access private
     * @param {stdClass} $w The library definition.
     * @return {boolean}
     **/
   private function is_valid_library($w){
      $response = true;

      // Todo: localization

      // library definition?
      if(!is_object($w) || $w->defines != 'library'){
         // log error
         $response = false;
      }

      // valid client files?
      if(isset($w->clientFiles)){
         $files = $this->get_files($w, 'clientFiles');

         if($files){
            foreach($files as $file){
               if(!is_file($this->library_dir.$file)){
                  // log error
                  $response = false;
               }
            }
         }
      }

      // valid stylesheets?
      if(isset($w->stylesheets)){
         $files = $this->get_files($w, 'stylesheets');

         if($files){
            foreach($files as $file){
               if(!is_file($this->library_dir.$file)){
                  // log error
                  $response = false;
               }
            }
         }
      }

      return $response;
   } // end is_valid_library()

   /** get_files() Returns an array with the files in the order listed ( load order ) in the definition.
     * Module definition files are listed in file groups like so:
     *
     * "clientFiles": [
     *    {
     *       "directory": "color-picker/",
     *       "files": [
     *         "Ext.ux.ColorPicker.js"
     *       ]
     *    }
     * ]
     *
     * @access private
     * @param {stdClass} $definition The module definition
     * @param {string} $key The definition key to get the files from.
     * @return {array/null} An array of the file paths on success.  Null on failure.
     **/
   private function get_files($definition, $key){
      // only stylesheets or client files
      if($key != 'stylesheets' && $key != 'clientFiles'){
         return null;
      }

      // isset? or is array? or count > 0?
      if(!isset($definition->$key) || !is_array($definition->$key) || !count($definition->$key) > 0){
         return null;
      }

      $file_groups = $definition->$key;
      $response = array();

      // loop through the file groups
      foreach($file_groups as $group){
         $directory = $group->directory;
         $files = $group->files;

         if(!isset($files) || !is_array($files) || !count($files) > 0){
            continue;
         }

         // loop through each file
         foreach($files as $file){
            $response[] = $directory.$file;
         }
      }

      if(!count($response) > 0){
         return null;
      }

      return $response;
   } // end get_files()
}
?>