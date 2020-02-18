<?php
#load param from file if provided
if($_GET["load"])
{
	$targetDir=$_SERVER['DOCUMENT_ROOT'].'/kaz/zhu_data/savedJson/'.$_GET["load"].'/';
	$postInfFile=glob($targetDir.'*_POST.txt')[0];
	$getInfFile=glob($targetDir.'*_GET.txt')[0];
	$_POST= json_decode(file_get_contents($postInfFile), true);
	$_GET=json_decode(file_get_contents($getInfFile), true);
}
else
{
	
}


if(!isset($_POST['plate'])) { $_POST['plate']=384;}

# set default dir, check if the dir with guid name exists, otherwise use 'zhu_data'
if(!isset($_POST['target'])) {
	//$directories = getGuideDir('zhu_data');  # too many subdir when ~ is linked
	//$_POST['target']= $directories[0];
	if ($_POST['target']=="") {$_POST['target']="zhu_data";}
}

function getGuideDir($base_dir) {
  $directories = array();
  foreach(scandir($base_dir) as $file) {
		if($file == '.' || $file == '..') continue;
		$dir = $base_dir.DIRECTORY_SEPARATOR.$file;
		if(is_dir($dir)) {
			if ($file==$_GET["base"]) {$directories []= $dir;}
			$directories = array_merge($directories, getGuideDir($dir));
		}
  }
  return $directories;
}

$selected= explode("_",$_POST["selected384"]); // get all selected wells (concatenated by "_")
?>

<html>

<head>

    <title><?php echo $_GET["base"];?></title>

<link rel="stylesheet" type="text/css" href="/kaz/zhu/style.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script><!--







   function ExtractQueryString() {
	 var oResult = {};
	 var aQueryString = (location.search.substr(1)).split("&");
	 for (var i = 0; i < aQueryString.length; i++) {
		 var aTemp = aQueryString[i].split("=");
		 if (aTemp[1].length > 0) {
			 oResult[aTemp[0]] = unescape(aTemp[1]);
		 }
	 }
	 return oResult;
	}
   var keyValues = ExtractQueryString(); //extract all key-value pairs
   
	function check(currid) {
        document.getElementById(currid).style.background = "#5bff00";
}
   
   
   
//jQuery
//concatanate all selected wells into a string, implode with "_"
function collect_selected_info( divs ) {
	var selected = [];
	for ( var i = 0; i < divs.length; i++ ) { selected.push( divs[ i ].id );}
	var all_selected= selected.join( "_" );	
	return all_selected;
}
//select the originally selected wells
function selectTargetDiv(element, index, array) { $("#"+element).attr("name","1"); $("div[name='0']").css('background-color','transparent'); $("div[name='1']").css('background-color','#F75D59');}

//function uncheck(){alert("here");  if ($('#singleWellMode').prop('checked')) {	$(".well_ID").attr("name","0"); } return true;}
		
		
		//AJAX of #TFdisp, only pass kazfromData is enough
	function refresh_TFdisp(callerID){
		if ($("#"+callerID).attr("name")=="0") {$("#"+callerID).attr("name","1")} else {$("#"+callerID).attr("name","0")} ;
		$("div[name='0']").css('background-color','transparent'); $("div[name='1']").css('background-color','#F75D59');
		var pos384= "&pos=" +collect_selected_info( $( "div[name='1']" ).toArray()) ; //a string containing info of all selected wells
		var kazformData = ""; //$("#kazform1").serialize()+"&"+$("#kazform2").serialize();
		if ($("#"+callerID).attr("id")=="filt") {
			pos384=""
			kazformData+= "&"+$("#kazform3").serialize(); //collect kaz data
		}
		$("#TFdisp").fadeIn();
		$.ajax({url: "zhu_kazdisp.php?base="+"<?php echo $_GET["base"];?>"+"&disp_mod=1"+ pos384+ kazformData+"#", type:"GET", success: function(result){
			$("#TFdisp").html(result);
			$("#closeX").click(function(){$("#TFdisp").fadeOut();}); //to close when x clicked
			$("#filt").click(function(){refresh_TFdisp("filt")});
		}});
    }
		//AJAX of main table
	function ajaxTable() {
		$(".well_ID").click(function(){
			if ($('#singleWellMode').prop('checked')) {	$(".well_ID").attr("name","0"); } //first clear selection of all cells, then select required
			refresh_TFdisp($(this).attr("id"));
		});  //make wells responsible again after AJAX
		$("input.textTrue, input.textFalse").on("blur keypress",function (e) {	//edit single well when ENTER pressed || blur in edit mode
			if (e.which == 0||e.which ==13 ) {
				var data= "&well="+$(this).attr("name")+"&field="+$(this).attr("field")+"&newVal="+$(this).val();
				var id= $(this).attr("id"); var newVal= $(this).val(); 
				$.ajax({url: "zhu/SELEXdisp/featureEdit_well.php?base="+"<?php echo $_GET["base"];?>"+data+"#", type:"GET", success: function(result){
					$("#"+id).val(result); //double check if editing worked
					if (newVal==result) {check(id);}
				}});
			 }
		});
	}
	
	function refresh_Table(param){
		var selected384= collect_selected_info( $( "div[name='1']" ).toArray()) ; //a string containing info of all selected wells
		if (!(typeof param === 'string' || param instanceof String)) { param= "";} //if nothing passed than add "", otherwise being an object
		$.ajax({url: "zhu_welldisp.php?base="+"<?php echo $_GET["base"];?>"+"#", type:"POST", data:$("#myform").serialize()+"&"+$("#tableStat").serialize()+param+"&selected384="+selected384, success: function(result){
		    //savePostGet(result);
			result=result.match(/<!--table_start[^]*?table_end-->/g)[1]; //[^] matches everything including \n, while . in [] do not function, take the 2nd match (1st is the regex)
			$("#mainDisp").html(result);
			ajaxTable();
			selected384.split("_").forEach(selectTargetDiv);
		}});
    }
		//AJAX of head
	function ajaxHead() {
		$("#Save").click(refresh_Table); //AJAX of main table
		$("#Save").click(refresh_Head);
		$("#choosePlate").change(refresh_Table);
		$("#choosePlate").change(refresh_Head);
		$(".dirSelect").on("change",function(e){refresh_Head($(this).val())});
		$("#edit").click(function(){refresh_Table("&edit=On")});
		$("#cancel").click(function(){refresh_Table("&edit=Off")});
		$("#Clear").click(function(){ $(".well_ID").attr("name","0"); $("div[name='0']").css('background-color','transparent'); $("#TFdisp").fadeOut(); $("#filterWords").val("");});
		$("#Select").click(applyFilter);
		$("#filterWords").keypress(function(e){if (e.which ==13 ) {applyFilter();}});
		$("#classifyField").change(function(){refresh_Table();});
		$("#savePostGet").click( savePostGetFile );
		
	}
	
	function applyFilter(){
			var filterWords=$("#filterWords").val();
			var filters=filterWords.split(" ");
			for ( var i = 0; i < filters.length; i++ ) {
				var objs=$("[cat$='dispdiv']").filter(function (index) { return $(this).text().match(RegExp(filters[i] ,"i")); });

				for ( var j = 0; j < objs.length; j++ ) {
					$("#"+objs[j].getAttribute("well")).attr("name","1"); //select by filter words (name=1 for selected)
				}
				var objs1=$("[cat$='disptext']").filter(function (index) { return this.value.match(RegExp(filters[i] ,"i")); }); // select textbox contains str, case insensitive

				for ( var k = 0; k < objs1.length; k++ ) {
					$("#"+objs1[k].getAttribute("well")).attr("name","1");
				}
			}
			refresh_TFdisp();
		}
		
	function refresh_Head(targetDir){
		if (!(typeof targetDir === 'string' || targetDir instanceof String)) { targetDir= $("input[name='currentDir']").val();} //post currentDir if no dirChange
		$.ajax({url: "zhu_welldisp.php?base="+"<?php echo $_GET["base"];?>"+"#", type:"POST", data:$("#myform").serialize()+"&target="+targetDir, success: function(result){
			//savePostGet(result);
			result=result.match(/<!--head_start[^]*?head_end-->/g)[1]; //[^] matches everything including \n, while . in [] do not function, take the 2nd match (1st is the regex)
			$("#headDisp").html(result);
			ajaxHead();
		}});
    }	
	
$(document).ready(function(){
	ajaxHead();
	ajaxTable();
	
	$("#TFdisp").draggable();
});

//function savePostGet(pageinfo) {		// save both the $POST and $GET data to hidden element 
//	post=pageinfo.match(/<!--post_start[^]*?post_end-->/g)[1]; $("#postInf").html(post); //extract POST info, save to current page
//	getInfo=pageinfo.match(/<!--get_start[^]*?get_end-->/g)[1]; $("#getInf").html(getInfo); //extract GET info, save to current page
//}

function savePostGetFile() {		// save data to json file
	//alert("save to file");
	$.ajax({url: "/kaz/zhu/SELEXdisp/savePostGet.php?base="+"<?php echo $_GET["base"];?>"+"&subdir="+$("#PostGetName").val()+"#", success:function(){alert("saved");}});
}


   -->
   
</script>

<script>
	

</script>
</head>








<body>
 
<div id="headDisp" class="fixedTop">
<!--head_start-->
<!-- display the options-->
	<form method="POST" action="#" name="myform" id="myform">  
	<input type="hidden" name="currentDir" value="<?php echo $_POST["target"]; ?>">


	Choose disp format
	<select id="choosePlate" name="plate">
	 <option value="384" <?php if ($_POST["plate"]==384) echo ' selected="selected"';?>> 1 x 384 well plate</option>
	 <option value="96" <?php if ($_POST["plate"]==96) echo ' selected="selected"';?>> 4 x 96 well plate</option>
	 <option value="1" <?php if ($_POST["plate"]==1) echo ' selected="selected"';?>> 1col for selected</option>
	 <option value="1x96" <?php if ($_POST["plate"]=="1x96") echo ' selected="selected"';?>> 1 x 96 well plate</option>
	</select>
	&nbsp;
	cell/row
	<input id="cellPerRow" type="text" name="cellPerRow" value="<?php echo $_POST["cellPerRow"];?>" size="1">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Folder to display

	<?php
	$folderlvs= split("/",$_POST["target"]);
	$total_lvs= sizeof($folderlvs);
	for($lv=0; $lv<$total_lvs;$lv++){  //1 select for each level
	  $currentDir.=$folderlvs[$lv]."/";
	  $subfolders = glob($currentDir.'*', GLOB_ONLYDIR);
	  if (sizeof($subfolders)<1) continue; #do not display dropdown list when no subfolder for current dir
	  echo "<select id='mySelect".$lv."' name=\"target\" class=\"dirSelect\" >"; 
	  //print void if no target folder
	  if ($lv==$total_lvs-1){echo('<option value="zhu_data" selected="selected">Select a folder</option>');}
	  for ($i = 0; $i < sizeof($subfolders); $i++)
	  {
		preg_match('/([^\/]*)$/',$subfolders[$i],$matched); 
		if ($subfolders[$i]==$_POST["target"]){echo('<option value="'.$subfolders[$i].'" selected="selected">'.$matched[1].'</option>');}
		else{echo('<option value="'.$subfolders[$i].'">'.$matched[1].'</option>');} //subfolder info for the list, full relpath for option value
	  }
	  echo "</select>";
	}
	?>
 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;	
EditMode (press ENTER after edit) <input id="edit" type="button" value="Edit"> &nbsp;
<input id="cancel" type="button" value="Quit"> 
	
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;	
    <?php
  //choose features to display
	$imgfolders = glob($_POST["target"].'/*', GLOB_ONLYDIR); $guide = glob($_SERVER['DOCUMENT_ROOT'].'/kaz/guide/'.$_GET["base"].'.txt')[0]; // !!!!!! may have multiple match? !!!!
	$allImgs_pre=preg_grep("/^img_.*/", array_keys($_POST)); # get info from post
				# reorder image
				if($_POST["reOrderImg"])
				{
					$allImgs_preTmp=array(); foreach($allImgs_pre as &$currPre){array_push($allImgs_preTmp,$currPre);} unset($currPre); # make index start from 0
					$allImgs_pre=$allImgs_preTmp;
					reorder_array($allImgs_pre, str_split($_POST["reOrderImg"]));
					$allImgs_preTmp=array(); foreach($allImgs_pre as &$currPre){array_push($allImgs_preTmp,$currPre);} unset($currPre);
					$allImgs_pre=$allImgs_preTmp;
				}
	
	echo "<input type=\"checkbox\" name=\"nolabel\" value=\"1\" ".(isset($_POST["nolabel"])? "checked":"").">no_label  ";
	echo "<input type=\"checkbox\" name=\"nocolor\" value=\"1\" ".(isset($_POST["nocolor"])? "checked":"").">no_color  ";
	
	echo "--------img_folders:  ";
	foreach ($imgfolders as &$img) {  //generate 1 checkbox for 1 subfolder (img folder), if not the same as checked ones
	$img= "/kaz/".$img; //change to abs path relative to server root, for cmp with checked img stored in $_POST
	preg_match('/([^\/]*)$/',$img,$matched);
	if (!in_array("img_".$matched[1],$allImgs_pre)){
		echo"<input type=\"checkbox\" name=\"img_".$matched[1]."\" value=\"".$img."\" ".(isset($_POST["img_".$matched[1]])? "checked":"").">".$matched[1]." |   ";}  //add img_ prefix to subdir name, folder full path in $value
	}
	//also generate checked boxes for the checked ones from last page
	foreach($allImgs_pre as &$img_curr){
		preg_match('/img_([^\/]*)$/',$img_curr,$no_prefix);
		//if the same path as current subfolder then do not disp
			//$_POST[$img_curr] and $imgfolders (now) contains full path relative to the root of php
			echo"<input type=\"checkbox\" name=\"".$img_curr."\" value=\"".$_POST[$img_curr]."\"".(isset($_POST[$img_curr])? "checked":"").">".$no_prefix[1]." |   ";  //folder full path in $value
	}unset($img_curr);
	echo "   OrderImg(0..n-1)<input type=\"text\" name=\"reOrderImg\"  value=\"\" size=6>   ";
	
	echo "--------
	<a href='javascript:window.open(\"/kaz/zhu/SELEXdisp/guideEdit.php?file=".$_GET["base"].".txt\", \"width=700, height=600\")' target='_blank'>
	guide_file</a>: ";
	$last_disp; $curr_disp=$guide;
	//foreach ($guide as &$curr_disp) {  //generate 1 checkbox for 1 field of guide files
	 $handle = fopen($curr_disp, "r"); $capline = fgets($handle); fclose($handle); //read the capline of file
	 $Features=split("\t",$capline);
	 foreach ($Features as &$curr_fe) {
	  $curr_fe=rtrim($curr_fe);
	  if ($curr_fe!=="pos") { echo"<input type=\"checkbox\" name=\"feat_".$curr_fe."\" value=\"".$curr_disp."\" ".(isset($_POST["feat_".$curr_fe])? "checked":"").">".
	  "<a href='javascript:window.open(\"/kaz/zhu/SELEXdisp/featureEdit.php?file=".$curr_disp."&field=".$curr_fe."&plate=".$_POST["plate"]."\",\"Edit Feature ".$curr_fe."\", \"width=700, height=600\")' target='_blank'>".$curr_fe."</a>"."
	  <input type=\"text\" value=\"".$_POST[$curr_fe."_col"]."\" name=\"".$curr_fe."_col\" size=1> |";
	  }
	 } //add feat_ prefix to field name, .disp file full path in $value
	$last_disp=$curr_disp; 
	//}
	echo "&nbsp;&nbsp;&nbsp;<a href='javascript:window.open(\"/kaz/zhu/SELEXdisp/featureEdit.php?file=".$last_disp."&plate=".$_POST["plate"]."\",\"add Feature\", \"width=700, height=600\")' target='_blank'>+ new</a>";
	?>
	&nbsp; 
	Filter_regex (join " " for multi):

	<input id="filterWords" type="text" name="filterWords" value="<?php echo $_POST["filterWords"];?>" size="30">
	<input id="Select" type="button" value="Filter">
	<input id="Clear" type="button" value="Clear">
	<input type="checkbox" id="singleWellMode" name="singleWellMode"  value="singleWellMode" <?php echo (isset($_POST["singleWellMode"])? "checked":"");?>> single_well
	
	&nbsp; &nbsp; 
	<input type="button" value="reCalc (Kaz calc NG)" onclick='<?php echo 'window.open("/kaz/update_list_all_only_spacek.php?base='.$_GET["base"].'","'.$_GET["base"].'","width=700, height=600")'; ?>' >
	
	&nbsp; Classify by
	<select id="classifyField" name="classifyField">
	 <option value="" <?php if (empty($_POST["classifyField"])) echo ' selected="selected"';?>> None</option>
	 <?php foreach ($Features as &$curr_fe) {
		$curr_fe=rtrim($curr_fe);
		echo"<option value=\"feat_$curr_fe\" ".($_POST["classifyField"]=="feat_".$curr_fe? "selected=\"selected\"":"").">$curr_fe</option>";
		}
	 ?>
	</select><input type="checkbox" id="allwells" name="allwells"  value="allwells" <?php echo (isset($_POST["allwells"])? "checked":"");?>> allwells
	
	&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp<input id="Save" type="button" value="Update Features" style="color: blue; background-color: #ff8a80;"> &nbsp; &nbsp;&nbsp;&nbsp;
	Name<input type="text" id="PostGetName" value="temp" size="10">&nbsp;<input type="button" value="savePostGet" id="savePostGet"> 
	<a href='javascript:window.open("/kaz/zhu/SELEXdisp/all_loads.php", "all loads" ,"width=700, height=600")' target='_blank'>load=</a>
	</form>
<!--head_end-->
</div>


 <div id="mainDisp"> 
<!--start of the main table-->
<!-- read feature file and make arrays of data-->
<!--table_start-->
<form method="POST" action="#" name="myTable" id="myTable"> 
<?php
//$allImgs_pre=preg_grep("/^img_.*/", array_keys($_POST)); # already done in the head
$allFeats_pre=preg_grep("/^feat_.*/", array_keys($_POST));
if ($_POST["classifyField"] && (!in_array($_POST["classifyField"],$allFeats_pre))) {array_push($allFeats_pre,$_POST["classifyField"]);$_POST[$_POST["classifyField"]]=$guide;} 

$allFeats=array();$allFiles;
//get actual feature field names of .disp file, remove prefix
$featIndex=0;
foreach($allFeats_pre as &$aaa)
{
 preg_match("/^feat_(.*)/",$aaa,$matched); $allFeats[$featIndex] = ["field"=>$matched[1],"file"=>$_POST[$aaa],"data"=>array()]; $allFiles[$_POST[$aaa]]=$_POST[$aaa];
 $featIndex++;
} //each element of $allFeats contains [actual_fieldName, .disp_fileName, array_for_data]
unset($aaa);

//read in each feature file and set data of corresponding fields
foreach($allFiles as &$file)
{
 $filehandle = fopen($file, 'r');
 $capline = fgetcsv($filehandle,0,"\t");
 $index_toget=array();  $pos_Col;
 for($i=0;$i<sizeof($allFeats);$i++){ //decide which features to get in current file and the corresponding rows
  $targetCol;
  for($j=0;$j<sizeof($capline);$j++){if ($allFeats[$i]["field"]==$capline[$j]){$targetCol=$j;} if ($capline[$j]==pos) {$pos_Col=$j;}}
  if ($allFeats[$i]["file"]==$file) {$index_toget[$targetCol]=$i;} 
 }
 
 while($row = fgetcsv($filehandle,0,"\t"))
 {
  foreach($index_toget as $key => $value) {
   $allFeats[$value]["data"][$row[$pos_Col]]=$row[$key];
  }
  unset($key,$value);
 }
 fclose($filehandle);
}
unset($file);



$classify=classifyArrGen($allFeats,$selected);
//echo "<div>".var_dump($classify)."</div>";

$plates=array();
if ($classify)
{
	$_POST["plate"]="1"; $_POST["cellPerRow"]= $_POST["cellPerRow"]? :"8";
	foreach ($classify as $value=>$wells)
	{
		array_push($plates,"<b>$value</b><br>");
		$platesTmp=genPlates($allFeats,$allImgs_pre,$wells);
		foreach ($platesTmp as &$plate) {array_push($plates,$plate);}
	}
	unset($value,$wells);
}
else {$plates=genPlates($allFeats,$allImgs_pre,$selected);}

foreach ($plates as &$plate) echo $plate;
?>
</form>
<form method="POST" action="#" name="tableStat" id="tableStat">
		<input type="hidden" name="edit" value="<?php echo $_POST["edit"]; ?>">		
</form>
<!--table_end-->
</div>

<?php	#files to save POST GET to json
	$jPFile= $_SERVER['DOCUMENT_ROOT'].'/kaz/zhu_data/savedJson/'.$_GET["base"].'_POST.txt';
	$jGFile= $_SERVER['DOCUMENT_ROOT'].'/kaz/zhu_data/savedJson/'.$_GET["base"].'_GET.txt';
	$jPost=json_encode($_POST); file_put_contents($jPFile, $jPost);
	$jGet=json_encode($_GET); file_put_contents($jGFile, $jGet);
?>

</body>

<div id="test"></div>
<div id="TFdisp" class="floatBottom"> <!--for disp of kaz TF info interface--> </div>
<div style="height: 400px">  </div>




</html>

<?php
function genPlates($allFeats,$allImgs_pre,$selected)
{
	$plates=array();
	$RowChar384=range('A', 'P'); $col384=range(1,24); 	$RowChar96 = range('A', 'H'); $cols96=range(1,12);
	if ($_POST["plate"]=="384"|| "1"){ $plates[0]=" ";}
	if ($_POST["plate"]=="96"){ $plates[0]=" ";$plates[1]=" ";$plates[2]=" ";$plates[3]=" ";}
	if ($_POST["plate"]=="1x96"){ $plates[0]=" ";}
	
	
	for ($p=0;$p<count($plates);$p++)
	{
	 if ($_POST["plate"]=="384"||"1"){ $rowChar=$RowChar384; $cols=$col384;}
	 if ($_POST["plate"]=="96"){ $rowChar=$RowChar96; $cols=$cols96; $plates[$p].="<b>plate".($p+1)." (quad".($p+1)." of 384)</b><br>";}
	 if ($_POST["plate"]=="1x96"){ $rowChar=$RowChar96; $cols=$cols96; }
	 array_unshift($rowChar,"");		
		$plates[$p].="<table border='1' width='30%' height='40%'>";
		$currRowcnt=0;$cnt1rowDisp=0;
		($_POST["plate"]=="1"? "<tr>":"");
		foreach ($rowChar as &$currRow) {
			$rowcol=""; if(!isset($_POST["nocolor"])) { $rowcol=($currRowcnt % 2)? 'tabelCol1':'tabelCol2';} //determine the color of the row
		   $plates[$p].= ($_POST["plate"]=="1"? "":"<tr class=".$rowcol.">"."<td><b>$currRow</b></td>");
		   foreach ($cols as &$currCol){
			 if ($currRow=="") {if ($_POST["plate"]!=="1") $plates[$p].= "<td><b>".$currCol."</b></td>";}  //print th row
			 else{
			  $currPos=$currRow.$currCol; //$posin384;
			  if(($_POST["plate"]=="1") && !in_array($currPos,$selected)) continue; //skip if not selected in 1-row mode
			  if ($_POST["plate"]=="384"||"1"||"1x96"){$posin384=$currPos;}
			  if ($_POST["plate"]=="96")
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
	  
			  list($Label,$features,$images)= genCellContent($allFeats,$allImgs_pre,$posin384,$currPos);
			  $plates[$p].= "<td width=".($_POST["plate"]=="1"? $color:'100px').">".$Label.$features.$images."</td>";
			  $plates[$p].= ($_POST["plate"]=="1"&& (($cnt1rowDisp+1) % $_POST["cellPerRow"]== 0) ? "</tr><tr>":""); $cnt1rowDisp++;
			  }
		   }
		  $plates[$p].= ($_POST["plate"]=="1"? "":"</tr>");
		  $currRowcnt++; 
		}
		unset($currRow,$currCol); // break the reference with the last element
		($_POST["plate"]=="1"? "</tr>":"");
		$plates[$p].= "</table><br><br>";
	}
	return $plates;
}


function genCellContent($allFeats,$allImgs_pre,$posin384,$currPos)
{
		  $Label= isset($_POST["nolabel"])? "":"<div cat=\"dispdiv\" name=\"0\" well=\"$posin384\" id=\"$posin384\" class=\"well_ID\" style=\"cursor: pointer;\"><font size='1.5'>".$currPos.($_POST["plate"]=="96"? "(384: ".$posin384.")":"" )."</font></div>"; # if disp in 96well format, add corresponding 384well info
		  $features="";

		  # ------------ display text features
		  foreach ($allFeats as &$feat_curr){
			 if($_POST["plate"]=="1" && $_POST["edit"]=="On") $features.="<span>   ".$feat_curr["field"]."</span>";
		   $dispVal=$feat_curr["data"][$posin384];
		   
		   //color the cells larger than text
		   $text=$_POST[$feat_curr["field"]."_col"]; $divCol="";
		   if (is_numeric($text)&&!(empty($text)))
		   {
			$divCol= ($dispVal>$text)? "red":"";
		   }
		   
		   $currid=$feat_curr["field"].$posin384;
		   $features.= ($_POST["edit"]=="On")?
		   "<input cat=\"disptext\" type=\"text\" field=\"".$feat_curr["field"]."\" id=\"".$currid."\" class=\"".($dispVal=="1"?"textTrue":"textFalse")."\" value=\"".$dispVal."\" name=\"".$posin384."\" well=\"".$posin384."\"
		   size=\"".(strlen($dispVal)==0?"5":strlen($dispVal))."\" >" //onchange=\"check('".$currid."');\" 
		   :
		   "<div cat=\"dispdiv\" well=\"".$posin384."\" style=\"background-color:".$divCol." !important; -webkit-print-color-adjust: exact; \"><font size='1.5'>".$dispVal."</font></div>";
		  }
		  # ----------------//
		  
		  $images="";
		  $poptitle=$posin384;
		  foreach($allImgs_pre as &$img_curr)
		  {
				$imgpath= $_POST[$img_curr]."/".$posin384.".png"; #support A1.png				
				
			   if (!file_exists ($_SERVER['DOCUMENT_ROOT'].$imgpath)) { # support A1_*.png
					//$path= $_SERVER['DOCUMENT_ROOT'].$_POST[$img_curr]."/".$posin384."_*[!pdf]"; #pick img of correct well
					$imgpath= $_SERVER['DOCUMENT_ROOT'].$_POST[$img_curr]."/".$posin384."_*png";
					$imgpath = glob($imgpath)[0];
					$imgpath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $imgpath); #!!!
						# link html if exist
						$htmlpath= glob($_SERVER['DOCUMENT_ROOT'].$_POST[$img_curr]."/".$posin384."_*html")[0];
						$htmlFlag= $htmlpath? 1 : 0;
						# take pdf also if no html
						if ($htmlFlag==0) {$htmlpath= glob($_SERVER['DOCUMENT_ROOT'].$_POST[$img_curr]."/".$posin384."_*pdf")[0]; $htmlFlag= $htmlpath? 1 : 0;}
						//$htmlpath= $_SERVER['DOCUMENT_ROOT'].str_replace(".png","",$imgpath); $htmlFlag=1;					
						$htmlpath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $htmlpath);
				}
	
			   $images.="<a href='javascript:window.open(\"".($htmlFlag? $htmlpath : $imgpath)."\",\"".$poptitle.rand()."\", \"width=700, height=600\")'><img  onerror=\"this.style.display='none'\"  width=100; src='".$imgpath."'></a>"; //onerror=\"this.style.display='none'\"
		   }
		  
		  //wright the content of cell
		  $color= "'' class=".(($currRowcnt % 2)? "'tabelCol1'":"'tabelCol2'"); //for 1-row display only
		  
		  return array($Label,$features,$images);
}


// $allFeats[0]["field"]="TF"; $allFeats[0]["file"]="xxx.txt"; $allFeats[0]["data"]["A1"]="xxx";					[actual_fieldName, .disp_fileName, array_for_data]
function classifyArrGen($allFeats,$selected)
{
	$classify;
	foreach ($allFeats as &$currFeat)
	{
		if ("feat_".$currFeat["field"]==$_POST["classifyField"])
		{
			foreach($currFeat["data"] as $well=>$value)
			{
				if (!$_POST["allwells"] && !in_array($well,$selected)) {continue;} # next if not displaying all wells and curr is not selected
				if (!$classify[$value]) $classify[$value]=array(); #ini if not exist, otherwise do not work
				array_push ($classify[$value],$well);
			}
			unset($well,$value);
			break;
		}
	}
	unset($currFeat);
	return $classify;
}


function reorder_array(&$array, $new_order) {
  $inverted = array_flip($new_order);
  uksort($array, function($a, $b) use ($inverted) {
    return $inverted[$a] > $inverted[$b];
  });
}

?>

