<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class definition {

	protected $os = null;

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
    * get_all() Get all definitions.
    *
    * @access public
    * @param {string} $defines The value that represents what the definition defines (e.g. 'library', 'module', 'privilege', 'theme', 'wallpaper').
    * @return {array} An associative array with the definition id as the index.
    */
   public function get_all($defines = null){
      $sql = "SELECT
         id,
         data
         FROM
         qo_definitions";

      if($this->defines_value_ok($defines)){
         $sql .= " WHERE defines = ".$defines;
      }

      return $this->query($sql);
   } // end get_all()

   /**
    * get_active() Get active definitions.
    *
    * @access public
    * @param {string} $defines The value that represents what the definition defines (e.g. 'library', 'module', 'privilege', 'theme', 'wallpaper').
    * @return {array} An associative array with the definition id as the index.
    */
   public function get_active($defines = null){
      $sql = "SELECT
         id,
         data
         FROM
         qo_definitions
         WHERE
         active = 1";

      if($this->defines_value_ok($defines)){
         $sql .= " AND defines = ".$defines;
      }

      return $this->query($sql);
   } // end get_active()

   /**
    * get_by_id() Get a definition by its id.
    *
    * @access public
    * @param {string} $id The id of the definiton.
    * @return {stdClass} The decoded data object.
    */
   public function get_by_id($id){
      if(isset($id) && $id != ''){
         $sql = "SELECT
            id,
            data
            FROM
            qo_definitions
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
    * query() Run a select query against the database 'qo_definitions' table.
    *
    * @access protected
    * @param {string} $sql The select statement.
    * @return {array} An associative array with the definition id as the index.
    */
   protected function query($sql){
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
    * @access protected
    * @param {PDOStatement} $result The result set as a PDOStatement object.
    * @return {array} An associative array with the definition id as the index.
    */
   protected function parse_result($result){
      $response = array();

      if($result){
         $errors = array();

         while($row = $result->fetch(PDO::FETCH_ASSOC)){
            // decode the json data
            $decoded = json_decode($row['data']);

            if(!is_object($decoded)){
               $errors[] = "Script: definition.php, Method: parse_result, Message: 'qo_definitons' table, 'id' ".$row['id']." has 'data' that could not be decoded";
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
    * defines_value_ok() Only allow valid defines values.
    *
    * @param {string} $defines The value of defines.
    * @return {boolean}
    */
   private function defines_value_ok($defines = null){
      if(isset($defines) && $defines != ''){
         switch($defines){
            case 'library':
            case 'module':
            case 'privilege':
            case 'theme':
            case 'wallpaper':
               return true;
         }
      }

      return false;
   } // end defines_value_ok()
}
?>