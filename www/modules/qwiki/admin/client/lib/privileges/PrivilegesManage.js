/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin.PrivilegesManage = Ext.extend(Ext.Window, {
   callback : null
   , iconCls : 'qo-admin-icon'
   , form : null

   , constructor : function(config){
      // constructor pre-processing
      config = config || {};

      this.ownerModule = config.ownerModule;
      this.callback = config.callback;

      this.form = new Ext.form.FormPanel({
				xtype:'form',
      	labelWidth:70,
      	border:false,
      	defaults:{anchor:"-20"},
      	autoHeight:true,
      	items:[
      		{
      		 fieldLabel:"Name",
      		 name:"name",
      		 xtype:'textfield'//,
      		 //value: config.data?config.data.name:''
      		},{
      			fieldLabel:"Description",
      			name:"description",
      			xtype:"textarea",
      			height:40//,
      			///value: config.data?config.data.description:''
      		},{
      			fieldLabel:"Active",
      			name:"active",
      			xtype:'checkbox'//,
      			//checked:config.data?config.data.active:false
      		}
      	]
      });

      if(config.data){
      	this.form.getForm().setValues(config.data);
      }

      // tree
      this.tree = new Ext.tree.TreePanel({
	      autoScroll: true
	      , border: true
	      , loader: new Ext.tree.TreeLoader({
	         baseParams: {
	            method: 'viewModuleMethods'
	            , moduleId: this.ownerModule.id
	            ,privilegeId:config.privilegeId
	         }
	         , dataUrl: this.ownerModule.app.connection
	      })
	      , region: 'center'
	      , rootVisible: false
	      , root: new Ext.tree.AsyncTreeNode({
	         text: 'Group'
	      })
	   });

      Ext.applyIf(config, {
         autoScroll: true
         , animCollapse: false
         , buttons: [
            {
               handler: this.onOk
               , scope: this
               , text: 'Ok'
            }
            , {
               handler: this.onCancel
               , scope: this
               , text: 'Cancel'
            }
         ]
         , constrainHeader: true
         , height: 300
         , iconCls: this.iconCls
         , items: [this.form,this.tree]
         , layout: 'fit'
         , maximizable: false
         , manager: this.ownerModule.app.getDesktop().getManager()
         , modal: true
         , shim: false
         , title: 'Manage Privileges'
         , width: 350
         , bodyStyle:"padding:8px"
      });

      QoDesk.QoAdmin.PrivilegesManage.superclass.constructor.apply(this, [config]);
      // constructor post-processing

   }

   // overrides

   /* , show : function(config, cb, scope){ // override the superclass show() so the callback is not called from show()
      if(!this.rendered){
         this.render(Ext.getBody());
      }
      if(this.hidden === false){
         this.toFront();
         return;
      }
      if(this.fireEvent("beforeshow", this) === false){
         return;
      }
      if(cb){
         this.callback = cb;
      }
      if(scope){
         this.scope = scope;
      }
      this.hidden = false;
      this.beforeShow();
      this.afterShow();

      //this.reset();

		if(config){

      }
	} */

   // added methods

   , onOk: function(){
			var selNodes = this.tree.getChecked();
			console.info(selNodes);
   }

   , onCancel : function(){
		this[this.closeAction]();
	}
});