<?php
$service = (isset($_POST['service'])) ? $_POST['service'] : '';
if($service != 'install'){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>qWikiOffice - Installation</title>

<!-- EXT -->
<!-- Using cachefly -->
<link rel="stylesheet" type="text/css" href="http://extjs.cachefly.net/ext-2.2/resources/css/ext-all.css" />
<!-- Custom build -->
<script src="client/ext.js"></script>

<!-- INSTALL -->
<link rel="stylesheet" type="text/css" href="resources/css/login.css" />
<link rel="stylesheet" type="text/css" href="install/install.css" />

<script src="install/install.js"></script>
</head>

<body>

<div id="qo-panel">
   <img src="resources/images/default/s.gif" class="qo-logo qo-abs-position" />

   <div class="qo-benefits qo-abs-position">
      <p>A familiar desktop environment where you can Access all your web applications in a single web page</p>
      <p>Change the theme, wallpaper and colors to your liking</p>
   </div>

   <img src="resources/images/default/s.gif" class="qo-screenshot qo-abs-position" />

   <span class="qo-supported qo-abs-position">
      <b>Supported Browsers</b><br />
      <a href="http://www.mozilla.org/download.html" target="_blank">Firefox 2+</a><br />
      <a href="http://www.microsoft.com/windows/downloads/ie/getitnow.mspx" target="_blank">Internet Explorer 7+</a><br />
      <a href="http://www.opera.com/download/" target="_blank">Opera 9+</a><br />
      <a href="http://www.apple.com/safari/download/" target="_blank">Safari 2+</a>
   </span>

   <a href="http://www.extjs.com/" target="_blank"><img src="resources/images/default/s.gif" class="qo-extjs-logo qo-abs-position" /></a>

   <span class="qo-library qo-abs-position">built with the <a href="http://www.extjs.com/" target="_blank">Ext JS</a> library.</span>

   <div class="qo-instructions qo-abs-position">
      <p><b>Instructions:</b></p>

      <p>Before installing... open the config file and update it with your settings.  The config file location is:</p>

      <p><b>'server/os-config.php'</b>.</p>

      <p>If you have updated the config file with your settings you are now ready to install qWikiOffice.<p>

      <p>Click the <b>'Install'</b> button below.</p>
   </div>

   <input id="submitBtn" class="qo-submit qo-abs-position" type="image" src="resources/images/default/s.gif" />
</div>

</body>
</html>
<?php
}else{
   require_once('server/os.php');
   $os = new os();

   require_once('install/install.php');
   $install = new install($os);

   $success = $install->begin();
   if($success){
      print '{success:true}';
   }else{
      print '{success:false}';
   }
}
?>