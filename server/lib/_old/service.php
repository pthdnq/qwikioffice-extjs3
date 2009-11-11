<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class service {

   private $os = null;

   /**
    * __construct()
    *
    * @access public
    * @param {class} $os The os.
    */
   public function __construct(os $os){
      $os->load('session');
      if(!$os->session->exists()){
         die('Session does not exist!');
      }

      $this->os = $os;
   } // end __construct()

   /**
    * print_all_css() Prints all the css link tags for the theme and the modules (and their dependencies) that the member can load
    *
    * @access public
    */
   public function print_all_css(){
      $arg = new stdClass();

      // get the theme
      $theme_id = $this->get_theme_id();
      if($theme_id){
         $arg->id = $theme_id;
         $arg->type = 'theme';

         $this->print_css($arg);
      }

      // get all active modules
      $this->os->load('module');
      $modules = $this->os->module->get_active();

      if(isset($modules) && is_array($modules) && count($modules) > 0){
         foreach($modules as $id => $module){
            $arg->id = $id;
            $arg->type = 'module';

            $this->print_css($arg);
         }
      }
   } // end print_all_css()

   /**
    * print_css() Will take an object or an array of objects that contain an 'id' and 'type' property.
    * The 'id' property holds the id of a library or module.
    * The 'type' property specifies if it is a 'library' or a 'module'.
    *
    * @access private
    * @param {array/object} $items An array of objects or an object.
    */
   private function print_css($items){
      // do we have the required param?
      if(!isset($items)){
         return '';
      }

      // is the param an array of items?
      if(is_array($items) && count($items == 0)){
         // loop through the items
         foreach($items as $item){
            $this->print_item_css($item);
         }
      }

      // is the param  an object?
      else if(is_object($items)){
         $this->print_item_css($items);
      }
   } // end print_css()

   /**
    * print_item_css()
    *
    * @access private
    * @param {stdClass} $item An object with 'id' and 'type' properties.
    */
   private function print_item_css($item){
      // do we have the required param?
      if(!isset($item->id, $item->type)){
         return '';
      }

      // is the item a library?
      if($item->type == 'library'){
         $this->print_library_css($item->id);
      }

      // is the item a module?
      else if($item->type == 'module'){
         $this->print_module_css($item->id);
      }

      // is the item a theme?
      if($item->type == 'theme'){
         $this->print_theme_css($item->id);
      }
   } // end print_item_css()

   /**
    * print_theme_css() Prints the css link tag of the theme.
    *
    * @access private
    * @param {string} $theme_id The id of the theme.
    */
   private function print_theme_css($theme_id){
      // do we have the required params?
      if(!isset($theme_id) || $theme_id == ''){
         print '';
         return false;
      }

      $this->os->load('theme');

      // get the css file of the theme and print their link tags
      $file = $this->os->theme->get_file($theme_id);

      if(!$file){
         print '';
         return false;
      }

      $document_root = $this->os->get_document_root();
      $theme_dir = $this->os->get_theme_dir();

      // todo: log errors
      if(is_file($document_root.$theme_dir.$file)){
         print "<link id='theme' rel='stylesheet' type='text/css' href='".$theme_dir.$file."' />\n";
      }
   } // end print_theme_css()

   /**
    * print_library_css() Prints the css link tags of the library.
    *
    * @access private
    * @param {string} $library_id The id of the library.
    */
   private function print_library_css($library_id){
      // do we have the required params?
      if(!isset($library_id) || $library_id == ''){
         return '';
      }

      $this->os->load('library');

      // get any dependencies and print their link tags first
      $dependencies = $this->os->library->get_dependencies($library_id);
      if($dependencies){
         $this->print_css($dependencies);
      }

      // get the css files of the library and print their link tags
      $files = $this->os->library->get_client_files($library_id, 'css');

      if(!$files){
         return '';
      }

      $document_root = $this->os->get_document_root();
      $library_dir = $this->os->get_library_dir();

      foreach($files as $file){
         // todo: log errors
         if(is_file($document_root.$library_dir.$file)){
            print "<link rel='stylesheet' type='text/css' href='".$library_dir.$file."' />\n";
         }
      }
   } // end print_library_css()

   /**
    * print_module_css() Print the css link tags of the module.
    *
    * @access private
    * @param {string} $module_id The module id.
    */
   private function print_module_css($module_id){
      // do we have the required params?
      if(!isset($module_id) || $module_id == ''){
         return '';
      }

      // check group privilege (is the member allowed to load this module)
      $this->os->load('session');
      $group_id = $this->os->session->get_group_id();

      if(!$group_id){
         return '';
      }

      if(!$this->os->is_group_allowed($group_id, $module_id)){
         return '';
      }

      $this->os->load('module');

      // get any dependencies and print their link tags first
      $dependencies = $this->os->module->get_dependencies($module_id);
      if($dependencies){
         $this->print_css($dependencies);
      }

      // get the css files of the module and print their link tags
      $files = $this->os->module->get_client_files($module_id, 'css');

      if(!$files){
         return '';
      }

      $document_root = $this->os->get_document_root();
      $module_dir = $this->os->get_module_dir();

      foreach($files as $file){
         // todo: log errors
         if(is_file($document_root.$module_dir.$file)){
            print "<link rel='stylesheet' type='text/css' href='".$module_dir.$file."' />\n";
         }
      }
   } // end print_module_css()

   /**
    * print_javascript() Will take an object or an array of objects that contain an 'id' and 'type' property.
    * The 'id' property holds the id of a library or module.
    * The 'type' property specifies if it is a 'library' or a 'module'.
    *
    * @access public
    * @param {array/object} $items An array of objects or an object.
    */
   public function print_javascript($items){
      // do we have the required param?
      if(!isset($items)){
         return '';
      }

      // is the param an array of items?
      if(is_array($items) && count($items == 0)){
         // loop through the items
         foreach($items as $item){
            $this->print_item_javascript($item);
         }
      }

      // is the param  an object?
      else if(is_object($items)){
         $this->print_item_javascript($items);
      }
   } // end print_javascript()

   /**
    * print_item_javascript()
    *
    * @access private
    * @param {stdClass} $item An object with 'id' and 'type' properties.
    */
   private function print_item_javascript($item){
      // do we have the required param?
      if(!isset($item->id, $item->type)){
         return '';
      }

      // is the item a library?
      if($item->type == 'library'){
         $this->print_library_javascript($item->id);
      }

      // is the item a module?
      else if($item->type == 'module'){
         $this->print_module_javascript($item->id);
      }
   } // end print_item_javascript()

   /**
    * print_library_javascript() Prints the contents of the javascript files of a library.
    *
    * @access private
    * @param {string} $library_id The id of the library.
    */
   private function print_library_javascript($library_id){
      // do we have the required params?
      if(!isset($library_id) || $library_id == ''){
         return '';
      }

      $this->os->load('library');

      // get any dependencies and print their contents first
      $dependencies = $this->os->library->get_dependencies($library_id);
      if($dependencies){
         $this->print_javascript($dependencies);
      }

      // get the javascript files of the library and print their contents
      $files = $this->os->library->get_client_files($library_id, 'javascript');

      if(!$files){
         return '';
      }

      $document_root = $this->os->get_document_root();
      $library_dir = $document_root.$this->os->get_library_dir();

      foreach($files as $file){
         // todo: log errors
         if(is_file($library_dir.$file)){
            $string = file_get_contents($library_dir.$file);
            if($string){
               print $string;
            }
         }
      }
   } // end print_library_javascript()

   /**
    * print_module_javascript() Prints the content of the javascript files of the module.
    * 
    * @access private
    * @param {string} $module_id The id of the module.
    */
   private function print_module_javascript($module_id){
      // do we have the required params?
      if(!isset($module_id) || $module_id == ''){
         return '';
      }

      // check group privilege (is the member allowed to load this module)
      $this->os->load('session');
      $group_id = $this->os->session->get_group_id();

      if(!$group_id){
         return '';
      }

      if(!$this->os->is_group_allowed($group_id, $module_id)){
         return '';
      }

      $this->os->load('module');

      // get any dependencies and print their contents first
      $dependencies = $this->os->module->get_dependencies($module_id);
      if($dependencies){
         $this->print_javascript($dependencies);
      }

      // todo: localize

      // get the javascript files of the module and print their contents
      $files = $this->os->module->get_client_files($module_id, 'javascript');

      if(!$files){
         return '';
      }

      $document_root = $this->os->get_document_root();
      $module_dir = $document_root.$this->os->get_module_dir();
         
      foreach($files as $file){
         // todo: log errors
         if(is_file($module_dir.$file)){
            $string = file_get_contents($module_dir.$file);
            if($string){
               print $string;
            }
         }
      }
   } // end print_module_javascript()

   /**
    * get_theme_id() Returns the id of the theme set.
    *
    * @access private
    */
	private function get_theme_id(){
      $this->os->load('session');
      $member_id = $this->os->session->get_member_id();
      $group_id = $this->os->session->get_group_id();

      if(!isset($member_id, $group_id)){
         return null;
      }

      $theme_id = $this->query_for_theme_id($member_id, $group_id);

      // use the default?
      if(!isset($theme_id)){
         $theme_id = $this->query_for_theme_id('0', '0');
      }

      if(isset($theme_id)){
         return $theme_id;
      }
	} // end get_theme_id()

   /** query_for_theme_id()
    *
    * @param {integer} $member_id
    * @param {integer} $group_id
    */
   private function query_for_theme_id($member_id, $group_id){
      $sql = "SELECT
         data
         FROM
         qo_groups_has_member_preferences
         WHERE
         qo_members_id = ".$member_id."
         AND
         qo_groups_id = ".$group_id;

      $result = $this->os->db->conn->query($sql);
      if($result){
         $row = $result->fetch(PDO::FETCH_ASSOC);
         if($row){
            $decoded = json_decode($row['data']);
            if(is_object($decoded) && isset($decoded->themeId) && $decoded->themeId != ''){
               return $decoded->themeId;
            }
         }
      }

      return null;
   } // end query_for_theme_id()
}
?>