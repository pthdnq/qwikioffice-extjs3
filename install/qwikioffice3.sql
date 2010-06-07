-- phpMyAdmin SQL Dump
-- version 3.1.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 06 Lut 2010, 10:57
-- Wersja serwera: 5.1.32
-- Wersja PHP: 5.2.9-1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `qwikioffice3`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_groups`
--

CREATE TABLE IF NOT EXISTS `qo_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(35) DEFAULT NULL,
  `description` text,
  `importance` int(3) unsigned DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Zrzut danych tabeli `qo_groups`
--

INSERT INTO `qo_groups` (`id`, `name`, `description`, `importance`, `active`) VALUES
(1, 'System Administrator', 'The administrator of this desktop system.', 100, 1),
(2, 'Demo', 'A demo user', 1, 1),
(3, 'Moderator', 'Moderate contents', 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_groups_has_members`
--

CREATE TABLE IF NOT EXISTS `qo_groups_has_members` (
  `qo_groups_id` int(11) unsigned NOT NULL DEFAULT '0',
  `qo_members_id` int(11) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Is the member currently active in this group',
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Is the member the administrator of this group',
  PRIMARY KEY (`qo_members_id`,`qo_groups_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `qo_groups_has_members`
--

INSERT INTO `qo_groups_has_members` (`qo_groups_id`, `qo_members_id`, `active`, `admin`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 0),
(3, 1, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_groups_has_member_preferences`
--

CREATE TABLE IF NOT EXISTS `qo_groups_has_member_preferences` (
  `qo_groups_id` int(11) unsigned NOT NULL DEFAULT '0',
  `qo_members_id` int(11) unsigned NOT NULL DEFAULT '0',
  `data` text COMMENT 'JSON data',
  PRIMARY KEY (`qo_members_id`,`qo_groups_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `qo_groups_has_member_preferences`
--

INSERT INTO `qo_groups_has_member_preferences` (`qo_groups_id`, `qo_members_id`, `data`) VALUES
(0, 0, '{"backgroundColor": "f9f9f9","fontColor": "000000","launchers": {"autorun": [],"quickstart": [],"shortcut": []},"themeId": 1,"transparency": 100,"wallpaperId": 11,"wallpaperPosition": "center"}'),
(1, 1, '{"launchers":{"shortcut":["demo-tab","qo-admin","demo-grid","demo-layout","qo-preferences"],"quickstart":["qo-preferences","demo-tab","demo-layout","qo-admin"],"autorun":[]},"backgroundColor":"F5FCF3","fontColor":"181717","wallpaperId":11,"wallpaperPosition":"center","themeId":1,"transparency":100}');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_groups_has_privileges`
--

CREATE TABLE IF NOT EXISTS `qo_groups_has_privileges` (
  `qo_groups_id` int(11) unsigned NOT NULL DEFAULT '0',
  `qo_privileges_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`qo_groups_id`,`qo_privileges_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `qo_groups_has_privileges`
--

INSERT INTO `qo_groups_has_privileges` (`qo_groups_id`, `qo_privileges_id`) VALUES
(1, 1),
(2, 2),
(3, 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_libraries`
--

CREATE TABLE IF NOT EXISTS `qo_libraries` (
  `id` varchar(35) NOT NULL DEFAULT '',
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `qo_libraries`
--

INSERT INTO `qo_libraries` (`id`, `data`, `active`) VALUES
('colorpicker', '{\r\n   "dependencies": [\r\n      { "id": "hexfield", "type": "library" }\r\n   ],\r\n\r\n   "client": {\r\n      "css": [\r\n         {\r\n           "directory": "color-picker/resources/",\r\n           "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "color-picker/",\r\n            "files": [ "Ext.ux.ColorPicker.js" ]\r\n         }\r\n      ]\r\n   }\r\n}', 1),
('columntree', '{\r\n   "client": {\r\n      "css": [\r\n         {\r\n            "directory": "column-tree/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ]\r\n   }\r\n}', 1),
('explorerview', '{\r\n   "client": {\r\n      "css": [\r\n         {\r\n            "directory": "explorer-view/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "explorer-view/",\r\n            "files": [ "Ext.ux.grid.ExplorerView.js", "Ext.ux.grid.GroupingExplorerView.js" ]\r\n         }\r\n      ]\r\n   }\r\n}', 1),
('hexfield', '{\r\n   "client": {\r\n      "javascript": [\r\n         {\r\n            "directory": "hex-field/",\r\n            "files": [ "Ext.ux.form.HexField.js" ]\r\n         }\r\n      ]\r\n   }\r\n}', 1),
('iframecomponent', '{\r\n   "client": {\r\n      "javascript": [\r\n         {\r\n            "directory": "iframe-component/",\r\n            "files": [ "Ext.ux.IFrameComponent.js" ]\r\n         }\r\n      ]\r\n   }\r\n}', 1),
('modalnotice', '{\r\n   "client": {\r\n      "javascript": [\r\n         {\r\n            "directory": "modal-notice/",\r\n            "files": [ "Ext.plugin.ModalNotice.js" ]\r\n         }\r\n      ]\r\n   }\r\n}', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_log`
--

CREATE TABLE IF NOT EXISTS `qo_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(15) DEFAULT NULL COMMENT 'ERROR, WARNING, MESSAGE or AUDIT',
  `text` text,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=391 ;

--
-- Zrzut danych tabeli `qo_log`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_members`
--

CREATE TABLE IF NOT EXISTS `qo_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(25) DEFAULT NULL,
  `last_name` varchar(35) DEFAULT NULL,
  `email_address` varchar(55) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `locale` varchar(5) DEFAULT 'en',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Is the member currently active',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Zrzut danych tabeli `qo_members`
--

INSERT INTO `qo_members` (`id`, `first_name`, `last_name`, `email_address`, `password`, `locale`, `active`) VALUES
(1, 'Todd', 'Murdock', 'Admin', 'cdbb696031cc82ad149f552b759417d6694ae320', 'en', 1),
(2, 'Todd', 'Murdock', 'demo', '3aa50a240649d35f2effc6b4a5247af9980adc37', 'en', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_modules`
--

CREATE TABLE IF NOT EXISTS `qo_modules` (
  `id` varchar(35) NOT NULL DEFAULT '',
  `type` varchar(35) DEFAULT NULL,
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `qo_modules`
--

INSERT INTO `qo_modules` (`id`, `type`, `data`, `active`) VALUES
('demo-accordion', 'demo/accordion', '{\r\n   "id": "demo-accordion",\r\n\r\n   "type": "demo/accordion",\r\n\r\n   "about": {\r\n      "author": "Jack Slocum",\r\n      "description": "Demo of window with accordion.",\r\n      "name": "Accordion Window",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "locale": {\r\n      "class": "QoDesk.AccordionWindow.Locale",\r\n      "directory": "demo/acc-win/client/locale/",\r\n      "extension": ".json",\r\n      "languages": [ "en" ]\r\n   },\r\n\r\n   "client": {\r\n      "class": "QoDesk.AccordionWindow",\r\n      "css": [\r\n         {\r\n            "directory": "demo/acc-win/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "demo/acc-win/client/",\r\n            "files": [ "acc-win.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "acc-icon",\r\n            "shortcutIconCls": "demo-acc-shortcut",\r\n            "text": "Accordion Window",\r\n            "tooltip": "<b>Accordion Window</b><br />A window with an accordion layout"\r\n         },\r\n         "paths": {\r\n            "startmenu": "/"\r\n         }\r\n      }\r\n   }\r\n}', 1),
('demo-bogus', 'demo/bogus', '{\r\n   "id": "demo-bogus",\r\n\r\n   "type": "demo/bogus",\r\n\r\n   "about": {\r\n      "author": "Jack Slocum",\r\n      "description": "Demo of bogus window.",\r\n      "name": "Accordion Window",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "client": {\r\n      "class": "QoDesk.BogusWindow",\r\n      "css": [\r\n         {\r\n            "directory": "demo/bogus-win/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "demo/bogus-win/client/",\r\n            "files": [ "bogus-win.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "bogus-icon",\r\n            "shortcutIconCls": "demo-bogus-shortcut",\r\n            "text": "Bogus Window",\r\n            "tooltip": "<b>Bogus Window</b><br />A bogus window"\r\n         },\r\n         "paths": {\r\n             "startmenu": "/Bogus Menu/Bogus Sub Menu"\r\n         }\r\n      }\r\n   }\r\n}', 1),
('demo-grid', 'demo/grid', '{\r\n   "id": "demo-grid",\r\n\r\n   "type": "demo/grid",\r\n\r\n   "about": {\r\n      "author": "Jack Slocum",\r\n      "description": "Demo of grid window.",\r\n      "name": "Grid Window",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "client": {\r\n      "class": "QoDesk.GridWindow",\r\n      "css": [\r\n         {\r\n            "directory": "demo/grid-win/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "demo/grid-win/client/",\r\n            "files": [ "grid-win.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "grid-icon",\r\n            "shortcutIconCls": "demo-grid-shortcut",\r\n            "text": "Grid Window",\r\n            "tooltip": "<b>Grid Window</b><br />A grid window"\r\n         },\r\n         "paths": {\r\n            "startmenu": "/"\r\n         }\r\n      }\r\n   }\r\n}', 1),
('demo-layout', 'demo/layout', '{\r\n   "id": "demo-layout",\r\n\r\n   "type": "demo/layout",\r\n\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "description": "Demo of layout window.",\r\n      "name": "Layout Window",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "client": {\r\n      "class": "QoDesk.LayoutWindow",\r\n      "css": [\r\n         {\r\n            "directory": "demo/layout-win/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "demo/layout-win/client/",\r\n            "files": [ "layout-win.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "layout-icon",\r\n            "shortcutIconCls": "demo-layout-shortcut",\r\n            "text": "Layout Window",\r\n            "tooltip": "<b>Layout Window</b><br />A layout window"\r\n         },\r\n         "paths": {\r\n            "startmenu": "/"\r\n         }\r\n      }\r\n   }\r\n}', 1),
('demo-tab', 'demo/tab', '{\r\n   "id": "demo-tab",\r\n   \r\n   "type": "demo/tab",\r\n\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "description": "Demo of tab window.",\r\n      "name": "Tab Window",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "client": {\r\n      "class": "QoDesk.TabWindow",\r\n      "css": [\r\n         {\r\n            "directory": "demo/tab-win/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "demo/tab-win/client/",\r\n            "files": [ "tab-win.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "tab-icon",\r\n            "shortcutIconCls": "demo-tab-shortcut",\r\n            "text": "Tab Window",\r\n            "tooltip": "<b>Tab Window</b><br />A tab window"\r\n         },\r\n         "paths": {\r\n            "startmenu": "/"\r\n         }\r\n      }\r\n   }\r\n}', 1),
('qo-admin', 'system/administration', '{\r\n   "id": "qo-admin",\r\n\r\n   "type": "system/administration",\r\n\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "description": "Allows system administration",\r\n      "name": "Admin",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "dependencies": [\r\n      { "id": "columntree", "type": "library" }\r\n   ],\r\n\r\n   "client": {\r\n      "class": "QoDesk.QoAdmin",\r\n      "css": [\r\n         {\r\n            "directory": "qwiki/admin/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "qwiki/admin/client/",\r\n            "files": [ "QoAdmin.js" ]\r\n         },\r\n         {\r\n            "directory": "qwiki/admin/client/lib/",\r\n            "files": [ "ActiveColumn.js", "ColumnNodeUI.js", "Nav.js" ]\r\n         },\r\n         {\r\n            "directory": "qwiki/admin/client/lib/groups/",\r\n            "files": [ "Groups.js", "GroupsAdd.js", "GroupsDetail.js", "GroupsGrid.js", "GroupsEdit.js", "GroupsTree.js" ]\r\n         },\r\n         {\r\n            "directory": "qwiki/admin/client/lib/members/",\r\n            "files": [ "Members.js", "MembersAdd.js", "MembersDetail.js", "MembersEdit.js", "MembersGrid.js", "MembersTree.js" ]\r\n         },\r\n         {\r\n            "directory": "qwiki/admin/client/lib/privileges/",\r\n            "files": [ "Privileges.js", "PrivilegesDetail.js", "PrivilegesGrid.js", "PrivilegesManage.js", "PrivilegesTree.js" ]\r\n         },\r\n         {\r\n            "directory": "qwiki/admin/client/lib/signups/",\r\n            "files": [ "Signups.js", "SignupsDetail.js", "SignupsGrid.js" ]\r\n         },\r\n         {\r\n            "directory": "qwiki/admin/client/lib/modules/",\r\n            "files": [ "Modules.js","ModulesGrid.js","ModulesDetail.js","ModulesMethodsGrid.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "qo-admin-icon",\r\n            "shortcutIconCls": "qo-admin-shortcut-icon",\r\n            "text": "Admin",\r\n            "tooltip": "<b>Admin</b><br />Allows system administration"\r\n         },\r\n         "paths": {\r\n            "startmenu": "/Admin"\r\n         }\r\n      }\r\n   },\r\n\r\n   "server": {\r\n      "methods": [\r\n         { "name": "addMember", "description": "Add a new member" },\r\n         { "name": "addMemberToGroup", "description": "Add a member to a group" },\r\n         { "name": "approveSignupsToGroup", "description": "Approve a signup request" },\r\n         { "name": "deleteMemberFromGroup", "description": "Delete a member from a group" },\r\n         { "name": "deleteMembers", "description": "Delete a member" },\r\n         { "name": "denySignups", "description": "Deny a signup request" },\r\n         { "name": "editMember", "description": "Edit a members information" }\r\n      ],\r\n      "class": "QoAdmin",\r\n      "file": "qwiki/admin/server/QoAdmin.php"\r\n   }\r\n}', 1),
('qo-mail', 'email', '{\r\n   "id": "qo-mail",\r\n\r\n   "type": "system/email",\r\n\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "description": "Allows users to send and receive email",\r\n      "name": "qWikiMail",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "dependencies": [\r\n         { "id": "iframecomponent", "type": "library" }\r\n   ],\r\n\r\n   "client": {\r\n      "class": "QoDesk.QoMail",\r\n      "css": [\r\n         {\r\n            "directory": "qwiki/mail/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "qwiki/mail/client/",\r\n            "files": [ "QoMail.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "qo-mail-icon",\r\n            "shortcutIconCls": "qo-mail-shortcut-icon",\r\n            "text": "Mail",\r\n            "tooltip": "<b>Mail</b><br />Allows you to send and receive email"\r\n         },\r\n         "paths": {\r\n            "startmenu": "/"\r\n         }\r\n      }\r\n   },\r\n\r\n   "server": {\r\n      "methods": [\r\n         { "name": "loadMemberFolders", "description": "Allow member to load (view) their folders" },\r\n         { "name": "addMemberFolder", "description": "Allow member to add a new folder" }\r\n      ],\r\n      "class": "QoMail",\r\n      "file": "qwiki/mail/server/QoMail.php"\r\n   }\r\n}', 0),
('qo-preferences', 'system/preferences', '{\r\n   "id": "qo-preferences",\r\n\r\n   "type": "system/preferences",\r\n\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "description": "Allows users to set and save their desktop preferences",\r\n      "name": "Preferences",\r\n      "url": "www.qwikioffice.com",\r\n      "version": "1.0"\r\n   },\r\n\r\n   "dependencies": [\r\n      { "id": "colorpicker", "type": "library" },\r\n      { "id": "explorerview", "type": "library" },\r\n      { "id": "modalnotice", "type": "library" }\r\n   ],\r\n\r\n   "locale": {\r\n      "class": "QoDesk.QoPreferences.Locale",\r\n      "directory": "qwiki/preferences/client/locale/",\r\n      "extension": ".json",\r\n      "languages": [ "en" ]\r\n   },\r\n\r\n   "client": {\r\n      "class": "QoDesk.QoPreferences",\r\n      "css": [\r\n         {\r\n            "directory": "qwiki/preferences/client/resources/",\r\n            "files": [ "styles.css" ]\r\n         }\r\n      ],\r\n      "javascript": [\r\n         {\r\n            "directory": "qwiki/preferences/client/",\r\n            "files": [ "QoPreferences.js" ]\r\n         },\r\n         {\r\n            "directory": "qwiki/preferences/client/lib/",\r\n            "files": [ "Appearance.js", "AutoRun.js", "Background.js", "Grid.js", "Nav.js", "QuickStart.js", "Shortcuts.js" ]\r\n         }\r\n      ],\r\n      "launcher": {\r\n         "config": {\r\n            "iconCls": "pref-icon",\r\n            "shortcutIconCls": "pref-shortcut-icon",\r\n            "text": "Preferences",\r\n            "tooltip": "<b>Preferences</b><br />Allows you to modify your desktop"\r\n         },\r\n         "paths": {\r\n            "contextmenu": "/",\r\n            "startmenutool": "/"\r\n         }\r\n      }\r\n   },\r\n\r\n   "server": {\r\n      "methods": [\r\n         { "name": "saveAppearance", "description": "Allow member to save appearance" },\r\n         { "name": "saveAutorun", "description": "Allow member to save which modules run at start up" },\r\n         { "name": "saveBackground", "description": "Allow member to save a wallpaper as the background" },\r\n         { "name": "saveQuickstart", "description": "Allow member to save which modules appear in the Quick Start panel" },\r\n         { "name": "saveShortcut", "description": "Allow member to save which modules appear as a Shortcut" },\r\n         { "name": "viewThemes", "description": "Allow member to view the available themes" },\r\n         { "name": "viewWallpapers", "description": "Allow member to view the available wallpapers" }\r\n      ],\r\n      "class": "QoPreferences",\r\n      "file": "qwiki/preferences/server/QoPreferences.php"\r\n   }\r\n}', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_privileges`
--

CREATE TABLE IF NOT EXISTS `qo_privileges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Zrzut danych tabeli `qo_privileges`
--

INSERT INTO `qo_privileges` (`id`, `data`, `active`) VALUES
(1, '{\r\n   "description": "A user with system administrator privileges.  The administrator for the desktop environment.",\r\n\r\n   "name": "System Administrator",\r\n\r\n   "modules": [\r\n      {\r\n         "id": "qo-admin",\r\n         "allow": 1,\r\n         "methods": [\r\n            { "name": "addGroup", "allow": 1 },\r\n            { "name": "addMember", "allow": 1 },\r\n            { "name": "addMemberToGroup", "allow": 1 },\r\n            { "name": "addPrivilege", "allow": 1 },\r\n            { "name": "approveSignupsToGroup", "allow": 1 },\r\n            { "name": "changeGroupPrivilege", "allow": 1 },\r\n            { "name": "deleteGroups", "allow": 1 },\r\n            { "name": "deleteMembers", "allow": 1 },\r\n            { "name": "deleteMemberFromGroup", "allow": 1 },\r\n            { "name": "deletePrivileges", "allow": 1 },\r\n            { "name": "denySignups", "allow": 1 },\r\n            { "name": "editGroup", "allow": 1 },\r\n            { "name": "editMember", "allow": 1 },\r\n            { "name": "editPrivilege", "allow": 1 },\r\n            { "name": "loadGroupsCombo", "allow": 1 },\r\n            { "name": "loadPrivilegesCombo", "allow": 1 },\r\n            { "name": "markSignupsAsSpam", "allow": 1 },\r\n            { "name": "viewAllGroups", "allow": 1 },\r\n            { "name": "viewAllMembers", "allow": 1 },\r\n            { "name": "viewAllPrivileges", "allow": 1 },\r\n            { "name": "viewAllSignups", "allow": 1 },\r\n            { "name": "viewGroup", "allow": 1 },\r\n            { "name": "viewGroupPrivileges", "allow": 1 },\r\n            { "name": "viewMember", "allow": 1 },\r\n            { "name": "viewMemberGroups", "allow": 1 },\r\n            { "name": "viewModuleMethods", "allow": 1 },\r\n            { "name": "viewPrivilegeModules", "allow": 1 },\r\n            { "name": "viewAllModules", "allow": 1 },\r\n            { "name": "viewModule", "allow": 1 },\r\n            { "name": "changeModuleStatus","allow":1},\r\n            { "name": "viewMethods","allow":1}\r\n         ]\r\n      },\r\n      {\r\n         "id": "qo-mail",\r\n         "allow": 1,\r\n         "methods": [\r\n            { "name": "addMemberFolder", "allow": 1 },\r\n            { "name": "loadMemberFolders", "allow": 1 }\r\n         ]\r\n      },\r\n      {\r\n         "id": "qo-preferences",\r\n         "allow": 1,\r\n         "methods": [\r\n            { "name": "saveAppearance", "allow": 1 },\r\n            { "name": "saveAutorun", "allow": 1 },\r\n            { "name": "saveBackground", "allow": 1 },\r\n            { "name": "saveQuickstart", "allow": 1 },\r\n            { "name": "saveShortcut", "allow": 1 },\r\n            { "name": "viewThemes", "allow": 1 },\r\n            { "name": "viewWallpapers", "allow": 1 }\r\n         ]\r\n      },\r\n      {\r\n         "id": "demo-layout",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-grid",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-bogus",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-tab",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-accordion",\r\n         "allow": 1\r\n      }\r\n   ]\r\n}', 1),
(2, '{\r\n   "description": "A user with minimum (demo) privileges.  Can not save or edit.",\r\n\r\n   "name": "Demo",\r\n\r\n   "modules": [\r\n      {\r\n         "id": "qo-preferences",\r\n         "allow": 1,\r\n         "methods": [\r\n            { "name": "viewThemes", "allow": 1 },\r\n            { "name": "viewWallpapers", "allow": 1 }\r\n         ]\r\n      },\r\n      {\r\n         "id": "demo-layout",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-grid",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-bogus",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-tab",\r\n         "allow": 1\r\n      },\r\n      {\r\n         "id": "demo-accordion",\r\n         "allow": 1\r\n      }\r\n   ]\r\n}', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_sessions`
--

CREATE TABLE IF NOT EXISTS `qo_sessions` (
  `id` varchar(128) NOT NULL DEFAULT '' COMMENT 'a randomly generated id',
  `qo_members_id` int(11) unsigned NOT NULL DEFAULT '0',
  `qo_groups_id` int(11) unsigned DEFAULT NULL COMMENT 'Group the member signed in under',
  `data` text,
  `ip` varchar(16) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`qo_members_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `qo_sessions`
--

INSERT INTO `qo_sessions` (`id`, `qo_members_id`, `qo_groups_id`, `data`, `ip`, `date`) VALUES
('8380258832e3bb957ccbdf6e1e3c646a', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-mail":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-04-13 22:10:27'),
('6d447af1e77c821642705ac2d3a6a669', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-11 16:10:11'),
('d129e9bebade6078d69a6c8124461a13', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-11 14:46:59'),
('ae6a5a6f9d98f9f6cdb8beb69ed08fe5', 2, 2, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-20 11:56:35'),
('b142e35d8222ad34b61950ec97e7f822', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-17 16:56:24'),
('37eb09952f448e39d114aaaa2eb38514', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1,"javascript":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1,"javascript":1}},"explorerview":{"loaded":{"css":1,"javascript":1}},"hexfield":{"loaded":{"javascript":1}},"modalnotice":{"loaded":{"javascript":1}}}}', '127.0.0.1', '2009-11-17 00:09:31'),
('150891d941d3ce87f8c51fa82c05136c', 2, 2, NULL, '127.0.0.1', '2009-11-18 21:35:59'),
('caeaf93f6808caefab3e461ee5398d18', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-18 22:00:37'),
('e5d8065d6cd74c2fac7858cc426b75e7', 2, 2, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-23 19:56:39'),
('45a3b96cd2b905428cc9519ffea7b937', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-22 17:09:30'),
('8ee2e9f76e055d81b71537118ff62240', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-29 00:28:03'),
('e6c0a0c719aba7bd5e058fa8dbd25f22', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1,"javascript":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1,"javascript":1}},"explorerview":{"loaded":{"css":1,"javascript":1}},"hexfield":{"loaded":{"javascript":1}},"modalnotice":{"loaded":{"javascript":1}}}}', '127.0.0.1', '2009-11-24 22:04:45'),
('7ccc9be47f0b1db13243dd890fe13c38', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-24 20:05:23'),
('6b1118fe056319d7294f581e8e6b83ad', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-23 20:22:31'),
('1df83406e20cee00111faf031da7ad70', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-08 22:38:46'),
('89d5a0406021bf11d32177e92b2838ff', 2, 2, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-08 22:38:30'),
('6506eb42cea72917d28f2d4843cb2734', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-30 11:42:55'),
('e03b6d03560808529bc8248792d1e02f', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-28 19:25:41'),
('8f904816fe90e1173abbe6476ac2b1be', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-28 19:26:01'),
('7a0bc9a3f5893ce977c87636de6f67c3', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-26 09:52:40'),
('e5d2a81d4f062fbad35d9835d8b03119', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-11-26 21:39:15'),
('15e3487fd59447f233c1aad0b1a16631', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-25 22:57:34'),
('3f574ad9a0d468c9d814da6390594a9c', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-22 22:21:10'),
('846e5262c0e2714e6fb947e4422de6ef', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-23 14:28:10'),
('e19f6c76e6245501604c4f9e7bc12c9d', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-23 23:35:21'),
('b5c580ed49f6a69d0f7f475d8b574c3d', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2009-12-25 12:40:18'),
('1f7cf9252f6a86be2ffd7bb76ae3c998', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2010-01-04 19:51:32'),
('bbaf80fa507d42ff28601721e9309291', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-bogus":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-grid":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-layout":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-tab":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1,"javascript":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1,"javascript":1}},"explorerview":{"loaded":{"css":1,"javascript":1}},"hexfield":{"loaded":{"javascript":1}},"modalnotice":{"loaded":{"javascript":1}}}}', '127.0.0.1', '2010-01-28 22:49:35'),
('8d637933af41bb16b89a1ed493e34ca8', 2, 2, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1,"javascript":1}}},"library":{"colorpicker":{"loaded":{"css":1,"javascript":1}},"explorerview":{"loaded":{"css":1,"javascript":1}},"hexfield":{"loaded":{"javascript":1}},"modalnotice":{"loaded":{"javascript":1}}}}', '127.0.0.1', '2010-01-11 21:57:08'),
('40fe0cffed3a3d7362a3e78ceb66d6e2', 2, 2, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1}}},"library":{"colorpicker":{"loaded":{"css":1}},"explorerview":{"loaded":{"css":1}}}}', '127.0.0.1', '2010-01-11 22:54:35'),
('30de5fbee7ccd786afc8d405e20a8608', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1}},"demo-layout":{"valid":1,"loaded":{"css":1}},"demo-tab":{"valid":1,"loaded":{"css":1}},"qo-admin":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-preferences":{"valid":1,"loaded":{"css":1,"javascript":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1,"javascript":1}},"explorerview":{"loaded":{"css":1,"javascript":1}},"hexfield":{"loaded":{"javascript":1}},"modalnotice":{"loaded":{"javascript":1}}}}', '127.0.0.1', '2010-01-28 12:41:50'),
('f770d7dcfaf08c80238395ac93a558d2', 1, 1, '{"module":{"demo-accordion":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-bogus":{"valid":1,"loaded":{"css":1}},"demo-grid":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-layout":{"valid":1,"loaded":{"css":1,"javascript":1}},"demo-tab":{"valid":1,"loaded":{"css":1,"javascript":1}},"qo-admin":{"valid":1,"loaded":{"css":1}},"qo-preferences":{"valid":1,"loaded":{"css":1,"javascript":1}}},"library":{"columntree":{"loaded":{"css":1}},"colorpicker":{"loaded":{"css":1,"javascript":1}},"explorerview":{"loaded":{"css":1,"javascript":1}},"hexfield":{"loaded":{"javascript":1}},"modalnotice":{"loaded":{"javascript":1}}}}', '127.0.0.1', '2010-02-06 10:38:15');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_signup_requests`
--

CREATE TABLE IF NOT EXISTS `qo_signup_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(25) DEFAULT NULL,
  `last_name` varchar(35) DEFAULT NULL,
  `email_address` varchar(55) DEFAULT NULL,
  `comments` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `qo_signup_requests`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_spam`
--

CREATE TABLE IF NOT EXISTS `qo_spam` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email_address` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `qo_spam`
--


-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_themes`
--

CREATE TABLE IF NOT EXISTS `qo_themes` (
  `id` varchar(35) NOT NULL DEFAULT '',
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `qo_themes`
--

INSERT INTO `qo_themes` (`id`, `data`, `active`) VALUES
('1', '{\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "version": "1.0",\r\n      "url": "www.qWikiOffice.com"\r\n   },\r\n   "group": "Vista",\r\n   "name": "Vista Black",\r\n   "thumbnail": "xtheme-vistablack/xtheme-vistablack.png",\r\n   "file": "xtheme-vistablack/css/xtheme-vistablack.css"\r\n}', 1),
('2', '{\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "version": "1.0",\r\n      "url": "www.qWikiOffice.com"\r\n   },\r\n   "group": "Vista",\r\n   "name": "Vista Blue",\r\n   "thumbnail": "xtheme-vistablue/xtheme-vistablue.png",\r\n   "file": "xtheme-vistablue/css/xtheme-vistablue.css"\r\n}', 1),
('3', '{\r\n   "about": {\r\n      "author": "Todd Murdock",\r\n      "version": "1.0",\r\n      "url": "www.qWikiOffice.com"\r\n   },\r\n   "group": "Vista",\r\n   "name": "Vista Glass",\r\n   "thumbnail": "xtheme-vistaglass/xtheme-vistaglass.png",\r\n   "file": "xtheme-vistaglass/css/xtheme-vistaglass.css"\r\n}', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `qo_wallpapers`
--

CREATE TABLE IF NOT EXISTS `qo_wallpapers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data` text COMMENT 'The definition data ( JSON )',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'A value of 1 or 0 is expected',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Zrzut danych tabeli `qo_wallpapers`
--

INSERT INTO `qo_wallpapers` (`id`, `data`, `active`) VALUES
(1, '{\r\n   "group": "Blank",\r\n   "name": "Blank",\r\n   "thumbnail": "thumbnails/blank.gif",\r\n   "file": "blank.gif"\r\n}', 1),
(2, '{\r\n   "group": "Pattern",\r\n   "name": "Blue Psychedelic",\r\n   "thumbnail": "thumbnails/blue-psychedelic.jpg",\r\n   "file": "blue-psychedelic.jpg"\r\n}', 1),
(3, '{\r\n   "group": "Pattern",\r\n   "name": "Blue Swirl",\r\n   "thumbnail": "thumbnails/blue-swirl.jpg",\r\n   "file": "blue-swirl.jpg"\r\n}', 1),
(4, '{\r\n   "group": "Nature",\r\n   "name": "Colorado Farm",\r\n   "thumbnail": "thumbnails/colorado-farm.jpg",\r\n   "file": "colorado-farm.jpg"\r\n}', 1),
(5, '{\r\n   "group": "Nature",\r\n   "name": "Curls On Green",\r\n   "thumbnail": "thumbnails/curls-on-green.jpg",\r\n   "file": "curls-on-green.jpg"\r\n}', 1),
(6, '{\r\n   "group": "Pattern",\r\n   "name": "Emotion",\r\n   "thumbnail": "thumbnails/emotion.jpg",\r\n   "file": "emotion.jpg"\r\n}', 1),
(7, '{\r\n   "group": "Pattern",\r\n   "name": "Eos",\r\n   "thumbnail": "thumbnails/eos.jpg",\r\n   "file": "eos.jpg"\r\n}', 1),
(8, '{\r\n   "group": "Nature",\r\n   "name": "Fields of Peace",\r\n   "thumbnail": "thumbnails/fields-of-peace.jpg",\r\n   "file": "fields-of-peace.jpg"\r\n}', 1),
(9, '{\r\n   "group": "Nature",\r\n   "name": "Fresh Morning",\r\n   "thumbnail": "thumbnails/fresh-morning.jpg",\r\n   "file": "fresh-morning.jpg"\r\n}', 1),
(10, '{\r\n   "group": "Nature",\r\n   "name": "Lady Buggin",\r\n   "thumbnail": "thumbnails/ladybuggin.jpg",\r\n   "file": "ladybuggin.jpg"\r\n}', 1),
(11, '{\r\n   "group": "qWikiOffice",\r\n   "name": "qWikiOffice",\r\n   "thumbnail": "thumbnails/qwikioffice.jpg",\r\n   "file": "qwikioffice.jpg"\r\n}', 1),
(12, '{\r\n   "group": "Nature",\r\n   "name": "Summer",\r\n   "thumbnail": "thumbnails/summer.jpg",\r\n   "file": "summer.jpg"\r\n}', 1);
