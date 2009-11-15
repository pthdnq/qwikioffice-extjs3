<?php
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

class privilege {

   private $os = null;

   public function __construct($os){
      $os->load('session');

      if(!$os->session->exists()){
         die('Session does not exist!');
      }

      $this->os = $os;
   }

   /** init() Initial page load or refresh has occured
     * Called from init() of os.php
     **
   public function init(){
      $this->prepare();
   } // end init() */

   /** prepare() Will prepare the privilege data associated with the current member and update the registry.
     *
     * @access public
     * @param {integer} $member_id The member id
     * @param {integer} $group_id The group id
     **
   private function prepare($member_id='', $group_id=''){
      $this->os->load('member');
      $member_id = $this->os->member->get_id();
      $this->os->load('group');
      $group_id = $this->os->group->get_id();

      if($member_id != "" && $group_id != ""){
         $privileges = array();

         $sql = "SELECT
				is_allowed,
				P.is_singular AS is_privilege_singular,
				A.name AS action,
				D.is_singular AS is_domain_singular,
				M.id AS module_id,
				M.module_id AS moduleId,
				G.importance
				FROM qo_groups_has_domain_privileges AS GDP
					-- Privileges Joins --
					INNER JOIN qo_privileges AS P ON P.id = GDP.qo_privileges_id
					INNER JOIN qo_privileges_has_module_actions AS PA ON PA.qo_privileges_id = P.id
					INNER JOIN qo_modules_actions AS A ON A.id = PA.qo_modules_actions_id
					-- Domain Joins --
					INNER JOIN qo_domains AS D ON D.id = GDP.qo_domains_id
					INNER JOIN qo_domains_has_modules AS DM ON DM.qo_domains_id = D.id
					INNER JOIN qo_modules AS M ON M.id = DM.qo_modules_id
					-- Groups to member Joins --
					INNER JOIN qo_groups AS G ON G.id = GDP.qo_groups_id
					INNER JOIN qo_groups_has_members AS MG ON MG.qo_groups_id = G.id
				WHERE
				qo_members_id = ".$member_id."
				AND
				G.id = ".$group_id."
				ORDER BY
				A.name, G.importance DESC";

         $result = $this->os->db->conn->query($sql);
			if($result){
				$weight = -1; // used to find out which privileges take precedence.
				$is_allowed = 0; // FALSE, initialise
				$prev_importance = '';
				$prev_action= '';
				$prev_module = '';
				$prev_is_allowed= '';
				$count = 0;
				$arr_data = array(); // store temporary data

				while($row = $result->fetch(PDO::FETCH_ASSOC)){ // loop through all matches
					$action = $row["action"];
					$module_id = $row["module_id"]; // MySQL table id
					$moduleId = $row["moduleId"]; // moduleId property of the module
					$importance = $row["importance"];
					$is_allowed = (int) $row["is_allowed"];

					// only interested in the groups with the most importance (some groups may have the same importance)

					if($count > 0 && $action === $prev_action && $module_id === $prev_module){
						if($importance < $prev_importance || $prev_is_allowed === 0){
							continue;
						}
					}

					$new_weight = (int) $row["is_privilege_singular"] + (int) $row["is_domain_singular"];

					if($new_weight > $weight){
						$weight = $new_weight;
					}else if($new_weight == $weight && (int) $is_allowed === 1 && $is_allowed === 0){
						$weight = $new_weight; // always give more weight to denials.
					}

					$prev_importance = $importance;
					$prev_module = $module_id;
					$prev_action = $action;
					$prev_is_allowed = $is_allowed;

					$count++;

					//$privileges[$action][$moduleId] = $is_allowed;
               $privileges[$moduleId][$action] = $is_allowed;
				}
			}

         $this->os->load('registry');
         $this->os->registry->set($this->os->REG_KEY_PRIVILEGE, $privileges); // store in the registry
		}
	} // end prepare() */

   /** get() Get the privilege definition object.
    *
    * @access public
    * @return {stdClass} The privilege definition object.
     **/
   public function get(){
      // check the registry
      $this->os->load('registry');

      $definition = $this->os->registry->get($this->os->REG_KEY_PRIVILEGE); // check if answer is already in the registry

      if($definition){
         return $definition;
      }

      // check the database
      //$this->os->load('member');
      //$member_id = $this->os->member->get_id();

      $this->os->load('group');
      $group_id = $this->os->group->get_id();

      if(isset($group_id)){
         $sql = "SELECT
            privilege_definition
            FROM
            qo_groups
            WHERE
            id = ".$group_id;

         $result = $this->os->db->conn->query($sql);
			if($result){
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if($row){
               $definition = json_decode($row['privilege_definition']);
               if($definition){
                  return $definition;
               }
            }
         }
      }

      return null;
   } // end get()

	/** is_allowed() checks whether a member (in group) is allowed an action on a module.
	  *
	  * @param {string} $action The action name
	  * @param {integer} $module_id The module id
	  * @param {integer} $member_id The member id
	  * @param {integer} $group_id The group id
	  * @return {boolean}
	  **/
	public function is_allowed($action, $moduleId, $member_id=null, $group_id=null){

      // Todo: this should not need member/group id, should work from registry ( current member ).

		if(isset($action, $moduleId)){

         // check the definition in the registry
         $this->os->load('registry');
         $definition = $this->os->registry->get($this->os->REG_KEY_PRIVILEGE);

         if(isset($definition->$moduleId->$action)){
            if($definition->$moduleId->$action){
               return true;
            }else{
               return false;
            }
         }

         // 2nd, check the database
         $definition = $this->get();

         if(isset($definition->$moduleId->$action)){
            if($definition->$moduleId->$action){
               return true;
            }else{
               return false;
            }
         }
      }

      return false;
	} // end is_allowed()
}
?>