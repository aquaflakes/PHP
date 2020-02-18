
<?php
$path=$_SERVER['DOCUMENT_ROOT'].'/kaz/zhu_data/savedJson/';
$guide= shell_exec("ls $path | grep -v .txt");
echo $guide;
?>