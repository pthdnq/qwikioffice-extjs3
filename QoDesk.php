<?php
//Header("content-type: application/x-javascript");

require_once('server/os.php');
if(!class_exists('os')){ die('os class is missing!'); }

class QoDesk {
   private $os = null;

   private $member_id = null;
   private $group_id = null;

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

      $this->member_id = $os->get_member_id();
      $this->group_id = $os->get_group_id();

      if(!isset($this->member_id, $this->group_id)){
         die('Member/Group not found!');
      }

      $this->os = $os;
   } // end __construct()

   /**
    * print_member_info()
    *
    * @access public
    */
   public function print_member_info(){
      if(isset($this->member_id, $this->group_id)){
         $this->os->load('group');
         $this->os->load('member');

         print '{'.
            '"name": "'.$this->os->member->get_name($this->member_id).'",'.
            '"group": "'.$this->os->group->get_name($this->group_id).'"'.
         '}';
      }else{
         print '';
      }
   }

   /**
    * print_privileges()
    *
    * @access public
    */
   public function print_privileges(){
      // have a group id?
      if(!isset($this->group_id)){
         print '{}';
         return false;
      }

      // get the privilege id for the group
      $this->os->load('group');
      $privilege_id = $this->os->group->get_privilege_id($this->group_id);

      if(!$privilege_id){
         print '{}';
         return false;
      }

      // get the simplified privilege data
      $this->os->load('privilege');
      $data = $this->os->privilege->simplify($privilege_id);

      if(!isset($data)){
         print '{}';
         return false;
      }

      print json_encode($data);
      return true;
   } // end print_privileges()

   /**
    * print_modules()
    *
    * @access public
    */
   public function print_modules(){
      $response = '';
      $ms = $this->os->get_modules();

      if(!isset($ms) || !is_array($ms) || count($ms) == 0){
         print '';
         return false;
      }

      foreach($ms as $id => $m){
         $response .= '{'.
            '"id":"'.$id.'",'.
            '"type":"'.$m->type.'",'.
            '"className":"'.$m->client->class.'",'.
            '"launcher":'.json_encode($m->client->launcher->config).','.
            '"launcherPaths":'.json_encode($m->client->launcher->paths).
         '},';
      }

      print rtrim($response, ',');
   } // end print_modules()

   /**
    * print_launchers()
    *
    * @access public
    */
   public function print_launchers(){
      $member_id = $this->os->get_member_id();
      $group_id = $this->os->get_group_id();

      // do we have the required params?
      if(!isset($member_id, $group_id)){
         return null;
      }

      $preference = $this->os->get_member_preference($member_id, $group_id);

      if(!isset($preference->launchers)){
         print 'null';
         return false;
      }

      $autorun = '[]';
      $quickstart = '[]';
      $shortcut = '[]';
      $systemtray = '[]';

      if(isset($preference->launchers->autorun)){
         $autorun = json_encode($preference->launchers->autorun);
      }
      if(isset($preference->launchers->quickstart)){
         $quickstart = json_encode($preference->launchers->quickstart);
      }
      if(isset($preference->launchers->shortcut)){
         $shortcut = json_encode($preference->launchers->shortcut);
      }
      if(isset($preference->launchers->systemtray)){
         $shortcut = json_encode($preference->launchers->systemtray);
      }
      print "{
         autorun: ".$autorun.",
         quickstart: ".$quickstart.",
         shortcut: ".$shortcut.",
         systemtray: ".$systemtray."
      }";
   } // end print_launchers()

   /**
    * print_styles()
    *
    * @access public
    */
   public function print_styles(){
      $member_id = $this->os->get_member_id();
      $group_id = $this->os->get_group_id();

      // do we have the required params?
      if(!isset($member_id, $group_id)){
         return null;
      }

      // get the member/group preference
      $member_preference = $this->os->get_member_preference($member_id, $group_id);

      // get the default preference
      $preference = $this->os->get_member_preference('0', '0');

      // overwrite default with any member/group preference
      foreach($member_preference as $id => $property){
         $preference->$id = $property;
      }

      // do we have the needed theme/wallpaper id
      if(!isset($preference->themeId, $preference->wallpaperId)){
         print '{}';
         return false;
      }

      // get the theme data
      $this->os->load('theme');
      $theme = $this->os->theme->get_by_id($preference->themeId);

      // get the wallpaper data
      $this->os->load('wallpaper');
      $wallpaper = $this->os->wallpaper->get_by_id($preference->wallpaperId);

      if(!$theme || !$wallpaper){
         print '{}';
         return false;
      }

      $theme_dir = $this->os->get_theme_dir();
      $wallpaper_dir = $this->os->get_wallpaper_dir();

      // todo: use this for new getPreferences() which will contain styles and launchers
      //print json_encode($preference);

      // print the result
      print '{'.
         '"backgroundcolor": "'.$preference->backgroundColor.'",'.
         '"fontcolor": "'.$preference->fontColor.'",'.
         '"transparency": "'.$preference->transparency.'",'.
         '"theme": {'.
            '"id": '.$preference->themeId.','.
            '"name": "'.$theme->name.'",'.
            '"pathtofile": "'.$theme_dir.$theme->file.'"'.
         '},'.
         '"wallpaper": {'.
            '"id": '.$preference->wallpaperId.','.
            '"name": "'.$wallpaper->name.'",'.
            '"pathtofile": "'.$wallpaper_dir.$wallpaper->file.'"'.
         '},'.
         '"wallpaperposition": "'.$preference->wallpaperPosition.'"'.
      '}';
   }
}

$os = new os();
$qo_desk = new QoDesk($os);
?>
/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

Ext.namespace('Ext.ux', 'QoDesk');

QoDesk.App = new Ext.app.App({

   init : function(){
      Ext.BLANK_IMAGE_URL = 'resources/images/default/s.gif';
      Ext.QuickTips.init();
   },

   /**
    * Returns the member's name and group name for this session.
    */
   getMemberInfo : function(){
      return <?php $qo_desk->print_member_info(); ?>;
   },

   /**
     * Returns the members privileges.
     *
     * Example:
     * {
     *    "qo-preferences": {
     *       "viewThemes": 1,
     *       "viewWallpapers": 1
     *    },
     *    "demo-layout":1,
     *    "demo-grid":1,
     *    "demo-bogus":1,
     *    "demo-tabs":1,
     *    "demo-accordion":1
     * }
     */
   getPrivileges : function(){
      return <?php $qo_desk->print_privileges(); ?>;
   },

   /**
    * Returns an array of the module definitions.
    * The definitions are used until the module is loaded on demand.
    *
    * Example:
    *
    * [
    *    {
    *       "id": "demo-accordion",
    *       "type": "demo/accordion",
    *       "className": "QoDesk.AccordionWindow",
    *       "launcher": {
    *          "iconCls":"acc-icon",
    *          "shortcutIconCls":"demo-acc-shortcut",
    *          "text":"Accordion Window",
    *          "tooltip":"A window with an accordion layout"
    *       },
    *       "launcherPaths": {
    *          "StartMenu": "/"
    *       }
    *    },
    *	   ...
    *
    */
   getModules : function(){
      return [ <?php $qo_desk->print_modules(); ?> ];
   },

   /**
    * Returns the launchers object.
    * Contains the moduleId's of the modules to add to each launcher.
    *
    * Example:
    *
    * {
    *		autorun: [
    *
    *		],
    *		quickstart: [
    *			'qo-preferences'
    *		],
    *		shortcut: [
    *			'qo-preferences'
    *		]
    *	}
    */
   getLaunchers : function(){
      return <?php $qo_desk->print_launchers(); ?>;
   },

   /**
    * Returns the Styles object.
    *
    * Example
    *
    * {
    *		backgroundcolor: '575757',
    *		fontcolor: 'FFFFFF',
    *		transparency: 100,
    *		theme: {
    *			id: 2,
    *			name: 'Vista Black',
    *			pathtofile: 'resources/themes/xtheme-vistablack/css/xtheme-vistablack.css'
    *		},
    *		wallpaper: {
    *			id: 10,
    *			name: 'Blue Swirl',
    *			pathtofile: 'resources/wallpapers/blue-swirl.jpg'
    *		},
    *		wallpaperposition: 'tile'
    *	}
    */
   getStyles : function(){
      return <?php $qo_desk->print_styles(); ?>;
   },

   /**
    * Returns the Start Menu's logout button config
    */
   getLogoutConfig : function(){
      return {
         text: 'Logout',
         iconCls: 'logout',
         handler: function(){ window.location = "logout.php"; },
         scope: this
      };
   },

   /**
    * Returns the Start Menu config
    */
   getStartConfig : function(){
      return {
         // iconCls: 'user',
         // title: get_cookie('memberName'),
         toolPanelWidth: 115
      };
   },

   /**
    * Function that handles sorting of the Start Menu
    * Return true to swap a and b
    */
   startMenuSortFn : function(a, b){
      // Sort in ASC alphabetical order
      // if( b.text < a.text ){
      //		return true;
      // }

      // Sort in ASC alphabetical order with menus at the bottom
      // if( (b.text < a.text) && !b.menu ){
      //		return true;
      // }

      // Sort in ASC alphabetical order with menus at the top
      if( ( ( b.menu && a.menu ) && ( b.text < a.text ) ) || ( b.menu && !a.menu ) || ( (b.text < a.text) && !a.menu ) ){
         return true;
      }

      return false;
   }
});