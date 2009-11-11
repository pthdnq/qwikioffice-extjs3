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
		var firstNameField = Ext.get("field1");
		var firstName = firstNameField.dom.value;
		
		var lastNameField = Ext.get("field2");
		var lastName = lastNameField.dom.value;
		
		var emailField = Ext.get("field3");
		var email = emailField.dom.value;
		
		var emailVerifyField = Ext.get("field4");
		var emailVerify = emailVerifyField.dom.value;
		
		var commentsField = Ext.get("field5");
		var comments = commentsField.dom.value;
		
		if(validate(firstName) === false){
			alert("Your first name is required");
			return false;
		}
		
		if(validate(lastName) === false){
			alert("Your last name is required");
			return false;
		}
		
		if(validate(email) === false){
			alert("Your email address is required");
			return false;
		}
		
		if(validate(emailVerify) === false || (email !== emailVerify)){
			alert("Please verify your email address again");
			return false;
		}
		
		panel.mask('Please wait...', 'x-mask-loading');
		
		Ext.Ajax.request({
			url: 'services.php'
			, params: {
				service: 'signup'
				, first_name: firstName
				, last_name: lastName
				, email: email
				, email_verify: emailVerify
				, comments: comments
			}
			, success: function(o){
				panel.unmask();
				
				if(typeof o == 'object'){
					var d = Ext.decode(o.responseText);
					
					if(typeof d == 'object'){
						if(d.success == true){
							firstNameField.dom.value = "";
							lastNameField.dom.value = "";
							emailField.dom.value = "";
							emailVerifyField.dom.value = "";
							commentsField.dom.value = "";
							
							alert('Your sign up request has been sent. \n\nYou will receive an email notification once we process your request.');
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
    
    function showGroupField(){
		Ext.get("field3-label").setDisplayed(true);
		Ext.get("field3").setDisplayed(true);
	}
    
    function validate(field){
		if(field === ""){
			//field.markInvalid();
			return false;
		}
		return true;
	}
});