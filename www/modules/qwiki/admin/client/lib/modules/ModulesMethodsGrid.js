/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin.ModulesMethodsGrid = Ext.extend(Ext.grid.GridPanel, {
	data:null,
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
				}
			},
			proxy: new Ext.data.HttpProxy ({
				scope: this,
				url: this.ownerModule.app.connection
			}),
			baseParams: {
				method: 'viewModuleMethods',
				moduleId: this.ownerModule.id,
				modulesId: this.moduleId
			},
			reader: new Ext.data.JsonReader ({
				root: 'qo_modules',
				id: 'id',
				fields: [
					{name: 'id'},
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
				id:'id',
				header: 'Id',
				dataIndex: 'id',
				menuDisabled: true,
				width: 90
			},{
				id: 'name', // +X+ ADDED for autoExpandColumn (below)
				header: 'Name',
				dataIndex: 'name',
				menuDisabled: true,
				width:160
			},{
				id: 'description', 
				header: 'Description',
				dataIndex: 'description',
				menuDisabled: true
			}
		]);
		
		cm.defaultSortable = true;
		
		Ext.applyIf(config, {
			autoExpandColumn: 'name', // +X+ CHANGED (see https://www.yui-ext.com/forum/showthread.php?t=44668 for reason)
			border: false,
			cls: 'qo-admin-grid-list',
			cm: cm,
			//plugins: activeColumn,
			region: 'west',
			selModel: sm,
			split: true,
			store: store,
			viewConfig: {
				emptyText: 'No methods to display...',
				ignoreAdd: true,
				// forceFit: true,
				getRowClass : function(r){
					var d = r.data;
					if(!d.active){
							return 'qo-admin-inactive';
						}
						return '';
					}
			}
		});
		
		QoDesk.QoAdmin.ModulesMethodsGrid.superclass.constructor.apply(this, [config]);
		
		store.load();
	},
	setModuleId:function(id){
		this.moduleId = id;
	},
	// added methods
	reload:function(record){
		console.info(record,'#');
		this.store.load({params: {
				method: 'viewModuleMethods',
				moduleId: this.ownerModule.id,
				modulesId: record.id
			}});
	},
	handleUpdate : function(record){
//		Ext.Ajax.request({
//			url: this.ownerModule.app.connection,
//			params: {
//				method: 'changeModuleStatus',
//				field: 'active',
//				id: record.data.id,
//				moduleId: this.ownerModule.id,
//				value: record.data.active
//			},
//			success: function(o){
//				var d = Ext.decode(o.responseText);
//				
//				if(d.success){
//					
//					this.fireEvent("activetoggled", record);
//				}else{
//					Ext.MessageBox.alert('Error', d.msg || 'Errors encountered on the server.');
//					// rollback
//					record.set('active',!record.data.active);
//				}
//			},
//			failure: function(){
//				Ext.MessageBox.alert('Error', 'Lost connection to server.');	
//			},
//			scope: this
//		});
	}
});