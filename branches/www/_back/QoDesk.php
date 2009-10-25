<?php
require_once("system/os/os.php");
if(class_exists('os')){
	$os = new os();
?>

/*
 * qWikiOffice Desktop 0.8.1
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 *
 * NOTE:
 * This code is based on code from the original Ext JS desktop demo.
 * I have made many modifications/additions.
 *
 * The Ext JS licensing can be viewed here:
 *
 * Ext JS Library 2.0 Beta 2
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 *
 */

QoDesk = new Ext.app.App({
	
	init : function(){
		Ext.QuickTips.init();
	},
	
	/**
	 * Returns the modules array.
	 * Supports Modules on Demand. If a module is not preloaded, a placeholder needs to be present.
	 * A placeholder is an object that represents the module until it is loaded.
	 *
	 * Example:
	 * 
	 * [
	 *      // This module will Load on Demand.
	 *      // This is its placeholder.
	 *		{
	 *			className: 'QoDesk.QoPreferences',
	 *			moduleType: 'system/preferences',
	 *			moduleId: 'qo-preferences',
	 * 			launcher: {
	 *				iconCls: 'pref-icon',
	 *				shortcutIconCls: 'pref-shortcut-icon',
	 *				text: 'Preferences',
	 *				tooltip: '<b>Preferences</b><br />Allows you to modify your desktop'
	 *			},
	 *			loaded: false
	 *		},
	 *      // This module is preloaded.
	 *      // Instantiate it here.
	 * 		new QoDesk.GridWindow(),
	 *		...
	 * ]
	 * 
	 */
    getModules : function(){
    	return [ <?php print $os->get_modules(); ?> ];
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
	 *		contextmenu: [
	 *			'qo-preferences'
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
    	return <?php print $os->get_launchers(); ?>;
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
    	return <?php print $os->get_styles(); ?>;
    },
	
	/**
	 * Returns the Start Menu's logout button configuration
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
	 * Returns the Start Menu configuration
	 */
    getStartConfig : function(){
    	return {
        	// iconCls: 'user',
            // title: get_cookie('memberName'),
			toolPanelWidth: 115
        };
    }
});
<?php } ?>