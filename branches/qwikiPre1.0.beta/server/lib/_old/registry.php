<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class registry {

	private $os;

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

      $this->os = $os;
	} // end __construct()

   /** init() Initial page load or refresh has occured.
     * Called from init() of os.php.
     *
     * @access public
     **/
   public function init(){
      if(isset($_SESSION['registry'])){
         unset($_SESSION['registry']);
      }

      $_SESSION['registry'] = new stdClass();
   } // end init()

   /** get() Return a registry value.
     *
     * @access public
     * @param {string} $path A list of registry indexes seperated by forward ( / ) slashes ( optional ).
     *
     * Example: 'module/qo-preferences'
     **/
   public function get($path=''){
      $base = $_SESSION['registry'];

      if($path != ''){
         $keys = explode('/', $path);

         if(count($keys) > 0){
            foreach($keys as $key){

               if(is_array($base)){
                  if(!isset($base[$key])){
                     return null;
                  }
                  $base = $base[$key];
               }

               else if(is_object($base)){
                  if(!isset($base->$key)){
                     return null;
                  }
                  $base = $base->$key;
               }

            }
         }
      }

      return $base;
   } // end get()

   /** set() Set a registry item to value.
     * Only the top level items can be set ( e.g. 'member', 'module', 'privilege', etc. ).
     *
     * @access public
     * @param {string} $key The registry key to set ( optional ).
     * @param {object/array/string} $value The value ( optional ).
     **/
   public function set($key='', $value=null){
      if(isset($key) && $key != ''){
         $_SESSION['registry']->$key = $value;

         return true;
      }

      return false;
   } // end set()

   /** update() Update a registry item.
     *
     * @param {string} $path The path ( e.g. 'module/qo-preferences/loaded' ).
     * @param {object/array/string} $value The value.
     **/
   public function update($path, $value){
      if(isset($path) && $path != ''){
         $item = $this->get($path);
         $item = $value;

         return true;
      }
      
      return false;
   } // end update()
}
?>