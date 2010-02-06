/*
MySQL Data Transfer
Source Host: localhost
Source Database: qwikioffice
Target Host: localhost
Target Database: qwikioffice
Date: 8/10/2009 10:26:41 PM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for qm_folders
-- ----------------------------
DROP TABLE IF EXISTS `qm_folders`;
CREATE TABLE `qm_folders` (
  `folder_id` int(11) unsigned NOT NULL auto_increment,
  `folder_name` varchar(25) NOT NULL default '',
  `folder_parent_id` int(11) unsigned NOT NULL default '0',
  `member_id` int(11) unsigned NOT NULL default '0',
  `folder_display_order` int(11) default NULL,
  PRIMARY KEY  (`folder_id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_groups
-- ----------------------------
DROP TABLE IF EXISTS `qo_groups`;
CREATE TABLE `qo_groups` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(35) default NULL,
  `description` text,
  `importance` int(3) unsigned default '1',
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_groups_has_member_preferences
-- ----------------------------
DROP TABLE IF EXISTS `qo_groups_has_member_preferences`;
CREATE TABLE `qo_groups_has_member_preferences` (
  `qo_groups_id` int(11) unsigned NOT NULL default '0',
  `qo_members_id` int(11) unsigned NOT NULL default '0',
  `data` text COMMENT 'JSON data',
  PRIMARY KEY  (`qo_members_id`,`qo_groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_groups_has_privileges
-- ----------------------------
DROP TABLE IF EXISTS `qo_groups_has_privileges`;
CREATE TABLE `qo_groups_has_privileges` (
  `qo_groups_id` int(11) unsigned NOT NULL default '0',
  `qo_privileges_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`qo_groups_id`,`qo_privileges_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_libraries
-- ----------------------------
DROP TABLE IF EXISTS `qo_libraries`;
CREATE TABLE `qo_libraries` (
  `id` varchar(35) NOT NULL default '',
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_log
-- ----------------------------
DROP TABLE IF EXISTS `qo_log`;
CREATE TABLE `qo_log` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `level` varchar(15) default NULL COMMENT 'ERROR, WARNING, MESSAGE or AUDIT',
  `text` text,
  `timestamp` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_members
-- ----------------------------
DROP TABLE IF EXISTS `qo_members`;
CREATE TABLE `qo_members` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `first_name` varchar(25) default NULL,
  `last_name` varchar(35) default NULL,
  `email_address` varchar(55) default NULL,
  `password` varchar(255) default NULL,
  `locale` varchar(5) default 'en',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Is the member currently active',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_modules
-- ----------------------------
DROP TABLE IF EXISTS `qo_modules`;
CREATE TABLE `qo_modules` (
  `id` varchar(35) NOT NULL default '',
  `type` varchar(35) NOT NULL,
  `data` text NOT NULL COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_privileges
-- ----------------------------
DROP TABLE IF EXISTS `qo_privileges`;
CREATE TABLE `qo_privileges` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `data` text NOT NULL COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_sessions
-- ----------------------------
DROP TABLE IF EXISTS `qo_sessions`;
CREATE TABLE `qo_sessions` (
  `id` varchar(128) NOT NULL default '' COMMENT 'a randomly generated id',
  `qo_members_id` int(11) unsigned NOT NULL default '0',
  `qo_groups_id` int(11) unsigned default NULL COMMENT 'Group the member signed in under',
  `data` text,
  `ip` varchar(16) default NULL,
  `date` datetime default NULL,
  PRIMARY KEY  (`id`,`qo_members_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_signup_requests
-- ----------------------------
DROP TABLE IF EXISTS `qo_signup_requests`;
CREATE TABLE `qo_signup_requests` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `first_name` varchar(25) default NULL,
  `last_name` varchar(35) default NULL,
  `email_address` varchar(55) default NULL,
  `comments` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_spam
-- ----------------------------
DROP TABLE IF EXISTS `qo_spam`;
CREATE TABLE `qo_spam` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `email_address` varchar(55) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_themes
-- ----------------------------
DROP TABLE IF EXISTS `qo_themes`;
CREATE TABLE `qo_themes` (
  `id` varchar(35) NOT NULL default '',
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for qo_wallpapers
-- ----------------------------
DROP TABLE IF EXISTS `qo_wallpapers`;
CREATE TABLE `qo_wallpapers` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL default '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `qm_folders` VALUES ('1', 'Inbox', '0', '0', '0');
INSERT INTO `qm_folders` VALUES ('2', 'Sent', '0', '0', '1');
INSERT INTO `qm_folders` VALUES ('3', 'Drafts', '0', '0', '2');
INSERT INTO `qm_folders` VALUES ('4', 'Trash', '0', '0', '3');
INSERT INTO `qm_folders` VALUES ('6', 'Work', '0', '1', '7');
INSERT INTO `qm_folders` VALUES ('7', 'qWikiMail', '6', '1', '9');
INSERT INTO `qm_folders` VALUES ('5', 'Spam', '0', '0', '4');
INSERT INTO `qm_folders` VALUES ('10', 'Completed', '57', '1', '5');
INSERT INTO `qm_folders` VALUES ('13', 'MPR Homes', '6', '1', '8');
INSERT INTO `qm_folders` VALUES ('37', 'PHP', '0', '1', '6');
INSERT INTO `qm_folders` VALUES ('38', 'Superior Auto', '6', '1', '10');
INSERT INTO `qm_folders` VALUES ('39', 'Alienware', '0', '1', '5');
INSERT INTO `qm_folders` VALUES ('57', 'Home', '4', '1', '4');
INSERT INTO `qm_folders` VALUES ('58', 'Family', '39', '1', '7');
INSERT INTO `qm_folders` VALUES ('59', 'NewName', '4', '1', '6');
INSERT INTO `qm_folders` VALUES ('69', 'aFolder', '39', '1', '6');
INSERT INTO `qm_folders` VALUES ('70', 'fred', '4', '1', '5');
INSERT INTO `qm_folders` VALUES ('71', 'Scripts', '37', '1', '7');
INSERT INTO `qo_groups` VALUES ('1', 'System Administrator', 'The administrator of this desktop system.', '100', '1');
INSERT INTO `qo_groups` VALUES ('2', 'Demo', 'A demo user', '1', '1');
INSERT INTO `qo_groups_has_member_preferences` VALUES ('0', '0', '{\"backgroundColor\": \"f9f9f9\",\"fontColor\": \"000000\",\"launchers\": {\"autorun\": [],\"quickstart\": [],\"shortcut\": []},\"themeId\": 1,\"transparency\": 100,\"wallpaperId\": 11,\"wallpaperPosition\": \"center\"}');
INSERT INTO `qo_groups_has_member_preferences` VALUES ('1', '1', '{\"launchers\":{\"shortcut\":[\"qo-admin\"]},\"backgroundColor\":\"f9f9f9\",\"fontColor\":\"000000\",\"wallpaperId\":2,\"wallpaperPosition\":\"tile\",\"themeId\":3,\"transparency\":100}');
INSERT INTO `qo_groups_has_members` VALUES ('1', '1', '1', '1');
INSERT INTO `qo_groups_has_members` VALUES ('2', '2', '1', '0');
INSERT INTO `qo_groups_has_privileges` VALUES ('1', '1');
INSERT INTO `qo_groups_has_privileges` VALUES ('2', '2');
INSERT INTO `qo_libraries` VALUES ('colorpicker', '{\r\n   \"dependencies\": [\r\n      { \"id\": \"hexfield\", \"type\": \"library\" }\r\n   ],\r\n\r\n   \"client\": {\r\n      \"css\": [\r\n         {\r\n           \"directory\": \"color-picker/resources/\",\r\n           \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"color-picker/\",\r\n            \"files\": [ \"Ext.ux.ColorPicker.js\" ]\r\n         }\r\n      ]\r\n   }\r\n}', '1');
INSERT INTO `qo_libraries` VALUES ('columntree', '{\r\n   \"client\": {\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"column-tree/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ]\r\n   }\r\n}', '1');
INSERT INTO `qo_libraries` VALUES ('explorerview', '{\r\n   \"client\": {\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"explorer-view/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"explorer-view/\",\r\n            \"files\": [ \"Ext.ux.grid.ExplorerView.js\", \"Ext.ux.grid.GroupingExplorerView.js\" ]\r\n         }\r\n      ]\r\n   }\r\n}', '1');
INSERT INTO `qo_libraries` VALUES ('hexfield', '{\r\n   \"client\": {\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"hex-field/\",\r\n            \"files\": [ \"Ext.ux.form.HexField.js\" ]\r\n         }\r\n      ]\r\n   }\r\n}', '1');
INSERT INTO `qo_libraries` VALUES ('iframecomponent', '{\r\n   \"client\": {\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"iframe-component/\",\r\n            \"files\": [ \"Ext.ux.IFrameComponent.js\" ]\r\n         }\r\n      ]\r\n   }\r\n}', '1');
INSERT INTO `qo_libraries` VALUES ('modalnotice', '{\r\n   \"client\": {\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"modal-notice/\",\r\n            \"files\": [ \"Ext.plugin.ModalNotice.js\" ]\r\n         }\r\n      ]\r\n   }\r\n}', '1');
INSERT INTO `qo_members` VALUES ('1', 'Todd', 'Murdock', 'info@qwikioffice.com', '5420bf0480c98cd026db646e7be40e537012bc75', 'en', '1');
INSERT INTO `qo_members` VALUES ('2', 'Todd', 'Murdock', 'demo', '3aa50a240649d35f2effc6b4a5247af9980adc37', 'en', '1');
INSERT INTO `qo_modules` VALUES ('demo-accordion', 'demo/accordion', '{\r\n   \"id\": \"demo-accordion\",\r\n\r\n   \"type\": \"demo/accordion\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Jack Slocum\",\r\n      \"description\": \"Demo of window with accordion.\",\r\n      \"name\": \"Accordion Window\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"locale\": {\r\n      \"class\": \"QoDesk.AccordionWindow.Locale\",\r\n      \"directory\": \"demo/acc-win/client/locale/\",\r\n      \"extension\": \".json\",\r\n      \"languages\": [ \"en\" ]\r\n   },\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.AccordionWindow\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"demo/acc-win/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"demo/acc-win/client/\",\r\n            \"files\": [ \"acc-win.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"acc-icon\",\r\n            \"shortcutIconCls\": \"demo-acc-shortcut\",\r\n            \"text\": \"Accordion Window\",\r\n            \"tooltip\": \"<b>Accordion Window</b><br />A window with an accordion layout\"\r\n         },\r\n         \"paths\": {\r\n            \"startmenu\": \"/\"\r\n         }\r\n      }\r\n   }\r\n}', '1');
INSERT INTO `qo_modules` VALUES ('demo-bogus', 'demo/bogus', '{\r\n   \"id\": \"demo-bogus\",\r\n\r\n   \"type\": \"demo/bogus\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Jack Slocum\",\r\n      \"description\": \"Demo of bogus window.\",\r\n      \"name\": \"Accordion Window\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.BogusWindow\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"demo/bogus-win/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"demo/bogus-win/client/\",\r\n            \"files\": [ \"bogus-win.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"bogus-icon\",\r\n            \"shortcutIconCls\": \"demo-bogus-shortcut\",\r\n            \"text\": \"Bogus Window\",\r\n            \"tooltip\": \"<b>Bogus Window</b><br />A bogus window\"\r\n         },\r\n         \"paths\": {\r\n             \"startmenu\": \"/Bogus Menu/Bogus Sub Menu\"\r\n         }\r\n      }\r\n   }\r\n}', '1');
INSERT INTO `qo_modules` VALUES ('demo-grid', 'demo/grid', '{\r\n   \"id\": \"demo-grid\",\r\n\r\n   \"type\": \"demo/grid\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Jack Slocum\",\r\n      \"description\": \"Demo of grid window.\",\r\n      \"name\": \"Grid Window\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.GridWindow\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"demo/grid-win/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"demo/grid-win/client/\",\r\n            \"files\": [ \"grid-win.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"grid-icon\",\r\n            \"shortcutIconCls\": \"demo-grid-shortcut\",\r\n            \"text\": \"Grid Window\",\r\n            \"tooltip\": \"<b>Grid Window</b><br />A grid window\"\r\n         },\r\n         \"paths\": {\r\n            \"startmenu\": \"/\"\r\n         }\r\n      }\r\n   }\r\n}', '1');
INSERT INTO `qo_modules` VALUES ('demo-layout', 'demo/layout', '{\r\n   \"id\": \"demo-layout\",\r\n\r\n   \"type\": \"demo/layout\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"description\": \"Demo of layout window.\",\r\n      \"name\": \"Layout Window\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.LayoutWindow\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"demo/layout-win/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"demo/layout-win/client/\",\r\n            \"files\": [ \"layout-win.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"layout-icon\",\r\n            \"shortcutIconCls\": \"demo-layout-shortcut\",\r\n            \"text\": \"Layout Window\",\r\n            \"tooltip\": \"<b>Layout Window</b><br />A layout window\"\r\n         },\r\n         \"paths\": {\r\n            \"startmenu\": \"/\"\r\n         }\r\n      }\r\n   }\r\n}', '1');
INSERT INTO `qo_modules` VALUES ('demo-tab', 'demo/tab', '{\r\n   \"id\": \"demo-tab\",\r\n   \r\n   \"type\": \"demo/tab\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"description\": \"Demo of tab window.\",\r\n      \"name\": \"Tab Window\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.TabWindow\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"demo/tab-win/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"demo/tab-win/client/\",\r\n            \"files\": [ \"tab-win.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"tab-icon\",\r\n            \"shortcutIconCls\": \"demo-tab-shortcut\",\r\n            \"text\": \"Tab Window\",\r\n            \"tooltip\": \"<b>Tab Window</b><br />A tab window\"\r\n         },\r\n         \"paths\": {\r\n            \"startmenu\": \"/\"\r\n         }\r\n      }\r\n   }\r\n}', '1');
INSERT INTO `qo_modules` VALUES ('qo-admin', 'system/administration', '{\r\n   \"id\": \"qo-admin\",\r\n\r\n   \"type\": \"system/administration\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"description\": \"Allows system administration\",\r\n      \"name\": \"Admin\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"dependencies\": [\r\n      { \"id\": \"columntree\", \"type\": \"library\" }\r\n   ],\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.QoAdmin\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"qwiki/admin/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"qwiki/admin/client/\",\r\n            \"files\": [ \"QoAdmin.js\" ]\r\n         },\r\n         {\r\n            \"directory\": \"qwiki/admin/client/lib/\",\r\n            \"files\": [ \"ActiveColumn.js\", \"ColumnNodeUI.js\", \"Nav.js\" ]\r\n         },\r\n         {\r\n            \"directory\": \"qwiki/admin/client/lib/groups/\",\r\n            \"files\": [ \"Groups.js\", \"GroupsAdd.js\", \"GroupsDetail.js\", \"GroupsGrid.js\", \"GroupsEdit.js\", \"GroupsTree.js\" ]\r\n         },\r\n         {\r\n            \"directory\": \"qwiki/admin/client/lib/members/\",\r\n            \"files\": [ \"Members.js\", \"MembersAdd.js\", \"MembersDetail.js\", \"MembersEdit.js\", \"MembersGrid.js\", \"MembersTree.js\" ]\r\n         },\r\n         {\r\n            \"directory\": \"qwiki/admin/client/lib/privileges/\",\r\n            \"files\": [ \"Privileges.js\", \"PrivilegesDetail.js\", \"PrivilegesGrid.js\", \"PrivilegesManage.js\", \"PrivilegesTree.js\" ]\r\n         },\r\n         {\r\n            \"directory\": \"qwiki/admin/client/lib/signups/\",\r\n            \"files\": [ \"Signups.js\", \"SignupsDetail.js\", \"SignupsGrid.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"qo-admin-icon\",\r\n            \"shortcutIconCls\": \"qo-admin-shortcut-icon\",\r\n            \"text\": \"Admin\",\r\n            \"tooltip\": \"<b>Admin</b><br />Allows system administration\"\r\n         },\r\n         \"paths\": {\r\n            \"startmenu\": \"/Admin\"\r\n         }\r\n      }\r\n   },\r\n\r\n   \"server\": {\r\n      \"methods\": [\r\n         { \"name\": \"addMember\", \"description\": \"Add a new member\" },\r\n         { \"name\": \"addMemberToGroup\", \"description\": \"Add a member to a group\" },\r\n         { \"name\": \"approveSignupsToGroup\", \"description\": \"Approve a signup request\" },\r\n         { \"name\": \"deleteMemberFromGroup\", \"description\": \"Delete a member from a group\" },\r\n         { \"name\": \"deleteMembers\", \"description\": \"Delete a member\" },\r\n         { \"name\": \"denySignups\", \"description\": \"Deny a signup request\" },\r\n         { \"name\": \"editMember\", \"description\": \"Edit a members information\" }\r\n      ],\r\n      \"class\": \"QoAdmin\",\r\n      \"file\": \"qwiki/admin/server/QoAdmin.php\"\r\n   }\r\n}', '1');
INSERT INTO `qo_modules` VALUES ('qo-mail', 'email', '{\r\n   \"id\": \"qo-mail\",\r\n\r\n   \"type\": \"system/email\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"description\": \"Allows users to send and receive email\",\r\n      \"name\": \"qWikiMail\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"dependencies\": [\r\n         { \"id\": \"iframecomponent\", \"type\": \"library\" }\r\n   ],\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.QoMail\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"qwiki/mail/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"qwiki/mail/client/\",\r\n            \"files\": [ \"QoMail.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"qo-mail-icon\",\r\n            \"shortcutIconCls\": \"qo-mail-shortcut-icon\",\r\n            \"text\": \"Mail\",\r\n            \"tooltip\": \"<b>Mail</b><br />Allows you to send and receive email\"\r\n         },\r\n         \"paths\": {\r\n            \"startmenu\": \"/\"\r\n         }\r\n      }\r\n   },\r\n\r\n   \"server\": {\r\n      \"methods\": [\r\n         { \"name\": \"loadMemberFolders\", \"description\": \"Allow member to load (view) their folders\" },\r\n         { \"name\": \"addMemberFolder\", \"description\": \"Allow member to add a new folder\" }\r\n      ],\r\n      \"class\": \"QoMail\",\r\n      \"file\": \"qwiki/mail/server/QoMail.php\"\r\n   }\r\n}', '1');
INSERT INTO `qo_modules` VALUES ('qo-preferences', 'system/preferences', '{\r\n   \"id\": \"qo-preferences\",\r\n\r\n   \"type\": \"system/preferences\",\r\n\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"description\": \"Allows users to set and save their desktop preferences\",\r\n      \"name\": \"Preferences\",\r\n      \"url\": \"www.qwikioffice.com\",\r\n      \"version\": \"1.0\"\r\n   },\r\n\r\n   \"dependencies\": [\r\n      { \"id\": \"colorpicker\", \"type\": \"library\" },\r\n      { \"id\": \"explorerview\", \"type\": \"library\" },\r\n      { \"id\": \"modalnotice\", \"type\": \"library\" }\r\n   ],\r\n\r\n   \"locale\": {\r\n      \"class\": \"QoDesk.QoPreferences.Locale\",\r\n      \"directory\": \"qwiki/preferences/client/locale/\",\r\n      \"extension\": \".json\",\r\n      \"languages\": [ \"en\" ]\r\n   },\r\n\r\n   \"client\": {\r\n      \"class\": \"QoDesk.QoPreferences\",\r\n      \"css\": [\r\n         {\r\n            \"directory\": \"qwiki/preferences/client/resources/\",\r\n            \"files\": [ \"styles.css\" ]\r\n         }\r\n      ],\r\n      \"javascript\": [\r\n         {\r\n            \"directory\": \"qwiki/preferences/client/\",\r\n            \"files\": [ \"QoPreferences.js\" ]\r\n         },\r\n         {\r\n            \"directory\": \"qwiki/preferences/client/lib/\",\r\n            \"files\": [ \"Appearance.js\", \"AutoRun.js\", \"Background.js\", \"Grid.js\", \"Nav.js\", \"QuickStart.js\", \"Shortcuts.js\" ]\r\n         }\r\n      ],\r\n      \"launcher\": {\r\n         \"config\": {\r\n            \"iconCls\": \"pref-icon\",\r\n            \"shortcutIconCls\": \"pref-shortcut-icon\",\r\n            \"text\": \"Preferences\",\r\n            \"tooltip\": \"<b>Preferences</b><br />Allows you to modify your desktop\"\r\n         },\r\n         \"paths\": {\r\n            \"contextmenu\": \"/\",\r\n            \"startmenutool\": \"/\"\r\n         }\r\n      }\r\n   },\r\n\r\n   \"server\": {\r\n      \"methods\": [\r\n         { \"name\": \"saveAppearance\", \"description\": \"Allow member to save appearance\" },\r\n         { \"name\": \"saveAutorun\", \"description\": \"Allow member to save which modules run at start up\" },\r\n         { \"name\": \"saveBackground\", \"description\": \"Allow member to save a wallpaper as the background\" },\r\n         { \"name\": \"saveQuickstart\", \"description\": \"Allow member to save which modules appear in the Quick Start panel\" },\r\n         { \"name\": \"saveShortcut\", \"description\": \"Allow member to save which modules appear as a Shortcut\" },\r\n         { \"name\": \"viewThemes\", \"description\": \"Allow member to view the available themes\" },\r\n         { \"name\": \"viewWallpapers\", \"description\": \"Allow member to view the available wallpapers\" }\r\n      ],\r\n      \"class\": \"QoPreferences\",\r\n      \"file\": \"qwiki/preferences/server/QoPreferences.php\"\r\n   }\r\n}', '1');
INSERT INTO `qo_privileges` VALUES ('1', '{\r\n   \"description\": \"A user with system administrator privileges.  The administrator for the desktop environment.\",\r\n\r\n   \"name\": \"System Administrator\",\r\n\r\n   \"modules\": [\r\n      {\r\n         \"id\": \"qo-admin\",\r\n         \"allow\": 1,\r\n         \"methods\": [\r\n            { \"name\": \"addGroup\", \"allow\": 1 },\r\n            { \"name\": \"addMember\", \"allow\": 1 },\r\n            { \"name\": \"addMemberToGroup\", \"allow\": 1 },\r\n            { \"name\": \"addPrivilege\", \"allow\": 1 },\r\n            { \"name\": \"approveSignupsToGroup\", \"allow\": 1 },\r\n            { \"name\": \"changeGroupPrivilege\", \"allow\": 1 },\r\n            { \"name\": \"deleteGroups\", \"allow\": 1 },\r\n            { \"name\": \"deleteMembers\", \"allow\": 1 },\r\n            { \"name\": \"deleteMemberFromGroup\", \"allow\": 1 },\r\n            { \"name\": \"deletePrivileges\", \"allow\": 1 },\r\n            { \"name\": \"denySignups\", \"allow\": 1 },\r\n            { \"name\": \"editGroup\", \"allow\": 1 },\r\n            { \"name\": \"editMember\", \"allow\": 1 },\r\n            { \"name\": \"editPrivilege\", \"allow\": 1 },\r\n            { \"name\": \"loadGroupsCombo\", \"allow\": 1 },\r\n            { \"name\": \"loadPrivilegesCombo\", \"allow\": 1 },\r\n            { \"name\": \"markSignupsAsSpam\", \"allow\": 1 },\r\n            { \"name\": \"viewAllGroups\", \"allow\": 1 },\r\n            { \"name\": \"viewAllMembers\", \"allow\": 1 },\r\n            { \"name\": \"viewAllPrivileges\", \"allow\": 1 },\r\n            { \"name\": \"viewAllSignups\", \"allow\": 1 },\r\n            { \"name\": \"viewGroup\", \"allow\": 1 },\r\n            { \"name\": \"viewGroupPrivileges\", \"allow\": 1 },\r\n            { \"name\": \"viewMember\", \"allow\": 1 },\r\n            { \"name\": \"viewMemberGroups\", \"allow\": 1 },\r\n            { \"name\": \"viewModuleMethods\", \"allow\": 1 },\r\n            { \"name\": \"viewPrivilegeModules\", \"allow\": 1 }\r\n         ]\r\n      },\r\n      {\r\n         \"id\": \"qo-mail\",\r\n         \"allow\": 1,\r\n         \"methods\": [\r\n            { \"name\": \"addMemberFolder\", \"allow\": 1 },\r\n            { \"name\": \"loadMemberFolders\", \"allow\": 1 }\r\n         ]\r\n      },\r\n      {\r\n         \"id\": \"qo-preferences\",\r\n         \"allow\": 1,\r\n         \"methods\": [\r\n            { \"name\": \"saveAppearance\", \"allow\": 1 },\r\n            { \"name\": \"saveAutorun\", \"allow\": 1 },\r\n            { \"name\": \"saveBackground\", \"allow\": 1 },\r\n            { \"name\": \"saveQuickstart\", \"allow\": 1 },\r\n            { \"name\": \"saveShortcut\", \"allow\": 1 },\r\n            { \"name\": \"viewThemes\", \"allow\": 1 },\r\n            { \"name\": \"viewWallpapers\", \"allow\": 1 }\r\n         ]\r\n      },\r\n      {\r\n         \"id\": \"demo-layout\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-grid\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-bogus\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-tab\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-accordion\",\r\n         \"allow\": 1\r\n      }\r\n   ]\r\n}', '1');
INSERT INTO `qo_privileges` VALUES ('2', '{\r\n   \"description\": \"A user with minimum (demo) privileges.  Can not save or edit.\",\r\n\r\n   \"name\": \"Demo\",\r\n\r\n   \"modules\": [\r\n      {\r\n         \"id\": \"qo-preferences\",\r\n         \"allow\": 1,\r\n         \"methods\": [\r\n            { \"name\": \"viewThemes\", \"allow\": 1 },\r\n            { \"name\": \"viewWallpapers\", \"allow\": 1 }\r\n         ]\r\n      },\r\n      {\r\n         \"id\": \"demo-layout\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-grid\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-bogus\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-tab\",\r\n         \"allow\": 1\r\n      },\r\n      {\r\n         \"id\": \"demo-accordion\",\r\n         \"allow\": 1\r\n      }\r\n   ]\r\n}', '1');
INSERT INTO `qo_sessions` VALUES ('8380258832e3bb957ccbdf6e1e3c646a', '1', '1', '{\"module\":{\"demo-accordion\":{\"valid\":1,\"loaded\":{\"css\":1}},\"demo-bogus\":{\"valid\":1,\"loaded\":{\"css\":1}},\"demo-grid\":{\"valid\":1,\"loaded\":{\"css\":1}},\"demo-layout\":{\"valid\":1,\"loaded\":{\"css\":1}},\"demo-tab\":{\"valid\":1,\"loaded\":{\"css\":1}},\"qo-admin\":{\"valid\":1,\"loaded\":{\"css\":1,\"javascript\":1}},\"qo-mail\":{\"valid\":1,\"loaded\":{\"css\":1}},\"qo-preferences\":{\"valid\":1,\"loaded\":{\"css\":1}}},\"library\":{\"columntree\":{\"loaded\":{\"css\":1}},\"colorpicker\":{\"loaded\":{\"css\":1}},\"explorerview\":{\"loaded\":{\"css\":1}}}}', '127.0.0.1', '2009-04-13 22:10:27');
INSERT INTO `qo_themes` VALUES ('1', '{\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"version\": \"1.0\",\r\n      \"url\": \"www.qWikiOffice.com\"\r\n   },\r\n   \"group\": \"Vista\",\r\n   \"name\": \"Vista Black\",\r\n   \"thumbnail\": \"xtheme-vistablack/xtheme-vistablack.png\",\r\n   \"file\": \"xtheme-vistablack/css/xtheme-vistablack.css\"\r\n}', '1');
INSERT INTO `qo_themes` VALUES ('2', '{\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"version\": \"1.0\",\r\n      \"url\": \"www.qWikiOffice.com\"\r\n   },\r\n   \"group\": \"Vista\",\r\n   \"name\": \"Vista Blue\",\r\n   \"thumbnail\": \"xtheme-vistablue/xtheme-vistablue.png\",\r\n   \"file\": \"xtheme-vistablue/css/xtheme-vistablue.css\"\r\n}', '1');
INSERT INTO `qo_themes` VALUES ('3', '{\r\n   \"about\": {\r\n      \"author\": \"Todd Murdock\",\r\n      \"version\": \"1.0\",\r\n      \"url\": \"www.qWikiOffice.com\"\r\n   },\r\n   \"group\": \"Vista\",\r\n   \"name\": \"Vista Glass\",\r\n   \"thumbnail\": \"xtheme-vistaglass/xtheme-vistaglass.png\",\r\n   \"file\": \"xtheme-vistaglass/css/xtheme-vistaglass.css\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('1', '{\r\n   \"group\": \"Blank\",\r\n   \"name\": \"Blank\",\r\n   \"thumbnail\": \"thumbnails/blank.gif\",\r\n   \"file\": \"blank.gif\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('2', '{\r\n   \"group\": \"Pattern\",\r\n   \"name\": \"Blue Psychedelic\",\r\n   \"thumbnail\": \"thumbnails/blue-psychedelic.jpg\",\r\n   \"file\": \"blue-psychedelic.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('3', '{\r\n   \"group\": \"Pattern\",\r\n   \"name\": \"Blue Swirl\",\r\n   \"thumbnail\": \"thumbnails/blue-swirl.jpg\",\r\n   \"file\": \"blue-swirl.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('4', '{\r\n   \"group\": \"Nature\",\r\n   \"name\": \"Colorado Farm\",\r\n   \"thumbnail\": \"thumbnails/colorado-farm.jpg\",\r\n   \"file\": \"colorado-farm.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('5', '{\r\n   \"group\": \"Nature\",\r\n   \"name\": \"Curls On Green\",\r\n   \"thumbnail\": \"thumbnails/curls-on-green.jpg\",\r\n   \"file\": \"curls-on-green.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('6', '{\r\n   \"group\": \"Pattern\",\r\n   \"name\": \"Emotion\",\r\n   \"thumbnail\": \"thumbnails/emotion.jpg\",\r\n   \"file\": \"emotion.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('7', '{\r\n   \"group\": \"Pattern\",\r\n   \"name\": \"Eos\",\r\n   \"thumbnail\": \"thumbnails/eos.jpg\",\r\n   \"file\": \"eos.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('8', '{\r\n   \"group\": \"Nature\",\r\n   \"name\": \"Fields of Peace\",\r\n   \"thumbnail\": \"thumbnails/fields-of-peace.jpg\",\r\n   \"file\": \"fields-of-peace.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('9', '{\r\n   \"group\": \"Nature\",\r\n   \"name\": \"Fresh Morning\",\r\n   \"thumbnail\": \"thumbnails/fresh-morning.jpg\",\r\n   \"file\": \"fresh-morning.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('10', '{\r\n   \"group\": \"Nature\",\r\n   \"name\": \"Lady Buggin\",\r\n   \"thumbnail\": \"thumbnails/ladybuggin.jpg\",\r\n   \"file\": \"ladybuggin.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('11', '{\r\n   \"group\": \"qWikiOffice\",\r\n   \"name\": \"qWikiOffice\",\r\n   \"thumbnail\": \"thumbnails/qwikioffice.jpg\",\r\n   \"file\": \"qwikioffice.jpg\"\r\n}', '1');
INSERT INTO `qo_wallpapers` VALUES ('12', '{\r\n   \"group\": \"Nature\",\r\n   \"name\": \"Summer\",\r\n   \"thumbnail\": \"thumbnails/summer.jpg\",\r\n   \"file\": \"summer.jpg\"\r\n}', '1');
