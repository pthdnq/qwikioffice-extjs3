/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin.ModulesMethodsGrid = Ext.extend(Ext.grid.GridPanel, {
	constructor : function(config){
		config = config || {};
		this.moduleId = null;
		this.addEvents({
			'activetoggled' : true
		});
		
		this.ownerModule = config.ownerModule;
		
		var sm = new Ext.grid.RowSelectionModel({
			singleSelect: false
		});
	
		var store = new Ext.data.Store ({
			listeners: {
				'load': {
					fn: function(s){
						if(s.data.length > 0){ this.selectRow(0); }
					},
					scope: sm,
					single: true
				},
				'loadexception':function(){
					this.removeAll();
				}
			},
			proxy: new Ext.data.HttpProxy ({
				scope: this,
				url: this.ownerModule.app.connection
			}),
			reader: new Ext.data.JsonReader ({
				root: 'methods',
				id: 'name',
				fields: [
					{name: 'name'},
					{name: 'description'}
				]
			})
		});
		
		var activeColumn = new QoDesk.QoAdmin.ActiveColumn({
			hidden: this.ownerModule.app.isAllowedTo('editMember', this.ownerModule.id) ? false : true
		});
		
		var cm = new Ext.grid.ColumnModel([
			//activeColumn,
			{
				id: 'name', // +X+ ADDED for autoExpandColumn (below)
				header: 'Name',
				dataIndex: 'name',
				menuDisabled: true,
				width:160
			},{
				id: 'description', 
				header: 'Description',
				dataIndex: 'description',
				menuDisabled: true,
				width:300
			}
		]);
		
		cm.defaultSortable = true;
		
		Ext.applyIf(config, {
			border: false,
			cm: cm,
			selModel: sm,
			store: store,
			viewConfig: {
				emptyText: 'No methods to display...'
			}
		});
		
		QoDesk.QoAdmin.ModulesMethodsGrid.superclass.constructor.apply(this, [config]);
		
		//store.load();
	},
	setModuleId:function(id){
		this.moduleId = id;
	},
	// added methods
	reload:function(record){
		//console.info(Ext.getCmp('testese'));
		this.store.load({params: {
				method: 'viewMethods',
				moduleId: this.ownerModule.id,
				id: record.id
			}});
			this.doLayout();
	}
});