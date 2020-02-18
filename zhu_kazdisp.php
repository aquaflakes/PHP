            <div id="closeX"> <a href="#" ><font color="red" size="4" face="arial">&nbsp;X</font></a> 
            </div>
<?php
$wells_to_disp = explode("_", $_GET["pos"]);

$welldispPath="/kaz/zhu_welldisp.php";
$disp_mod = 1;	#0: Standard; 1: Compact
$filter = "";
$range = "";
$range_s = 0;
$range_e = 99999;
include('path_ini.php');
include_once("$script_path/common_function.php");
include("$script_path/display_functions.php");
set_time_limit(0);
$method = "post";
$enctype = "application/x-www-form-urlencoded";

 	$html_head = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<title>SELEX PHP site</title>
<link rel=\"stylesheet\" href=\"$script_path/common.css\" type=\"text/css\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />
<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\" />
<meta http-equiv=\"Pragma\" content=\"no-cache\">
<meta http-equiv=\"Cache-Control\" content=\"no-cache\">
<meta http-equiv=\"Expires\" content=\"Thu, 01 Dec 1994 16:00:00 GMT\">
<script type=\"text/javascript\" src=\"./$script_path/jquery-1.7.1.min.js\"></script>
<script type=\"text/javascript\" src=\"./$script_path/script.js\"></script>
<script type=\"text/javascript\">
window.onunload = function(){};
history.forward();
</script>
</head>
<body>
";


if(isset($_GET['base'])) {
	$current_base_name = $_GET['base'];
}

//if with guide and TF info
if(isset($_GET['disp_mod'])) {
	$disp_mod = $_GET['disp_mod'];
}
if(isset($_GET['filter'])) {
	$filter = $_GET['filter'];
}
if(isset($_GET['range'])) {
	if(preg_match("/^-/", $_GET['range'])){
		$range_e = preg_replace("/^-/", "", $_GET['range']);
	}elseif(preg_match("/-$/", $_GET['range'])){
		$range_s = preg_replace("/-$/", "", $_GET['range']);
	}elseif(!preg_match("/-/", $_GET['range'])){
		$range_s = $_GET['range'];
	}else{
		list ($range_s, $range_e) = preg_split("/-/", $_GET['range']);
	}
	if($range_s != 0 or $range_e != 99999){
		$range = $range_s."-".$range_e;
		$range = preg_replace("/^0-/", "-", $range);
		$range = preg_replace("/-99999$/", "-", $range);
	}
}
if(isset($_POST['button'])) {
	//Display mode
	if($_POST['button'] === "Standard Mode"){
		$disp_mod = 0;
	}elseif($_POST['button'] === "Conpact Mode"){
		$disp_mod = 1;
	}

	//Range
	if($_POST['button'] === "Set range"){
		$range_s = $_POST['range_s'];
		$range_e = $_POST['range_e'];
		if($range_s != 0 or $range_e != 99999){
			$range = $range_s."-".$range_e;
			$range = preg_replace("/^0-/", "-", $range);
			$range = preg_replace("/-99999$/", "-", $range);
		}
	}elseif($_POST['button'] === "Clear range"){
		$range_s = 0;
		$range_e = 99999;
		$range = "";
	}

	//Filter
	if(isset($_POST['filter'])){
		if($_POST['button'] === "Filter"){
			$filter = $_POST['filter'];
		}elseif($_POST['button'] === "Clear"){
			$filter = "";
		}
	}

	//Load with altered GET parameter
	$param = "";
	if($disp_mod != 0){$param = $param."&disp_mod=$disp_mod";}
	if($range != ""){$param = $param."&range=$range";}
	if($filter != ""){$param = $param."&filter=$filter";}
	header("Location: ./$welldispPath?base=$current_base_name$param");
}

$guide = $guide_path."/".$current_base_name.".txt";

$all_data = file_get_contents($guide);
$lines = explode("\n", $all_data);
$default_raw = explode("\t", $lines[0]);
$group_mode = "off";
if($default_raw[0] == "GROUP" or $default_raw[0] == "MGROUP"){
	$group_mode = "on";
	$group_count = 1;
}

$pos_Col;
for($j=0;$j<sizeof($default_raw);$j++){if ($default_raw[$j]==pos) {$pos_Col=$j;}} # find pos column

$rows = count($lines);
$cols = 10;
$delimiter = "";

//Load expand information
$expand_edit = 0;
$exp_seeds = $guide_path  . "/" . $expand_guide_file;
$exp_seeds_data = file_get_contents($exp_seeds);
$exp_seeds_lines = explode("\n", $exp_seeds_data);
foreach($exp_seeds_lines as $exp_seed){
	$exp_information_temp = explode("\t", $exp_seed);
	if($exp_information_temp[0] == "editable_guide"){
		if($exp_information_temp[1] == "$current_base_name"){
			$expand_edit = 1;
		}
	}elseif($exp_information_temp[0] == "current_human"){
		$current_human = $exp_information_temp[1];
	}elseif($exp_information_temp[0] == "current_fly"){
		$current_fly = $exp_information_temp[1];
	}else{
		$seed = $exp_information_temp[0];
		$exp_information[$seed] = $exp_seed;
	}
}

// MAIN PAGE GENERATOR
echo "$html_head";
echo "<table width=\"700\" border=1 style=\"background-color:#FFFFFF;\"><tr>";
echo "<th width=\"50\">No</th><th colspan=\"2\">";
echo "<form id=\"kazform1\" action=\"$welldispPath?base=$current_base_name&disp_mod=$disp_mod";
if($range != ""){echo "&range=$range";}
if($filter != ""){echo "&filter=$filter";}
echo "\" value=\"YES\" name=\"dataform\" method='$method' enctype=\"$enctype\" style=\"display: inline\" onSubmit=\"SaveScrollXY();this.form.dataform.value = RemoveCR(this.form.dataform.value);\">";
echo "Display Mode&nbsp;<input type=\"button\" name=\"button\" value=\"Standard Mode\">";
echo "&nbsp;<input type=\"button\" name=\"button\" value=\"Conpact Mode\">";
echo "</form>";
echo "<form id=\"kazform2\" action=\"$welldispPath?base=$current_base_name&disp_mod=$disp_mod";
if($range != ""){echo "&range=$range";}
if($filter != ""){echo "&filter=$filter";}
echo "\" value=\"YES\" name=\"dataform\" method='$method' enctype=\"$enctype\" style=\"display: inline\" onSubmit=\"SaveScrollXY();this.form.dataform.value = RemoveCR(this.form.dataform.value);\">";
echo "&nbsp;&nbsp;Line Range&nbsp;<input type=\"text\" name=\"range_s\" value=\"$range_s\" size=\"3\"></input>&nbsp;-&nbsp;<input type=\"text\" name=\"range_e\" value=\"$range_e\" size=\"3\"></input>";
echo "&nbsp;<input type=\"button\" name=\"button\" value=\"Set range\">";
echo "&nbsp;<input type=\"button\" name=\"button\" value=\"Clear range\">";
echo "</form>";
echo "<form id=\"kazform3\" action=\"$welldispPath?base=$current_base_name&disp_mod=$disp_mod";
if($range != ""){echo "&range=$range";}
if($filter != ""){echo "&filter=$filter";}
echo "\" value=\"YES\" name=\"dataform\" method='$method' enctype=\"$enctype\" style=\"display: inline\" onSubmit=\"SaveScrollXY();this.form.dataform.value = RemoveCR(this.form.dataform.value);\">";
echo "&nbsp;&nbsp;&nbsp;Filter&nbsp;<input type=\"text\" name=\"filter\" value=\"$filter\" size=\"50\"></input>";
echo "&nbsp;<input type=\"button\" name=\"button\" id=\"filt\" value=\"Filter\">";
echo "&nbsp;<input type=\"button\" name=\"button\" value=\"Clear\">";
echo "</form>";
echo "</th>";
echo "</tr>";




for($line = 1;  $line +1 < $rows; $line += 1){
//	Header
	$cell = 1 . "_" . $line;
	$default = ParseGuideLine($lines[$line]);

	if($range_s != 0 or $range_e != 99999){
		if($line < $range_s or $line > $range_e){continue;}
	}

	if($filter !== ""){
		$fwords = explode(" ", $filter);
		$temp = 0;
		foreach($fwords as $fword){
			if(!preg_match("/$fword/", $lines[$line])){
				$temp = 1;
				continue;
			}
		}
		if($temp == 1){continue;}
	}
	

	//check if it is the right well
	if (!in_array($default[$pos_Col],$wells_to_disp) && $filter=="" ) {continue;}

	echo "<tr>";
	if($default[0] == "GROUP" or $default[0] == "MGROUP"){
		//Count group member
		unset($gmember_raw);
		unset($gmember_name);
		unset($gmember_seqfile);
		unset($filgmember_name);
		unset($filgmember_seqfile);
		unset($filgmember_bakfile);
		unset($filgmember_pfmfile);
		$gmember_seqfile = array();
		for($idx = $line + 1; $idx < $rows; $idx++){
			$gmember_raw = explode("\t", $lines[$idx]);
			if($gmember_raw[0] == "GROUP" or $gmember_raw[0] == "MGROUP"){break;}
			if(!isset($gmember_raw[2]) or !isset($gmember_raw[3]) or !isset($gmember_raw[6])){
				continue;
			}
			if(!in_array($gmember_raw[2].$gmember_raw[3].$gmember_raw[6], $gmember_seqfile)){
				$gmember_name[] = $gmember_raw[0];
				$gmember_seqfile[] = $gmember_raw[2].$gmember_raw[3].$gmember_raw[6];
			}
			$filgmember_name[] = $gmember_raw[0];
			$filgmember_seqfile[] = $gmember_raw[2].$gmember_raw[3].$gmember_raw[6];
			$back_cycle = $gmember_raw[6] - 1;
			$filgmember_bakfile[] = $gmember_raw[2].$gmember_raw[3].$back_cycle;
			$filgmember_pfmfile[] = $gmember_raw[2]."_".$gmember_raw[3]."_".$gmember_raw[4]."_m".$gmember_raw[5]."_c".$gmember_raw[6];
		}
		if(isset($gmember_name)){
			echo "<td id=\"$cell\" class=\"group\" width=\"600\" colspan=\"2\" align=center>";
			DispGroup($default, $gmember_name, $filgmember_pfmfile, $group_count, $gmember_seqfile, $filgmember_seqfile, $filgmember_bakfile, $current_base_name, $cell, $filgmember_name);
			echo "<td width=\"50\" class=\"group\" align=center>";
			echo "<input type=\"button\" value=\"EDIT\" class=\"modal_edit\" href=\"edit_group.php?cell=$cell&base=$current_base_name\"></input>";
			if($default[0] == "MGROUP"){
				echo "<input type=\"button\" value=\"XYPLOT\" class=\"modal_xyplot\" href=\"edit_xyplot.php?cell=$cell&base=$current_base_name\"></input>";
			}
			echo "</td></tr></td>";
			$group_count++;
		}
	} else{
		$filename_base = $default[2] . "_" . $default[3] . "_" . $default[4] . "_m" . $default[5] . "_c" . $default[6];
		unset($exp_seed_data);
		$exp_seed_data = "NULL";
		if(isset($exp_information[$filename_base])){$exp_seed_data = explode("\t", $exp_information[$filename_base]);}
		if($expand_edit == 1){
			echo "<td rowspan = \"2\" align=center>$cell<br \>";
			echo "<input type=\"button\" value=\"DEL\" name=\"$cell\" base=\"$current_base_name\" class=\"list_del\"></input><br><br>";
			echo "<input type=\"button\" value=\"EDIT\" class=\"modal_edit\" href=\"edit_card.php?cell=$cell&base=$current_base_name\"></input><br><br>";
			echo "<input type=\"button\" value=\"DUP\" name=\"$cell\" base=\"$current_base_name\" class=\"list_dup\"></input></td>";

			echo "<td colspan=\"2\">";
			include("$script_path/expand_edit.php");
			echo "</td>";	
			echo "<tr><td id=\"$cell\" colspan=\"2\">";
			DispCard($default, $exp_seed_data, $disp_mod);
			echo "</td>";	
		}else{
			echo "<td align=center>$cell<br \>";
			echo "<input type=\"button\" value=\"DEL\" name=\"$cell\" base=\"$current_base_name\" class=\"list_del\"></input><br><br>";
			echo "<input type=\"button\" value=\"EDIT\" class=\"modal_edit\" href=\"edit_card.php?cell=$cell&base=$current_base_name\"></input><br><br>";
			echo "<input type=\"button\" value=\"DUP\" name=\"$cell\" base=\"$current_base_name\" class=\"list_dup\"></input></td>";

			echo "<td id=\"$cell\" colspan=\"2\">";
			DispCard($default, $exp_seed_data, $disp_mod);
			echo "</td>";	
		}
	}
	echo "</tr>";
}	
echo "</table>";
echo "</body></html>";
?>
