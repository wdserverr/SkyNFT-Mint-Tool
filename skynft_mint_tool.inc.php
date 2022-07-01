<?
function Page_title($TITLE)			{
	ob_start();
	print "
	<!DOCTYPE html>
	<html>
	<head>
	  <meta charset=\"utf-8\">
	  <title>$TITLE</title>
	  <meta name=\"renderer\" content=\"webkit\">
	  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
	  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0\">
	  <link rel=\"stylesheet\" href=\"https://skynft.org/layuiadmin/layui/css/layui.pc.css\" media=\"all\">
	  <link rel=\"stylesheet\" href=\"https://skynft.org/layuiadmin/layui/css/tablelist.css\" media=\"all\">
	  <link rel=\"stylesheet\" href=\"https://skynft.org/layuiadmin/style/admin.css\" media=\"all\">
	  <script src=\"https://skynft.org/layuiadmin/layui/layui.js\"></script> 
	  <style>body {font: 14px Helvetica Neue,Helvetica,PingFang SC,Tahoma,Arial,sans-serif}</style>
	</head>
	<body>
	";
	$string = ob_get_contents();
	ob_flush();
	flush();
}


function Filter($str) {
	$str  = str_replace('"',"",$str);
	$str  = str_replace('?',"",$str);
	$str  = str_replace('*',"",$str);
	$str  = str_replace('$',"",$str);
	$str  = str_replace('%',"",$str);
	$str  = str_replace('^',"",$str);
	$str  = str_replace('+',"",$str);
	$str  = str_replace("'","",$str);
	$str  = str_replace("<","",$str);
	$str  = str_replace(">","",$str);
	$str  = str_replace("{","",$str);
	$str  = str_replace("}","",$str);
	$str  = str_replace("`","",$str);
	$str  = str_replace("!","",$str);
	$str  = str_replace("@","",$str);
	$str  = str_replace("$","",$str);
	$str  = str_replace("%","",$str);
	$str  = str_replace("&","",$str);
	$str  = str_replace("+","",$str);
	$str  = str_replace(":","",$str);
	$str  = str_replace("/","",$str);
	$str  = str_replace("|","",$str);
	$str  = str_replace("+","",$str);
	$str  = str_replace("\\","",$str);
	return $str;
}


function SafeSavePng($sourcefilename,$targetfilename)   {
	$im = imagecreatefrompng($sourcefilename); 
	if(is_file($targetfilename)) {
		unlink($targetfilename);
	}
	imagesavealpha($im, true);
	$result = imagepng($im,$targetfilename);   
	return $result;
}


function CalculateAllRandomNumber_1($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		$DataMapNew[] = "$i";
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_2($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			$DataMapNew[] = "$i $ii";
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_3($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				$DataMapNew[] = "$i $ii $iii";
			}
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_4($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				for($iiii=1;$iiii<=$PartValueArray[4];$iiii++)									{
					$DataMapNew[] = "$i $ii $iii $iiii";
				}
			}
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_5($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				for($iiii=1;$iiii<=$PartValueArray[4];$iiii++)									{
					for($iiiii=1;$iiiii<=$PartValueArray[5];$iiiii++)									{
						$DataMapNew[] = "$i $ii $iii $iiii $iiiii";
					}
				}
			}
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_6($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				for($iiii=1;$iiii<=$PartValueArray[4];$iiii++)									{
					for($iiiii=1;$iiiii<=$PartValueArray[5];$iiiii++)									{
						for($iiiiii=1;$iiiiii<=$PartValueArray[6];$iiiiii++)									{
							$DataMapNew[] = "$i $ii $iii $iiii $iiiii $iiiiii";
						}
					}
				}
			}
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_7($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				for($iiii=1;$iiii<=$PartValueArray[4];$iiii++)									{
					for($iiiii=1;$iiiii<=$PartValueArray[5];$iiiii++)									{
						for($iiiiii=1;$iiiiii<=$PartValueArray[6];$iiiiii++)									{
							for($iiiiiii=1;$iiiiiii<=$PartValueArray[7];$iiiiiii++)									{
								$DataMapNew[] = "$i $ii $iii $iiii $iiiii $iiiiii $iiiiiii";
							}
						}
					}
				}
			}
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_8($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				for($iiii=1;$iiii<=$PartValueArray[4];$iiii++)									{
					for($iiiii=1;$iiiii<=$PartValueArray[5];$iiiii++)									{
						for($iiiiii=1;$iiiiii<=$PartValueArray[6];$iiiiii++)									{
							for($iiiiiii=1;$iiiiiii<=$PartValueArray[7];$iiiiiii++)									{
								for($iiiiiiii=1;$iiiiiiii<=$PartValueArray[8];$iiiiiiii++)									{
									$DataMapNew[] = "$i $ii $iii $iiii $iiiii $iiiiii $iiiiiii $iiiiiiii";
								}
							}
						}
					}
				}
			}
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_9($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				for($iiii=1;$iiii<=$PartValueArray[4];$iiii++)									{
					for($iiiii=1;$iiiii<=$PartValueArray[5];$iiiii++)									{
						for($iiiiii=1;$iiiiii<=$PartValueArray[6];$iiiiii++)									{
							for($iiiiiii=1;$iiiiiii<=$PartValueArray[7];$iiiiiii++)									{
								for($iiiiiiii=1;$iiiiiiii<=$PartValueArray[8];$iiiiiiii++)									{
									for($iiiiiiiii=1;$iiiiiiiii<=$PartValueArray[9];$iiiiiiiii++)									{
										$DataMapNew[] = "$i $ii $iii $iiii $iiiii $iiiiii $iiiiiii $iiiiiiii $iiiiiiiii";
									}
								}
							}
						}
					}
				}
			}
		}
	}
	return $DataMapNew;
}
function CalculateAllRandomNumber_10($PartValueArray) {
	for($i=1;$i<=$PartValueArray[1];$i++)									{
		for($ii=1;$ii<=$PartValueArray[2];$ii++)									{
			for($iii=1;$iii<=$PartValueArray[3];$iii++)									{
				for($iiii=1;$iiii<=$PartValueArray[4];$iiii++)									{
					for($iiiii=1;$iiiii<=$PartValueArray[5];$iiiii++)									{
						for($iiiiii=1;$iiiiii<=$PartValueArray[6];$iiiiii++)									{
							for($iiiiiii=1;$iiiiiii<=$PartValueArray[7];$iiiiiii++)									{
								for($iiiiiiii=1;$iiiiiiii<=$PartValueArray[8];$iiiiiiii++)									{
									for($iiiiiiiii=1;$iiiiiiiii<=$PartValueArray[9];$iiiiiiiii++)									{
										for($iiiiiiiiii=1;$iiiiiiiiii<=$PartValueArray[10];$iiiiiiiiii++)									{
											$DataMapNew[] = "$i $ii $iii $iiii $iiiii $iiiiii $iiiiiii $iiiiiiii $iiiiiiiii $iiiiiiiiii";
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	return $DataMapNew;
}


function PackagingMultiMetaDataFilesToOneFile($SKYNFT_MINT_TOOL_HASH)			{
	$TargetDir 	= "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/metafile/";
	$DraftDir 	= "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/";
	$ZipFile	= $DraftDir."".$SKYNFT_MINT_TOOL_HASH."_metadta.zip";;
	if(!file_exists($ZipFile)){
		$zip = new ZipArchive();
		if ($zip->open($ZipFile, ZipArchive::CREATE)==TRUE) {
			if ($dh = opendir($TargetDir)) {
				while (($file = readdir($dh)) !== false) {
					if($file!="."&&$file!="..") {
						$zip->addFile($TargetDir.$file, $file);
					}
				}
				closedir($dh);
			}
			$zip->close();
		}
	}
	if(!file_exists($ZipFile)){
		exit("Can not found file.");
	}
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header('Content-disposition: attachment; filename=SkyNFTMintTool_'.$SKYNFT_MINT_TOOL_HASH.'_metadta.zip' ); //文件名
	header("Content-Type: application/zip"); //zip格式的
	header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
	header('Content-Length: '. filesize($ZipFile)); //告诉浏览器，文件大小
	@readfile($ZipFile);
	@unlink($ZipFile);
}


function PackagingMultiNftImageFilesToOneFile($SKYNFT_MINT_TOOL_HASH)			{
	$TargetDir 	= "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/nftfile/";
	$DraftDir 	= "./SkyNFTMintTool/".$SKYNFT_MINT_TOOL_HASH."/draft/";
	$ZipFile	= $DraftDir."".$SKYNFT_MINT_TOOL_HASH."_nftimages.zip";;
	if(!file_exists($ZipFile)){
		$zip = new ZipArchive();
		if ($zip->open($ZipFile, ZipArchive::CREATE)==TRUE) {
			if ($dh = opendir($TargetDir)) {
				while (($file = readdir($dh)) !== false) {
					if($file!="."&&$file!="..") {
						$zip->addFile($TargetDir.$file, $file);
					}
				}
				closedir($dh);
			}
			$zip->close();
		}
	}
	if(!file_exists($ZipFile))	{
		exit("Can not found file.");
	}
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header('Content-disposition: attachment; filename=SkyNFTMintTool_'.$SKYNFT_MINT_TOOL_HASH.'_nftimages.zip' ); //文件名
	header("Content-Type: application/zip"); //zip格式的
	header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
	header('Content-Length: '. filesize($ZipFile)); //告诉浏览器，文件大小
	@readfile($ZipFile);
	@unlink($ZipFile);
}

?>