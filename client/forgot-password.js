Ext.onReady(function(){
	Ext.EventManager.onWindowResize(centerPanel);
	
	var panel = Ext.get("qo-panel");
	var btn = Ext.get("submitBtn");
   btn.on({
      'click': { fn: onClick }
      , 'mouseover': { fn: function(){ btn.addClass('qo-submit-over'); } }
      , 'mouseout': { fn: function(){ btn.removeClass('qo-submit-over'); } }
   });
	
	centerPanel();
   
   function centerPanel(){
      var xy = panel.getAlignToXY(document, 'c-c');
      positionPanel(panel, xy[0], xy[1]);
   }
   
   function positionPanel(el, x, y){
      if(x && typeof x[1] == 'number'){
         y = x[1];
         x = x[0];
      }
      el.pageX = x;
      el.pageY = y;
         
      if(x === undefined || y === undefined){ // cannot translate undefined points
         return null;
      }
        
      if(y < 0){ y = 10; }
        
      var p = el.translatePoints(x, y);
      el.setLocation(p.left, p.top);
      return el;
   }
	
	function onClick(){		
		var emailField = Ext.get("field1");
		var email = emailField.dom.value;
		
		if(validate(email) === false){
			alert("Your email address is required");
			return false;
		}
		
		panel.mask('Please wait...', 'x-mask-loading');
		
		Ext.Ajax.request({
			url: 'services.php'
			, params: {
				service: 'forgotPassword'
				, user: email
			}
			, success: function(o){
				panel.unmask();
				
				if(typeof o == 'object'){
					var d = Ext.decode(o.responseText);
					
					if(typeof d == 'object'){
						if(d.success == true){
							alert('Your password has been sent to your email.');
						}else{
							if(d.errors){
								alert(d.errors[0].msg);
							}else{
								alert('Errors encountered on the server.');
							}
						}
					}
				}
			}
			, failure: function(){
				panel.unmask();
				alert('Lost connection to server.');
			}
		});
	}

    function validate(field){
		if(field === ""){
			//field.markInvalid();
			return false;
		}
		return true;
	}
});