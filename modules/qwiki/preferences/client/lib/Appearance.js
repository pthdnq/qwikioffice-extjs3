/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

QoDesk.QoPreferences.Appearance = Ext.extend(Ext.Panel, {
	constructor : function(config){
		// constructor pre-processing
		config = config || {};
		
		this.ownerModule = config.ownerModule;
		
		var desktop = this.ownerModule.app.getDesktop();
		
		this.grid = new QoDesk.QoPreferences.Grid({
			 border: true
			, cls: 'pref-card-subpanel pref-theme-groups'
			, margins: '10 15 0 15'
			, mode: 'themes'
			, ownerModule: this.ownerModule
			, region: 'center'
		});
		
		this.slider = createSlider({
			handler: new Ext.util.DelayedTask(updateTransparency, this)
			, min: 0
			, max: 100
			, x: 15
			, y: 35
			, width: 100
		});
		
		var formPanel = new Ext.FormPanel({
			border: false
			, height: 70
			, items: [
				{x: 15, y: 15, xtype: 'label', text: this.ownerModule.locale.label.transparency }
				, this.slider.slider
				, this.slider.display
			]
			, layout: 'absolute'
			, split: false
			, region: 'south'
		});
	
		// this config
		Ext.applyIf(config, {
			border: false
			, buttons: [
				{
					disabled: this.ownerModule.app.isAllowedTo('saveAppearance', this.ownerModule.id) ? false : true
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
				, formPanel
			]
			, layout: 'border'
			, title: this.ownerModule.locale.title.appearance
		});
		
		QoDesk.QoPreferences.Appearance.superclass.constructor.apply(this, [config]);
		// constructor post-processing
		
		function createSlider(config){
			var handler = config.handler, min = config.min, max = config.max
				, width = config.width || 100, x = config.x, y = config.y;
	
			var slider = new Ext.Slider({
				minValue: min
				, maxValue: max
				, width: width
				, x: x
				, y: y
			});
			
			var display =  new Ext.form.NumberField({
				cls: 'pref-percent-field'
				, enableKeyEvents: true
				, maxValue: max
				, minValue: min
				, width: 45
				, x: x + width + 15
				, y: y - 1
			});
				
			function sliderHandler(slider){
				var v = slider.getValue();
				display.setValue(v);
				handler.delay(100, null, null, [v]); // delayed task prevents IE bog
			}
			
			slider.on({
				'change': { fn: sliderHandler, scope: this }
				, 'drag': { fn: sliderHandler, scope: this }
			});
			
			display.on({
				'keyup': {
					fn: function(field){
						var v = field.getValue();
						if(v !== '' && !isNaN(v) && v >= field.minValue && v <= field.maxValue){
							slider.setValue(v);
						}
					}
					, buffer: 350
					, scope: this
				}
			});
	
			return { slider: slider, display: display }
		}
		
		function onClose(){
			this.ownerModule.win.close();
		}
		
		function onSave(){
			var c = this.ownerModule.app.styles;
			var data = {
            themeId: c.theme.id
            , transparency: c.transparency
         };

			this.buttons[0].disable();
	    	this.ownerModule.save({
	    		method: 'saveAppearance'
	    		, callback: function(){
	    			this.buttons[0].enable();
	    		}
	    		, callbackScope: this
            , data: Ext.encode(data)
	    	});
		}
		
		function onSelectionChange(view, sel){
			if(sel.length > 0){
				var cId = this.ownerModule.app.styles.theme.id,
					r = view.getRecord(sel[0]),
					d = r.data;
				
				if(parseInt(cId) !== parseInt(r.id)){
					if(r && r.id && d.name && d.pathtofile){
						desktop.setTheme({
							id: r.id,
							name: d.name,
							pathtofile: d.pathtofile
						});
					}
				}
			}
		}
		
		function updateTransparency(v){
			desktop.setTransparency(v);
		}
	}
	
	// overrides
	
	, afterRender : function(){
		QoDesk.QoPreferences.Appearance.superclass.afterRender.call(this);
		
		this.on('show', this.initAppearance, this, {single: true});
	}
	
	// added methods
	
	, initAppearance : function(){
		this.grid.store.load();
		this.slider.slider.setValue(this.ownerModule.app.styles.transparency);
	}
});