/*
MySQL Data Transfer
Source Host: localhost
Source Database: qwikioffice-distro
Target Host: localhost
Target Database: qwikioffice-distro
Date: 10/10/2008 5:04:25 PM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for qo_groups
-- ----------------------------
DROP TABLE IF EXISTS `qo_groups`;
CREATE TABLE `qo_groups` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(35) default NULL,
  `description` text,
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_groups_has_members
-- ----------------------------
DROP TABLE IF EXISTS `qo_groups_has_members`;
CREATE TABLE `qo_groups_has_members` (
  `qo_groups_id` int(11) unsigned NOT NULL default '0',
  `qo_members_id` int(11) unsigned NOT NULL default '0',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Is the member currently active in this group',
  `admin` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Is the member the administrator of this group',
  PRIMARY KEY  (`qo_members_id`,`qo_groups_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_groups_has_modules
-- ----------------------------
DROP TABLE IF EXISTS `qo_groups_has_modules`;
CREATE TABLE `qo_groups_has_modules` (
  `qo_groups_id` int(11) unsigned NOT NULL default '0',
  `qo_modules_id` int(11) unsigned NOT NULL default '0',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Is the module currently active in this group',
  PRIMARY KEY  (`qo_groups_id`,`qo_modules_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='This table stores what modules each group has access to';

-- ----------------------------
-- Table structure for qo_launchers
-- ----------------------------
DROP TABLE IF EXISTS `qo_launchers`;
CREATE TABLE `qo_launchers` (
  `id` int(2) unsigned NOT NULL auto_increment,
  `name` varchar(25) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_members
-- ----------------------------
DROP TABLE IF EXISTS `qo_members`;
CREATE TABLE `qo_members` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `first_name` varchar(25) default NULL,
  `last_name` varchar(35) default NULL,
  `email_address` varchar(55) default NULL,
  `password` varchar(15) default NULL,
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Is the member currently active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_members_has_module_launchers
-- ----------------------------
DROP TABLE IF EXISTS `qo_members_has_module_launchers`;
CREATE TABLE `qo_members_has_module_launchers` (
  `qo_members_id` int(11) unsigned NOT NULL default '0',
  `qo_groups_id` int(11) unsigned NOT NULL default '0',
  `qo_modules_id` int(11) unsigned NOT NULL default '0',
  `qo_launchers_id` int(10) unsigned NOT NULL default '0',
  `sort_order` int(5) unsigned NOT NULL default '0' COMMENT 'sort within each launcher',
  PRIMARY KEY  (`qo_members_id`,`qo_groups_id`,`qo_modules_id`,`qo_launchers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_modules
-- ----------------------------
DROP TABLE IF EXISTS `qo_modules`;
CREATE TABLE `qo_modules` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `directory` varchar(255) default NULL,
  `author` varchar(35) default NULL,
  `version` varchar(15) default NULL,
  `url` varchar(255) default NULL COMMENT 'Url which provides information',
  `description` text,
  `class_name` varchar(55) default NULL COMMENT 'The class name of the module (including namespace)',
  `module_type` varchar(35) default NULL COMMENT 'The ''moduleType'' property of the module',
  `module_id` varchar(35) default NULL COMMENT 'The ''moduleId'' property of the module',
  `menu_path` varchar(255) default NULL,
  `launcher_icon_cls` varchar(35) default NULL COMMENT 'The launcher''s ''iconCls'' property',
  `launcher_shortcut_icon_cls` varchar(255) default NULL COMMENT 'The launcher''s ''shortcutIconCls'' property',
  `launcher_text` varchar(55) default NULL COMMENT 'The launcher''s ''text'' property',
  `launcher_tooltip` varchar(100) default NULL COMMENT 'The launcher''s ''tooltip'' property',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Is the module currently active',
  `preload` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Preload this module at start up?',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_modules_dependencies
-- ----------------------------
DROP TABLE IF EXISTS `qo_modules_dependencies`;
CREATE TABLE `qo_modules_dependencies` (
  `qo_modules_id` int(11) unsigned NOT NULL default '0',
  `directory` varchar(255) default '' COMMENT 'The directory within the modules directory stated in the system/os/config.php',
  `file` varchar(255) NOT NULL COMMENT 'The file that contains the dependency',
  PRIMARY KEY  (`qo_modules_id`,`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_modules_files
-- ----------------------------
DROP TABLE IF EXISTS `qo_modules_files`;
CREATE TABLE `qo_modules_files` (
  `qo_modules_id` int(11) unsigned NOT NULL default '0',
  `directory` varchar(255) default '' COMMENT 'The directory within the modules directory stated in the system/os/config.php',
  `file` varchar(255) NOT NULL COMMENT 'The file that contains the dependency',
  PRIMARY KEY  (`qo_modules_id`,`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_modules_stylesheets
-- ----------------------------
DROP TABLE IF EXISTS `qo_modules_stylesheets`;
CREATE TABLE `qo_modules_stylesheets` (
  `qo_modules_id` int(11) unsigned NOT NULL default '0',
  `directory` varchar(255) default '' COMMENT 'The directory within the modules directory stated in the system/os/config.php',
  `file` varchar(255) NOT NULL COMMENT 'The file that contains the dependency',
  PRIMARY KEY  (`qo_modules_id`,`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_sessions
-- ----------------------------
DROP TABLE IF EXISTS `qo_sessions`;
CREATE TABLE `qo_sessions` (
  `id` varchar(128) NOT NULL default '' COMMENT 'a randomly generated id',
  `qo_members_id` int(11) unsigned NOT NULL default '0',
  `ip` varchar(16) default NULL,
  `date` datetime default NULL,
  PRIMARY KEY  (`id`,`qo_members_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_styles
-- ----------------------------
DROP TABLE IF EXISTS `qo_styles`;
CREATE TABLE `qo_styles` (
  `qo_members_id` int(11) unsigned NOT NULL default '0',
  `qo_groups_id` int(11) unsigned NOT NULL default '0',
  `qo_themes_id` int(11) unsigned NOT NULL default '1',
  `qo_wallpapers_id` int(11) unsigned NOT NULL default '1',
  `backgroundcolor` varchar(6) NOT NULL default 'ffffff',
  `fontcolor` varchar(6) default NULL,
  `transparency` int(3) NOT NULL default '100',
  `wallpaperposition` varchar(6) NOT NULL default 'center',
  PRIMARY KEY  (`qo_members_id`,`qo_groups_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_themes
-- ----------------------------
DROP TABLE IF EXISTS `qo_themes`;
CREATE TABLE `qo_themes` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(25) default NULL COMMENT 'The display name',
  `author` varchar(55) default NULL,
  `version` varchar(25) default NULL,
  `url` varchar(255) default NULL COMMENT 'Url which provides additional information',
  `path_to_thumbnail` varchar(255) default NULL,
  `path_to_file` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_wallpapers
-- ----------------------------
DROP TABLE IF EXISTS `qo_wallpapers`;
CREATE TABLE `qo_wallpapers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(25) default NULL COMMENT 'Display name',
  `author` varchar(55) default NULL,
  `url` varchar(255) default NULL COMMENT 'Url which provides information',
  `path_to_thumbnail` varchar(255) default NULL,
  `path_to_file` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `qo_groups` VALUES ('1', 'administrator', 'System administrator', '1');
INSERT INTO `qo_groups` VALUES ('2', 'user', 'General user', '1');
INSERT INTO `qo_groups` VALUES ('3', 'demo', 'Demo user', '1');
INSERT INTO `qo_groups_has_members` VALUES ('3', '3', '1', '1');
INSERT INTO `qo_groups_has_members` VALUES ('1', '4', '1', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '1', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '2', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '5', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '4', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '3', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '6', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '7', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('1', '8', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '1', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '2', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '3', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '4', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '5', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '6', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '7', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('2', '8', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '1', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '2', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '3', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '4', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '5', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '6', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '7', '1');
INSERT INTO `qo_groups_has_modules` VALUES ('3', '8', '1');
INSERT INTO `qo_launchers` VALUES ('1', 'autorun');
INSERT INTO `qo_launchers` VALUES ('2', 'contextmenu');
INSERT INTO `qo_launchers` VALUES ('3', 'quickstart');
INSERT INTO `qo_launchers` VALUES ('4', 'shortcut');
INSERT INTO `qo_members` VALUES ('3', 'Todd', 'Murdock', 'demo', 'demo', '1');
INSERT INTO `qo_members` VALUES ('4', 'Todd', 'Murdock', 'info@qwikioffice.com', 'Todd', '1');
INSERT INTO `qo_members_has_module_launchers` VALUES ('0', '0', '1', '2', '0');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '1', '3', '1');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '4', '3', '0');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '8', '4', '5');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '5', '4', '4');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '4', '4', '3');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '3', '4', '2');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '2', '4', '1');
INSERT INTO `qo_members_has_module_launchers` VALUES ('3', '3', '1', '4', '0');
INSERT INTO `qo_modules` VALUES ('1', 'qo-preferences/', 'Todd Murdock', '1.0', 'http://www.qwikioffice.com', 'A system application.  Allows users to set, and save their desktop preferences to the database.', 'QoDesk.QoPreferences', 'system/preferences', 'qo-preferences', 'ToolMenu', 'pref-icon', 'pref-shortcut-icon', 'Preferences', '<b>Preferences</b><br />Allows you to modify your desktop', '1', '0');
INSERT INTO `qo_modules` VALUES ('2', 'grid-win/', 'Jack Slocum', '1.0', 'http://www.qwikioffice.com', 'Demo of window with grid.', 'QoDesk.GridWindow', 'demo', 'demo-grid', 'StartMenu', 'grid-icon', 'demo-grid-shortcut', 'Grid Window', '<b>Grid Window</b><br />A window with a grid', '1', '0');
INSERT INTO `qo_modules` VALUES ('3', 'tab-win/', 'Jack Slocum', '1.0', 'http://www.qwikioffice.com', 'Demo of window with tabs.', 'QoDesk.TabWindow', 'demo', 'demo-tabs', 'StartMenu', 'tab-icon', 'demo-tab-shortcut', 'Tab Window', '<b>Tab Window</b><br />A window with tabs', '1', '0');
INSERT INTO `qo_modules` VALUES ('4', 'acc-win/', 'Jack Slocum', '1.0', 'http://www.qwikioffice.com', 'Demo of window with accordion.', 'QoDesk.AccordionWindow', 'demo', 'demo-acc', 'StartMenu', 'acc-icon', 'demo-acc-shortcut', 'Accordion Window', '<b>Accordion Window</b><br />A window with an accordion layout', '1', '0');
INSERT INTO `qo_modules` VALUES ('5', 'layout-win/', 'Jack Slocum', '1.0', 'http://www.qwikioffice.com', 'Demo of window with layout.', 'QoDesk.LayoutWindow', 'demo', 'demo-layout', 'StartMenu/Bogus Menu', 'layout-icon', 'demo-layout-shortcut', 'Layout Window', '<b>Layout Window</b><br />A window with a border layout', '1', '0');
INSERT INTO `qo_modules` VALUES ('8', 'bogus/bogus-win/', 'Jack Slocum', '1.0', 'http://www.qwikioffice.com', 'Demo of bogus window.', 'QoDesk.BogusModule', 'demo', 'demo-bogus', 'StartMenu/Bogus Menu/Bogus Sub Menu', 'bogus-icon', 'demo-bogus-shortcut', 'Bogus Window', '<b>Bogus Window</b><br />A bogus window', '1', '0');
INSERT INTO `qo_modules_dependencies` VALUES ('1', 'grid-win/', 'grid-win.js');
INSERT INTO `qo_modules_files` VALUES ('1', 'qo-preferences/', 'qo-preferences.js');
INSERT INTO `qo_modules_files` VALUES ('2', 'grid-win/', 'grid-win.js');
INSERT INTO `qo_modules_files` VALUES ('3', 'tab-win/', 'tab-win.js');
INSERT INTO `qo_modules_files` VALUES ('4', 'acc-win/', 'acc-win.js');
INSERT INTO `qo_modules_files` VALUES ('5', 'layout-win/', 'layout-win.js');
INSERT INTO `qo_modules_files` VALUES ('8', 'bogus/bogus-win/', 'bogus-win.js');
INSERT INTO `qo_modules_stylesheets` VALUES ('1', 'qo-preferences/', 'qo-preferences.css');
INSERT INTO `qo_modules_stylesheets` VALUES ('2', 'grid-win/', 'grid-win.css');
INSERT INTO `qo_modules_stylesheets` VALUES ('3', 'tab-win/', 'tab-win.css');
INSERT INTO `qo_modules_stylesheets` VALUES ('4', 'acc-win/', 'acc-win.css');
INSERT INTO `qo_modules_stylesheets` VALUES ('5', 'layout-win/', 'layout-win.css');
INSERT INTO `qo_modules_stylesheets` VALUES ('8', 'bogus/bogus-win/', 'bogus-win.css');
INSERT INTO `qo_sessions` VALUES ('6ed44cad666bf151312836caf00d3329', '3', '127.0.0.1', '2008-10-04 15:36:21');
INSERT INTO `qo_sessions` VALUES ('7a76a34abde3c28511fb38b9fbcdc526', '3', '127.0.0.1', '2008-10-08 22:29:15');
INSERT INTO `qo_styles` VALUES ('0', '0', '2', '1', 'f9f9f9', '000000', '100', 'center');
INSERT INTO `qo_styles` VALUES ('3', '3', '3', '11', '575757', 'FFFFFF', '100', 'tile');
INSERT INTO `qo_themes` VALUES ('1', 'Vista Blue', 'Todd Murdock', '0.8', null, 'xtheme-vistablue/xtheme-vistablue.png', 'xtheme-vistablue/css/xtheme-vistablue.css');
INSERT INTO `qo_themes` VALUES ('2', 'Vista Black', 'Todd Murdock', '0.8', null, 'xtheme-vistablack/xtheme-vistablack.png', 'xtheme-vistablack/css/xtheme-vistablack.css');
INSERT INTO `qo_themes` VALUES ('3', 'Vista Glass', 'Todd Murdock', '0.8', null, 'xtheme-vistaglass/xtheme-vistaglass.png', 'xtheme-vistaglass/css/xtheme-vistaglass.css');
INSERT INTO `qo_wallpapers` VALUES ('1', 'qWikiOffice', 'Todd Murdock', null, 'thumbnails/qwikioffice.jpg', 'qwikioffice.jpg');
INSERT INTO `qo_wallpapers` VALUES ('2', 'Colorado Farm', null, null, 'thumbnails/colorado-farm.jpg', 'colorado-farm.jpg');
INSERT INTO `qo_wallpapers` VALUES ('3', 'Curls On Green', null, null, 'thumbnails/curls-on-green.jpg', 'curls-on-green.jpg');
INSERT INTO `qo_wallpapers` VALUES ('4', 'Emotion', null, null, 'thumbnails/emotion.jpg', 'emotion.jpg');
INSERT INTO `qo_wallpapers` VALUES ('5', 'Eos', null, null, 'thumbnails/eos.jpg', 'eos.jpg');
INSERT INTO `qo_wallpapers` VALUES ('6', 'Fields of Peace', null, null, 'thumbnails/fields-of-peace.jpg', 'fields-of-peace.jpg');
INSERT INTO `qo_wallpapers` VALUES ('7', 'Fresh Morning', null, null, 'thumbnails/fresh-morning.jpg', 'fresh-morning.jpg');
INSERT INTO `qo_wallpapers` VALUES ('8', 'Ladybuggin', null, null, 'thumbnails/ladybuggin.jpg', 'ladybuggin.jpg');
INSERT INTO `qo_wallpapers` VALUES ('9', 'Summer', null, null, 'thumbnails/summer.jpg', 'summer.jpg');
INSERT INTO `qo_wallpapers` VALUES ('10', 'Blue Swirl', null, null, 'thumbnails/blue-swirl.jpg', 'blue-swirl.jpg');
INSERT INTO `qo_wallpapers` VALUES ('11', 'Blue Psychedelic', null, null, 'thumbnails/blue-psychedelic.jpg', 'blue-psychedelic.jpg');
INSERT INTO `qo_wallpapers` VALUES ('12', 'Blue Curtain', null, null, 'thumbnails/blue-curtain.jpg', 'blue-curtain.jpg');
INSERT INTO `qo_wallpapers` VALUES ('13', 'Blank', null, null, 'thumbnails/blank.gif', 'blank.gif');
