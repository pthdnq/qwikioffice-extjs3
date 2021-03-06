/*
 * qWikiOffice Desktop 1.0
 * Copyright(c) 2007-2008, Integrated Technologies, Inc.
 * licensing@qwikioffice.com
 *
 * http://www.qwikioffice.com/license
 */

QoDesk.QoAdmin.ModulesDetail = function(config){
   this.ownerModule = config.ownerModule;

   QoDesk.QoAdmin.ModulesDetail.superclass.constructor.call(this, Ext.apply({
      autoScroll: true
      , border: false
      , cls: 'qo-module-detail'
   }, config));
};

Ext.extend(QoDesk.QoAdmin.ModulesDetail, Ext.Panel, {
   moduleId : null

   , afterRender : function(){
      QoDesk.QoAdmin.ModulesDetail.superclass.afterRender.call(this);
      this.ownerPanel.on('moduleedited', this.onModuleEdited, this);
   }

   , getModuleId : function(){
      return this.moduleId;
   }

   , onModuleEdited : function(record){
      if(record && record.id === this.moduleId){
         this.updateDetail(record.data);
      }
   }

   , setModuleId : function(id){
      if(id){
         this.moduleId = id;
      }
   }

   , updateDetail : function(data,row){
   		data.about.active = row.active ? Ext.MessageBox.buttonText.yes :Ext.MessageBox.buttonText.no;
      var tpl = new Ext.XTemplate(
         '<table id="qo-module-detail-table">'
         , '<tr><th>Name:</th><td>{values.about.name}</td></tr>'
         , '<tr><th>Author:</th><td>{values.about.author}</td></tr>'
         , '<tr><th>Description:</th><td>{values.about.description}</td></tr>'
         , '<tr><th>Active:</th><td>{values.about.active}</td></tr>'
         , '<tr><th>WWW:</th><td>{values.about.url}</td></tr>'
         ,'<tr><th>Version:</th><td>{values.about.version}</td></tr>'
         ,'<tr><th>Type:</th><td>{values.type}</td></tr>'
         //, '<td class="qo-admin-edit-btn"><p><button id="qo-admin-edit">Edit</button></p></td>'
         , '</table>'
      );
      tpl.overwrite(this.body, data);
    }
});