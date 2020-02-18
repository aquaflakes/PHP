    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Edit feature <?php echo $_GET["field"];?></title>
<link rel="stylesheet" type="text/css" href="../style.css">
<script>
    //window.onunload = refreshParent;
    //function refreshParent() {
    //    window.opener.location.reload();
    //}

	function check(currid) {
        document.getElementById(currid).style.background = "#5bff00";
}

</script>

<?php
////var for debug
//$_GET["file"]="./Fangjie/php_FJ5.1KSELtest/diss_curve_php/non_img_data.disp";
//$_GET["field"]="beadsLost";
//$_GET["plate"]=384;

if ($_GET["plate"]=="1") $_GET["plate"]="384";
?>
</head>
<body>
<?php //write column of .disp file if submitted, or delete
$ori_field=isset($_POST["ori_field"])? $_POST["ori_field"]:$_GET["field"];
if(sizeof($_POST)>0 && ($ori_field!=="")) //edit existed field
{
	$fh=fopen($_GET["file"],'r');
	$capline = fgetcsv($fh,0,"\t");
	
	$targetCol;$pos_Col;
	for($j=0;$j<sizeof($capline);$j++){if ($ori_field==$capline[$j]){$targetCol=$j;} if ($capline[$j]==pos) {$pos_Col=$j;}}
	if (isset($_POST["delete_feat"])) {unset ($capline[$targetCol]);}
	else{ if (isset($_POST["field"])) {$capline[$targetCol] = $_POST["field"];$_GET["field"]=$_POST["field"];}}
	$out=implode("\t",$capline)."\n"; //edit field in capline
	
    while ($row = fgetcsv($fh,0,"\t")) {
        if (isset($_POST["delete_feat"])) {unset ($row[$targetCol]);}
		else
		{
		$row[$targetCol] = $_POST[$row[$pos_Col]];
		if($row[$targetCol]==$_POST["sub_ori"]) {$row[$targetCol]=$_POST["sub_to"];}
		}
		$out .= implode("\t",$row) . "\n";
    }
    fclose($fh);
	file_put_contents($_GET["file"], $out);
}

if (isset($_POST["delete_feat"]))
{
	echo  "<script type='text/javascript'>";
	echo "window.close();";
	echo "</script>";
}

if(sizeof($_POST)>0 && ($ori_field=="")) //add new field if no field name in $_GET
{
	$fh=fopen($_GET["file"],'r');
	$capline = fgetcsv($fh,0,"\t");

	$pos_Col;
	for($j=0;$j<sizeof($capline);$j++){if ($capline[$j]==pos) {$pos_Col=$j;}}
	if(($_POST["field"]=="")){$_POST["field"]="newFeature";}
	array_push($capline, $_POST["field"]);$_GET["field"]=$_POST["field"];
	
	$out=implode("\t",$capline)."\n"; //edit field in capline
	
    while ($row = fgetcsv($fh,0,"\t")) {
        array_push($row, $_POST[$row[$pos_Col]]);
		if($row[count($row)-1]==$_POST["sub_ori"]) {$row[count($row)-1]=$_POST["sub_to"];}
        $out .= implode("\t",$row) . "\n";
    }
    fclose($fh);
	file_put_contents($_GET["file"], $out);
}

?>
	
	
<?php //read from .disp file the specified field
	$fh=fopen($_GET["file"],'r');
	$capline = fgetcsv($fh,0,"\t");
	$targetCol;$pos_Col;
	for($j=0;$j<sizeof($capline);$j++){if ($_GET["field"]==$capline[$j]){$targetCol=$j;} if ($capline[$j]==pos) {$pos_Col=$j;}}
	
	$Data;
	while($row = fgetcsv($fh,0,"\t"))
	{
		$Data[$row[$pos_Col]]=$row[$targetCol];
	}
	fclose($fh);
	
	//make array of data compatible with output function below
	$allFeats[$_GET["field"]]["data"]=$Data;
?>


<form method="POST" action="#" name="myform">
<input type="hidden" name="ori_field" value="<?php echo $_GET["field"];?>">
<div class="floatTop">Field name*:  <input type="text" value="<?php echo $_GET["field"];?>" size="15" name="field">	
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;substitute
	<input type="text" value="" size="5" name="sub_ori"> &nbsp;to&nbsp; <input type="text" value="" size="5" name="sub_to">
	<br>
	
	<input type="submit" value="Save"> 
	<input type="button" value="Close Window" onClick="window.close()">
	<?php if (isset($ori_field)) {echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"delete_feat\" value=\"aa\"> delete_feature(!)";} ?>
</div>
<br><br>
<div>
<?php
$plates=array();
$RowChar384=range('A', 'P'); $col384=range(1,24); 	$RowChar96 = range('A', 'H'); $cols96=range(1,12);
if ($_GET["plate"]=="384"){ $plates[0]=" ";}
if ($_GET["plate"]=="96"){ $plates[0]=" ";$plates[1]=" ";$plates[2]=" ";$plates[3]=" ";}
if ($_GET["plate"]=="1x96"){ $plates[0]=" ";}


for ($p=0;$p<count($plates);$p++)
{
 if ($_GET["plate"]=="384"){ $rowChar=$RowChar384; $cols=$col384;}
 if ($_GET["plate"]=="96"){ $rowChar=$RowChar96; $cols=$cols96; $plates[$p].="<b>plate".($p+1)." (quad".($p+1)." of 384)</b><br>";}
  if ($_GET["plate"]=="1x96"){ $rowChar=$RowChar96; $cols=$cols96; }
 array_unshift($rowChar,"");		
	$plates[$p].="<table border='1'>";
	$currRowcnt=0;
	foreach ($rowChar as &$currRow) {
	   $plates[$p].= "<tr class=".(($currRowcnt % 2)? 'tabelCol1':'tabelCol2')."><td><b>$currRow</b></td>";
	   foreach ($cols as &$currCol){
	     if ($currRow=="") {$plates[$p].= "<td><b>".$currCol."</b></td>";}  //print th row
		 else{
		  $currPos=$currRow.$currCol; $posin384;
		  if ($_GET["plate"]=="384"||"1x96"){$posin384=$currPos;}
		  if ($_GET["plate"]=="96")
		  {
		   switch ($p)
		   {
		   case 0:
			   $actualRow=($currRowcnt-1)*2+1;
			   $actualCol=($currCol-1)*2+1;
			   break;
		   case 1:
			   $actualRow=($currRowcnt-1)*2+1;
			   $actualCol=($currCol-1)*2+2;
			   break;
		   case 2:
			   $actualRow=($currRowcnt-1)*2+2;
			   $actualCol=($currCol-1)*2+1;
			   break;
		   case 3:
			   $actualRow=($currRowcnt-1)*2+2;
			   $actualCol=($currCol-1)*2+2;
			   break;
		   }
		   $posin384=$RowChar384[$actualRow-1].$actualCol;
		  }		  
		  $Label= "<div><font size='1.5'>".$currPos.($_GET["plate"]=="96"? "(384: ".$posin384.")":"" )."</font></div>";
		  $features="";
		  foreach ($allFeats as &$feat_curr){
		   $dispVal=$feat_curr["data"][$posin384]; 
		   $currid=$_GET["field"].$posin384;
		   $features.="<input type=\"text\" id=\"".$currid."\" class=\"".($dispVal=="1"?"textTrue":"textFalse")."\" value=\"".$dispVal."\" name=\"".$posin384."\"
		   size=\"".(strlen($dispVal)==0?"5":strlen($dispVal))."\" onchange=\"check('".$currid."');\" >";
		  }
		  $poptitle=$posin384;
		  $plates[$p].= "<td width='100px'>".$Label.$features."</td>"; //wright the content of cell
		  }
	   }
	  $plates[$p].= "</tr>";
	  $currRowcnt++;
	}
	unset($currRow,$currCol); // break the reference with the last element
	$plates[$p].= "</table><br><br>";
}
foreach ($plates as &$plate) echo $plate;
?>

	
</div></form>
</body></html>