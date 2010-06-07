/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

QoDesk.QoPreferences.Background = Ext.extend(Ext.Panel, {
	constructor : function(config){
		// constructor pre-processing
		config = config || {};

		this.ownerModule = config.ownerModule;

		var desktop = this.ownerModule.app.getDesktop();

		this.grid = new QoDesk.QoPreferences.Grid({
			 border: true
			, cls: 'pref-card-subpanel pref-wallpaper-groups'
			, margins: '10 15 0 15'
			, mode: 'wallpapers'
			, ownerModule: this.ownerModule
			, region: 'center'
		});

		var wpp = this.ownerModule.app.styles.wallpaperposition;
		var tileRadio = createRadio('tile', wpp == 'tile' ? true : false, 90, 40);
		var centerRadio = createRadio('center', wpp == 'center' ? true : false, 200, 40);

		var position = Ext.extend( Ext.FormPanel, {
		    initComponent:function(){
		        var config = {
        			border: false
        			, height: 100
        			, items: [
        				{x: 15, y: 15, xtype: 'label', text: this.scope.ownerModule.locale.label.wallpaperPosition }
        				, {
        					border: false
        					, items: {border: false, html: '<img class="pref-bg-pos-tile" src="'+Ext.BLANK_IMAGE_URL+'" width="64" height="44" border="0" alt="" />'}
        					, x: 15
        					, y: 40
        					, width: 64
        					, height: 44
        				}
        				, tileRadio
        				, {
        					border: false
        					, items: {border: false, html: '<img class="pref-bg-pos-center" src="'+Ext.BLANK_IMAGE_URL+'" width="64" height="44" border="0" alt="" />'}
        					, x: 125
        					, y: 40
        					, width: 64
        					, height: 44
        				}
        				, centerRadio
        				, {x: 252, y: 15, xtype: 'label', text: this.scope.ownerModule.locale.label.backgroundColor }
        				, {
        					border: false
        					, items: new Ext.Button({
        						iconCls: 'pref-bg-color-icon'
                          , handler: onChangeBgColor
        						, scope: this.scope
        						, text: this.scope.ownerModule.locale.button.backgroundColor.text
        					})
        					, x: 253
        					, y: 40
        					, width : 120
        				}
        				, {x: 425, y: 15, xtype: 'label', text: this.scope.ownerModule.locale.label.fontColor }
        				, {
        					border: false
        					, items: new Ext.Button({
        						iconCls: 'pref-font-color-icon'
        						, handler: onChangeFontColor
        						, scope: this.scope
        						, text: this.scope.ownerModule.locale.button.fontColor.text
        					})
        					, x: 425
        					, y: 40
        					, width: 100
        				}
        			]
        			, layout: 'absolute'
        			, region: 'south'
        			, split: false
		        };
		        Ext.apply(this,Ext.apply(this.initialConfig,config));
                position.superclass.initComponent.apply(this,arguments);
		    },
		    onRender:function(){
		        position.superclass.onRender.apply(this,arguments);
		    }
		});

		Ext.reg('BackgroundSettingsUxBox',position);
		// this config
		Ext.applyIf(config, {
			border: false
			, buttons: [
				{
					disabled: this.ownerModule.app.isAllowedTo('saveBackground', this.ownerModule.id) ? false : true
					, handler: onSave
					, scope: this
					, text: this.ownerModule.locale.button.save.text
				}
				, {
					handler: onClose
					, scope: this
					, text: this.ownerModule.locale.button.close.text
				}
			]
			, cls: 'pref-card'
			, items: [
				this.grid
				, { xtype:'BackgroundSettingsUxBox',id:"desctopBgSettings",scope:this}
			]
			, layout: 'border'
			, title: this.ownerModule.locale.title.background
		});

		QoDesk.QoPreferences.Background.superclass.constructor.apply(this, [config]);
		// constructor post-processing

		function createRadio(value, checked, x, y){
			if(value){
				radio = new Ext.form.Radio({
					name: 'position'
					, inputValue: value
					, checked: checked
					, x: x
					, y: y
				});
				radio.on('check', togglePosition, radio);
				return radio;
			}
		}

	    function onChangeBgColor(){
	    	var hex = this.ownerModule.app.styles.backgroundcolor;
	    	var dialog = new Ext.ux.ColorDialog({
				border: false
				, closeAction: 'close'
				, iconCls: 'pref-bg-color-icon'
				, listeners: {
					'cancel': { fn: onColorCancel.createDelegate(this, [hex]), scope: this },
					'select': { fn: onColorSelect, scope: this, buffer: 350 }
				}
				, manager: this.ownerModule.app.getDesktop().getManager()
				, resizable: false
				, title: 'Pick A Background Color'
				, modal: true
				, plugins: new Ext.plugin.ModalNotice()
			});
			dialog.show(hex);
	    }

	    function onColorSelect(p, hex){
			desktop.setBackgroundColor(hex);
		}

		function onColorCancel(hex){
			desktop.setBackgroundColor(hex);
		}

		function onChangeFontColor(){
			var hex = this.ownerModule.app.styles.fontcolor;
	    	var dialog = new Ext.ux.ColorDialog({
				border: false
				, closeAction: 'close'
				, iconCls: 'pref-font-color-icon'
				, listeners: {
					'cancel': { fn: onFontColorCancel.createDelegate(this, [hex]), scope: this },
					'select': { fn: onFontColorSelect, scope: this, buffer: 350 }
				}
				, manager: this.ownerModule.app.getDesktop().getManager()
				, resizable: false
				, title: 'Pick A Font Color'
				, modal: true
				, plugins: new Ext.plugin.ModalNotice()
			});
			dialog.show(hex);
	    }

		function onFontColorSelect(p, hex){
			desktop.setFontColor(hex);
		}

		function onFontColorCancel(hex){
			desktop.setFontColor(hex);
		}

		function onClose(){
			this.ownerModule.win.close();
		}

		function onSave(){
			var c = this.ownerModule.app.styles;
			var data = {
            backgroundColor: c.backgroundcolor
            , fontColor: c.fontcolor
            , wallpaperId: c.wallpaper.id
            , wallpaperPosition: c.wallpaperposition
         };

			this.buttons[0].disable();
	    	this.ownerModule.save({
	    		method: 'saveBackground'
	    		, callback: function(){
	    			this.buttons[0].enable();
	    		}
	    		, callbackScope: this
            , data: Ext.encode(data)
	    	});
		}

		function togglePosition(field, checked){
			if(checked === true){
				desktop.setWallpaperPosition(field.inputValue);
			}
		}
	}

	// overrides

	, afterRender : function(){
		QoDesk.QoPreferences.Background.superclass.afterRender.call(this);

		this.on('show', this.loadGrid, this, {single: true});

	}

	// added methods

	, loadGrid : function(){
		this.grid.store.load();
	}
});