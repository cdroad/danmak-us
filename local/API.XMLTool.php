<?php if (!defined('PmWiki')) exit();
if (!empty($_REQUEST['group']))
{
	$DMF_Group = ($_REQUEST['group'] == 'Bilibili2') ? "Bilibili2" : "Acfun2";
} else {
	$DMF_Group = FALSE;
}

include_once("./cookbook/dmf_exp/lib.XML.php");
// 下载仅动态池XML是属于DMR组的功能
$XMLToolHandles = array(
	"Validate"  => "admin",
	"XMLLoad"   => "read" ,   // 具体权限判断在函数体内
	'PoolConv'	=> 'admin',
	'PoolClear' => 'admin',
	'PairConv'	=> 'read' ,
);

foreach ($XMLToolHandles as $Handles => $Auth)
{
	$HandleActions[$Handles] = "Handle$Handles";
	$HandleAuth[$Handles] = $Auth;
}

function HandlePairConv($pagename, $auth = 'read')
{
	/*
	//转换所有XML到DMF用统一XML格式
	//Acfun2 假定不存在动态，只处理静态
	$AcfunDanmakuDir = opendir('./uploads/Acfun2/');
	while ($file = readdir($AcfunDanmakuDir))
		{
			if (strtolower(pathinfo("$file", PATHINFO_EXTENSION)) != "xml")
			{
				continue;
			}
			
			$id = pathinfo("$file", PATHINFO_FILENAME);
			$oldPair = new AcfunOldDanmakuPair($id, PAIR_STATIC);
			$newSPairObj = ConvertAcfunXML_Data($oldPair->get(PAIR_STATIC));
			$newPair = new AcfunUniDanmakuPair_Data($id, PAIR_NONE);
			$newPair->set(PAIR_STATIC, $newSPairObj);
			$newPair->save(PAIR_STATIC);
		}
	$file = NULL;
	closedir($AcfunDanmakuDir);
	*/
	
	//转换Bilibili2 转换静态和动态
	///静态
	
	$BilibiliDanmakuDir = opendir('./uploads/Bilibili2/');
	$BilibiliPairIdArray = array();
	while ($file = readdir($BilibiliDanmakuDir))
		{
			if (strtolower(pathinfo("$file", PATHINFO_EXTENSION)) != "xml")
			{
				continue;
			}
			$id = pathinfo("$file", PATHINFO_FILENAME);
			
			$oldPair = new BiliNorDanmakuPair($id, PAIR_STATIC);
			$SPair = $oldPair->get(PAIR_STATIC);
			$newSPairObj = ConvertBilibiliXML_d($SPair);
			$newPair = new BiliUniDanmakuPair($id);
			$newPair->PAIR_STATIC = $newSPairObj;
			$newPair->save(PAIR_STATIC);
		}
	/*
	///动态
	//TODO
	$BilibiliDPoolPages = ListPages("/DMR\.B/");
	foreach ($BilibiliDPoolPages as $pn)
	{
		if ($pn == 'DMR.Bilibili')
			continue;
		$id = substr(pathinfo($pn, PATHINFO_EXTENSION), 1);
		
			$oldPair = new BiliNorDanmakuPair($id, PAIR_DYNAMIC);
			$DPair = $oldPair->get(PAIR_DYNAMIC);
			$newDPairObj = ConvertBilibiliXML_dD($DPair);
			$newPair = new BiliUniDanmakuPair($id, PAIR_NONE);
			$newPair->PAIR_DYNAMIC =  dom_import_simplexml($newDPairObj)->ownerDocument;
			$newPair->save(PAIR_DYNAMIC);
	}
	*/

	$pp = 'Site.DMFPerfs';
	$n = $p = ReadPage($pp);
	$n['isXMLConverted'] = 'YES';
	UpdatePage($pp, $p, $n);
	$GLOBALS['MessagesFmt'] = '弹幕转换完毕，可以继续使用。';
	HandleBrowse('API.XMLTool');

}

function HandlePoolConv($pagename, $auth = 'admin')
{
	global $DMF_Group;
	//TODO:
	myAssert(!empty($DMF_Group), "DMF_Group Not Defined.");
	
	$id = basename($_REQUEST['dmid']);
	
	$Func = "doPoolConv_$DMF_Group";
	myAssert(function_exists($Func), "Pool Conv Function Not Exists.");
	
	$Pool = $_POST['Pool'];
	switch (strtoupper($Pool))
	{
		case "SD":
			$target = PAIR_DYNAMIC;
			$from = PAIR_STATIC;
			break;
		case "DS":
			$target = PAIR_STATIC;
			$from = PAIR_DYNAMIC;
			break;
		default:
			myAssert(FALSE, "Unexpected Pool Conv OP : $Pool.");
			return;
	}
	
	$Before = microtime(true);
	$Func($id, $from, $target);
	$T = microtime(true) - $Before;
	$GLOBALS['MessagesFmt'] = '弹幕池转换'.$_POST['Pool']."过程结束. 用时：$T 秒";
	HandleBrowse($pagename);
}

function HandleXMLLoad($pagename, $auth = 'read')
{
	global $DMF_Group;
	
	myAssert(!empty($DMF_Group), "DMF_Group Not Defined.");

	$id = basename($_REQUEST['dmid']);
	
	$Func = "doXMLLoad_$DMF_Group";
	myAssert(function_exists($Func), "Pool Conv Function Not Exists.");
	
	ob_start("ob_gzhandler");
	
	if ($_GET['cmd'] == "download")
	{
		header("Content-type: ".
			"application/octet-stream");
		header("Content-disposition: ".
			"attachment; filename=\"".$id.".xml\"");
	} else {
		//header("Content-Type: text/plain; charset=utf-8");
	}
	
	$before = microtime(TRUE);
	$Func($id);
	$exetime = microtime(true) - $before;
	echo "\n<!--XML generated in $exetime seconds.-->";
	
	ob_end_flush();
	exit;
}

function HandleValidate($pagename, $auth = 'admin')
{
	global $DMF_Group;
	
	myAssert(!empty($DMF_Group), "DMF_Group Not Defined.");
	
	$id = basename($_REQUEST['dmid']);
	
	$Func = "doValidate_$DMF_Group";
	myAssert(function_exists($Func), "Pool Conv Function Not Exists.");
		
	$Func($id, PAIR_ALL);
	HandleBrowse($pagename);
}

function HandlePoolClear($pagename, $auth = 'admin')
{
	global $DMF_Group;
	global $MessagesFmt;
	
	myAssert(!empty($DMF_Group), "DMF_Group Not Defined.");
	
	$id = basename($_REQUEST['dmid']);
	$Func = "doXMLLoad_$DMF_Group";
	myAssert(function_exists($Func), "Pool Conv Function Not Exists.");
	switch (strtoupper($_POST['Pool']))
	{
		case 'S':
			$DMPair = PAIR_STATIC;
			break;
		case 'D':
			$DMPair = PAIR_DYNAMIC;
			break;
		case 'ALL':
			$DMPair = PAIR_ALL;
			break;
		default:
			$DMPair = PAIR_NONE;
			break;
	}
	$DMPair = ($_POST['Pool'] == 'S') ? PAIR_STATIC : PAIR_DYNAMIC ;
	
	$Pair = new BiliUniDanmakuPair($id, $DMPair);
	$Pair->clearPool($DMPair);
	$Pair->save($DMPair);
	$MessagesFmt = "清空弹幕池 $DMF_Group :: $id :: $DMPair 完毕。";
	
	HandleBrowse($pagename);
}