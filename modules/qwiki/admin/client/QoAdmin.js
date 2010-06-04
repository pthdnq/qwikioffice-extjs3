/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin = Ext.extend(Ext.app.Module, {
   id: 'qo-admin'
   , type: 'system/admin'
   //, menuPath : 'StartMenu/Admin'

   , actions: null
   , tabPanel: null
   , win: null

   , createWindow : function(){
      var desktop = this.app.getDesktop();
      this.win = desktop.getWindow(this.id);

      if(!this.win){
         var winWidth = parseInt( desktop.getWinWidth() / 1.1);
         var winHeight = parseInt( desktop.getWinHeight() / 1.1);

         this.tabPanel = new Ext.TabPanel({
            activeTab:0
            , border: false
            , items: new QoDesk.QoAdmin.Nav(this)
            , region: 'center'
         });

         this.win = desktop.createWindow({
            animCollapse: false
            , constrainHeader: true
            , id: this.id
            , height: winHeight
            , iconCls: 'qo-admin-icon'
            , items: [
               this.tabPanel
               , {
	               bodyStyle: 'border-width:1px 0 0 0'
	               , height: 24
	               , id: 'qo-admin-status'
	               , region: 'south'
	            }
            ]
            , layout: 'border'
            , shim: false
            , taskbuttonTooltip: '<b>Admin</b><br />Allows you to administer your desktop'
            , title: 'Admin'
            , width: winWidth
         });
      }

      this.win.show();
   }

   , openTab : function(tab){
      if(tab){
         this.tabPanel.add(tab);
      }
      this.tabPanel.setActiveTab(tab);
   }

   , viewGroups : function(){
      var tab = this.tabPanel.getItem('qo-admin-groups');
      if(!tab){
         tab = new QoDesk.QoAdmin.Groups(this);
         this.openTab(tab);
      }else{
         this.tabPanel.setActiveTab(tab);
      }
   }

   , viewMembers : function(){
      var tab = this.tabPanel.getItem('qo-admin-members');
      if(!tab){
         tab = new QoDesk.QoAdmin.Members(this);
         this.openTab(tab);
      }else{
         this.tabPanel.setActiveTab(tab);
      }
   }

   , viewPrivileges : function(){
      var tab = this.tabPanel.getItem('qo-admin-privileges');
      if(!tab){
         tab = new QoDesk.QoAdmin.Privileges(this);
         this.openTab(tab);
      }else{
         this.tabPanel.setActiveTab(tab);
      }
   }

   , viewSignups : function(){
      var tab = this.tabPanel.getItem('qo-admin-signups');
      if(!tab){
         tab = new QoDesk.QoAdmin.Signups(this);
         this.openTab(tab);
      }else{
         this.tabPanel.setActiveTab(tab);
      }
   }
  , viewModules : function(){
      var tab = this.tabPanel.getItem('qo-admin-modules');
      if(!tab){
         tab = new QoDesk.QoAdmin.Modules(this);
         this.openTab(tab);
      }else{
         this.tabPanel.setActiveTab(tab);
      }
   }

   , showMask : function(msg){
      this.win.body.mask(msg+'...', 'x-mask-loading');
   }

   , hideMask : function(){
      this.win.body.unmask();
   }
});