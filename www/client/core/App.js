/*
 * qWikiOffice Desktop 1.0
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
 * Ext JS Library 3.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 *
 * http://extjs.com/license
 *
 */

Ext.app.App = function(config){
	Ext.apply(this, config);

	this.addEvents({
		'ready': true,
		'beforeunload': true,
		'moduleactioncomplete': true
	});

	Ext.onReady(this.initApp, this);
};

Ext.extend(Ext.app.App, Ext.util.Observable, {
	/**
	* Read-only. This app's ready state
	* @type boolean
	*/
	isReady : false,
    startMenu : null,
	/**
	 * Read-only. This app's launchers
	 * @type object
	 */
	launchers : null,
	/**
	 * Read-only. This app's modules
	 * @type array
	 */
	modules : null,
	 /**
	 * Read-only. This app's styles
	 * @type object
	 */
	styles : null,
	/**
	 * Read-only. This app's Start Menu config
	 * @type object
	 */
	startConfig : null,
	/**
	 * Read-only. This app's Start Menu's items and toolItems configs.
	 * @type object
	 */
	startItemsConfig : null,
	/**
	 * Read-only. This app's logout button config
	 * @type object
	 */
	logoutButtonConfig : null,
	/**
	* Read-only. The url of this app's server connection
	*
	* Allows a module to connect to its server script without knowing the path.
	* Example ajax call:
	*
	* Ext.Ajax.request({
	*		url: this.app.connection,
	*		// Could also pass the module id in the querystring like this.
	*		// url: this.app.connection+'?id='+this.id,
	*		params: {
	*			id: this.id
	*			...
	*		},
	*		success: function(){
	*			...
	*		},
	*		failure: function(){
	*			...
	*		},
	*		scope: this
	* });
	*/
	connection : 'services.php',
	/**
	 * Read-only. The queue of requests to run once a module is loaded
	 */
	requestQueue : [],

	init : Ext.emptyFn,
	getMemberInfo : Ext.emptyFn,
	startMenuSortFn : Ext.emptyFn,
	getModules : Ext.emptyFn,
	getLaunchers : Ext.emptyFn,
	getPrivileges : Ext.emptyFn,
	getStyles : Ext.emtpyFn,
	getStartConfig : Ext.emptyFn,
	getLogoutConfig : Ext.emptyFn,

	initApp : function(){
		this.init();

		this.preventBackspace();

		this.memberInfo = this.memberInfo || this.getMemberInfo();
		this.privileges = this.privileges || this.getPrivileges();
		this.modules = this.modules || this.getModules();
		this.initModules();

		this.startConfig = this.startConfig || this.getStartConfig();
		this.startItemsConfig = this.startItemsConfig || this.getStartItemsConfig();
		Ext.apply(this.startConfig, this.startItemsConfig);

		this.desktop = new Ext.Desktop(this);
        
		this.styles = this.styles || this.getStyles();
		this.initStyles();

		this.launchers = this.launchers || this.getLaunchers();
        this.desktop.taskbar.startMenu.doLayout();
		this.initLaunchers();
		this.initContextMenu();

		this.logoutConfig = this.logoutConfig || this.getLogoutConfig();
		this.initLogout();

		Ext.EventManager.on(window, 'beforeunload', this.onBeforeUnload, this);
		this.fireEvent('ready', this);
		this.isReady = true;
	},

	initLogout : function(){
		if(this.logoutConfig){
			this.desktop.taskbar.startMenu.addTool(this.logoutConfig);
		}
	},

	initStyles : function(){
		var s = this.styles;
		if(!s){
			return false;
		}

		var d = this.desktop;

		d.setBackgroundColor(s.backgroundcolor);
		d.setFontColor(s.fontcolor);
		d.setTheme(s.theme);
		d.setTransparency(s.transparency);
		d.setWallpaper(s.wallpaper);
		d.setWallpaperPosition(s.wallpaperposition);

		return true;
	},

	initModules : function(){
		var ms = this.modules;
		if(!ms){
			return false;
		}

		for(var i = 0, len = ms.length; i < len; i++){
			if(ms[i].launcher){
				ms[i].launcher.handler = this.createWindow.createDelegate(this, [ms[i].id]);
			}
			ms[i].loaded = false;
		}

		return true;
	},

	initLaunchers : function(){
		var l = this.launchers;
		if(!l){
			this.launchers = { quickstart: [], shortcut: [], autorun: [], systemtray: [] };
			return false;
		}

		if(l.quickstart){
			this.initQuickStart(l.quickstart);
		}else{
			l.quickstart = [];
		}

		if(l.shortcut){
			this.initShortcut(l.shortcut);
		}else{
			l.shortcut = [];
		}

		if(l.autorun){
			this.onReady(this.initAutoRun.createDelegate(this, [l.autorun]), this);
		}else{
			l.autorun = [];
		}

		return true;
	},

	/**
	* @param {array} mIds An array of the module ids to run when this app is ready
	*/
	initAutoRun : function(mIds){
		if(mIds){
			for(var i = 0, len = mIds.length; i < len; i++){
				var m = this.getModule(mIds[i]);
				if(m){
					m.autorun = true;
					this.createWindow(mIds[i]);
				}
			}
		}
	},

	initContextMenu : function(){
		var ms = this.modules;
		if(ms){
			for(var i = 0, len = ms.length; i < len; i++){
				if(ms[i].launcherPaths && ms[i].launcherPaths.contextmenu){
					this.desktop.addContextMenuItem(ms[i].id);
				}
			}
		}
	},

	/**
	* @param {array} mIds An array of the module ids to add to the Desktop Shortcuts
	*/
	initShortcut : function(mIds){
		if(mIds){
			for(var i = 0, len = mIds.length; i < len; i++){
				this.desktop.addShortcut(mIds[i], false);
			}
		}
	},

	/**
	* @param {array} mIds An array of the modulId's to add to the Quick Start panel
	*/
	initQuickStart : function(mIds){
		if(mIds){
			for(var i = 0, len = mIds.length; i < len; i++){
				this.desktop.addQuickStartButton(mIds[i], false);
			}
		}
	},

	/**
	* Returns the Start Menu items and toolItems configs
	*/
	getStartItemsConfig : function(){
		var ms = this.modules;
		var sortFn = this.startMenuSortFn;
        
		if(ms){
			var launcherPaths;
			var paths;
			var sm = { menu: { items: [] } }; // Start Menu
			var smi = sm.menu.items;

			smi.push({text: 'startmenu', menu: { items: [] } });
			smi.push({text: 'startmenutool', menu: { items: [] } });

			for(var i = 0, iLen = ms.length; i < iLen; i++){ // loop through the modules
				if(ms[i].launcherPaths){
					launcherPaths = ms[i].launcherPaths;

					for(var id in launcherPaths){ // loop through the module's launcher paths
						paths = launcherPaths[id].split('/');

						if(paths.length > 0){
							if(id === 'startmenu'){
								simplify(smi[0].menu, paths, ms[i].launcher);
								sort(smi[0].menu);
							}else if(id === 'startmenutool'){
								simplify(smi[1].menu, paths, ms[i].launcher);
								sort(smi[1].menu);
							}
						}
					}
				}
			}

			return {
				items: smi[0].menu.items,
				toolItems: smi[1].menu.items
			};
		}

		return null;

		/**
		* Creates nested arrays that represent the Start Menu.
		*
		* @param {array} pMenu The Start Menu
		* @param {array} paths The menu texts
		* @param {object} launcher The launcher config
		*/
		function simplify(pMenu, paths, launcher){
			var newMenu;
			var foundMenu;

			for(var i = 0, len = paths.length; i < len; i++){
				if(paths[i] === ''){
					continue;
				}

				foundMenu = findMenu(pMenu.items, paths[i]); // text exists?

				if(!foundMenu){
					newMenu = {
						iconCls: 'ux-start-menu-submenu',
						handler: function(){ return false; },
						menu: { items: [] },
						text: paths[i]
					};
					pMenu.items.push(newMenu);
					pMenu = newMenu.menu;
				}else{
					pMenu = foundMenu;
				}
			}

			pMenu.items.push(launcher);
		}

		/**
		* Returns the menu if found.
		*
		* @param {array} pMenu The parent menu to search
		* @param {string} text
		*/
		function findMenu(pMenu, text){
			for(var j = 0, jlen = pMenu.length; j < jlen; j++){
				if(pMenu[j].text === text){
					return pMenu[j].menu; // found the menu, return it
				}
			}
			return null;
		}

		/**
		* @param {array} menu The nested array to sort
		*/
		function sort(menu){
			var items = menu.items;
			for(var i = 0, ilen = items.length; i < ilen; i++){
				if(items[i].menu){
					sort(items[i].menu); // use recursion to iterate nested arrays
				}
				bubbleSort(items, 0, items.length); // sort the menu items
			}
		}

		/**
		* @param {array} items Menu items to sort
		* @param {integer} start The start index
		* @param {integer} stop The stop index
		*/
		function bubbleSort(items, start, stop){
			for(var i = stop - 1; i >= start;  i--){
				for(var j = start; j <= i; j++){
					if(items[j+1] && items[j]){
						if(sortFn(items[j], items[j+1])){
							var tempValue = items[j];
							items[j] = items[j+1];
							items[j+1] = tempValue;
						}

					}
				}
			}
			return items;
		}
	},

	/**
	* @param {string} id
	*
	* Provides the handler to the module launcher.
	* Requests the module, which will load the module if needed.
	* Passes in the callback and scope as params.
	*/
	createWindow : function(id){
		var m = this.requestModule(id, function(m){
			if(m){ m.createWindow(); }
		}, this);
	},

	/**
	* @param {string} v The id or moduleType you want returned
	* @param {Function} cb The Function to call when the module is ready/loaded
	* @param {object} scope The scope in which to execute the function
	*/
	requestModule : function(v, cb, scope){
		var m = this.getModule(v);
		if(m){
			if(m.loaded === true){
				cb.call(scope, m);
			}else{
				if(cb && scope){
					this.requestQueue.push({ id: m.id, callback: cb, scope: scope });
					this.loadModule(m);
				}
			}
		}
	},

	/**
	* @param {Ext.app.Module} m The module
	*/
	loadModule : function(m){
		if(m.isLoading){ return false; }

		var id = m.id;
		var moduleName = m.launcher.text;
		var notifyWin = this.desktop.showNotification({
			html: 'Loading ' + moduleName + '...'
			, title: 'Please wait'
		});

		m.isLoading = true;

		Ext.Ajax.request({
			url: this.connection,
			params: {
				service: 'load',
				moduleId: id
			},
			success: function(o){
				notifyWin.setIconClass('x-icon-done');
				notifyWin.setTitle('Finished');
				notifyWin.setMessage(moduleName + ' loaded.');
				this.desktop.hideNotification(notifyWin);
				notifyWin = null;

				if(o.responseText !== ''){
					eval(o.responseText);
					this.loadModuleComplete(true, id);
				}else{
					alert('An error occured on the server.');
				}
			},
			failure: function(){
				alert('Connection to the server failed!');
			},
			scope: this
		});

		return true;
	},

	/**
	* @param {boolean} success
	* @param {string} id
	*
	* Will be called when a module is loaded.
	* If a request for this module is waiting in the
	* queue, it as executed and removed from the queue.
	*/
	loadModuleComplete : function(success, id){
		if(success === true && id){
			var m = this.createModule(id);

			if(m){
				m.isLoading = false;
				m.loaded = true;
				m.init();
				m.on('actioncomplete', this.onModuleActionComplete, this);

				var q = this.requestQueue;
				var nq = [];
				var found = false;

				for(var i = 0, len = q.length; i < len; i++){
					if(found === false && q[i].id === id){
						found = q[i];
					}else{
						nq.push(q[i]);
					}
				}

				this.requestQueue = nq;

				if(found){
					found.callback.call(found.scope, m);
				}
			}
		}
	},

	/**
	* Private
	* @param {string} id
	*/
	createModule : function(id){
		var p = this.getModule(id); // get the placeholder

		if(p && p.loaded === false){
			if( eval('typeof ' + p.className) === 'function'){
				var m = eval('new ' + p.className + '()');
				m.app = this;

				var ms = this.modules;
				for(var i = 0, len = ms.length; i < len; i++){ // replace the placeholder with the module
					if(ms[i].id === m.id){
						Ext.apply(m, ms[i]); // transfer launcher properties
						ms[i] = m;
					}
				}

				return m;
			}
		}
		return null;
	},

	/**
	* @param {string} v The id or moduleType you want returned
	*/
	getModule : function(v){
		var ms = this.modules;

		for(var i = 0, len = ms.length; i < len; i++){
			if(ms[i].id == v || ms[i].moduleType == v){
				return ms[i];
			}
		}

		return null;
	},

	/**
	* @param {Ext.app.Module} m The module to register
	*/
	registerModule: function(m){
		if(!m){ return false; }
		this.modules.push(m);
		m.launcher.handler = this.createWindow.createDelegate(this, [m.id]);
		m.app = this;
		return true;
	},

	/**
	* @param {string} id or moduleType
	* @param {array} requests An array of request objects
	*
	* Example:
	* this.app.makeRequest('module-id', {
	*		requests: [
	*			{
	*				method: 'createWindow',
	*				params: '',
	*				callback: this.myCallbackFunction,
	*				scope: this
	*			},
	*			{ ... }
	*		]
	* });
	*/
	makeRequest : function(id, requests){
		if(id !== '' && requests){
			var m = this.requestModule(id, function(m){
				if(m){
					m.handleRequest(requests);
				}
			}, this);
		}
	},

	/**
	* @param {string} action The module action
	* @param {string} id The id property
	*/
	isAllowedTo : function(action, id){
		if(action !== '' && id != ''){
			var p = this.privileges;
			if(p[id] && p[id][action]){
				if(p[id][action] === 1){
					return true;
				}
			}
		}
		return false;
	},

	getDesktop : function(){
		return this.desktop;
	},

	/**
	* @param {Function} fn The function to call after the app is ready
	* @param {object} scope The scope in which to execute the function
	*/
	onReady : function(fn, scope){
		if(!this.isReady){
				this.on('ready', fn, scope);
		}else{
				fn.call(scope, this);
		}
	},

	onBeforeUnload : function(e){
		if(this.fireEvent('beforeunload', this) === false){
				e.stopEvent();
		}
	},

	/**
	* Prevent the backspace (history -1) shortcut
	*/
	preventBackspace : function(){
		var map = new Ext.KeyMap(document, [{
			key: Ext.EventObject.BACKSPACE,
			stopEvent: false,
			fn: function(key, e){
				var t = e.target.tagName;
				if(t != "INPUT" && t != "TEXTAREA"){
					e.stopEvent();
				}
			}
		}]);
	},

	/**
	* @param {Ext.app.Module} module
	* @param {object} data
	* @param {object} options
	*
	* It may be benificial for a system module to register all (or some) module
	* activity in the database.
	*
	* Example usage:
	*
	* this.app.on('moduleactioncomplete', this.onModuleActionComplete, this);
	*
	* onModuleActionComplete : function(app, module, params, options){
	*		if(module && params){
	*
	*			if(typeof options === 'object'){
	*				var keepExisting = options.keepExisting || false;
	*			}
	*
	*			Ext.Ajax.request({
	*				url: this.app.connection
	*				, params: {
	*					action: 'registerModuleAction'
	*					, id: this.id
	*					, actionModuleId: module.id
	*					, actionData: typeof params.data == 'object' ? Ext.encode(params.data) : params.data
	*					, actionDesc: params.description
	*					, keepExisting: keepExisting || false
	*				}
	*				, failure: function(response,options){
	*					// failed
	*				}
	*				, success: function(o){
	*					// success
	*				}
	*				, scope: this
	*			});
	*		}
	* }
	*/
	onModuleActionComplete : function(module, data, options){
		this.fireEvent('moduleactioncomplete', this, module, data, options);
	}
});

/* *****************************************************
 * Purpose of below (override) is to (only) provide greater control over styling of form elements/fields
*/
Ext.layout.ContainerLayout.prototype.fieldTpl = (function() {
	var t = new Ext.Template(
		'<div class="x-form-item {itemCls}" style="{itemStyle}" tabIndex="-1">',
			'<label for="{id}" style="{labelStyle}" class="x-form-item-label {labelCls}">{label}{labelSeparator}</label>',
			'<div class="x-form-element" id="x-form-el-{id}" style="{elementStyle}">',
			'</div><div class="{clearCls}"></div>',
		'</div>'
	);
	t.disableFormats = true;
	return t.compile();
})();
Ext.layout.FormLayout.prototype.getTemplateArgs = function(field) {
	var noLabelSep = !field.fieldLabel || field.hideLabel;
	return {
		id: field.id,
		label: field.fieldLabel,
		labelCls: field.labelCls||this.labelCls||'',
		labelStyle: field.labelStyle||this.labelStyle||'',
		elementStyle: field.elementStyle||this.elementStyle||'',
		labelSeparator: noLabelSep ? '' : (typeof field.labelSeparator == 'undefined' ? this.labelSeparator : field.labelSeparator),
		itemStyle: (field.itemStyle||this.container.itemStyle||''),
		itemCls: (field.itemCls||this.container.itemCls||'') + (field.hideLabel ? ' x-hide-label' : ''),
		clearCls: field.clearCls || 'x-form-clear-left'
	};
}

/* *****************************************************
 * Bug fix for Ext.Resizable
 * Fixes problem where el 'auto' size styles replaced.
*/
Ext.Resizable.prototype.resizeElement = function(){
	var box = this.proxy.getBox();

//  START FIX-PATCH
	if(!this.east && !this.west){
		box.width = this.el.getStyle('width');
	}
	if(!this.north && !this.south){
		box.height = this.el.getStyle('height');
	}
//  END FIX-PATCH

	if(this.updateBox){
		this.el.setBox(box, false, this.animate, this.duration, null, this.easing);
	}else{
		this.el.setSize(box.width, box.height, this.animate, this.duration, null, this.easing);
	}
	this.updateChildSize();
	if(!this.dynamic){
		this.proxy.hide();
	}
	return box;
};

// For fix @ https://extjs.net/forum/showthread.php?p=395236
Ext.override(Ext.Slider, {
	setValue : function(v, animate, changeComplete){
		v = this.normalizeValue(v);
		if(v !== this.value && this.fireEvent('beforechange', this, v, this.value) !== false){
			this.value = v;
			if(this.thumb){
				this.moveThumb(this.translateValue(v), animate !== false);
			}
			this.fireEvent('change', this, v);
			if(changeComplete){
				this.fireEvent('changecomplete', this, v);
			}
		}
	}
});
