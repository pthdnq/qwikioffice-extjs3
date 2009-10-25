<?php

$fp = fopen("test.txt",'ab');
fwrite($fp,print_r($_FILES,true));
fclose($fp);

?>