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
 */

Ext.Desktop = Ext.extend(Ext.util.Observable, {
	/**
	 * Read only. {Ext.app.App}
	 */
	app: null,
	/**
	 * Read only. {Ext.menu.Menu}
	 */
	cmenu : new Ext.menu.Menu(),
	/**
	 * Read only. {Ext.ux.Shortcuts}
	 */
	shortcuts : null,
	/**
	 * Read only. {Ext.WindowGroup}
	 */
	windows : new Ext.WindowGroup(),
	/**
	 * Read only. {Ext.Window}
	 */
	activeWindow : null,

	/**
	 * @param {Ext.app.App} app The instance of the application.
	 */
	constructor : function(app){
		this.addEvents({
			winactivate: true,
			winbeforeclose: true,
			windeactivate: true
		});

		this.app = app;
		this.el = Ext.getBody().createChild({ tag: 'div', cls: 'x-desktop' });
		this.taskbar = new Ext.ux.TaskBar(app);
		this.shortcuts = new Ext.ux.Shortcuts(this);

		// todo: fix bug where Ext.Msg are not displayed properly
		// this.windows.zseed = 7000; //10000;

		Ext.Desktop.superclass.constructor.call(this);

		this.initEvents();
		this.layout();
	},

	initEvents : function(){
		Ext.EventManager.onWindowResize(this.layout, this);

		this.el.on('contextmenu', function(e){
			if(e.target.id === this.el.id){
				e.stopEvent();
				if(!this.cmenu.el){
					this.cmenu.render();
				}
				var xy = e.getXY();
				xy[1] -= this.cmenu.el.getHeight();
				this.cmenu.showAt(xy);
			}
		}, this);
	},

	/**
	 * @param {object} config The window config object.
	 * @param {string} cls The class to use instead of Ext.Window.
	 */
	createWindow : function(config, cls){
		var win = new (cls||Ext.Window)(
			Ext.applyIf(config||{}, {
				manager: this.windows,
				minimizable: true,
				maximizable: true
			})
		);

		win.render(this.el);
		win.taskButton = this.taskbar.taskButtonPanel.add(win);
		win.cmenu = new Ext.menu.Menu({
			items: [

			]
		});
		win.animateTarget = win.taskButton.el;

		win.on({
			'activate': {
				fn: function(win){
					this.markActive(win);
					this.fireEvent('winactivate', this, win);
				}
				, scope: this
			},
			'beforeclose': {
				fn: function(win){
					this.fireEvent('winbeforeclose', this, win);
				},
				scope: this
			},
			'beforeshow': {
				fn: this.markActive
				, scope: this
			},
			'deactivate': {
				fn: function(win){
					this.markInactive(win);
					this.fireEvent('windeactivate', this, win);
				}
				, scope: this
			},
			'minimize': {
				fn: this.minimizeWin
				, scope: this
			},
			'close': {
				fn: this.removeWin
				, scope: this
			}
		});

		this.layout();
		return win;
	},

	/**
	 * @param {Ext.Window} win The window to minimize.
	 */
	minimizeWin : function(win){
		win.minimized = true;
		win.hide();
	},

	/**
	 * @param {Ext.Window} win The window to mark active.
	 */
	markActive : function(win){
		if(this.activeWindow && this.activeWindow != win){
			this.markInactive(this.activeWindow);
		}
		this.taskbar.setActiveButton(win.taskButton);
		this.activeWindow = win;
		Ext.fly(win.taskButton.el).addClass('active-win');
		win.minimized = false;
	},

	/**
	 * @param {Ext.Window} win The window to mark inactive.
	 */
	markInactive : function(win){
		if(win == this.activeWindow){
			this.activeWindow = null;
			Ext.fly(win.taskButton.el).removeClass('active-win');
		}
	},

	/**
	 * @param {Ext.Window} win The window to remove.
	 */
	removeWin : function(win){
		this.taskbar.taskButtonPanel.remove(win.taskButton);
		this.layout();
	},

	layout : function(){
		this.el.setHeight(Ext.lib.Dom.getViewHeight() - this.taskbar.el.getHeight());
	},

	getManager : function(){
		return this.windows;
	},

	/**
	 * @param {string} id The window id.
	 */
	getWindow : function(id){
		return this.windows.get(id);
	},

	getViewHeight : function(){
		return (Ext.lib.Dom.getViewHeight() - this.taskbar.el.getHeight());
	},

	getViewWidth : function(){
		return Ext.lib.Dom.getViewWidth();
	},

	getWinWidth : function(){
		var width = this.getViewWidth();
		return width < 200 ? 200 : width;
	},

	getWinHeight : function(){
		var height = this.getViewHeight();
		return height < 100 ? 100 : height;
	},

	/**
	 * @param {integer} width The width.
	 */
	getWinX : function(width){
		return (Ext.lib.Dom.getViewWidth() - width) / 2
	},

	/**
	 * @param {integer} height The height.
	 */
	getWinY : function(height){
		return (Ext.lib.Dom.getViewHeight() - this.taskbar.el.getHeight() - height) / 2;
	},

	/**
	 * @param {string} hex The hexidecimal number for the color.
	 */
	setBackgroundColor : function(hex){
		if(hex){
			Ext.get(document.body).setStyle('background-color', '#'+hex);
			this.app.styles.backgroundcolor = hex;
		}
	},

	/**
	 * @param {string} hex The hexidecimal number for the color.
	 */
	setFontColor : function(hex){
		if(hex){
			Ext.util.CSS.updateRule('.ux-shortcut-btn-text', 'color', '#'+hex);
			this.app.styles.fontcolor = hex;
		}
	},

	/**
	 * @param {object} o The data for the theme.
	 * Example:
	 * {
	 *		id: 1,
	 *		name: 'Vista Black',
	 *		pathtofile: 'path/to/file'
	 * }
	 */
	setTheme : function(o){
		if(o && o.id && o.name && o.pathtofile){
			Ext.util.CSS.swapStyleSheet('theme', o.pathtofile);
			this.app.styles.theme = o;
		}
	},

	/**
	 * @param {integer} v The value.	 An integer from 0 to 100.
	 */
	setTransparency : function(v){
		if(v >= 0 && v <= 100){
			this.taskbar.el.addClass("transparent");
			Ext.util.CSS.updateRule('.transparent','opacity', v/100);
			Ext.util.CSS.updateRule('.transparent','-moz-opacity', v/100);
			Ext.util.CSS.updateRule('.transparent','filter', 'alpha(opacity='+v+')');

			this.app.styles.transparency = v;
		}
	},

	/**
	 * @param {object} o The data for the wallpaper.
	 * Example:
	 * {
	 *		id: 1,
	 *		name: 'Blank',
	 *		pathtofile: 'path/to/file'
	 * }
	 */
	setWallpaper : function(o){
		if(o && o.id && o.name && o.pathtofile){

			var notifyWin = this.showNotification({
				html: 'Loading wallpaper...',
				title: 'Please wait'
			});

			var wp = new Image();
			wp.src = o.pathtofile;

			var task = new Ext.util.DelayedTask(verify, this);
			task.delay(200);

			this.app.styles.wallpaper = o;
		}

		function verify(){
			if(wp.complete){
				task.cancel();

				notifyWin.setIconClass('x-icon-done');
				notifyWin.setTitle('Finished');
				notifyWin.setMessage('Wallpaper loaded.');
				this.hideNotification(notifyWin);

				document.body.background = wp.src;
			}else{
				task.delay(200);
			}
		}
	},

	/**
	 * @param {string} pos Options are 'tile' or 'center'.
	 */
	setWallpaperPosition : function(pos){
		if(pos){
			if(pos === "center"){
				var b = Ext.get(document.body);
				b.removeClass('wallpaper-tile');
				b.addClass('wallpaper-center');
			}else if(pos === "tile"){
				var b = Ext.get(document.body);
				b.removeClass('wallpaper-center');
				b.addClass('wallpaper-tile');
			}
			this.app.styles.wallpaperposition = pos;
		}
	},

	/**
	 * @param {object} config The config object.
	 */
	showNotification : function(config){
		var win = new Ext.ux.Notification(Ext.apply({
			animateTarget: this.taskbar.el,
			autoDestroy: true,
			hideDelay: 5000,
			html: '',
			iconCls: 'x-icon-waiting',
			title: ''
		}, config));
		win.show();

		return win;
	},

	/**
	 * @param {Ext.ux.Notification} win The notification window.
	 * @param {integer} delay The delay time in milliseconds.
	 */
	hideNotification : function(win, delay){
		if (win) {
			(function() {
				win.animHide();
				win.manager.unregister(win); // +X+ ADDED (Fix suggested @ http://qwikioffice.com/forum/viewtopic.php?f=2&t=416&p=2342&hilit=ext3#p2342)
			}).defer(delay || 3000);
		}
	},

	/**
	 * @param {string} id The id of the module to add.
	 */
	addAutoRun : function(id){
		var m = this.app.getModule(id);
		var c = this.app.launchers.autorun;
			
		if(c && m && !m.autorun){
			m.autorun = true;
			c.push(id);
		}
	},

	/**
	 * @param {string} id The id of the module to remove.
	 */
	removeAutoRun : function(id){
		var m = this.app.getModule(id);
		var c = this.app.launchers.autorun;

		if(c && m && m.autorun){
			var i = 0;

			while(i < c.length){
				if(c[i] == id){
					c.splice(i, 1);
				}else{
					i++;
				}
			}

			m.autorun = null;
		}
	},

	/**
	 * @param {string} id The id of the module to add.
	 */
	addContextMenuItem : function(id){
		var m = this.app.getModule(id);

		if(m && !m.contextMenuItem && m.launcher){
			var c = m.launcher;

			this.cmenu.add({
				handler: this.app.createWindow.createDelegate(this.app, [id]),
				iconCls: c.iconCls,
				text: c.text,
				tooltip: c.tooltip || ''
			});
		}
	},

	/**
	 * @param {string} id The module id
	 * @param {boolean} updateConfig 
	 */
	addShortcut : function(id, updateConfig){
		var m = this.app.getModule(id);

		if(m && !m.shortcut){
			var c = m.launcher;

			m.shortcut = this.shortcuts.addShortcut({
				handler: this.app.createWindow.createDelegate(this.app, [id]),
				iconCls: c.shortcutIconCls,
				text: c.text,
				tooltip: c.tooltip || ''
			});

			if(updateConfig){
				this.app.launchers.shortcut.push(id);
			}
		}
	},

	/**
	 * @param {string} id The module id
	 * @param {boolean} updateConfig
	 */
	removeShortcut : function(id, updateConfig){
		var m = this.app.getModule(id);

		if(m && m.shortcut){
			this.shortcuts.removeShortcut(m.shortcut);
			m.shortcut = null;

			if(updateConfig){
				var sc = this.app.launchers.shortcut;
				var i = 0;
				while(i < sc.length){
					if(sc[i] == id){
						sc.splice(i, 1);
					}else{
						i++;
					}
				}
			}
		}
	},

	/**
	 * @param {string} id The module id
	 * @param {boolean} updateConfig
	 */
	addQuickStartButton : function(id, updateConfig){
		var m = this.app.getModule(id);

		if(m && !m.quickStartButton){
			var c = m.launcher;

			m.quickStartButton = this.taskbar.quickStartPanel.add({
				handler: this.app.createWindow.createDelegate(this.app, [id]),
				iconCls: c.iconCls,
				scope: c.scope,
				text: c.text,
				tooltip: c.tooltip || c.text
			});

			if(updateConfig){
				this.app.launchers.quickstart.push(id);
			}
		}
	},

	/**
	 * @param {string} id The module id
	 * @param {boolean} updateConfig
	 */
	removeQuickStartButton : function(id, updateConfig){
		var m = this.app.getModule(id);

		if(m && m.quickStartButton){
			this.taskbar.quickStartPanel.remove(m.quickStartButton);
			m.quickStartButton = null;

			if(updateConfig){
				var qs = this.app.launchers.quickstart;
				var i = 0;
				while(i < qs.length){
					if(qs[i] == id){
						qs.splice(i, 1);
					}else{
						i++;
					}
				}
			}
		}
	}
});