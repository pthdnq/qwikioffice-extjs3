<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class module {

	private $document_root = null;
   private $errors = array();
	private $module_dir = null;
	private $os = null;

	public function __construct($os){
      $os->load('session');
      if(!$os->session->exists()){ die('Session does not exist!'); }

		$this->module_dir = $os->get_module_dir();
		$this->document_root = $os->get_document_root();

		// needed libraries
		$os->load('member');
		$os->load('group');
		$os->load('privilege');

      $this->os = $os;
	}

	/** init() Initial page load or refresh has occured.
	  * Called from init() of os.php
	  **/
	public function init(){
		$this->prepare();
	} // end init()

   /** get_definitions() Returns an array of stdClass objects
     *
     * @param {array} $ids The moduleId's
     **/
   private function get_definitions($ids){
      $filters = array(
         array('key' => 'defines', 'values' => array('module')),
         array('key' => 'module_id', 'values' => $ids)
      );

      $this->os->load('definition');
      return $this->os->definition->get($filters);
   } // end get_definitions()



	/** prepare() Builds an associative array that contains all the module data for the user.
	  * Runs at startup/refresh.
	  * Loops through the active modules that the member is allowed to load.
	  *
	  * 1.) Checks the localization support.
	  * 2.) Checks the dependencies.
	  * 3.) Checks the files
	  * 4.) Logs errors.
	  **/
   private function prepare(){
      $member_id = $this->os->member->get_id();
      $group_id = $this->os->group->get_id();

      if($member_id != "" && $group_id != ""){
         $modules = array();

			$sql = "SELECT
				id,
				module_id AS moduleId
				FROM
				qo_modules
				WHERE
				active = 1";

         $result = $this->os->db->conn->query($sql);
			if($result){
				$action = $this->os->get_module_load_action();

				while($row = $result->fetch(PDO::FETCH_ASSOC)){
					// if the member is allowed to load this module
					if($this->os->privilege->is_allowed($action, $row["moduleId"], $member_id, $group_id)){

						// initialize
                  $modules[ $row['moduleId'] ]['has_error'] = 0;

						// check localization support
						$this->check_locale($modules, $row['id']);

						// check the dependencies
						$this->check_dependencies($modules, $row['id']);

						// check the files
						$this->check_files($modules, $row['id']);

					}
				}

            $this->os->registry->set('modules', $modules); // store in the registry

				// report errors
				if(count($this->errors) > 0){
					$this->os->load('log');
				    $this->os->log->error($this->errors);
				}
			}
		}
	} // end prepare()

	/** check_locale()
	  *
	  * $param {integer} $id The server (database) module id
	  **/
	private function check_locale(&$modules, $id){
		if($id != ''){
			$member_locale = $this->os->member->get_locale();
			$found_locale = null;

			$sql = "SELECT
				module_id AS moduleId,
				locales,
				locale_directory AS directory
				FROM
				qo_modules
				WHERE
				id = ".$id;

         $result = $this->os->db->conn->query($sql);
			if($result){
            $row = $result->fetch(PDO::FETCH_ASSOC);
				if($row){
					if($row['locales'] != '' && $row['directory'] != ''){
						$locales = explode(',', $row['locales']);

						for($i = 0, $len = count($locales); $i < $len; $i++){
							if($locales[$i] == $member_locale){
								$found_locale = $locales[$i];
								break;
							}
						}

						if(!$found_locale){
							// use first locale listed as the default
							$found_locale = $locales[0];
						}

						$locale_directory = $this->module_dir.$row['directory'].$found_locale.'/';

						// if the localization files are found
						if(is_file($locale_directory.'declaration.js') && is_file($locale_directory.'override.js')){
                     $modules[ $row['moduleId'] ]['locale_directory'] = $locale_directory;
						}
						// log error
						else{
							$this->errors[] = 'Script: module.php, Method: check_locale, Message: Missing localization file - '.$locale_directory;
                     $modules[ $row['moduleId'] ]['has_error'] = 1;
						}
					}
				}
			}
		}
	} // end check_locale()

	/** check_dependencies()
	  *
	  * $param {integer} $id The server (database) module id
	  **/
	private function check_dependencies(&$modules, $id){
		if($id != ''){
			$sql = "SELECT
		    	M.module_id AS moduleId,
		    	D.id,
				CONCAT(D.directory, D.file) AS path,
				D.is_stylesheet
				FROM
				qo_modules_has_dependencies AS MD
					INNER JOIN qo_dependencies AS D ON D.id = MD.qo_dependencies_id
					INNER JOIN qo_modules AS M ON M.id = MD.qo_modules_id
				WHERE
				M.id = ".$id;

         $result = $this->os->db->conn->query($sql);
			if($result){
            $dependencies = array();
            
				while($row = $result->fetch(PDO::FETCH_ASSOC)){
					$path = $row['path'];

					if($path == ''){
						continue;
					}

					$path = $this->module_dir.$path;

					// if the filename does not exist and is not a regular file, record error and skip it
					if(!is_file($path)){
						$this->errors[] = 'Script: module.php, Method: check_dependencies, Message: Missing file - '.$path;
                  $modules[ $row['moduleId'] ]['has_error'] = 1;
						continue;
					}

					$dependencies[ $row['id'] ]['is_stylesheet'] = $row['is_stylesheet'];
					$dependencies[ $row['id'] ]['path'] = $path;
					$dependencies[ $row['id'] ]['is_loaded'] = 0;

               $modules[ $row['moduleId'] ]['dependencies'][] = $row['id'];
            }

            $this->os->registry->set('dependencies', $dependencies); // store in the registry
         }
      }
   } // end check_dependencies()

	/** check_files() Loops through the module's files and makes sure they exist and are a regular file.
	  * Any errors found will be logged.
	  *
	  * $param {integer} $id The server (database) module id
	  **/
	private function check_files(&$modules, $id){
	    if($id != ''){
		    $sql = "SELECT
				M.module_id AS moduleId,
				F.directory,
				F.file,
				F.is_stylesheet,
				F.is_client_module,
				F.is_server_module,
				F.class_name AS class
				FROM
				qo_modules_files AS F
					INNER JOIN qo_modules AS M ON M.id = F.qo_modules_id
				WHERE
				M.id = ".$id;

         $result = $this->os->db->conn->query($sql);
			if($result){
				// loop through all module files
				while($row = $result->fetch(PDO::FETCH_ASSOC)){
					$has_error = false;
					$error_message = '';
					$directory = $row['directory'];
					$file = $row['file'];

					// if the directory or file is missing
					if($directory == '' || $file == ''){
						$error_message = 'Message: '.$row['moduleId'].' has a missing directory or file in the qo_module_files table';
						$has_error = true;
					}

					$path = $this->module_dir.$directory.$file;

					// if the filename does not exist and is not a regular file
					if(!is_file($path)){
						$error_message = 'Message: Missing file: '.$path;
						$has_error = true;
					}

					// if we have a valid path (directory/file)
					if($has_error == false){
						// is the file a stylesheet
						if($row['is_stylesheet'] == 1){
							$modules[ $row['moduleId'] ]['stylesheets'][]['path'] = $path;
						}

						// is the file the client module
						else if($row['is_client_module'] == 1){
							$modules[ $row['moduleId'] ]['client_module']['path'] = $path;

							// is the client module class available
							if($row['class'] != ''){
								$modules[ $row['moduleId'] ]['client_module']['class'] = $row['class'];
							}else{
							    $error_message = 'Message: Missing class for client module: '.$row['moduleId'];
							    $has_error = true;
							}
						}

						// is the file the server module
						else if($row['is_server_module'] == 1){
							$modules[ $row['moduleId'] ]['server_module']['path'] = $path;

							// is the server module class available
							if($row['class'] != ''){
								$modules[ $row['moduleId'] ]['server_module']['class'] = $row['class'];
							}else{
							    $error_message = 'Message: Missing class for server module: '.$row['moduleId'];
							    $has_error = true;
							}
						}

						// must be a supporting client side file
						else{
							$modules[ $row['moduleId'] ]['files'][]['path'] = $path;
						}
					}

					// was an error found
					if($has_error == true){
					    $modules[ $row['moduleId'] ]['has_error'] = 1;
					    $this->errors[] = 'Script: module.php, Method: check_files, Message: '.$error_message;
					}
				}
			}
	    }
	} // end check_files

	/** create_instances() Returns new instances of the loaded modules.
	  * Used by getModules() of QoDesk.php
	  **/
	public function create_instances(){
      $response = '';
      $modules = $this->os->registry->get('modules');

      if($modules){
         foreach($modules as $module){
				if($module['has_error'] == 0){
					$response .= "new ".$module['client_module']['class']."(),";
				}
			}
         $response = rtrim($response, ","); // trim the trailing comma
      }

		return $response;
	} // end create_instances()

	/** get_link_tags() Prints link tags for the module's css files.
	  * Used by index.php
	  **/
	public function get_link_tags(){
      $modules = $this->os->registry->get('modules');

      if($modules){
         foreach($modules as $moduleId => $module){
				if($module['has_error'] == 0){
					$this->get_dep_link_tags($moduleId);

					if(isset($module['stylesheets'])){
						foreach($module['stylesheets'] as $stylesheet){
							print "<link rel='stylesheet' type='text/css' href='".$stylesheet['path']."' />\n";
						}
					}
				}
			}
      }
	} // end get_link_tags()

	/** get_dep_link_tags() Prints link tags for the module dependencies css files.
	  **/
	private function get_dep_link_tags($moduleId){
      $modules = $this->os->registry->get('modules');
      $dependencies = $this->os->registry->get('dependencies');

      if($modules && $dependencies){
         if(isset($modules[$moduleId]['dependencies'])){
            foreach($modules[$moduleId]['dependencies'] as $dependency_id){ // for each dependency
               if($dependencies[$dependency_id]['is_stylesheet'] == 1){ // is it a stylesheet
                  if($dependencies[$dependency_id]['is_loaded'] == 0){ // has it been loaded

                     print "<link rel='stylesheet' type='text/css' href='".$dependencies[$dependency_id]['path']."' />\n";
                     $dependencies[$dependency_id]['is_loaded'] = 1; // mark it as loaded
                  }
               }
            }
         }
      }
	} // end get_dep_link_tags()

	/** load() Prints the contents of the module's javascript files.
	  * Dependencies will also be loaded if needed.
	  * Provides Module on Demand functionality.
	  * Used by load.php
	  *
	  * @param {string} $moduleId The client moduleId property
	  **/
	public function load($moduleId){
      $modules = $this->os->registry->get('modules');
      $dependencies = $this->os->registry->get('dependencies');

      if($modules && $dependencies){
         // if module has an error
         if($modules[$moduleId]['has_error'] == 1){
            die("{success: false, msg: 'There is a problem with this module'}");
         }

         // load dependency files
         if(isset($modules[$moduleId]['dependencies'])){
            foreach($modules[$moduleId]['dependencies'] as $dependency_id){ // for each dependency
               if($dependencies[$dependency_id]['is_stylesheet'] == 0){ // if not a stylesheet
                  if($dependencies[$dependency_id]['is_loaded'] == 0){ // has it been loaded

                     print file_get_contents($this->document_root.$dependencies[$dependency_id]['path']);
                     $dependencies[$dependency_id]['is_loaded'] = 1; // mark it as loaded
                  }
               }
            }
         }

         // load localization override file
         if(isset($modules[$moduleId]['locale_directory'])){
            print file_get_contents($this->document_root.$modules[$moduleId]['locale_directory'].'override.js');
         }

         // load module files
         if(isset($modules[$moduleId]['files'])){
            foreach($modules[$moduleId]['files'] as $file){
               print file_get_contents($this->document_root.$file['path']);
            }
         }
      }
	} // end load()

	/** load_declarations() Prints the content of the declaration files of the modules the member has access to.
	  * Used by QoDesk.php
	  **/
	public function load_declarations(){
      $modules = $this->os->registry->get('modules');

      if($modules){
         foreach($modules as $module){
            if($module['has_error'] == 0){
               print file_get_contents($this->document_root.$module['client_module']['path']);

               // load localization declaration file
               if(isset($module['locale_directory'])){
                  print file_get_contents($this->document_root.$module['locale_directory'].'declaration.js');
               }
            }
         }
      }
	} // end load_declarations()

	/** run_action() Will check the users privileges and execute the action if allowed
	  *
	  * @param {string} $moduleId The client moduleId property
	  * @param {string} $action, the name of the action/method to call (e.g. $module->action())
	  **/
	public function run_action($moduleId, $action){
		$member_id = $this->os->member->get_id();
		$group_id = $this->os->group->get_id();

		if($member_id == '' || $group_id == ''){
			die("{success: false, msg: 'You are not currently logged in'}");
		}else{
			//if member is allowed this action on this module
			if(!$this->os->privilege->is_allowed($action, $moduleId, $member_id, $group_id)){
				die("{success: false, msg: 'You do not have the required privileges!'}");
			}

			$error_found = false;
			$error_message = '';

         $modules = $this->os->registry->get('modules');
         if($modules){
            // if the server module is found
            if(isset($modules[$moduleId]['server_module'])){
               $file = $this->document_root.$modules[$moduleId]['server_module']['path'];
               $class = $modules[$moduleId]['server_module']['class'];

               if($class != ''){
                  // if the filename exists and is a regular file
                  if(is_file($file)){
                     require($file);

                     // if the class exists
                     if(class_exists($class)){
                        $module = new $class($this->os);

                        // if the method exists, run it
                        if(method_exists($module, $action)){
                           $module->$action();
                        }else{
                           $error_found = true;
                            $error_message = 'Message: '.$action.' does not exist for server module: '.$moduleId;
                        }
                     }else{
                        $error_found = true;
                         $error_message = 'Message: '.$class.' does not exist for server module: '.$moduleId;
                     }
                  }else{
                     $error_found = true;
                      $error_message = 'Message: File missing for server module: '.$moduleId;
                  }
               }else{
                  $error_found = true;
                   $error_message = 'Message: Class name missing for server module: '.$moduleId;
               }
            }else{
               $error_found = true;
               $error_message = 'Message: Missing server module for: '.$moduleId;
            }
         }
		}

		// report errors
		if($error_found){
			$this->errors[] = 'Script: module.php, Method: run_action, Message: '.$error_message;
			$this->os->load('log');
		    $this->os->log->error($this->errors);
		}
	} // end run_action()

	/** get_id() Returns the id of a module's database record.
	  *
	  * @param {string} $moduleId The client module's moduleId property.
	  **/
	public function get_id($moduleId){
		$id = '';

		if($this->os->session->exists() && $moduleId != ""){
			$sql = "SELECT
				id
				FROM
				qo_modules
				WHERE
				module_id = '".$moduleId."'";

         $result = $this->os->db->conn->query($sql);
			if($result){
            $row = $result->fetch(PDO::FETCH_ASSOC);
				if($row){
					$id = $row["id"];
				}
			}
		}

		return $id;
	} // end get_id()

// Todo: finish module install code

	/** install_module() install the module(s)
	  *
	  * @param {string} $path The path to the module
	  * @return {boolean}
	  **/
	public function install_module($path){
        // TODO: enforce security

        $response = true;

        // get ids if needed
		//$member_id = $member_id != "" ? $member_id : $this->os->member->get_id();
		//$group_id = $group_id != "" ? $group_id : $this->os->group->get_id();

		//if($member_id != "" && $group_id != "" && is_array($info) && count($info) > 0){

        $document_root = $this->os->get_document_root();
        $modules_dir = $this->os->get_module_dir();
        $modules_dir = $document_root.$modules_dir;
        //$modules_dir = preg_replace("/\/modules\/(.)*/", "/modules/", $modules_dir);
        $module_dir =preg_replace("/(.)*modules\//", "", $path);
        $path = str_replace("//", "/", $modules_dir.$module_dir);

        // if this is a directory
        if(is_dir($path)){

            // open the directory
            $dh = opendir($path);
            if($dh){

            	// loop through each file in the directory
	            while(($file = readdir($dh)) !== false){

	            	// if the file is a directory and not the current or parent directory
	                if(is_dir($path.$file) && $file != '.' && $file != '..' && $file != '.svn'){ // .svn for testing locally

	                	// use recursive call to search sub directories
	                	$this->install_module($path.$file."/");
	                }

	                // if file is module.xml file
	                if($file == "module.xml"){
						//print 'XML Found: '.$path.$file.'<br />';
	                	// read the xml file
	                	$info = $this->read_xml($path.$file);
print '<pre>';
print_r($info);
print '</pre>';
		                // if the module is not already installed
		                if(!$this->is_installed($info["module_id"], $info["version"])){
		                	// register the module into the database
//		                	if(!$this->register($info)){
//		                		$response = false;
//		                	}
		                }else{
		                	$response = false;
		                }
					}else{
						$response = false;
					}
            	}

            	closedir($dh);
			}else{
				$response = false;
			}
        }else{
        	$response = false;
        }

        return $response;
    } // end install_module()

    /** read_xml() Reads the module.xml file
	  *
	  * @access private
	  * @param {string} $file The file path
	  * @return {array}
	  **/
    private function read_xml($file){
        $info= Array();

        $dom = new DomDocument();
        $dom->load($file);

		// info
		$info['author'] = $dom->getElementsByTagName('author')->item(0)->nodeValue;
      $info['version'] = $dom->getElementsByTagName('version')->item(0)->nodeValue;
      $info['name'] = $dom->getElementsByTagName('name')->item(0)->nodeValue;
      $info['url'] = $dom->getElementsByTagName('url')->item(0)->nodeValue;
      $info['description'] = $dom->getElementsByTagName('description')->item(0)->nodeValue;
      $info['module_type'] = $dom->getElementsByTagName('module_type')->item(0)->nodeValue;
      $info['module_id'] = $dom->getElementsByTagName('module_id')->item(0)->nodeValue;
      $info['locales'] = $dom->getElementsByTagName('locales')->item(0)->nodeValue;
      $info['locale_directory'] = $dom->getElementsByTagName('locale_directory')->item(0)->nodeValue;

		// actions
		$row = $dom->getElementsByTagName('action');
		for($i = 0, $len = $row->length; $i < $len; $i++){
			$info['actions'][$i] = $row->item($i)->nodeValue;
		}

		// dependencies
		$row = $dom->getElementsByTagName('dependency');
		for($i = 0, $len = $row->length; $i < $len; $i++){
			$info['dependencies'][$i] = $row->item($i)->nodeValue;
		}

		// files
		$count = 0;
		$dataset = $dom->getElementsByTagName('file');
		foreach($dataset as $row){
			// attributes
			$info['files'][$count]['is_stylesheet'] = 0;
			$info['files'][$count]['is_server_module'] = 0;
			$info['files'][$count]['is_client_module'] = 0;
			$info['files'][$count]['class_name'] = '';

			if($row->hasAttributes()){
				// type
				$type = $row->getAttribute('type');

				if($type == 'stylesheet'){ $info['files'][$count]['is_stylesheet'] = 1; }
				else if($type == 'server_module'){ $info['files'][$count]['is_server_module'] = 1; }
				else if($type == 'client_module'){ $info['files'][$count]['is_client_module'] = 1; }

				// class_name
				if($type == 'client_module' || $type == 'server_module'){
					$info['files'][$count]['class_name'] = $row->getAttribute('class_name');
				}
			}

			// directory
			$xml_directories = $row->getElementsByTagName('directory');
			$info['files'][$count]['directory'] = $xml_directories->item(0)->nodeValue;

			// name
			$xml_names = $row->getElementsByTagName('name');
			$info['files'][$count]['name'] = $xml_names->item(0)->nodeValue;

			$count++;
		}

		return $info;
	} // end read_xml()

	/** is_installed() checks whether a module is installed
	  *
	  * @param {string} $moduleId The module id
	  * @param {string} $version The module version
	  * @return {boolean}
	  **/
	private function is_installed($moduleId, $version){
		// TODO: enforce security

		$response = false;

		// get ids if needed
		//$member_id = $member_id != "" ? $member_id : $this->get_member_id();
		//$group_id = $group_id != "" ? $group_id : $this->get_group_id($member_id);

		//if($member_id != "" && $group_id != "" && $moduleId != "" && $version != ""){
		if($moduleId != "" && $version != ""){
			$sql = "SELECT
				id
				FROM
				qo_modules
				WHERE
				module_id = '".$moduleId."'
				AND
				version = '".$version."'";

         $result = $this->os->db->conn->query($sql);
			if($result){
            $row = $result->fetch(PDO::FETCH_ASSOC);
				if($row){
					$response = true;
				}
			}
		}

		return $response;
	} // end is_installed()

	/** register() registers/inserts the module data into the database
	  *
	  * @param $info array, the module info
	  * @return {boolean} Success
	  **/
	private function register($info){
		// TODO: enforce security

		$response = true;

		// get ids if needed
		$member_id = $member_id != "" ? $member_id : $this->os->member->get_id();
		$group_id = $group_id != "" ? $group_id : $this->os->group->get_id();

		//if($member_id != "" && $group_id != "" && is_array($info) && count($info) > 0){
		if(is_array($info) && count($info) > 0){

			//print "<pre>";
			//print_r($info);
			//print "</pre>";

			// insert into qo_modules table
			$sql = "INSERT INTO qo_modules (
				author,
				version,
				url,
				name,
				description,
				module_type,
				module_id,
				locales,
				locale_directory,
				active
				) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			// prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
			$sql = $this->os->db->conn->prepare($sql);
			$sql->bindParam(1, $info['author']);
			$sql->bindParam(2, $info['version']);
			$sql->bindParam(3, $info['url']);
			$sql->bindParam(4, $info['name']);
			$sql->bindParam(5, $info['description']);
			$sql->bindParam(6, $info['module_type']);
			$sql->bindParam(7, $info['module_id']);
			$sql->bindParam(8, $info['locales']);
			$sql->bindParam(9, $info['locale_directory']);
			$sql->bindParam(10, 1);
			$sql->execute();

			$code = $sql->errorCode();
			if($code == '00000'){
				// Todo: register actions
				$response = $this->register_files($info);
			}else{
				$this->errors[] = "Script: module.php, Method: register, Message: PDO error code - ".$code;
				$this->os->load('log');
				$this->os->log->error($this->errors);
			}
		}else{
			$response = false;
		}

		return $response;
	} // end register()

	private function register_files($info){
		if(!is_array($info) || !isset($info['files'])){ return false; }
		$response = true;

		// Todo: enforce security

		// get record id of the module
		$sql = "SELECT
			id
			FROM
			qo_modules
			WHERE
			moduleId = '".$info['module_id']."'
			AND
			version = '".$info['version']."'";

      $result = $this->os->db->conn->query($sql);
		if($result){
         $row = $result->fetch(PDO::FETCH_ASSOC);
			if($row){
				$id = $row['id'];
				$files = $info['files'];

				// insert each file into qo_modules_files table
				for($i = 0, $len = count($files); $i < $len; $i++){
					$sql = "INSERT INTO qo_modules_files (
						qo_modules_id,
						directory,
						file,
						is_stylesheet,
						is_server_module,
						is_client_module,
						class_name) VALUES (?, ?, ?, ?, ?, ?, ?)";

					// prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
					$sql = $this->os->db->conn->prepare($sql);
					$sql->bindParam(1, $id);
					$sql->bindParam(2, $files[$i]['directory']);
					$sql->bindParam(3, $files[$i]['name']);
					$sql->bindParam(4, $files[$i]['is_stylesheet']);
					$sql->bindParam(5, $files[$i]['is_server_module']);
					$sql->bindParam(6, $files[$i]['is_client_module']);
					$sql->bindParam(7, $files[$i]['class_name']);
					$sql->execute();

					$code = $sql->errorCode();
					if($code != '00000'){
						$this->errors[] = "Script: module.php, Method: register_files, Message: PDO error code - ".$code;
						$this->os->load('log');
						$this->os->log->error($this->errors);
						$response = false;
					}
			    }
			}
		}else{
			$response = false;
		}

		return $response;
	} // end register_files()
}
?>