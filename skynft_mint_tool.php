<?php
session_start();

$redis 			= new Redis();
$HTTP_HOST 		= $_SERVER['HTTP_HOST'];
$redis->connect('127.0.0.1', 6379);

######################################################################################
$MaxPartNumber 				= 10;
$MaxSupportMakeNftsNumber 	= 5000;
$MinSupportMakeNftsNumber 	= 10;
######################################################################################

require_once("skynft_mint_tool.inc.php");

$_GET['PARTID'] 				= Filter($_GET['PARTID']);
$_GET['IDRULES'] 				= Filter($_GET['IDRULES']);
$_GET['NFTNAME'] 				= Filter($_GET['NFTNAME']);
$_GET['SKYNFT_MINT_TOOL_HASH'] 	= Filter($_GET['SKYNFT_MINT_TOOL_HASH']);
$_GET['description'] 			= htmlentities($_GET['description']);
//$_GET['collection'] = Filter($_GET['collection']);
//$_GET['icon'] 		= Filter($_GET['icon']);
//$_GET['banner'] 	= Filter($_GET['banner']);
//$_GET['twitter'] 	= Filter($_GET['twitter']);
//$_GET['website'] 	= Filter($_GET['website']);
//$_GET['discord'] 	= Filter($_GET['discord']);
//$_GET['instagram']	= Filter($_GET['instagram']);


		
//Recover data from redis
if($_GET['SKYNFT_MINT_TOOL_HASH']!=""&&$redis->hget("SKYNFT_MINT_PROJECT",$_GET['SKYNFT_MINT_TOOL_HASH'])!="") {
	$_SESSION['SKYNFT_MINT_TOOL_HASH'] = $_GET['SKYNFT_MINT_TOOL_HASH'];
}
//Initial value to user
if($_SESSION['SKYNFT_MINT_TOOL_HASH']=="") {
	$_SESSION['SKYNFT_MINT_TOOL_HASH'] = md5(time().rand(1,999));
}
$SKYNFT_MINT_TOOL_HASH 			= $_SESSION['SKYNFT_MINT_TOOL_HASH'];

if(!is_dir("SkyNFTMintTool"))  {
	mkdir("SkyNFTMintTool");
}
if(!is_dir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH))  {
	mkdir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH);
}
if(!is_dir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile"))  {
	mkdir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile");
}
if(!is_dir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/metafile"))  {
	mkdir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/metafile");
}
if(!is_dir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft"))  {
	mkdir("SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft");
}
if(is_file("./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/UploadImage_1_1.png"))  {
	list($RequiredWidth, $RequiredHeight, $RequiredType, $RequiredAttr) = getImageSize("./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/UploadImage_1_1.png");
	$RequiredWidthHtml = "<font color=red>Require all png file format width:".$RequiredWidth." Height:".$RequiredHeight."</font>";
}

if($_GET['action']=="uploadimage"&&$_GET['PARTID']!="")				{	
	if(is_file($_FILES['file']['tmp_name'])&&$_FILES['file']['error']==0&&$_FILES['file']['type']=="image/png") {
		list($Width, $Height, $Type, $Attr) = getImageSize($_FILES['file']['tmp_name']);
		if($_GET['PARTID']!="UploadImage_1_1" && ($Width!=$RequiredWidth||$Height!=$RequiredHeight) ) {
			$RS['code']				= 1;
			$RS['msg'] 				= "Require your png file width:".(int)$RequiredWidth."px height:".(int)$RequiredHeight."px (your file width:".$Width."px height:".$Height."px)".$RequiredWidthHtml;
			$RS['data'] 			= [];
			print_R(json_encode($RS,JSON_UNESCAPED_SLASHES));
			exit;
		}
		$SafeSavePng = SafeSavePng($_FILES['file']['tmp_name'],"./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/".$_GET['PARTID'].".png");
		$RS['code']				= 0;
		$RS['msg'] 				= "Upload File Successful";
		$RS['filename'] 		= "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/".$_GET['PARTID'].".png";
		$RS['data'] 			= $SafeSavePng;
		print_R(json_encode($RS,JSON_UNESCAPED_SLASHES));
		exit;
	}
	$RS['code']				= 2;
	$RS['msg'] 				= "Only support png format.";
	$RS['data'] 			= [];
	print_R(json_encode($RS,JSON_UNESCAPED_SLASHES));
	exit;	
}

$_POST['MaxNFTNumber'] = (int)$_POST['MaxNFTNumber'];
if($_POST['MaxNFTNumber']>$MaxSupportMakeNftsNumber) $_POST['MaxNFTNumber'] = $MaxSupportMakeNftsNumber;
if($_POST['MaxNFTNumber']<$MinSupportMakeNftsNumber) $_POST['MaxNFTNumber'] = $MinSupportMakeNftsNumber;
$_POST['PartsNumber'] = (int)$_POST['PartsNumber'];
if($_POST['PartsNumber']>$MaxPartNumber) $_POST['PartsNumber'] = $MaxPartNumber;
if($_POST['PartsNumber']<3) $_POST['PartsNumber'] = 3;

if($_GET['action']=="setting")	{
	Page_title("SkyNFT Mint Tool");
	$redis->hdel("SKYNFT_MINT_RENAME",$SKYNFT_MINT_TOOL_HASH);
	$redis->hdel("SKYNFT_MINT_HAVE_MADE_NFT_IMAGE",$SKYNFT_MINT_TOOL_HASH);
	$redis->hdel("SKYNFT_MINT_HAVE_MADE_META_FILE",$SKYNFT_MINT_TOOL_HASH);
	$redis->hdel("SKYNFT_MINT_HAVE_MADE",$SKYNFT_MINT_TOOL_HASH);
	$redis->hset("SKYNFT_MINT_PROJECT",$SKYNFT_MINT_TOOL_HASH,json_encode($_POST,JSON_UNESCAPED_SLASHES));
	
	//delete the exist files
	$dir  = "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile/";
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if(is_file($dir . $file)) {
				unlink($dir . $file);
				#print "$dir . $file";
			}
		}
		closedir($dh);
	}
	
	print "<BR><BR><h2 style='text-align:center;'>SAVE PROJECT SUCCESSFULLY</h2>";
	print "<META HTTP-EQUIV=REFRESH CONTENT='1;URL=?'>";
	
	exit;
}

if($_GET['action']=="setpart")	{
	Page_title("SkyNFT Mint Tool");
	$redis->hset("SKYNFT_MINT_PART",$SKYNFT_MINT_TOOL_HASH,json_encode($_POST,JSON_UNESCAPED_SLASHES));
	print "<BR><BR><h2 style='text-align:center;'>SAVE PROJECT SUCCESSFULLY</h2>";
	print "<META HTTP-EQUIV=REFRESH CONTENT='1;URL=?'>";
	exit;
}

$MAP = $redis->hget("SKYNFT_MINT_PROJECT",$SKYNFT_MINT_TOOL_HASH);
$MAP = json_decode($MAP,true);

$PART = $redis->hget("SKYNFT_MINT_PART",$SKYNFT_MINT_TOOL_HASH);
$PART = json_decode($PART,true);

if($MAP['MaxNFTNumber']=="")	$MAP['MaxNFTNumber'] 	= 10;
if($MAP['PartsNumber']=="")		$MAP['PartsNumber'] 	= 5;
if($MAP['NFTNAME']=="") 		$MAP['NFTNAME'] = "Chinese Tiger 2022";
if($MAP['IDRULES']=="") 		$MAP['IDRULES'] = "[NFTNAME] #[ID]";

$CurrentSettingSupportMaxNFTs = 1;
for($i=1;$i<=$MAP['PartsNumber'];$i++)		{
	if($PART['ValueNumber_'.$i]=="") $PART['ValueNumber_'.$i] = 3;
	$CurrentSettingSupportMaxNFTs *= $PART['ValueNumber_'.$i];
}
$SKYNFT_MINT_CalculateAllRandomNumber = $redis->hget("SKYNFT_MINT_CalculateAllRandomNumber",$SKYNFT_MINT_TOOL_HASH);
$SKYNFT_MINT_CalculateAllRandomNumber = json_decode($SKYNFT_MINT_CalculateAllRandomNumber,true);
if($SKYNFT_MINT_CalculateAllRandomNumber=="") $SKYNFT_MINT_CalculateAllRandomNumber = [];
//print $CurrentSettingSupportMaxNFTs;

if($_GET['action']=="CalculateAllRandomNumber"&&$CurrentSettingSupportMaxNFTs!=count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	$PartValueArray = [];
	for($i=1;$i<=$MAP['PartsNumber'];$i++)		{
		$PartValueArray[$i] = $PART['ValueNumber_'.$i];
	}
	$FunctionName = "CalculateAllRandomNumber_".$MAP['PartsNumber'];
	$result = $FunctionName($PartValueArray);
	Page_title("SkyNFT Mint Tool");
	$redis->hset("SKYNFT_MINT_CalculateAllRandomNumber",$SKYNFT_MINT_TOOL_HASH,json_encode($result,JSON_UNESCAPED_SLASHES));
	print "<BR><BR><h2 style='text-align:center;'>Calculate All Random Number Successful</h2>";
	print "<META HTTP-EQUIV=REFRESH CONTENT='1;URL=?'>";
	exit;
}

if($_GET['action']=="MakeNFTsData"&&$CurrentSettingSupportMaxNFTs==count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	$EveryTimeMakeNFTs = 5;
	$MakeNFTsData = MakeNFTsData($SKYNFT_MINT_TOOL_HASH,$MAP,$PART,$EveryTimeMakeNFTs,$SKYNFT_MINT_CalculateAllRandomNumber);
	Page_title("SkyNFT Mint Tool");
	print "<BR><BR><h2 style='text-align:center;'>Every Time Made 5 Nfts. And Wait 5 Seconds to continue.</h2>";
	print "<BR><BR><h2 style='text-align:center;'>Have Made Nfts Images: ".$MakeNFTsData.", Total Need to Made: ".$MAP['MaxNFTNumber'].".</h2>";
	print "<META HTTP-EQUIV=REFRESH CONTENT='3;URL=?action=MakeNFTsData'>";
	exit;
}

if($_GET['action']=="RenameNFTs"&&$CurrentSettingSupportMaxNFTs==count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	$EveryTimeMakeNFTs = 5;
	$HAVE_MADE = $redis->hget("SKYNFT_MINT_HAVE_MADE",$SKYNFT_MINT_TOOL_HASH);
	$HAVE_MADE = json_decode($HAVE_MADE,true);
	$ALLNewFileName = $redis->hget("SKYNFT_MINT_RENAME",$SKYNFT_MINT_TOOL_HASH);
	$ALLNewFileName = json_decode($ALLNewFileName,true);
	foreach($HAVE_MADE AS $INDEX) {
		$INDEXNAME = str_replace(" ","_",$INDEX);
		$FileName  = "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile/".$INDEXNAME.".png";
		$ID ++;
		if(is_file($FileName))  { 
			//unlink($FileName);
			$NFTNAME = $MAP['NFTNAME'];
			$IDRULES = $MAP['IDRULES'];
			$NewFileName = str_replace("[NFTNAME]",$NFTNAME,$IDRULES);
			$NewFileName = str_replace("[ID]",$ID,$NewFileName);
			$attributes 		= [];
			$INDEX_ARRAY = explode(" ",$INDEX);
			for($i=0;$i<sizeof($INDEX_ARRAY);$i++)		{
				$FieldName 		= $PART["PartName_".($i+1)];
				$FieldValue 	= $PART["PartValue_".($i+1)."_".($INDEX_ARRAY[$i])];
				$attributes[] 	= $FieldName."_".$FieldValue;	
			}
			$attributes_text 	= join('__',$attributes);
			$TargetFileName  = "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile/".$NewFileName."____5____".$attributes_text.".png";
			//print $TargetFileName."<BR>";
			//exit;
			rename($FileName,$TargetFileName);
			$ALLNewFileName[] = $NewFileName;
		}	
	}
	
	$DraftDir 	= "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/";
	$ZipFile	= $DraftDir."".$SKYNFT_MINT_TOOL_HASH."_nftimages.zip";;
	if(is_file($ZipFile)) {
		unlink($ZipFile);
	}
	
	$redis->hset("SKYNFT_MINT_RENAME",$SKYNFT_MINT_TOOL_HASH,json_encode($ALLNewFileName));
	$redis->hset("SKYNFT_MINT_HAVE_MADE_NFT_IMAGE",$SKYNFT_MINT_TOOL_HASH,time());
	//print_R($MAP);
	Page_title("SkyNFT Mint Tool");
	print "<BR><BR><h2 style='text-align:center;'>Rename NFTs Successful</h2>";
	print "<META HTTP-EQUIV=REFRESH CONTENT='3;URL=?action='>";
	exit;
}

if($_GET['action']=="MakeMetaFile"&&$CurrentSettingSupportMaxNFTs==count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	$EveryTimeMakeNFTs = 5;
	$HAVE_MADE = $redis->hget("SKYNFT_MINT_HAVE_MADE",$SKYNFT_MINT_TOOL_HASH);
	$HAVE_MADE = json_decode($HAVE_MADE,true);
	foreach($HAVE_MADE AS $INDEX) {
		$INDEX_ARRAY = explode(" ",$INDEX);
		$ID ++;
		$NFTNAME = $MAP['NFTNAME'];
		$IDRULES = $MAP['IDRULES'];
		$NewFileName = str_replace("[NFTNAME]",$NFTNAME,$IDRULES);
		$NewFileName = str_replace("[ID]",$ID,$NewFileName);
		$TargetFileName  	= "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/metafile/".$NewFileName.".json";
		
		$attributes 		= [];
		for($i=0;$i<sizeof($INDEX_ARRAY);$i++)		{
			$FieldName 		= $PART["PartName_".($i+1)];
			$FieldValue 	= $PART["PartValue_".($i+1)."_".($INDEX_ARRAY[$i])];
			$attributes[] 	= ["trait_type"=>$FieldName,"value"=>$FieldValue];	
		}
		
		$MetaJsonFormat 				= [];
		$MetaJsonFormat['format'] 		= "CHIP-0007";
		$MetaJsonFormat['name'] 		= $NewFileName;
		$MetaJsonFormat['description'] 	= "";
		$MetaJsonFormat['minting_tool'] = "SkyNft Mint Tool V1";
		$MetaJsonFormat['sensitive_content']	= "false";
		$MetaJsonFormat['attributes'] 			= $attributes;
		$MetaJsonFormat['collection']['id'] 	= $SKYNFT_MINT_TOOL_HASH;
		$MetaJsonFormat['collection']['name'] 	= $MAP['collection'];
		$MetaJsonFormat['collection']['attributes'] = [];
		if($MAP['description']!="") $MetaJsonFormat['collection']['attributes'][] 	= ["type"=>"description","value"=>$MAP['description']];
		if($MAP['icon']!="") $MetaJsonFormat['collection']['attributes'][] 		= ["type"=>"icon","value"=>$MAP['icon']];
		if($MAP['banner']!="") $MetaJsonFormat['collection']['attributes'][] 	= ["type"=>"banner","value"=>$MAP['banner']];
		if($MAP['twitter']!="") $MetaJsonFormat['collection']['attributes'][] 	= ["type"=>"twitter","value"=>$MAP['twitter']];
		if($MAP['website']!="") $MetaJsonFormat['collection']['attributes'][] 	= ["type"=>"website","value"=>$MAP['website']];
		$MetaJsonFormatJson = json_encode($MetaJsonFormat,JSON_UNESCAPED_SLASHES);
		file_put_contents($TargetFileName,$MetaJsonFormatJson);
		//print_R($MetaJsonFormatJson);exit;
	}
	$redis->hset("SKYNFT_MINT_HAVE_MADE_META_FILE",$SKYNFT_MINT_TOOL_HASH,time());
	print "<BR><BR><h2 style='text-align:center;'>Rename NFTs Successful</h2>";
	print "<META HTTP-EQUIV=REFRESH CONTENT='3;URL=?'>";
	exit;
}

if($_GET['action']=="ViewMintCommand"&&$CurrentSettingSupportMaxNFTs==count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	$ALLNewFileName = $redis->hget("SKYNFT_MINT_RENAME",$SKYNFT_MINT_TOOL_HASH);
	$ALLNewFileName = (array)json_decode($ALLNewFileName,true);
	foreach($ALLNewFileName AS $INDEX) {
		$NftSha256Value  = hash("sha256", file_get_contents("./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile/".$INDEX.".png"));
		$MetaSha256Value  = hash("sha256", file_get_contents("./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/metafile/".$INDEX.".json"));
		//hash("sha256", file_get_contents("pexels-photo-11053072.jpeg"))
		//print $NftSha256Value;
		print "chia wallet nft mint -u '".$MAP['NftUrlPath']."/".$INDEX.".png' -nh ".$NftSha256Value." -mu '".$MAP['MetaUrlPath']."/".$INDEX.".json' -mh ".$MetaSha256Value." -i 2 <BR>";
	}
	//print_R($ALLNewFileName);
	//print "<BR><BR><h2 style='text-align:center;'>Rename NFTs Successful</h2>";
	//print "<META HTTP-EQUIV=REFRESH CONTENT='3;URL=?'>";
	exit;
}

if($_GET['action']=="DownloadMetaFile"&&$CurrentSettingSupportMaxNFTs==count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	PackagingMultiMetaDataFilesToOneFile($SKYNFT_MINT_TOOL_HASH);
	//print "<BR><BR><h2 style='text-align:center;'>Download Meta data file Successful</h2>";
	//print "<META HTTP-EQUIV=REFRESH CONTENT='3;URL=?action=MakeNFTsData'>";
	exit;
}

if($_GET['action']=="DownloadNftImage"&&$CurrentSettingSupportMaxNFTs==count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	PackagingMultiNftImageFilesToOneFile($SKYNFT_MINT_TOOL_HASH);
	//print "<BR><BR><h2 style='text-align:center;'>Download Meta data file Successful</h2>";
	//print "<META HTTP-EQUIV=REFRESH CONTENT='3;URL=?action=MakeNFTsData'>";
	exit;
}

function MakeNFTsData($SKYNFT_MINT_TOOL_HASH,$MAP,$PART,$EveryTimeMakeNFTs,$SKYNFT_MINT_CalculateAllRandomNumber) {
	global $redis;
	$HAVE_MADE = $redis->hget("SKYNFT_MINT_HAVE_MADE",$SKYNFT_MINT_TOOL_HASH);
	$HAVE_MADE = json_decode($HAVE_MADE,true);
	if($HAVE_MADE=="") $HAVE_MADE = [];
	Page_title("SkyNFT Mint Tool");
	$redis->hset("SKYNFT_MINT_HAVE_MADE",$SKYNFT_MINT_TOOL_HASH,json_encode($HAVE_MADE));
	if(count($HAVE_MADE)>=$MAP['MaxNFTNumber']) {
		print "<BR><BR><h2 style='text-align:center;'>Have made all need NFT images. <BR><BR>Will automate return to index page in 5 minutes.</h2>";
		print "<META HTTP-EQUIV=REFRESH CONTENT='5;URL=?'>";
		exit;
	}
	
	$UnMintList = array_diff($SKYNFT_MINT_CalculateAllRandomNumber,$HAVE_MADE);
	$UnMintList = array_values($UnMintList);
	shuffle($UnMintList);
	$UnMintListSize = sizeof($UnMintList);
	if($UnMintListSize>$EveryTimeMakeNFTs) {
		$NeedToMade = array_slice($UnMintList, 0, $EveryTimeMakeNFTs);   
		//print_R($NeedToMade);
		foreach($NeedToMade AS $INDEX)	{
			$HAVE_MADE[] = MakeNFTsDataFromIndex($SKYNFT_MINT_TOOL_HASH,$MAP,$PART,$INDEX);
		}
	}
	$redis->hset("SKYNFT_MINT_HAVE_MADE",$SKYNFT_MINT_TOOL_HASH,json_encode($HAVE_MADE));
	return count($HAVE_MADE);
}
function MakeNFTsDataFromIndex($SKYNFT_MINT_TOOL_HASH,$MAP,$PART,$INDEX) {
	global $redis;
	$INDEX_ARRAY = explode(" ",$INDEX);
	#Frist Png	
	$FileName  = "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/UploadImage_1_".$INDEX_ARRAY[0].".png";
	if(!is_file($FileName))  { return $FileName.' Not Exist.';}	
	//print $FileName."<BR>";
	$image_dst_size = getimagesize($FileName);
	$image_dst_w = $image_dst_size[0];
	$image_dst_h = $image_dst_size[1];
	$image_dst = imagecreatefrompng($FileName);
	imagesavealpha($image_dst, true);
	#Other layer
	for($i=1;$i<sizeof($INDEX_ARRAY);$i++)		{
		$PartIndex = $i;
		$PartValue = $INDEX_ARRAY[$i];
		$FileName  = "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/UploadImage_".($PartIndex+1)."_".$PartValue.".png";
		//print $FileName."<BR>";
		if(is_file($FileName))  {
			$image_src_size = getimagesize($FileName);
			$image_src = imagecreatefrompng($FileName);
			imagesavealpha($image_src, true);
			imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, $image_dst_w, $image_dst_h, $image_src_size[0], $image_src_size[1]);
		}		
	}
	#Make Png
	$INDEXNAME = str_replace(" ","_",$INDEX);
	$FileName  = "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile/".$INDEXNAME.".png";
	if(is_file($FileName))  { unlink($FileName);}	
	imagepng($image_dst,$FileName);
	print "<img src='".$FileName."?time=".time()."' width=180px border=0 style='margin:10px;'>";
	return $INDEX;
}


Page_title("SkyNFT Mint Tool");

print "
<script src=\"https://skynft.org/layuiadmin/layui/layui.js\"></script>  
<script src=\"https://skynft.org/layuiadmin/form-attribute-input.js\"></script> 
<form name=form0 method=post action='?action=setting'>
	<table class='TableList' width='80%' align=center>
		<tr class=TableHeader>
			<td nowrap class=TableHeader colspan=4 >SkyNFT Mint Tool</td>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData colspan=1 >SKYNFT_MINT_TOOL_HASH</td>
			<td nowrap class=TableData colspan=3><input class='layui-input readonly' disabled name='SKYNFT_MINT_TOOL_HASH' value='".$SKYNFT_MINT_TOOL_HASH."'>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData>Parts Number:</td><td nowrap class=TableData><input type='number' class='layui-input' layui-filter='number' name='PartsNumber' value='".$MAP['PartsNumber']."'></td>
			<td nowrap class=TableData>Nft Name:</td><td nowrap class=TableData><input class='layui-input' name='NFTNAME' value='".$MAP['NFTNAME']."'></td>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData>Max NFT Number</td><td nowrap class=TableData><input type='number' class='layui-input' layui-filter='number' name='MaxNFTNumber' value='".$MAP['MaxNFTNumber']."'></td>
			<td nowrap class=TableData>ID Rules:</td><td nowrap class=TableData><input class='layui-input' name='IDRULES' value='".$MAP['IDRULES']."'></td>
		</tr>
		";
		/*
print "
		<tr class=TableData>
			<td nowrap class=TableData>collection</td><td nowrap class=TableData><input type='input' class='layui-input' name='collection' value='".$MAP['collection']."'></td>
			<td nowrap class=TableData>description</td><td nowrap class=TableData><textarea name='description' class='layui-textarea' style='min-height:50px;'>".$MAP['description']."</textarea></td>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData>icon</td><td nowrap class=TableData><input type='input' class='layui-input' name='icon' value='".$MAP['icon']."'></td>
			<td nowrap class=TableData>banner</td><td nowrap class=TableData><input type='input' class='layui-input' name='banner' value='".$MAP['banner']."'></td>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData>twitter</td><td nowrap class=TableData><input type='input' class='layui-input' name='twitter' value='".$MAP['twitter']."'></td>
			<td nowrap class=TableData>website</td><td nowrap class=TableData><input type='input' class='layui-input' name='website' value='".$MAP['website']."'></td>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData>discord</td><td nowrap class=TableData><input type='input' class='layui-input' name='discord' value='".$MAP['discord']."'></td>
			<td nowrap class=TableData>instagram</td><td nowrap class=TableData><input type='input' class='layui-input' name='instagram' value='".$MAP['instagram']."'></td>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData>NftUrlPath</td><td nowrap class=TableData><input type='input' class='layui-input' name='NftUrlPath' value='".$MAP['NftUrlPath']."'></td>
			<td nowrap class=TableData>MetaUrlPath</td><td nowrap class=TableData><input type='input' class='layui-input' name='MetaUrlPath' value='".$MAP['MetaUrlPath']."'></td>
		</tr>
		";
		*/
print "
		<tr class=TableData>
			<td nowrap class=TableData colspan=4 style='height:28px;text-align:center;'><input type=submit class='layui-btn layui-btn-sm' name='submit' value='Save NFT Configuration (Step 1)'></td>
		</tr>
		<tr class=TableData>
			<td nowrap class=TableData colspan=4 >
			Trips:<BR>
			1 All file format requirements are PNG.<BR>
			2 The height and width of all files must be the same, and are based on the size of the first image.<BR>
			3 Your data will save 3 days. your can access https://".$_SERVER['HTTP_HOST']."/skynft_mint_tool.php?SKYNFT_MINT_TOOL_HASH=".$SKYNFT_MINT_TOOL_HASH." when close your broswer.<BR>
			".$RequiredWidthHtml."<BR>
			<font color=blue>Max Make Nfts Number: ".Count($SKYNFT_MINT_CalculateAllRandomNumber)."</font><BR>
			</td>
		</tr>
	</table>
</form>

";

print "<form name=form".$i." method=post action='?action=setpart' encType=multipart/form-data>";

print "<HR><HR><BR><div style='text-align:center;'>";
print "<input type=submit class='layui-btn layui-btn-sm' name='submit' value='Save Part Configuration (Step 2)'>";
if($CurrentSettingSupportMaxNFTs==count($SKYNFT_MINT_CalculateAllRandomNumber)) {
	$SKYNFT_MINT_HAVE_MADE_NFT_IMAGE = $redis->hget("SKYNFT_MINT_HAVE_MADE_NFT_IMAGE",$SKYNFT_MINT_TOOL_HASH);
	print "<input type=button class='layui-btn layui-btn-sm layui-btn-danger' name='MakeNFTsData' value='Make NFTs Images (Step 3)' Onclick=\"location='?action=MakeNFTsData'\">";
	print "<input type=button class='layui-btn layui-btn-sm layui-btn-danger' name='RenameNFTs' value='Rename NFTs (Step 4)' Onclick=\"location='?action=RenameNFTs'\">";
	print "<input type=button class='layui-btn layui-btn-sm layui-btn-danger' name='DownloadNftImage' value='Download Nft Images (Step 5)' Onclick=\"location='?action=DownloadNftImage'\" title='After click, just wait, will reponse in one or two minutes.'>";
	//$SKYNFT_MINT_HAVE_MADE_META_FILE = $redis->hget("SKYNFT_MINT_HAVE_MADE_META_FILE",$SKYNFT_MINT_TOOL_HASH);
	//print "<input type=button class='layui-btn layui-btn-sm layui-btn-normal' name='MakeMetaFile' value='Make Meta File' Onclick=\"location='?action=MakeMetaFile'\">";
	//print "<input type=button class='layui-btn layui-btn-sm layui-btn-normal' name='DownloadMetaFile' value='Download Meta Files' Onclick=\"location='?action=DownloadMetaFile'\">";
	//print "<input type=button class='layui-btn layui-btn-sm layui-btn-normal' name='ViewMintCommand' value='View Mint Command' Onclick=\"location='?action=ViewMintCommand'\">";
	print "<BR>
		<table class='TableList' width='80%' align=center>
			<tr class=TableData>
				<td  class=TableData colspan=4 >
					<font color=green>When your prepare the nft images, you can login <a href='https://skynft.org' target=_blank><font color=red>SkyNFT.org</font></a>, deposit 0.001 XCH<font color=gray>(if you not have, pls contact us)</font>, create a collection, and upload these nft images, click 'Mint All' button, just wait for mint process finish. Do not forget give your nft collection a good storyline.</font>
				</td>
			</tr>
		</table>";
}
else	{
	print "<input type=button class='layui-btn layui-btn-sm layui-btn-normal' name='CalculateAllRandomNumber' value='Calculate All Random Number' Onclick=\"location='?action=CalculateAllRandomNumber'\">";
}
print "</div><BR>";

for($i=1;$i<=$MAP['PartsNumber'];$i++)					{
	print "
		<table class='TableList' width='80%' align=center>
			<tr class=TableHeader>
				<td nowrap class=TableHeader colspan=4 >NFT Part ID: ".$i."</td>
			</tr>
			<tr class=TableData>
				<td nowrap class=TableData colspan=1 >Part Name:</td>
				<td nowrap class=TableData colspan=1><input class='layui-input' name='PartName_".$i."' value='".$PART["PartName_".$i]."'>
				<td nowrap class=TableData colspan=1 >Value Number:</td>
				<td nowrap class=TableData colspan=1><input type='number' class='layui-input' name='ValueNumber_".$i."' value='".$PART["ValueNumber_".$i]."'>
			</tr>
	";
	for($X=1;$X<=$PART["ValueNumber_".$i];$X++)					{
		print "
			<tr class=TableData>
				<td nowrap class=TableData>Parts Value ".$X.":</td><td nowrap class=TableData><input type='input' class='layui-input' layui-filter='number' name='PartValue_".$i."_".$X."' value='".$PART["PartValue_".$i."_".$X]."'></td>
				<td nowrap class=TableData>
					<img class=\"layui-upload-img\" id=\"PartImageSrc_".$i."_".$X."\" style=\"max-width:60px;\" src=\"".$PART["PartImage_".$i."_".$X]."\">
					<p id=\"PartTrip_".$i."_".$X."\"></p>
				</td>
				<td nowrap class=TableData>
					<button class='layui-btn layui-btn-sm' type='button' id='UploadImage_".$i."_".$X."'>Upload Part Images</button>
					<input type=hidden id=\"PartImage_".$i."_".$X."\" name=\"PartImage_".$i."_".$X."\" value=\"".$PART["PartImage_".$i."_".$X]."\">
				</td>
				<script>
				layui.config({
					base: 'https://skynft.org/layuiadmin/' 
				  }).extend({
				  }).use(['upload'], function(){
					var $ = layui.$
					,upload = layui.upload;
					
					var uploadInst = upload.render({
					elem: '#UploadImage_".$i."_".$X."'
					,url: '?action=uploadimage&PARTID=UploadImage_".$i."_".$X."'
					,before: function(obj){
					  $('#PartImageSrc_".$i."_".$X."').attr('src', '');
					}
					,done: function(res){
					  console.log(res)
					  if(res.code > 0){
						  $('#PartTrip_".$i."_".$X."').val(res.msg);
						  return layer.msg(res.msg,{icon: 5});
					  }
					  else	{						  
						  $('#PartImageSrc_".$i."_".$X."').attr('src', res.filename+'?'+Math.random());
						  $('#PartImage_".$i."_".$X."').val(res.filename);
						  $('#PartTrip_".$i."_".$X."').val(res.msg);
						  return layer.msg(res.msg,{icon: 1});
					  }
					}
				  });
			  
			  });
				</script>
			</tr>
			";
	}
	print "</table>";
}

print "</form>";





?>