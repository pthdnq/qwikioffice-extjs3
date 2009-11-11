/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin.Groups = function(ownerModule){
   this.addEvents({
      'groupedited' : true
   });
   
   this.ownerModule = ownerModule;
   
   this.detail = new QoDesk.QoAdmin.GroupsDetail({
      ownerModule: this.ownerModule
      , ownerPanel: this
      , region: 'north'
      , split: true
   });
   
   this.tree = new QoDesk.QoAdmin.GroupsTree({
      ownerModule: this.ownerModule
      , ownerPanel: this
      , region: 'center'
   });
            
   this.grid = new QoDesk.QoAdmin.GroupsGrid({
      ownerModule: ownerModule
      , ownerPanel: this
      , region: 'west'
      , width: 280
   });
   this.grid.on('activetoggled', this.onActiveToggled, this);
   this.grid.getSelectionModel().on('rowselect', this.viewDetail, this, {buffer: 450});
   
   QoDesk.QoAdmin.Groups.superclass.constructor.call(this, {
      border: false
      , closable: true
      , iconCls: 'qo-admin-group'
      , id: 'qo-admin-groups'
      , items: [
         this.grid
         , {
            border: false
            , items: [
               this.detail
               , this.tree
              ]
            , layout: 'border'
            , region: 'center'
           }
        ]
      , layout: 'border'
      , tbar: [{
            disabled: this.ownerModule.app.isAllowedTo('viewAllGroups', this.ownerModule.id) ? false : true
            , handler: this.onRefresh
            , iconCls: 'qo-admin-refresh'
            , scope: this
            //, text: 'Refresh'
            , tooltip: 'Refresh'
            
         },'-',{
            disabled: this.ownerModule.app.isAllowedTo('addGroup', this.ownerModule.id) ? false : true
            , handler: this.onAdd
            //, iconCls: 'qo-admin-add'
            , scope: this
            , text: 'Add'
            , tooltip: 'Add a new group'
         },{
            disabled: this.ownerModule.app.isAllowedTo('editGroup', this.ownerModule.id) ? false : true
            , handler: this.onEdit
            //, iconCls: 'qo-admin-edit'
            , scope: this
            , text: 'Edit'
            , tooltip: 'Edit selected'
         },{
            disabled: this.ownerModule.app.isAllowedTo('deleteGroups', this.ownerModule.id) ? false : true
            , handler: this.onDelete
            //, iconCls: 'qo-admin-delete'
            , scope: this
            , text: 'Delete'
            , tooltip: 'Delete selected'
         } /*,{
            //disabled: this.ownerModule.app.isAllowedTo('viewGroupGroups', this.ownerModule.id) ? false : true
            handler: this.viewDetail
            //, iconCls: 'qo-admin-groups'
            , scope: this
            , text: 'View'
            , tooltip: 'View details'
         } */
      ]
      , title: 'Groups'
   });
};

Ext.extend(QoDesk.QoAdmin.Groups, Ext.Panel, {
   progressIndicator : null
   , selectedId : null

   , onRender : function(ct, position){
      QoDesk.QoAdmin.Groups.superclass.onRender.call(this, ct, position);

      this.progressIndicator = new Ext.LoadMask(Ext.get(this.body.dom.parentNode), {
         msg: 'Saving...'
      });
   }

   , hideMask : function(){
      this.progressIndicator.hide();
   }

   , onActiveToggled : function(record){
      this.fireEvent('groupedited', record);
   }

   , onAdd : function(){
      var g = this.grid;
      var s = g.getStore();
      var sm = g.getSelectionModel();

      var callback = function(id){
         if(id){
            // callback to select record after load
            var reloadCb = function(){
               sm.selectRecords([s.getById(id)]);
            };
            
            s.reload({callback: reloadCb});
         }
       };
      
      var d = new QoDesk.QoAdmin.GroupsAdd({
         callback: callback
         , ownerModule: this.ownerModule
         , scope: this
      });
      d.show();
    }
    
    , onDelete : function(){
      var sm = this.grid.getSelectionModel(),
         count = sm.getCount();
      
      if(count > 0){
         Ext.MessageBox.confirm('Confirm', 'Are you sure you want to delete the selected group(s)?', function(btn){
            if(btn === "yes"){
               this.showMask('Deleting...');
               
               var selected = sm.getSelections(),
                  selectedIds = [];
               
               for(var i = 0; i < count; i++){
                  selectedIds[i] = selected[i].id;
               }
               
               var encodedIds = Ext.encode(selectedIds);
               
               //submit to server
               Ext.Ajax.request({
                  waitMsg: 'Saving changes...'
                  , url: this.ownerModule.app.connection
                  , params: { 
                     method: "deleteGroups"
                     , moduleId: this.ownerModule.id
                     , groupIds: encodedIds
                    }
                  , failure:function(response,options){
                     this.hideMask();
                     Ext.MessageBox.alert('Warning', 'Lost connection to the server!');
                    }                                      
                  , success:function(o){
                     var ds = this.grid.getStore(), decoded = Ext.decode(o.responseText),
                        rCount = decoded.r.length, kCount = decoded.k.length, firstSelIndex = 9999;
                     
                     // if some msgs(s) were not removed, display alert
                     if(kCount > 0){
                        Ext.MessageBox.alert('Warning', kCount+' group(s) were not deleted!');
                     }
                     
                     // loop through removed messages
                     for(var i = 0; i < rCount; i++){
                        // get the last (largest) msg index
                        var groupIndex = ds.indexOfId(decoded.r[i]);
                        
                        firstSelIndex = groupIndex < firstSelIndex ? groupIndex : firstSelIndex;
                        // remove the deleted from the ds
                        ds.remove(ds.getById(decoded.r[i]));
                     }
                     
                     // handle new selection, leave kept messages selected
                     if(kCount == 0){
                        var dsCount = ds.getCount();
                        if(dsCount <= firstSelIndex){
                           this.grid.getSelectionModel().selectRow(firstSelIndex-1);
                        }else{
                           this.grid.getSelectionModel().selectRow(firstSelIndex);
                        }
                     }
                     
                     this.hideMask();
                    }
                  , scope: this
               });
            }
          }, this);
      }
    }
    
   , onEdit : function(){
      var record = this.grid.getSelectionModel().getSelected();
      
      if(record && record.id){
         var id = record.id,
            g = this.grid,
            s = g.getStore();
         
         // callback to reload the grid, fire
         var callback = function(){
            s.reload();
          };
      
         var d = new QoDesk.QoAdmin.GroupsEdit({
            callback: callback
            , groupId: id
            , ownerModule: this.ownerModule
            , scope: this
         });
         d.show();
      }
   }
   
   , onRefresh : function(){
      this.showMask('Refreshing...');
      this.grid.store.reload({
         callback: this.hideMask
         , scope: this
      });
   }
   
   , showMask : function(msg){
      var pi = this.progressIndicator;
      
      if(msg){
         pi.msg = msg;
      }
      pi.show();
    }
   
   , viewDetail : function(sm, index, record){
      if(record && record.data){          
         var data = record.data,
            detail = this.detail,
            privileges = this.tree,
            groupId = data.id;
         
         //if(this.selectedId !== groupId){
            this.selectedId = groupId;
            
            // update the detail
            detail.setGroupId(groupId);
            detail.updateDetail(data);
            
            // load the privileges
            privileges.setGroupId(groupId);
            privileges.reloadPrivileges();
         //}
      }
    }
});