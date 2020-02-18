<?php
$guide_file= $_SERVER['DOCUMENT_ROOT']."/kaz/guide/".$_GET["file"];

$bkcolor="";
if (isset($_POST["guide_content"])) {
	file_put_contents($guide_file,$_POST["guide_content"]);
	$guide = file_get_contents($guide_file);
	if ($guide==$_POST["guide_content"]) {$bkcolor="#5bff00";}
}else{
	$guide = file_get_contents($guide_file);
}

?>


<html><head></head><body>

	<form method="POST" action="#" name="myform">
	<input type="submit" value="Save"> <input type="button" value="Close Window" onClick="window.close()">
	<textarea name="guide_content" style="width:100%;height:100%; background-color: <?php echo $bkcolor;?> "><?php echo $guide; ?></textarea>
	</form>

</body></html>