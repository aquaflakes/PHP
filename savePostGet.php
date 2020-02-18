<?php
	
	$targetDir=$_SERVER['DOCUMENT_ROOT'].'/kaz/zhu_data/savedJson/'.$_GET["subdir"].'/';
	$cmd='mkdir '.$targetDir; system($cmd);
	$cmd='cp '.$_SERVER['DOCUMENT_ROOT'].'/kaz/zhu_data/savedJson/'.$_GET["base"].'_POST.txt '.$targetDir; system($cmd);
	$cmd='cp '.$_SERVER['DOCUMENT_ROOT'].'/kaz/zhu_data/savedJson/'.$_GET["base"].'_GET.txt '.$targetDir; system($cmd);
	$cmd='chmod 777 '.$targetDir; system($cmd);

?>