<?php
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

//	$current_website = basename($_SERVER['PHP_SELF']);
//	$parts = explode(".", $current_website);
//	$current_base_name = $parts[0];
if(isset($_GET['base'])) {
	$current_base_name = $_GET['base'];
}

//if without guide info
else
{
	$guide_dir = opendir($guide_path);
	$guide_list = "";
	while(($filename = readdir($guide_dir)) !== false) {
		if(preg_match("/.txt$/", $filename)){
			if($filename === "expand_seeds.txt"){continue;}
			if(preg_match("/^\._/", $filename)){continue;}
			if(preg_match("/\.meta\.txt$/", $filename)){continue;}
			$file_time[] = filemtime($guide_path."/".$filename);
			$file = preg_replace("/.txt/", "", $filename);
			$guide_list[] = $file;
			$guide_list_for_time_order[] = $file;
		}
	}
	closedir($guide_dir);
	natcasesort($guide_list);
	$guide_list = array_merge($guide_list);
	array_multisort($file_time, SORT_DESC, $guide_list_for_time_order);
	echo "$html_head";
	echo "<table class=\"select_guide\">\n";
	echo "<tr><th colspan=4>Summary of Motif collection (Statistical table)</th></tr>\n";
	echo "<tr><td><a href=\"list_approved_pwm.php?species=Human&version=V2\">Human Motif Version 2</a></td>\n";
	echo "<td><a href=\"list_approved_pwm.php?species=Human&version=V3.1\">Human Motif Version 3.1</a></td>\n";
	echo "<td><a href=\"list_approved_pwm.php?species=Human&version=V4\">Human Motif Version 4</a></td>\n";
	echo "<td></td></tr>\n";
	echo "<tr><td><a href=\"list_approved_pwm.php?species=Fly&version=V2\">Fly Motif Version 2</a></td>\n";
	echo "<td><a href=\"list_approved_pwm.php?species=Fly&version=V3\">Fly Motif Version 3</a></td>\n";
	echo "<td><a href=\"list_approved_pwm.php?species=Ciona&version=V3\">Ciona Motif Version 3</a></td>\n";
	echo "<td></td></tr>\n";
	echo "<tr><th colspan=6>Update all list in a guide file (batch mode)</th></tr>\n";
	echo "<tr><td colspan=6><a href=\"update_list_all.php\">type guide file name in the URL as \"update_list_all.php?base=<i>guide file</i>\"</a></td></tr>\n";
	echo "<tr><td colspan=6><a href=\"update_list_all_only_spacek.php\">type guide file name in the URL as \"update_list_all_only_spacek.php?base=<i>guide file</i>\"</a></td></tr>\n";
	echo "<tr><td colspan=6><a href=\"update_group_all.php\">type guide file name in the URL as \"update_group_all.php?base=<i>guide file</i>\"</a></td></tr>\n";
	echo "<tr><th colspan=6>Tools</th></tr>\n";
	echo "<tr><td colspan=6><a href=\"batch_register.php\">Registration of batch information (CloneID etc) to peanut SELEX database</a></td></tr>\n";
	echo "<tr><td colspan=6><a href=\"one_hamming_founder.php\">Inspection of one hamming oligo combination</a></td></tr>\n";
	echo "<tr><td colspan=6><a href=\"extract_query_from_guide.php\">Query search from guide files</a></td></tr>\n";
	echo "<tr><td colspan=6><a href=\"dna_protein_filtering.php\">DNA / Protein filtering (remove return/space/numbers etc from pasted sequence.)</a></td></tr>\n";
	echo "<tr><th colspan=4>Useful guide files</th></tr>\n";
	echo "<tr><td colspan=2><a href=\"$welldispPath?base=all-human-complete2\">Human SELEX motifs Version 2 (Seeds from Arttu etal ST2)</a>&nbsp;<a href=\"$welldispPath?base=all-human-complete2&disp_mod=1\">**</a></td>\n";
	echo "<td colspan=2><a href=\"$welldispPath?base=all-human-version4\">Human SELEX motifs Version 4</a>&nbsp;<a href=\"$welldispPath?base=all-human-version4&disp_mod=1\">**</a></td></tr>\n";
	echo "<tr><td colspan=2><a href=\"$welldispPath?base=fly_motif_v2\">FLY SELEX motifs Version 2</a>&nbsp;<a href=\"$welldispPath?base=fly_motif_v2&disp_mod=1\">**</a></td>\n";
	echo "<td colspan=2><a href=\"$welldispPath?base=all-human-version3_1\">Human SELEX motifs Version 3.1</a>&nbsp;<a href=\"$welldispPath?base=all-human-version3_1&disp_mod=1\">**</a></td></tr>\n";
	echo "<tr><td colspan=2><a href=\"$welldispPath?base=fly_motif_v3\">FLY SELEX motifs Version 3</a>&nbsp;<a href=\"$welldispPath?base=fly_motif_v3&disp_mod=1\">**</a></td>\n";
	echo "<td colspan=2><a href=\"$welldispPath?base=fly_putative_ok\">FLY SELEX motifs Putative</a></td></tr>\n";
	echo "<tr><td colspan=2><a href=\"$welldispPath?base=ciona_motif_v3\">Ciona SELEX motifs Version 3</a></td>\n";
	echo "<td colspan=2><a href=\"$welldispPath?base=Ciona_putative_ok\">Ciona SELEX motifs Putative</a></td></tr>\n";
	echo "<tr><td colspan=4><a href=\"$welldispPath?base=fly_human\">FLY-Human SELEX motifs comparison</a></td></tr>\n";
	echo "<tr><td colspan=4><a href=\"$welldispPath?base=man_ciona_fly\">Human-Ciona-FLY ALL SELEX motifs</a></td></tr>\n";
	echo "<tr><td colspan=4><a href=\"$welldispPath?base=Funny_motifs\">Funny Motif Gallery</a></td></tr>\n";
	echo "<tr><th colspan=4>Guide files recently edited</th></tr>\n";
	for($idx = 0; $idx < 16; $idx++){
		if($idx == 0){
			echo "<tr>\n";
		}elseif($idx % 4 == 0){
			echo "</tr>\n<tr>\n";
		}
		echo "<td><a href=\"$welldispPath?base=$guide_list_for_time_order[$idx]\">$guide_list_for_time_order[$idx]</a>&nbsp;<a href=\"$welldispPath?base=$guide_list_for_time_order[$idx]&disp_mod=1\">**</a></td>\n";
	}
	echo "<tr><th colspan=4>ALL guide files</th></tr>\n";
	foreach($guide_list as $num => $file){
		if($num == 0){
			echo "<tr>\n";
		}elseif($num % 4 == 0){
			echo "</tr>\n<tr>\n";
		}
		echo "<td><a href=\"$welldispPath?base=$file\">$file</a>&nbsp;<a href=\"$welldispPath?base=$file&disp_mod=1\">**</a></td>\n";
	}
	echo "</tr>\n</table></body></html>";
	die;
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
echo "<table width=\"700\" border=1><tr>";
echo "<th width=\"50\">No</th><th colspan=\"2\">";
echo "<form action=\"$welldispPath?base=$current_base_name&disp_mod=$disp_mod";
if($range != ""){echo "&range=$range";}
if($filter != ""){echo "&filter=$filter";}
echo "\" value=\"YES\" name=\"dataform\" method='$method' enctype=\"$enctype\" style=\"display: inline\" onSubmit=\"SaveScrollXY();this.form.dataform.value = RemoveCR(this.form.dataform.value);\">";
echo "Display Mode&nbsp;<input type=\"submit\" name=\"button\" value=\"Standard Mode\">";
echo "&nbsp;<input type=\"submit\" name=\"button\" value=\"Conpact Mode\">";
echo "</form>";
echo "<form action=\"$welldispPath?base=$current_base_name&disp_mod=$disp_mod";
if($range != ""){echo "&range=$range";}
if($filter != ""){echo "&filter=$filter";}
echo "\" value=\"YES\" name=\"dataform\" method='$method' enctype=\"$enctype\" style=\"display: inline\" onSubmit=\"SaveScrollXY();this.form.dataform.value = RemoveCR(this.form.dataform.value);\">";
echo "&nbsp;&nbsp;Line Range&nbsp;<input type=\"text\" name=\"range_s\" value=\"$range_s\" size=\"3\"></input>&nbsp;-&nbsp;<input type=\"text\" name=\"range_e\" value=\"$range_e\" size=\"3\"></input>";
echo "&nbsp;<input type=\"submit\" name=\"button\" value=\"Set range\">";
echo "&nbsp;<input type=\"submit\" name=\"button\" value=\"Clear range\">";
echo "</form>";
echo "<form action=\"$welldispPath?base=$current_base_name&disp_mod=$disp_mod";
if($range != ""){echo "&range=$range";}
if($filter != ""){echo "&filter=$filter";}
echo "\" value=\"YES\" name=\"dataform\" method='$method' enctype=\"$enctype\" style=\"display: inline\" onSubmit=\"SaveScrollXY();this.form.dataform.value = RemoveCR(this.form.dataform.value);\">";
echo "&nbsp;&nbsp;&nbsp;Filter&nbsp;<input type=\"text\" name=\"filter\" value=\"$filter\" size=\"50\"></input>";
echo "&nbsp;<input type=\"submit\" name=\"button\" value=\"Filter\">";
echo "&nbsp;<input type=\"submit\" name=\"button\" value=\"Clear\">";
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
