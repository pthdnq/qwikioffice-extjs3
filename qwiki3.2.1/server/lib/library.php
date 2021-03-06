<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class library {

   //private $errors = array();
	private $library_dir = null;
   private $os = null;
	
	/**
    * __construct()
    *
    * @access public
    * @param {class} $os The os.
    */
	public function __construct(os $os){
      if(!$os->session_exists()){
         die('Session does not exist!');
      }

      $this->document_root = $os->get_document_root();
		$this->library_dir = $os->get_library_dir();
      $this->os = $os;
	} // end __construct()

   /**
    * get_all() Get all library definitions.
    *
    * @access public
    * @return {array} An associative array with the library id as the index.
    */
   public function get_all(){
      $sql = "SELECT
         id,
         data
         FROM
         qo_libraries";

      return $this->query($sql);
   } // end get_all()

   /**
    * get_active() Get active library definitions.
    *
    * @access public
    * @return {array} An associative array with the library id as the index.
    */
   public function get_active(){
      $sql = "SELECT
         id,
         data
         FROM
         qo_libraries
         WHERE
         active = 1";

      return $this->query($sql);
   } // end get_active()

   /**
    * get_by_id()
    *
    * @param {string} $id The library id.
    * @return {stdClass} A data object
    */
   public function get_by_id($id){
      if(isset($id) && $id != ''){
         $sql = "SELECT
            id,
            data
            FROM
            qo_libraries
            WHERE
            id = '".$id."'";

         $result = $this->query($sql);

         if($result){
            return $result[$id];
         }
      }

      return null;
   } // end get_by_id()

   /**
    * query() Run a select query against the database.
    *
    * @access private
    * @param {string} $sql The select statement.
    * @return {array} An associative array with the definition id as the index.
    */
   private function query($sql){
      if(isset($sql) && $sql != ''){
         $result = $this->os->db->conn->query($sql);

         if($result){
            return $this->parse_result($result);
         }
      }

      return null;
   } // end query()

   /**
    * parse_result() Parses the query result.  Expects 'id' and 'data' fields.
    *
    * @access private
    * @param {PDOStatement} $result The result set as a PDOStatement object.
    * @return {array} An associative array with the definition id as the index.
    */
   private function parse_result($result){
      $response = array();

      if($result){
         $errors = array();

         while($row = $result->fetch(PDO::FETCH_ASSOC)){
            // decode the json data
            $decoded = json_decode($row['data']);

            if(!is_object($decoded)){
               $errors[] = "Script: library.php, Method: parse_result, Message: 'qo_libraries' table, 'id' ".$row['id']." has 'data' that could not be decoded";
               continue;
            }

            $response[$row['id']] = $decoded;
         }

         // errors to log?
         if(count($errors) > 0){
            $this->os->load('log');
            $this->os->log->error($errors);
         }
      }

      return count($response) > 0 ? $response : null;
   } // end parse_result()

   /**
    * get_dependencies() Returns an array of dependency objects.
    *
    * @param {string} $library_id
    * @return {array}
    */
   public function get_dependencies($library_id){
      // do we have the required params?
      if(!isset($library_id) || $library_id == ''){
         return null;
      }

      $library = $this->get_by_id($library_id);
      if(!$library || !isset($library->dependencies) || !is_array($library->dependencies)){
         return null;
      }

      return $library->dependencies;
   } // end get_dependencies()

   /** get_client_files() Returns an array with the files in the order listed ( load order ) in the library definition data.
     * The client files are expected to be listed in the library definition data like so:
     *
     * "client": {
     *    "css": [
     *       {
     *          "directory": "demo/grid-win/client/resources/",
     *          "files": [ "styles.css" ]
     *       }
     *    ],
     *    "javascript": [
     *       {
     *          "directory": "demo/grid-win/client/",
     *          "files": [
     *            "grid-win.js"
     *          ]
     *       }
     *    ]
     * }
     *
     * @access public
     * @param {string} $library_id The library id.
     * @param {string} $key The key to access (.e.g. 'css' or 'javascript').
     * @return {array/null} An array of the file paths on success.  Null on failure.
     **/
   public function get_client_files($library_id, $key){
      // do we have the required params
      if(!isset($library_id) || $library_id == '' || !isset($key) || $key == ''){
         return null;
      }

      $library = $this->get_by_id($library_id);
      if(!$library || !isset($library->client->$key) || !is_array($library->client->$key)){
         return null;
      }

      $file_groups = $library->client->$key;
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
   } // end get_client_files()





   /**
    * print_link_tags() Prints the link tags for each of the library stylesheets.
    * Used by index.php
    *
	public function print_link_tags(){
      // get the library definitions from the registry
      $libraries = $this->os->registry->get($this->os->REG_KEY_LIBRARY);
      if(isset($libraries) && is_array($libraries) && count($libraries) > 0){

         // loop through each library definition
         foreach($libraries as $library){

            // get the stylesheet files for each library
            $files = $this->get_files($library, 'stylesheets');
            if($files){
               foreach($files as $file){
                  if(is_file($this->library_dir.$file)){
                     print "<link rel='stylesheet' type='text/css' href='".$this->library_dir.$file."' />\n";
                  }
               }
            }

         }

      }
   } // end print_link_tags() */

   /** prepare() Prepare a library definition for the registry.
     * Used by kernal.php
     *
     * @access public
     * @param {stdClass} $w The library definition
     * @return {stdClass/null} The library definition on success, null on failure.
	  **
   public function prepare($w){
      if(!is_object($w) || $w->defines != 'library'){
         return null;
      }

      // validate
      if(!$this->validate($w)){
         return null;
      }

      return $w;
   } // end prepare() */

   /** validate() Validate the library definition.
     *
     * @access private
     * @param {stdClass} $w The library definition.
     * @return {boolean}
     **
   private function validate($w){
      // library definition?
      if(!is_object($w) || $w->defines != 'library'){
         return false;
      }

      $valid = true;

      // validate client files
      if(isset($w->clientFiles)){
         $files = $this->get_files($w, 'clientFiles');

         if($files){
            foreach($files as $file){
               if(!is_file($this->library_dir.$file)){
                  // log error with library
                  $valid = false;
               }
            }
         }
      }

      if($valid == false){
         return false;
      }

      // validate stylesheets
      if(isset($w->stylesheets)){
         $files = $this->get_files($w, 'stylesheets');

         if($files){
            foreach($files as $file){
               if(!is_file($this->library_dir.$file)){
                  // log error with library
                  $valid = false;
               }
            }
         }
      }

      return $valid;
   } // end validate() */

   /** get_files() Returns an array with the file paths in the order listed ( load order ) in the definition.
     * Library definition files are listed in file groups like so:
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
     * @param {stdClass} $definition The library definition
     * @param {string} $key The definition key to get the files from.
     * @return {array/null} An array of the file paths on success.  Null on failure.
     **
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
   } // end get_files() */
}
?>