<?php //write column of guide file
$guidefile = glob($_SERVER['DOCUMENT_ROOT'].'/kaz/guide/'.$_GET["base"].'.txt')[0]; // !!!!!! may have multiple matches? !!!!
$well= $_GET["well"];
$field= $_GET["field"];
$newVal= $_GET["newVal"];

if(isset($field) && ($field!=="")) //edit existed field
{
	$fh=fopen($guidefile,'r');
	$capline = fgetcsv($fh,0,"\t");
	
	$targetCol;$pos_Col;
	for($j=0;$j<sizeof($capline);$j++){if ($field==$capline[$j]){$targetCol=$j;} if ($capline[$j]==pos) {$pos_Col=$j;}}
	$out=implode("\t",$capline)."\n"; //edit field in capline
	
    while ($row = fgetcsv($fh,0,"\t")) {
		if ($row[$pos_Col]==$well) {$row[$targetCol] = $newVal;} //only change the row that matches
		$out .= implode("\t",$row) . "\n";
    }
    fclose($fh);
	file_put_contents($guidefile, $out);
}


 //read from guide file the new value to double check
	$fh=fopen($guidefile,'r');
	$capline = fgetcsv($fh,0,"\t");
	$targetCol;$pos_Col;
	for($j=0;$j<sizeof($capline);$j++){if ($field==$capline[$j]){$targetCol=$j;} if ($capline[$j]==pos) {$pos_Col=$j;}}
	
	$Data;
	while($row = fgetcsv($fh,0,"\t"))
	{
		if ($row[$pos_Col]==$well) { $Data= $row[$targetCol]; break; }
		
	}
	fclose($fh);
	
	//echo the new value in the guide file
	echo $Data;
?>
