<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class definition {

	private $os = null;
   private $document_root = null;
   private $definition_dir = null;

   /** __construct()
    *
    * @param {class} $os The os.
    */
	public function __construct($os){
      $os->load('session');
      if(!$os->session->exists()){
         die('Session does not exist!');
      }

      $this->document_root = $os->get_document_root();
      $this->definition_dir = $os->get_definition_dir();
      $this->definition_dir = $this->document_root.$this->definition_dir;

      $this->os = $os;
	} // end __construct()

   /** get_all() Get all definitions.
    *
    * @access public
    * @param {boolean} $from_db (optional)
    * @return {array} An associative array with the definition id as the index.
    */
   public function get_all($from_db=true){
      if($from_db == true){
         return $this->get_from_db();
      }
   } // end get_all()

   /** get_all_active() Get all definitions.
    *
    * @access public
    * @param {boolean} $from_db (optional)
    * @return {array} An associative array with the definition id as the index.
    */
   public function get_all_active($from_db=true){
      if($from_db == true){
         return $this->get_from_db('WHERE active = 1');
      }
   } // end get_all_active()

   /** get_by_id()
    *
    * @access public
    * @param {string} $id The id of the definiton.
    * @param {boolean} $from_db (optional) True to get from the database.
    */
   public function get_by_id($id, $from_db=true){
      if(!isset($id) || $id == ''){
         return null;
      }

      if($from_db == true){
         return $this->get_from_db('WHERE id = '.$id);
      }
   } // end get_by_id()

   /** get_from_db()
    *
    * @access private
    * @param {string} $clause (optional) The WHERE clause for the select query.
    * @return {array}
    */
   private function get_from_db($clause=''){
      $response = array();
      $errors = array();
      $sql = "SELECT id, data FROM qo_definitions ".$clause;

      $result = $this->os->db->conn->query($sql);
      if($result){
         while($row = $result->fetch(PDO::FETCH_ASSOC)){
            // decode the json data
            $decoded = json_decode($row['data']);
            if(!is_object($decoded)){
               $errors[] = "Script: definition.php, Method: get_from_db, Message: 'qo_definitons' table, 'id' ".$row['id']." has 'data' that could not be decoded";
               continue;
            }

            $response[$row['id']] = $decoded;
         }
      }

      // errors to log?
      if(count($errors) > 0){
         $this->os->load('log');
         $this->os->log->error($errors);
      }

      return $response;
   } // end get_from_db()

   /** get_from_files() Will return an array of definition objects.
    *
    * @access private
    * @param {array} $filters (optional) An associative array.
    * @param {string} $assoc_key (optional) The definition key to use as the return array indexes.
    * @return {array}
    *
    * Example filters:
    * $filters = array(
    *    // only want module definitions
    *    array('key' => 'defines', 'values' => array('module')),
    *    // for each 'moduleId'
    *    array('key' => 'moduleId', 'values' => array('demo-acc', 'qo-preferences'))
    * );
    */
   private function get_from_files($filters=null, $assoc_key=null){
      $response = array();
      $errors = array();
      $apply_filters = $filters != null && is_array($filters) && count($filters) > 0 ? true : false;

      // find all definition files
      $files = $this->find_files();

      // loop through the found files
      for($i = 0, $iLen = count($files); $i < $iLen; $i++){
         // put each file into a string
         $string = file_get_contents($files[$i]);
         if(!$string){
            $errors[] = 'Script: definition.php, Method: get_from_files, Message: Bad definition file - '.$files[$i];
            continue;
         }

         // decode the json data
         $decoded = json_decode($string);
         if(!is_object($decoded)){
            $errors[] = 'Script: definition.php, Method: get_from_files, Message: Incorrect JSON data in definition file - '.$files[$i];
            continue;
         }

         $definition = null;

         // filters?
         if($apply_filters){
            // if definition passes the filters
            if($this->filter($decoded, $filters)){
               $definition = $decoded;
            }
         }else{
            $definition = $decoded;
         }

         if($definition){
            // return associative array?
            if($assoc_key){
               $response[$definition->$assoc_key] = $definition;
            }else{
               $response[] = $definition;
            }
         }
      }

      // errors to log?
      if(count($errors) > 0){
         $this->os->load('log');
         $this->os->log->error($errors);
      }

      return $response;
   } // end get_from_files()

   /** find_files() Will return the paths to definition files.
    * Does not search sub directories
    *
    * @access {private}
    * @return {array}
    */
	private function find_files(){
      $files = array();
      $path = str_replace("//", "/", $this->definition_dir);

      if(is_dir($path)){
         $dh = opendir($path);
         if($dh){
            // loop through each file in the directory
	         while(($file = readdir($dh)) !== false){
               $ext = end(explode('.', $file));
               if(is_file($path.$file) && $ext == 'json'){
                  $files[] = $path.$file;
					}
            }
            closedir($dh);
			}
      }

      return $files;
   } // end find_files()

   /** find_files() Will find the paths to definition files.
    * Uses recursion to search sub directories.
    *
    * @param {array} $files The array to hold the found definition file paths.
    * @param {string} $path The search path (optional)
	 *
	private function find_files(&$files, $path=''){
      $path = preg_replace("/(.)*modules\//", "", $path);
      $path = str_replace("//", "/", $this->definition_dir.$path);

      if(is_dir($path)){
         $dh = opendir($path);
         if($dh){
            // loop through each file in the directory
	         while(($file = readdir($dh)) !== false){
               if(is_dir($path.$file) && $file != '.' && $file != '..' && $file != '.svn'){ // .svn for testing locally
                  $this->find_files($files, $path.$file."/");
               }
	            if($file == 'definition.json'){
                  $files[] = $path.$file;
					}
            }
            closedir($dh);
			}
      }
   } // end find_files() */

   /** filter() Checks a definition against the filters.
    *
    * @param {stdClass} $definition The definition object.
    * @param {array} $filters An associative array.
    * @return {boolean}
    */
   private function filter($definition, $filters){
      $filter_count = count($filters);
      $found_count = 0;

      foreach($filters as $filter){
         $key = $filter['key'];
         $values = $filter['values'];

         if(isset($definition->$key)){
            foreach($values as $value){
               if($definition->$key == $value){
                  $found_count++;
               }
            }
         }
      }

      return $filter_count == $found_count ? true : false;
   } // end filter()
}
?>