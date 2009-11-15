/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 * 
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin.Nav = function(ownerModule){
   this.ownerModule = ownerModule;
   
   QoDesk.QoAdmin.Nav.superclass.constructor.call(this, {
      autoScroll: true
      , bodyStyle: 'padding:15px 0;'
      , border: false
      , html: '<ul id="qo-admin-nav-list">' +
            '<li>' +
               '<a id="viewGroups" href="#"><img src="'+Ext.BLANK_IMAGE_URL+'" class="qo-admin-groups-icon"/><br />' +
               'Groups</a>' +
            '</li>' +
            '<li>' +
               '<a id="viewMembers" href="#"><img src="'+Ext.BLANK_IMAGE_URL+'" class="qo-admin-members-icon"/><br />' +
               'Members</a>' +
            '</li>' +
            '<li>' +
               '<a id="viewPrivileges" href="#"><img src="'+Ext.BLANK_IMAGE_URL+'" class="qo-admin-privileges-icon"/><br />' +
               'Privileges</a>' +
            '</li>' +
            '<li>' +
               '<a id="viewSignups" href="#"><img src="'+Ext.BLANK_IMAGE_URL+'" class="qo-admin-signups-icon"/><br />' +
               'Signups</a>' +
            '</li>' +
         '</ul>'
      , region: 'west'
      , split: true
      , title: 'Home'
      , width: 200
   });
   
   this.actions = {
      'viewGroups' : function(ownerModule){
         ownerModule.viewGroups();
      }
      , 'viewMembers' : function(ownerModule){
         ownerModule.viewMembers();
      }
      , 'viewPrivileges' : function(ownerModule){
         ownerModule.viewPrivileges();
      }
      , 'viewSignups' : function(ownerModule){
         ownerModule.viewSignups();
      }
   };
};

Ext.extend(QoDesk.QoAdmin.Nav, Ext.Panel, {
   afterRender : function(){
      this.body.on({
         'mousedown': {
            fn: this.doAction
            , scope: this
            , delegate: 'a'
         }
         , 'click': {
            fn: Ext.emptyFn
            , scope: null
            , delegate: 'a'
            , preventDefault: true
         }
      });
      
      QoDesk.QoAdmin.Nav.superclass.afterRender.call(this); // do sizing calcs last
   }
   
   , doAction : function(e, t){
      e.stopEvent();
      this.actions[t.id](this.ownerModule);
    }
});