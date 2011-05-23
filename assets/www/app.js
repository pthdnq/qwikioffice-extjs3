/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var panel = '';
Ext.setup({
 
     tabletStartupScreen: 'tablet_startup.png',
    phoneStartupScreen: 'phone_startup.png',
    icon: 'icon.png',
    glossOnIcon: false,
    onReady: function() {
        
   // Create a Carousel of Items
        var carousel1 = new Ext.Carousel({
            defaults: {
                cls: 'card'
            },
            items: [{
                html: '<h1>Carousel</h1><p>Navigate the two carousels on this page by swiping left/right or clicking on one side of the circle indicators below.</p>'
            },
            {
                title: 'Tab 2',
                html: '2'
            },
            {
                title: 'Tab 3',
                html: '3'
            }]
        });
        
  
        var tbTop = new Ext.Toolbar({xtype: 'toolbar', dock: 'top'});
        var tbBottom = new Ext.Toolbar({xtype:'toolbar',dock:'bottom'});
        panel = new Ext.Panel({
            fullscreen: true,
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            defaults: {
                flex: 1
            },
            items: [carousel1],
            dockedItems:[tbTop,tbBottom]
        });
        Ext.EventManager.onWindowResize(setActivePanel);
    }
   
});

function setActivePanel(){ 
    //alert("asdg");
    panel.setOrientation( Ext.getOrientation() , window.innerWidth , window.innerHeight );
}

