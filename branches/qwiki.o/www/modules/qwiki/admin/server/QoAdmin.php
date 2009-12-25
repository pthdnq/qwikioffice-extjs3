<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class QoAdmin {

   private $os;

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

		$this->os = $os;
	} // end __construct()

	// begin public module actions

   // members

   /**
    * viewAllMembers()
    *
    * @access public
    */
	public function viewAllMembers(){
		$response = '{qo_members: []}';

		$sql = 'SELECT
			id,
			first_name,
			last_name,
			email_address,
			active
			FROM
			qo_members
			ORDER BY
			last_name ASC';

      $result = $this->os->db->conn->query($sql);
		if($result){
			$items = array();

			while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $row['active'] = $row['active'] == 1 ? true : false;
				$items[] = $row;
			}

			$response = '{"qo_members":'.json_encode($items).'}';
		}

		print $response;
	} // end viewAllMembers()

   /**
    * viewMember
    *
    * @access public
    */
   public function viewMember(){
      $response = '{success:false}';
      $member_id = $_POST['memberId'];

      if(isset($member_id) && $member_id != '' && is_numeric($member_id)){
         $sql = 'SELECT
            first_name,
            last_name,
            email_address,
            password,
            active
            FROM
            qo_members
            WHERE
            id = '.$member_id;

         $result = $this->os->db->conn->query($sql);
         if($result){

            $row = $result->fetch(PDO::FETCH_ASSOC);
            if($row){
               $row['active'] = $row['active'] == 1 ? true : false;
               $response = '{"success": true, "data":'.json_encode($row).'}';
            }
         }
      }

      print $response;
   } // end viewMember()

   /**
    * addMember()
    *
    * @access public
    */
	public function addMember(){
		$response = "{success:false}";

		// make all the strings safe
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$email_address = $_POST['email_address'];
		$password = $_POST['password'];
		$active = $_POST['active'];

	    $a = $this->add_member($first_name, $last_name, $email_address, $password, $active);

		if($a["success"] == "true" && $a["id"] != ""){
			$response = "{success:true, id: ".$a["id"]."}";
		}

	    print $response;
	} // end addMember()

   /**
    * editMember()
    *
    * @access public
    */
   public function editMember(){
      $response = '{success:false}';

      $member_id = $_POST['memberId'];
      $field = $_POST['field'];
      $value = isset($_POST['value']) ? $_POST['value'] : '';

      if(isset($field, $member_id) && $field != '' && $member_id != '' && is_numeric($member_id)){
         if($field == 'all'){
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email_address = $_POST['email_address'];
            //$password = $_POST['password'];
            $active = $_POST['active'];

            // convert active field to a 1 or 0
            if($active == 'true'){
               $active = 1;
            }else{
               $active = 0;
            }

            $sql = 'UPDATE qo_members SET first_name = ?, last_name = ?, email_address = ?, active = '.$active.' WHERE id = '.$member_id;

            // prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
            $sql = $this->os->db->conn->prepare($sql);
            $sql->bindParam(1, $first_name);
            $sql->bindParam(2, $last_name);
            $sql->bindParam(3, $email_address);
            //$sql->bindParam(4, $password);
            $sql->execute();

            $code = $sql->errorCode();
            if($code == '00000'){
               $response = '{success:true}';
            }else{
               $this->errors[] = 'Script: QoAdmin.php, Method: editMember, Message: PDO error code - '.$code;
               $this->os->load('log');
               $this->os->log->error($this->errors);
            }

         }else if(isset($value) && $value != ''){
            // convert active field to a 1 or 0
            if($field == 'active'){
               if($value == 'true'){
                  $value = 1;
               }else{
                  $value = 0;
               }
            }

            $sql = "UPDATE qo_members SET ".$field." = ? WHERE id = ".$member_id;

            // prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
            $sql = $this->os->db->conn->prepare($sql);
            $sql->bindParam(1, $value);
            $sql->execute();

            $code = $sql->errorCode();
            if($code == '00000'){
               $response = '{success: true}';
            }else{
               $this->errors[] = 'Script: QoAdmin.php, Method: editMember, Message: PDO error code - '.$code;
               $this->os->load('log');
               $this->os->log->error($this->errors);
            }
         }
      }

      print $response;
   } // end editMember()

   /**
    * addMemberToGroup()
    *
    * @access public
    */
   public function addMemberToGroup(){
      $response = "{'success': false}";

      $group_id = $_POST["groupId"];
      $member_id = $_POST["memberId"];

      if($group_id != "" && $member_id != ""){
         $active = "true";
         $admin = "false";
         if($this->add_member_to_group($member_id, $group_id, $active, $admin)){
            $response = "{'success': true}";
         }
      }

      print $response;
   } // end addMemberToGroup()

   /**
    * deleteMemberFromGroup
    *
    * @access public
    */
   public function deleteMemberFromGroup(){
      $response = "{'success': false}";

      $group_id = $_POST["groupId"];
      $member_id = $_POST["memberId"];

      if($group_id != "" && $member_id != ""){
         $sql = "DELETE FROM
            qo_groups_has_members
            WHERE
            qo_members_id = ".$member_id."
            AND
            qo_groups_id = ".$group_id;

         $st = $this->os->db->conn->prepare($sql);
         $st->execute();

         $code = $st->errorCode();
         if($code == '00000'){
            //$result = true;
            $response = "{'success': true}";
         }else{
            $this->errors[] = "Script: QoAdmin.php, Method: deleteMemberFromGroup, Message: PDO error code - ".$code;
            $this->os->load('log');
            $this->os->log->error($this->errors);
         }
      }

      print $response;
   } // deleteMemberFromGroup()

   /**
    * deleteMembers()
    *
    * @access public
    */
	public function deleteMembers(){
		$member_ids = $_POST['memberIds'];
		$member_ids = json_decode(stripslashes($member_ids));

		$r = array();
		$k = array();

      if(is_array($member_ids) && count($member_ids) > 0){
         foreach($member_ids as $id){
            $success = false;

            // delete the member from any preferences
            if($this->delete_group_member_preference('member', $id)){
               // delete the member from any groups
               if($this->delete_group_member_relationship('member', $id)){
                  // delete the member from any sessions
                  if($this->delete_members_session($id)){
                     // delete the member
                     if($this->delete_member($id)){
                        $success = true;
                     }
                  }
               }
            }

            if($success){
               $r[] = $id;
            }else{
               $k[] = $id;
            }
         }
      }

      // return ids of removed and kept
      print '{r: '.json_encode($r).', k: '.json_encode($k).'}';
   } // end deleteMembers()

   // Signup Requests

	// todo: auto generate an email message
	public function approveSignupsToGroup(){
		$signup_ids = $_POST["signupIds"];
		$signup_ids = json_decode(stripslashes($signup_ids));
		$group_id = $_POST["groupId"];

		$r = array(); // ids to remove from the grid
		$k = array(); // ids to keep, attempt failed

		if(count($signup_ids) > 0 && $group_id != ""){
		    foreach($signup_ids as $id){
				$success = false;

				$sql = "SELECT COUNT(*) FROM qo_signup_requests WHERE id = ".$id;

				$result = $this->os->db->conn->query($sql);

		        if($result->fetchColumn() > 0){

		        	$sql2 = "SELECT
                  first_name,
                  last_name,
                  email_address
                  FROM
                  qo_signup_requests
                  WHERE
                  id = ".$id;

					$result2 = $this->os->db->conn->query($sql2);

		        	$row = $result2->fetch(PDO::FETCH_ASSOC);

               $this->os->load('utility');
		        	$password = substr($this->os->utility->build_random_id(), 0, 8);
		        	$active = "true";

		        	// add this new member
		        	$returned = $this->add_member($row["first_name"], $row["last_name"], $row["email_address"], $password, $active);

		        	if($returned["success"]){

			        	// add this new member to the group
			        	$active = "true";
						$admin = "false";
						if($this->add_member_to_group($returned["id"], $group_id, $active, $admin)){

							// delete from the qo_signup_requests table
							if($this->delete_signup($id)){
								$success = true;
							}
			        	}
			        }
		        }

		        if($success){
					$r[] = $id;
				}else{
					$k[] = $id;
				}
		    }
		}

		// return ids of removed and kept
		print '{success: true, r: '.json_encode($r).', k: '.json_encode($k).'}';
	}

   /**
    * denySignups()
    *
    * @access public
    */
   public function denySignups(){
      $signup_ids = $_POST['signupIds'];
      $signup_ids = json_decode(stripslashes($signup_ids));

      $r = array(); // ids to remove from the grid
      $k = array(); // ids to keep, attempt failed

      if(count($signup_ids) > 0){
         foreach($signup_ids as $id){

            if($this->delete_signup($id)){
               $r[] = $id;
            }else{
               $k[] = $id;
            }
         }
      }

      // return ids of removed and kept
      print '{success: true, r: '.json_encode($r).', k: '.json_encode($k).'}';
   } // end denySignups()

   /**
    * markSignupsAsSpam()
    *
    * @access public
    */
   public function markSignupsAsSpam(){
      $signup_ids = isset($_POST['signupIds']) ? $_POST['signupIds'] : null;
      $signup_ids = json_decode(stripslashes($signup_ids));

      $r = array(); // ids to remove from the grid
      $k = array(); // ids to keep, attempt failed

      if(count($signup_ids) > 0){
         foreach($signup_ids as $id){
            $success = false;

            $sql = "SELECT COUNT(*) FROM qo_signup_requests WHERE id = ".$id;

            $result = $this->os->db->conn->query($sql);

            if($result->fetchColumn() > 0){
               $sql2 = "SELECT
                  email_address
                  FROM
                  qo_signup_requests
                  WHERE
                  id = ".$id;

               $result2 = $this->os->db->conn->query($sql2);

               $row = $result2->fetch(PDO::FETCH_ASSOC);

               if($this->add_spam($row['email_address'])){
                  if($this->delete_signup($id)){
                     $success = true;
                  }
               }
            }

            if($success){
               $r[] = $id;
            }else{
               $k[] = $id;
            }
         }
      }

      // return ids of removed and kept
      print '{success: true, r: '.json_encode($r).', k: '.json_encode($k).'}';
   } // end markSignupsAsSpam()

	/**
    * loadGroupsCombo()
    * If memberId is passed in, returns only the groups the member is not currently assigned to
    *
    * @access public
    */
   public function loadGroupsCombo(){
      $response = "{'success': false, 'total': 0, 'results': []}";

      $member_id = $_POST['memberId'];

      $sql = "SELECT COUNT(*) FROM qo_groups";

      $result = $this->os->db->conn->query($sql);

      if($result->fetchColumn() > 0){
         $all_groups = array();

         $sql2 = "SELECT
            id,
            name,
            description,
            active
            FROM
            qo_groups";

         $result2 = $this->os->db->conn->query($sql2);

         while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $all_groups[] = $row;
         }
      }

      $groups = array();

      if($member_id != ''){
         $sql = "SELECT COUNT(*) FROM qo_groups_has_members WHERE qo_members_id = ".$member_id;

         $result = $this->os->db->conn->query($sql);

         if($result->fetchColumn() > 0){
            $group_ids = array();

            $sql2 = "SELECT
               qo_groups_id AS id
               FROM
               qo_groups_has_members
               WHERE
               qo_members_id = ".$member_id;

            $result2 = $this->os->db->conn->query($sql2);

            while($row = $result2->fetch(PDO::FETCH_ASSOC)){
               $group_ids[] = $row;
            }
         }

         $all_groups_len = count($all_groups);

         for($i = 0; $i < $all_groups_len; $i++){
            $group_ids_len = isset($group_ids) ? count($group_ids) : 0;
            $found = false;

            for($j = 0; $j < $group_ids_len; $j++){
               if($all_groups[$i]["id"] == $group_ids[$j]["id"]){
                  $found = true;
               }
            }

            if(!$found){
               $groups[] = $all_groups[$i];
            }
         }
      }else{
         $groups = $all_groups;
      }

      if(count($groups) > 0){
         $response = '{"success": true, "total": '.count($groups).', "groups":'.json_encode($groups).'}';
      }

      print $response;
   } // end loadGroupsCombo()

   /**
    * loadPrivilegesCombo()
    * If groupId is passed in, returns only the privileges the group is not currently assigned to
    *
    * @access public
    */
   public function loadPrivilegesCombo(){
      $response = "{'success': false, 'total': 0, 'results': []}";

      $group_id = $_POST['groupId'];

      $sql = "SELECT COUNT(*) FROM qo_privileges";

      $result = $this->os->db->conn->query($sql);

      if($result->fetchColumn() > 0){
         $all_privileges = array();

         $sql2 = "SELECT
            id,
            data,
            active
            FROM
            qo_privileges";

         $result2 = $this->os->db->conn->query($sql2);

         while($row = $result2->fetch(PDO::FETCH_ASSOC)){
            $data = json_decode($row['data']);
            if($data){
               $all_privileges[] = array(
                  "id" => $row['id'],
                  "name" => $data->name,
                  "active" => $row['active']
               );
            }
         }
      }

      $privileges = array();

      if($group_id != ''){
         $sql = "SELECT COUNT(*) FROM qo_groups_has_privileges WHERE qo_groups_id = ".$group_id;

         $result = $this->os->db->conn->query($sql);

         if($result->fetchColumn() > 0){
            $privilege_ids = array();

            $sql2 = "SELECT
               qo_privileges_id AS id
               FROM
               qo_groups_has_privileges
               WHERE
               qo_groups_id = ".$group_id;

            $result2 = $this->os->db->conn->query($sql2);

            while($row = $result2->fetch(PDO::FETCH_ASSOC)){
               $privilege_ids[] = $row;
            }
         }

         $all_privileges_len = count($all_privileges);

         for($i = 0; $i < $all_privileges_len; $i++){
            $privilege_ids_len = isset($privilege_ids) ? count($privilege_ids) : 0;
            $found = false;

            for($j = 0; $j < $privilege_ids_len; $j++){
               if($all_privileges[$i]["id"] == $privilege_ids[$j]["id"]){
                  $found = true;
               }
            }

            if(!$found){
               $privileges[] = $all_privileges[$i];
            }
         }
      }else{
         $privileges = $all_privileges;
      }

      if(count($privileges) > 0){
         $response = '{"success": true, "total": '.count($privileges).', "privileges":'.json_encode($privileges).'}';
      }

      print $response;
   } // end loadPrivilegesCombo()

   /**
    * viewAllPrivileges()
    *
    * @access public
    */
	public function viewAllPrivileges(){
		$response = '{qo_members: []}';

		$sql = 'SELECT
			id,
			data,
			active
			FROM
			qo_privileges';

      $result = $this->os->db->conn->query($sql);
		if($result){
			$items = array();

			while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $decoded = json_decode($row['data']);
            if($decoded){
               $items[] = array(
                  "id" => $row['id'],
                  "name" => $decoded->name,
                  "description" => $decoded->description,
                  "active" => $row['active'] == 1 ? true : false
               );
            }
			}

			$response = '{"qo_privileges":'.json_encode($items).'}';
		}

		print $response;
	} // end viewAllPrivileges()

   /**
    * viewAllGroups() Returns all the groups.
    *
    * @access public
    */
   public function viewAllGroups(){
      $response = "{'qo_groups': []}";

      $sql = 'SELECT
         id,
         name,
         description,
         importance,
         active
         FROM
         qo_groups
         ORDER BY
         name ASC';

      $result = $this->os->db->conn->query($sql);
      if($result){
         $items = array();

         while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $row['active'] = $row['active'] == 1 ? true : false;
            $items[] = $row;
         }

         $response = '{"qo_groups":'.json_encode($items).'}';
      }

      print $response;
   } // end viewAllGroups()

   /**
    * viewGroup
    *
    * @access public
    */
   public function viewGroup(){
      $response = '{success:false}';
      $group_id = $_POST['groupId'];

      if(isset($group_id) && $group_id != '' && is_numeric($group_id)){
         $sql = 'SELECT
            name,
            description,
            active
            FROM
            qo_groups
            WHERE
            id = '.$group_id;

         $result = $this->os->db->conn->query($sql);
         if($result){
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if($row){
               $row['active'] = $row['active'] == 1 ? true : false;
               $response = '{"success": true, "data":'.json_encode($row).'}';
            }
         }
      }

      print $response;
   } // end viewGroup()

   /**
    * addGroup()
    *
    * @access public
    */
   public function addGroup(){
      $response = '{success:false}';

      $name = $_POST['name'];
      $description = $_POST['description'];
      $active = $_POST['active'];

      $a = $this->add_group($name, $description, $active);

      if($a['success'] == 'true' && $a['id'] != ''){
         $response = '{success:true, id:'.$a['id'].'}';
      }

      print $response;
   } // end addGroup()

   /**
    * editGroup()
    *
    * @access public
    */
   public function editGroup(){
      $response = '{success:false}';

      $group_id = $_POST['groupId'];
      $field = $_POST['field'];
      $value = isset($_POST['value']) ? $_POST['value'] : '';

      if(isset($field, $group_id) && $field != '' && $group_id != '' && is_numeric($group_id)){
         if($field == 'all'){
            $name = $_POST['name'];
            $description = $_POST['description'];
            $active = $_POST['active'];

            // convert active field to a 1 or 0
            if($active == 'true'){
               $active = 1;
            }else{
               $active = 0;
            }

            $sql = 'UPDATE qo_groups SET name = ?, description = ?, active = '.$active.' WHERE id = '.$group_id;

            // prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
            $sql = $this->os->db->conn->prepare($sql);
            $sql->bindParam(1, $name);
            $sql->bindParam(2, $description);
            $sql->execute();

            $code = $sql->errorCode();
            if($code == '00000'){
               $response = '{success:true}';
            }else{
               $this->errors[] = 'Script: QoAdmin.php, Method: editGroup, Message: PDO error code - '.$code;
               $this->os->load('log');
               $this->os->log->error($this->errors);
            }

         }else if(isset($value) && $value != ''){
            // convert active field to a 1 or 0
            if($field == 'active'){
               if($value == 'true'){
                  $value = 1;
               }else{
                  $value = 0;
               }
            }

            $sql = "UPDATE qo_groups SET ".$field." = ? WHERE id = ".$group_id;

            // prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
            $sql = $this->os->db->conn->prepare($sql);
            $sql->bindParam(1, $value);
            $sql->execute();

            $code = $sql->errorCode();
            if($code == '00000'){
               $response = '{success: true}';
            }else{
               $this->errors[] = 'Script: QoAdmin.php, Method: editGroup, Message: PDO error code - '.$code;
               $this->os->load('log');
               $this->os->log->error($this->errors);
            }
         }
      }

      print $response;
   } // end editGroup()

   /**
    * deleteGroups()
    *
    * @access public
    */
	public function deleteGroups(){
		$group_ids = $_POST['groupIds'];
		$group_ids = json_decode(stripslashes($group_ids));

		$r = array();
		$k = array();

      if(is_array($group_ids) && count($group_ids) > 0){
         foreach($group_ids as $id){
            $success = false;

            // delete the group from any preferences
            if($this->delete_group_member_preference('group', $id)){
               // delete the group from any members
               if($this->delete_group_member_relationship('group', $id)){
                  // delete the group from any privileges
                  if($this->delete_group_privilege_relationship('group', $id)){
                     // delete the group
                     if($this->delete_group($id)){
                        $success = true;
                     }
                  }
               }
            }

            if($success){
               $r[] = $id;
            }else{
               $k[] = $id;
            }
         }
      }

      // return ids of removed and kept
      print '{r: '.json_encode($r).', k: '.json_encode($k).'}';
   } // end deleteGroups()

   /**
    * changeGroupPrivilege()
    *
    * @access public
    */
   public function changeGroupPrivilege(){
      $response = "{'success': false}";

      $group_id = $_POST['groupId'];
      $privilege_id = $_POST['privilegeId'];

      if(isset($group_id, $privilege_id) && $group_id != '' && $privilege_id != ''){
         // delete existing
         $sql = "DELETE
            FROM
            qo_groups_has_privileges
            WHERE
            qo_groups_id = ".$group_id;

         $this->os->db->conn->query($sql);

         // add new record
         $sql = "INSERT INTO qo_groups_has_privileges (qo_groups_id, qo_privileges_id) VALUES (?, ?)";

         $sql = $this->os->db->conn->prepare($sql);
         $sql->bindParam(1, $group_id);
         $sql->bindParam(2, $privilege_id);
         $sql->execute();

         $code = $sql->errorCode();
         if($code == '00000'){
            $response = "{'success': true}";
         }else{
            $this->errors[] = "Script: QoAdmin.php, Method: changeGroupPrivilege, Message: PDO error code - ".$code;
            $this->os->load('log');
            $this->os->log->error($this->errors);
         }
      }

      print $response;
   } // end changeGroupPrivilege()
   /**
    * View module
    *
    * @param unknown_type $id
    */
   public function viewModule(){
   		$id = $_POST['id'];
   		$response = '{success:false}';
   		$module = $this->os->module->get_by_id($id);
   		$response = '{"qo_module":'.json_encode($module).'}';
		print $response;
   }

	/**
	 * View all modules method
	 *
	 */
	public function viewAllModules(){
		$response = '{success:false}';
		$modules = $this->os->module->get_all();
		$children = array();
		foreach($modules as $module_id => $module){
			$module_item = new stdClass();
			$module_item->id = $module_id;
			$module_item->name = $module->about->name;
			$module_item->description = $module->about->description;
			$module_item->active = $this->os->module->is_active($module_id);
			$children[]=$module_item;
		}

		$response = '{"qo_modules":'.json_encode($children).'}';
		print $response;
	}
   /**
    * viewModuleMethods()
    * returun string json
    */
   public function viewModuleMethods(){
      $response = '{success:false}';
			$privilegeId = isset($_POST['privilegeId'])?intval($_POST['privilegeId']):null;
      // get all the module data
      $this->os->load('module');
      $modules = $this->os->module->get_all();

      if(!isset($modules) || !is_array($modules) || count($modules) == 0){
         print $response;
         return false;
      }

      $nodes = array();
			$this->os->load('privilege');
      // loop through each module
      foreach($modules as $module_id => $module){
         $module_node = new stdClass();

         //$module_node->checked = false;
         $module_node->checked = $this->os->privilege->is_allowed($privilegeId,$module->id);
         $module_node->moduleId = $module->id;
         $module_node->iconCls = 'qo-admin-app';
         $module_node->id = $module->id;
         $module_node->text = $module->about->name;

         $children = array();

         // does the module have server methods?
         if(isset($module->server->methods) && is_array($module->server->methods) && count($module->server->methods) > 0){
            foreach($module->server->methods as $method){
               if(!isset($method->name)){
                  continue;
               }

               $method_node = new stdClass();

               //$method_node->checked = false;
               $method_node->checked = $this->os->privilege->is_allowed($privilegeId,$module->id,$method->name);
               $method_node->iconCls = 'qo-admin-method';
               $method_node->id = $method->name;
               $method_node->leaf = true;
               $method_node->text = $method->name;

               $children[] = $method_node;
            }
         }

         if(count($children) > 0){
            $module_node->children = $children;
         }else{
            $module_node->leaf = true;
         }

         $nodes[] = $module_node;
      }

      if(count($nodes) == 0){
         print $response;
         return false;
      }

      print json_encode($nodes);
   } // end viewModuleMethods()

   public function viewMethods(){
   	$response = '{success:false}';

   	if(!empty($_POST['id'])){
   	// get all the module data
      $this->os->load('module');
      $module = $this->os->module->get_by_id($_POST['id']);
      if(!empty($module->server)){
      	print '{succes:true,methods:'.json_encode($module->server->methods).'}';
      	return true;
      }
   	}
   	print $response;
   }
   /**
    * Change status of module
    * acitve = 1|0
    *
    */
   public function changeModuleStatus(){
   	$response = '{success:false}';
   	$status = 0;

   	if($_POST['value']=='true'){
   		$status = 1;
   	}
   	if($_POST['value']=='false'){
   		$status = 0;
   	}

   	$module = $_POST['id'];
   	$return = $this->os->module->set_active($module,$status);
   	if($return == 1){
   		$response = '{success:true}';
   	}else{
   		$response = '{success:false,error:"error change status"}';
   	}

   	print $response;
   }

   /**
    * viewGroupPrivileges() Returns data to load an Ext.tree.TreePanel
    *
    * @access public
    */
	public function viewGroupPrivileges(){
      $response = '{success:false}';
      $group_id = $_POST['groupId'];

      // do we have the group id?
      if(!isset($group_id) || $group_id == ''){
         print $response;
         return false;
      }

      // get the privilege id associated with the group
      $this->os->load('group');
      $privilege_id = $this->os->group->get_privilege_id($group_id);

      if(!$privilege_id){
         print $response;
         return false;
      }

      // get the privilege nodes
      $nodes = $this->build_privilege_nodes($privilege_id);

      if(count($nodes) == 0){
         print $response;
         return false;
      }

      print json_encode($nodes);
      return true;
   } // end viewGroupPrivileges()

   /**
    * viewMemberGroups() Returns data to load an Ext.tree.TreePanel
    *
    * @access public
    */
   public function viewMemberGroups(){
      $response = '{success:false}';
      $member_id = $_POST['memberId'];

      // do we have the member id?
      if(!isset($member_id) || $member_id == ''){
         print $response;
         return false;
      }

      $this->os->load('group');

      // get all of the groups the member is assigned to
      $groups = $this->os->group->get_by_member_id($member_id);
      if(!isset($groups) || !is_array($groups) || count($groups) == 0){
         print $response;
         return false;
      }

      $data = $this->build_group_nodes($groups);

      if(count($data) == 0){
         print $response;
         return false;
      }

      print json_encode($data);
      return true;
   } // end viewMemberGroups()

   /**
    * viewPrivilegeModules() Returns data to load an Ext.tree.TreePanel
    *
    * @access public
    */
   public function viewPrivilegeModules(){
      $response = '{success:false}';
      $privilege_id = $_POST['privilegeId'];

      // do we have the privilege id?
      if(!isset($privilege_id) || $privilege_id == ''){
         print $response;
         return false;
      }

      // get the privilege nodes
      $nodes = $this->build_privilege_nodes($privilege_id);

      // we only need the privilege child nodes
      if(count($nodes) == 0 || !isset($nodes[0]->children) || !is_array($nodes[0]->children)){
         print $response;
         return false;
      }

      print json_encode($nodes[0]->children);
      return true;
   } // end viewPrivilegeModules()

   /**
    * build_group_nodes() Return group data to build an Ext.tree.Node
    *
    * @acess private
    * @param {array} $groups
    * @return {array}
    */
   private function build_group_nodes($groups){
      $nodes = array();

      // do we have the required param?
		if(!isset($groups) || !is_array($groups) || count($groups) == 0){
         return $nodes;
      }

      // loop through each group
      foreach($groups as $group){
         // build the node data
         $data = new stdClass();

         $data->active = $group['active'];
         $data->groupId = $group['id'];
         $data->iconCls = 'qo-admin-group';
         $data->text = $group['name'];
         $data->uiProvider = 'col';

         // node children data?
         $children = null;

         // get the group privilege id
         $privilege_id = $this->os->group->get_privilege_id($group['id']);
         if($privilege_id){
            $children = $this->build_privilege_nodes($privilege_id);
         }

         if($children){
            $data->children = $children;
         }else{
            $data->leaf = true;
         }

         $nodes[] = $data;
      }

      return $nodes;
	} // end build_group_nodes()

   /**
    * build_privilege_nodes() Returns an array of data (config for an Ext.tree.Node)
    *
    * @acess private
    * @param {integer/array} $param A privilege id or an array of privilege ids.
    * @return {array}
    */
   private function build_privilege_nodes($param){
      $nodes = array();

      // do we have the required param?
      if(!isset($param) && !is_numeric($param) && !is_array($param)){
         return $nodes;
      }

      $ids = null;

      // if the param is an integer (id) then create an array
      if(is_numeric($param)){
         $ids = array($param);
      }

      // else if the param is an array
      else if(is_array($param) && count($param) > 0){
         $ids = $param;
      }

      if(!$ids){
         return $nodes;
      }

      $this->os->load('privilege');

      // loop through each privilege id
      foreach($ids as $id){
         // get the privilege record
         $privilege = $this->os->privilege->get_record($id);

         // if a record was not returned then continue
         if(!isset($privilege)){
            continue;
         }

         // build the privilege node data
         $privilege_node = new stdClass();

         $privilege_node->active = $privilege->active;
         $privilege_node->iconCls = 'qo-admin-privilege';
         $privilege_node->privilegeId = $privilege->id;
         $privilege_node->text = $privilege->data->name;
         $privilege_node->uiProvider = 'col';

         $privilege_children = array();

         // does the privilege have any children (modules)
         if(isset($privilege->data->modules) && is_array($privilege->data->modules) && count($privilege->data->modules) > 0){
            foreach($privilege->data->modules as $module){
               if(!isset($module->id)){
                  continue;
               }

               // get the module record data
               $module_record = $this->os->module->get_record($module->id);
               if(!$module_record){
                  continue;
               }

               $module_node = new stdClass();

               $module_node->active = $module_record->active;
               $module_node->iconCls = 'qo-admin-app';
               $module_node->moduleId = $module->id;
               $module_node->text = $module_record->data->about->name;
               $module_node->uiProvider = 'col';

               $module_children = array();

               // does the module have any children (methods)
               if(isset($module->methods) && is_array($module->methods) && count($module->methods) > 0){
                  foreach($module->methods as $method){
                     if(!isset($method->name)){
                        continue;
                     }

                     $method_node = new stdClass();

                     //$method_node->active = $module_record->active;
                     $method_node->iconCls = 'qo-admin-method';
                     $method_node->methodId = $method->name;
                     $method_node->text = $method->name;
                     $method_node->uiProvider = 'col';
                     $method_node->leaf = true;

                     $module_children[] = $method_node;
                  }
               }

               if(count($module_children)){
                  $module_node->children = $module_children;
               }else{
                  $module_node->leaf = true;
               }

               $privilege_children[] = $module_node;
            }
         }

         if(count($privilege_children)){
            $privilege_node->children = $privilege_children;
         }else{
            $privilege_node->leaf = true;
         }

         $nodes[] = $privilege_node;
      }

      return $nodes;
	} // end build_privilege_nodes()

   /**
    * build_module_nodes() Return module data to build an Ext.tree.Node
    *
    * @acess private
    * @param {array} $modules The modules property (array) from a privilege definition data
    * @return {array}
    */
   private function build_module_nodes($modules){
      if(!is_array($modules)){
         return null;
      }

      $this->os->load('group');
      $this->os->load('privilege');

		// get the group privilege id
      $privilege_id = $this->os->group->get_privilege_id($modules);

      // get the simplified privilege data
      $privilege = $this->os->privilege->simplify($privilege_id);

      $nodes = array();

      if(isset($privilege)){
         $this->os->load('module');

         // loop through the allowed modules
         foreach($privilege as $module_id => $methods){
            // get the module data
            $module = $this->os->module->get_by_id($module_id);

            if(isset($module)){
               $data = new stdClass();

               $data->active = $this->os->module->is_active($module_id) ? 1 : 0;
               $data->iconCls = 'qo-admin-app';
               $data->moduleId = $module->id;
               $data->text = $module->about->name;
               $data->uiProvider = 'col';

               $children = $this->build_method_nodes($methods);
               if($children){
                  $data->children = $children;
               }else{
                  $data->leaf = true;
               }

               $nodes[] = $data;
            }
         }
      }

      if(count($nodes) == 0){
         return null;
      }

      return $nodes;
	} // end build_module_nodes()

   /**
    * build_method_nodes() Return method data to build an Ext.tree.Node
    *
    * @acess private
    * @param <type> $methods
    * @return {array}
    */
   private function build_method_nodes($methods){
		if(!isset($methods) || !is_array($methods) || count($methods) == 0){
         return null;
      }

      $nodes = array();

      foreach($methods as $method => $allow){
         // methods
         $data = new stdClass();

         $data->iconCls = 'qo-admin-method';
         $data->methodId = $method;
         $data->leaf = true;
         $data->text = $method;
         $data->uiProvider = 'col';

         $nodes[] = $data;
      }

      if(count($nodes) == 0){
         return null;
      }

      return $nodes;
	} // end build_method_nodes()

   /**
    * viewAllSignups()
    *
    * @access public
    */
   public function viewAllSignups(){

		$response = "{'qo_signups': []}";

		$sql = 'SELECT
			id,
			first_name,
			last_name,
			email_address,
			comments
			FROM
			qo_signup_requests
			ORDER BY
			last_name ASC';

      $result = $this->os->db->conn->query($sql);
		if($result){
			$items = array();

			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				$items[] = $row;
			}

			$response = '{"qo_signups":'.json_encode($items).'}';

			$response = str_replace('"true"', 'true', $response);
			$response = str_replace('"false"', 'false', $response);
		}

		print $response;
	}

	// end public module actions

   /**
    * add_group() Returns the new member id on success.
    *
    * @access private
    * @param {string} $name
    * @param {string} $description
    * @param {integer} $active
    * @return {array}
    */
	private function add_group($name, $description, $active){
		$response = array('success' => 'false');

      $sql = "INSERT INTO qo_groups (name, description, active) VALUES (?, ?, ?)";

      // prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
      $sql = $this->os->db->conn->prepare($sql);
      $sql->bindParam(1, $name);
      $sql->bindParam(2, $description);
      $sql->bindParam(3, $active);
      $sql->execute();

      $code = $sql->errorCode();
      if($code == '00000'){
			$id = $this->os->db->conn->lastInsertId();
			$response = array('success' => 'true', 'id' => $id);
		}else{
			$this->errors[] = "Script: QoAdmin.php, Method: add_group, Message: PDO error code - ".$code;
			$this->os->load('log');
			$this->os->log->error($this->errors);
		}

	    return $response;
	} // end add_group()

	/**
    * add_member() Returns the new member id on success.
    *
    * @access private
    * @param {string} $first_name
    * @param {string} $last_name
    * @param {string} $email_address
    * @param {string} $password
    * @param {integer} $active
    * @return {array}
    */
	private function add_member($first_name, $last_name, $email_address, $password, $active){
		$response = array("success" => "false");

      $sql = "INSERT INTO qo_members (first_name, last_name, email_address, password, active) VALUES (?, ?, ?, ?, ?)";

      // prepare the statement, prevents SQL injection by calling the PDO::quote() method internally
      $sql = $this->os->db->conn->prepare($sql);
      $sql->bindParam(1, $first_name);
      $sql->bindParam(2, $last_name);
      $sql->bindParam(3, $email_address);
      $sql->bindParam(4, $password);
      $sql->bindParam(5, $active);
      $sql->execute();

      $code = $sql->errorCode();
      if($code == '00000'){
			$id = $this->os->db->conn->lastInsertId();
			$response = array("success" => "true", "id" => $id);
		}else{
			$this->errors[] = "Script: QoAdmin.php, Method: add_member, Message: PDO error code - ".$code;
			$this->os->load('log');
			$this->os->log->error($this->errors);
		}

	    return $response;
	} // end add_member()

   /**
    * add_member_to_group()
    *
    * @access private
    * @param {integer} $member_id
    * @param {integer} $group_id
    * @param {integer} $active
    * @param {integer} $admin
    * @return {array}
    */
   private function add_member_to_group($member_id, $group_id, $active, $admin){
      $response = false;

      if($group_id != '' && $member_id != '' && $active != '' && $admin != ''){
         $sql = "INSERT INTO qo_groups_has_members (qo_members_id, qo_groups_id, active, admin) VALUES (?, ?, ?, ?)";

         $sql = $this->os->db->conn->prepare($sql);
         $sql->bindParam(1, $member_id);
         $sql->bindParam(2, $group_id);
         $sql->bindParam(3, $active);
         $sql->bindParam(4, $admin);
         $sql->execute();

         $code = $sql->errorCode();
         if($code == '00000'){
            $response = true;
         }else{
            $this->errors[] = "Script: QoAdmin.php, Method: add_member_to_group, Message: PDO error code - ".$code;
            $this->os->load('log');
            $this->os->log->error($this->errors);
         }
      }

      return $response;
   } // end add_member_to_group()

   /**
    * add_spam()
    *
    * @access private
    * @param {string} $email
    * @return {boolean}
    */
	private function add_spam($email){
		if(!isset($email) || $email == ''){
         return false;
      }

      $sql = "INSERT INTO qo_spam (email_address) VALUES (?)";

      $st = $this->os->db->conn->prepare($sql);
      $st->bindParam(1, $email);
      $st->execute();

      $code = $st->errorCode();
      if($code == '00000'){
         return true;
      }else{
         $this->errors[] = "Script: QoAdmin.php, Method: add_spam, Message: PDO error code - ".$code;
         $this->os->load('log');
         $this->os->log->error($this->errors);
      }

      return false;
	} // end add_spam()

   /**
    * delete_group()
    *
    * @access private
    * @param {integer} $id
    * @return {boolean}
    */
   private function delete_group($id){
      // do we have the required param?
      if(!isset($id) || $id == ''){
         return false;
      }

      $st = $this->os->db->conn->prepare("DELETE FROM qo_groups WHERE id = ".$id);
      $st->execute();

      $code = $st->errorCode();
      if($code == '00000'){
         return true;
      }

      $this->errors[] = "Script: QoAdmin.php, Method: delete_group, Message: PDO error code - ".$code;
      $this->os->load('log');
      $this->os->log->error($this->errors);

      return false;
   } // end delete_group()

   /**
    * delete_member()
    *
    * @access private
    * @param {integer} $id
    * @return {boolean}
    */
   private function delete_member($id){
      // do we have the required param?
      if(!isset($id) || $id == ''){
         return false;
      }

      $st = $this->os->db->conn->prepare("DELETE FROM qo_members WHERE id = ".$id);
      $st->execute();

      $code = $st->errorCode();
      if($code == '00000'){
         return true;
      }

      $this->errors[] = "Script: QoAdmin.php, Method: delete_member, Message: PDO error code - ".$code;
      $this->os->load('log');
      $this->os->log->error($this->errors);

      return false;
   } // end delete_member()

   /**
    * delete_group_member_relationship()
    *
    * @access private
    * @param {string} $option
    * @param {integer} $id
    * @return {boolean}
    */
   private function delete_group_member_relationship($option, $id){
      $response = false;

      // do we have the required params?
      if(!isset($option, $id) || $id == ''){
         return false;
      }

      $sql = null;

      // delete where group id = $id?
      if($option == 'group'){
         $sql = "DELETE FROM qo_groups_has_members WHERE qo_groups_id = ".$id;
      }

      // delete where group id = $id?
      else if ($option == 'member'){
         $sql = "DELETE FROM qo_groups_has_members WHERE qo_members_id = ".$id;
      }

      if(!$sql){
         return false;
      }

      $st = $this->os->db->conn->prepare($sql);
      $st->execute();

      $code = $st->errorCode();
      if($code == '00000'){
         return true;
      }else{
         $this->errors[] = "Script: QoAdmin.php, Method: delete_group_member_relationship, Message: PDO error code - ".$code;
         $this->os->load('log');
         $this->os->log->error($this->errors);
      }

      return false;
   } // end delete_group_member_relationship()

   /**
    * delete_group_privilege_relationship()
    *
    * @access private
    * @param {string} $option
    * @param {integer} $id
    * @return {boolean}
    */
   private function delete_group_privilege_relationship($option, $id){
      // do we have the required params?
      if(!isset($option, $id) || $option == '' || $id == ''){
         return false;
      }

      // do we have the required params?
      if(!isset($option, $id) || $id == ''){
         return false;
      }

      $sql = null;

      // delete where group id = $id?
      if($option == 'group'){
         $sql = "DELETE FROM qo_groups_has_privileges WHERE qo_groups_id = ".$id;
      }

      // delete where privilege id = $id?
      else if ($option == 'privilege'){
         $sql = "DELETE FROM qo_groups_has_privileges WHERE qo_privileges_id = ".$id;
      }

      if(!$sql){
         return false;
      }

      $st = $this->os->db->conn->prepare($sql);
      $st->execute();

      $code = $st->errorCode();
      if($code == '00000'){
         return true;
      }else{
         $this->errors[] = "Script: QoAdmin.php, Method: delete_group_privilege_relationship, Message: PDO error code - ".$code;
         $this->os->load('log');
         $this->os->log->error($this->errors);
      }

      return false;
   } // end delete_group_privilege_relationship()

	private function delete_members_launchers($id){
		$response = false;

		if($id != ""){
			$st = $this->os->db->conn->prepare("DELETE FROM qo_members_has_module_launchers WHERE qo_members_id = ".$id);
			$st->execute();

			$code = $st->errorCode();
			if($code == '00000'){
				$response = true;
			}else{
				$this->errors[] = "Script: QoAdmin.php, Method: delete_members_launchers, Message: PDO error code - ".$code;
				$this->os->load('log');
				$this->os->log->error($this->errors);
			}
		}
		return $response;
	}



	private function delete_members_session($id){
		$response = false;

		if($id != ""){
			$st = $this->os->db->conn->prepare("DELETE FROM qo_sessions WHERE qo_members_id = ".$id);
			$st->execute();

			$code = $st->errorCode();
			if($code == '00000'){
				$response = true;
			}else{
				$this->errors[] = "Script: QoAdmin.php, Method: delete_members_session, Message: PDO error code - ".$code;
				$this->os->load('log');
				$this->os->log->error($this->errors);
			}
		}
		return $response;
	}

   /**
    * delete_group_member_preference()
    *
    * @access private
    * @param {string} $option
    * @param {integer} $id
    * @return {boolean}
    */
   private function delete_group_member_preference($option, $id){
      // do we have the required params?
      if(!isset($id) || $id == ''){
         return false;
      }

      $sql = null;

      // delete where group id = $id?
      if($option == 'group'){
         $sql = "DELETE FROM qo_groups_has_member_preferences WHERE qo_groups_id = ".$id;
      }

      // delete where member id = $id?
      if($option == 'member'){
         $sql = "DELETE FROM qo_groups_has_member_preferences WHERE qo_members_id = ".$id;
      }

      if(!$sql){
         return false;
      }

      $st = $this->os->db->conn->prepare($sql);
      $st->execute();

      $code = $st->errorCode();
      if($code == '00000'){
         return true;
      }else{
         $this->errors[] = "Script: QoAdmin.php, Method: delete_group_member_preference, Message: PDO error code - ".$code;
         $this->os->load('log');
         $this->os->log->error($this->errors);
      }

      return false;
   } // end delete_group_member_preference()

	private function delete_signup($id){
		$response = false;

		if($id != ""){
			$st = $this->os->db->conn->prepare("DELETE FROM qo_signup_requests WHERE id = ".$id);
			$st->execute();

			$code = $st->errorCode();
			if($code == '00000'){
				$response = true;
			}else{
				$this->errors[] = "Script: QoAdmin.php, Method: delete_signup, Message: PDO error code - ".$code;
				$this->os->load('log');
				$this->os->log->error($this->errors);
			}
		}

		return $response;
	}
}
?>