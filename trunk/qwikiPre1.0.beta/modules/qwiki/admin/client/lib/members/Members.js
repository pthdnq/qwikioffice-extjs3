/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin.Members = function(ownerModule){
   this.addEvents({
      'memberedited' : true
   });
   
   this.ownerModule = ownerModule;
   
   this.detail = new QoDesk.QoAdmin.MembersDetail({
      ownerModule: this.ownerModule
      , ownerPanel: this
      , region: 'north'
      , split: true
   });
   
   this.tree = new QoDesk.QoAdmin.MembersTree({
      ownerModule: this.ownerModule
      , ownerPanel: this
      , region: 'center'
   });
            
   this.grid = new QoDesk.QoAdmin.MembersGrid({
      ownerModule: this.ownerModule
      , ownerPanel: this
      , region: 'west'
      , width: 280
   });
   this.grid.on('activetoggled', this.onActiveToggled, this);
   this.grid.getSelectionModel().on('rowselect', this.viewDetail, this, {buffer: 450});
   
   QoDesk.QoAdmin.Members.superclass.constructor.call(this, {
      border: false
      , closable:true
      , iconCls: 'qo-admin-member'
      , id: 'qo-admin-members'
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
            disabled: this.ownerModule.app.isAllowedTo('viewAllMembers', this.ownerModule.id) ? false : true
            , handler: this.onRefresh
            , iconCls: 'qo-admin-refresh'
            , scope: this
            //, text: 'Refresh'
            , tooltip: 'Refresh'
            
         },'-',{
            disabled: this.ownerModule.app.isAllowedTo('addMember', this.ownerModule.id) ? false : true
            , handler: this.onAdd
            //, iconCls: 'qo-admin-add'
            , scope: this
            , text: 'Add'
            , tooltip: 'Add a new member'
         },{
            disabled: this.ownerModule.app.isAllowedTo('editMember', this.ownerModule.id) ? false : true
            , handler: this.onEdit
            //, iconCls: 'qo-admin-edit'
            , scope: this
            , text: 'Edit'
            , tooltip: 'Edit selected'
         },{
            disabled: this.ownerModule.app.isAllowedTo('deleteMembers', this.ownerModule.id) ? false : true
            , handler: this.onDelete
            //, iconCls: 'qo-admin-delete'
            , scope: this
            , text: 'Delete'
            , tooltip: 'Delete selected'
         } /*,{
            //disabled: this.ownerModule.app.isAllowedTo('viewMemberGroups', this.ownerModule.id) ? false : true
            handler: this.viewDetail
            //, iconCls: 'qo-admin-groups'
            , scope: this
            , text: 'View'
            , tooltip: 'View details'
         } */
      ]
      , title: 'Members'
   });
};

Ext.extend(QoDesk.QoAdmin.Members, Ext.Panel, {
   progressIndicator : null
   , selectedId : null
    
    , hideMask : function(){
      this.progressIndicator.hide();
    }
    
    , onActiveToggled : function(record){
      this.fireEvent('memberedited', record);
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
      
      var d = new QoDesk.QoAdmin.MembersAdd({
         callback: callback
         , ownerModule: this.ownerModule
         , scope: this
      });
      d.show();
    }
    
    , onDelete : function(){
      var sm = this.grid.getSelectionModel();
      var count = sm.getCount();
      
      if(count > 0){
         Ext.MessageBox.confirm('Confirm', 'Are you sure you want to delete the selected member(s)?', function(btn){
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
                     method: "deleteMembers"
                     , moduleId: this.ownerModule.id
                     , memberIds: encodedIds
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
                        Ext.MessageBox.alert('Warning', kCount+' member(s) were not deleted!');
                     }
                     
                     // loop through removed messages
                     for(var i = 0; i < rCount; i++){
                        // get the last (largest) msg index
                        var memberIndex = ds.indexOfId(decoded.r[i]);
                        
                        firstSelIndex = memberIndex < firstSelIndex ? memberIndex : firstSelIndex;
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
      
         var d = new QoDesk.QoAdmin.MembersEdit({
            callback: callback
            , memberId: id
            , ownerModule: this.ownerModule
            , scope: this
         });
         d.show();
      }
    }
    
    , onRender : function(ct, position){
        QoDesk.QoAdmin.Members.superclass.onRender.call(this, ct, position);
        
        this.progressIndicator = new Ext.LoadMask(Ext.get(this.body.dom.parentNode), {
         msg: 'Saving...'
        });
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
            groups = this.tree,
            memberId = data.id;
         
         //if(this.selectedId !== memberId){
            this.selectedId = memberId;
            
            // update the detail
            detail.setMemberId(memberId);
            detail.updateDetail(data);
            
            // load the groups
            groups.setMemberId(memberId);
            groups.reloadGroups();
         //}
      }
    }
});