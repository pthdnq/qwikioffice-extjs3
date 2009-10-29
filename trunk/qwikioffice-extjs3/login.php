<?php 
require_once("system/os/config.php");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>qWikiOffice Login</title>

<!-- EXT JS LIBRARY -->
<?php
	echo config::loadExtJs();
?>
<!-- LOGIN -->
<link rel="stylesheet" type="text/css" href="system/login/shared.css" />
<script src="system/login/cookies.js"></script>
<script src="system/login/login.js"></script>
</head>
<body>
<div id="qo-login-panel">
	<img src="system/login/images/blank.gif" class="qo-login-logo qo-abs-position" />
	
	<div class="qo-login-benefits qo-abs-position">
		<p>A familiar desktop environment where you can
		Access all your web applications in a single web page</p>
		<p>Change the theme, wallpaper and colors to your liking</p>
		<p>Uses the <a href="http://www.extjs.com/" target="_blank">Ext JS</a> javascript library.</p>
	</div>
	
	<img src="system/login/images/blank.gif" class="qo-login-screenshot qo-abs-position" />
	
	<span class="qo-login-supported qo-abs-position">
		<b>Supported Browsers</b><br />
		<a href="http://www.mozilla.org/download.html" target="_blank">Firefox 2+</a><br />
		<a href="http://www.microsoft.com/windows/downloads/ie/getitnow.mspx" target="_blank">Internet Explorer 7+</a><br />
		<a href="http://www.opera.com/download/" target="_blank">Opera 9+</a><br />
		<a href="http://www.apple.com/safari/download/" target="_blank">Safari 2+</a>
	</span>
	
	<span class="qo-login-signup qo-abs-position">
		<a href="#">I want to sign up</a>
	</span>
	
	<span class="qo-login-forgot qo-abs-position">
		<a href="#">I forgot my password</a>
	</span>
	
	<label id="field1-label" class="qo-abs-position" accesskey="e" for="field1"><span class="key">E</span>mail Address</label>
	<input class="qo-abs-position" type="text" name="field1" id="field1" value="demo" />
	
	<label id="field2-label" class="qo-abs-position" accesskey="p" for="field2"><span class="key">P</span>assword</label>
	<input class="qo-abs-position" type="password" name="field2" id="field2" value="demo" />
	
	<label id="field3-label" class="qo-abs-position" accesskey="g" for="field3"><span class="key">G</span>roup</label>
	<select class="qo-abs-position" name="field3" id="field3" /></select>
	
	<input id="submitBtn" class="qo-login-submit qo-abs-position" type="image" src="system/login/images/blank.gif" />
</div>

</body>
</html>