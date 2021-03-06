/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

QoDesk.QoPreferences.QuickStart = Ext.extend(Ext.Panel, {
	constructor : function(config){
		// constructor pre-processing
		config = config || {};
		
		this.ownerModule = config.ownerModule;
		
		// this config
		Ext.applyIf(config, {
			border: false
			, buttons: [
				{
					disabled: this.ownerModule.app.isAllowedTo('saveQuickstart', this.ownerModule.id) ? false : true
					, handler: onSave
					, scope: this
					, text: 'Save'
				}
				, {
					handler: onClose
					, scope: this
					, text: 'Close'
				}
			]
			, cls: 'pref-card'
			, items: [
				{
					bodyStyle: 'padding:10px 10px 10px 0'
					, border: false
					, html: this.ownerModule.locale.html.quickstart
					, region: 'center'
					, xtype: 'panel'
				}
				, new QoDesk.QoPreferences.QuickStart.Tree({
					ownerModule: config.ownerModule
					, region: 'west'
				})
			]
			, layout: 'border'
			, title: this.ownerModule.locale.title.quickstart
		});
		
		QoDesk.QoPreferences.QuickStart.superclass.constructor.apply(this, [config]);
		// constructor post-processing
		
		function onClose(){
			this.ownerModule.win.close();
		}
		
	    function onSave(){
	    	this.buttons[0].disable();
	    	this.ownerModule.save({
	    		method: 'saveQuickstart'
	    		, callback: function(){
	    			this.buttons[0].enable();
	    		}
	    		, callbackScope: this
	    		, ids: Ext.encode(this.ownerModule.app.launchers.quickstart)
	    	});
	    }
	}
});

QoDesk.QoPreferences.QuickStart.Tree = Ext.extend(Ext.tree.TreePanel, {
	constructor : function(config){
		// constructor pre-processing
		config = config || {};
		
		this.ownerModule = config.ownerModule;
	
		var ms = this.ownerModule.app.modules;
		var ids = this.ownerModule.app.launchers.quickstart;
		var nodes = expandNodes(ms, ids);
		
		// this config
		Ext.applyIf(config, {
			autoScroll: true
			, bodyStyle: 'padding:10px'
			, border: true
			, cls: 'pref-card pref-check-tree'
			, lines: false
			, listeners: {
				'checkchange': { fn: onCheckChange, scope: this }
			}
			, loader: new Ext.tree.TreeLoader()
			, margins: '10 15 15 15'
			, rootVisible: false
			, root: new Ext.tree.AsyncTreeNode({
				text: 'Hidden Root'
				, children: nodes
			})
			, split: false
			, width: 220
		});
		
		QoDesk.QoPreferences.QuickStart.Tree.superclass.constructor.apply(this, [config]);
		// constructor post-processing
		
		new Ext.tree.TreeSorter(this, {dir: "asc"});
			
		function expandNodes(ms, ids){
			var nodes = [];
			
			for(var i = 0, len = ms.length; i < len; i++){
				if(ms[i].moduleType === 'menu'){
					/* nodes.push({
						leaf: false,
						text: ms[i].launcher.text,
						children: this.expandNodes(o.menu.items, ids)
					}); */
				}else{
					nodes.push({
			           	checked: isChecked(ms[i].id, ids) ? true : false,
			           	iconCls: ms[i].launcher.iconCls,
			           	id: ms[i].id,
			           	leaf: true,
			           	selected: true,
			           	text: ms[i].launcher.text
					});
				}
			}
			
			return nodes;
		}
		
		function isChecked(id, ids){
			for(var i = 0, len = ids.length; i < len; i++){
				if(id == ids[i]){
					return true;
				}
			}
		}
		
		function onCheckChange(node, checked){
			if(node.leaf && node.id){
	    		if(checked){
					this.ownerModule.app.desktop.addQuickStartButton(node.id, true);
	    		}else{
					this.ownerModule.app.desktop.removeQuickStartButton(node.id, true);
	    		}
	    	}
	    	node.ownerTree.selModel.select(node);
	    }
	}
});