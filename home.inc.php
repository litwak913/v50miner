<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}
require_once libfile("miner_func",'plugin/v50miner');
loadcache('magics');

$navtitle="挖矿";
$onemagicid=$_G['cache']['plugin']['v50miner']['miner_magic_one'];
$tenmagicid=$_G['cache']['plugin']['v50miner']['miner_magic_ten'];
$debugmode=$_G['cache']['plugin']['v50miner']['miner_debug'];
$custom_html=$_G['cache']['plugin']['v50miner']['miner_custom_html'];
$custom_css='<style>'.$_G['cache']['plugin']['v50miner']['miner_custom_css'].'</style>';
$onemagictext=$_G['cache']['magics'][$onemagicid]['name'];
$tenmagictext=$_G['cache']['magics'][$tenmagicid]['name'];

$onemagiccount=C::t('common_member_magic')->fetch_magic($_G['uid'],$onemagicid)['num']??0;
$tenmagiccount=C::t('common_member_magic')->fetch_magic($_G['uid'], $tenmagicid)['num']??0;
if(submitcheck('domine')) {
	require libfile('function/magic');
	$mode=$_GET['domine'];
	$res=[];
	if($mode==1){
		if($onemagiccount<=0){
			showmessage("道具不足，请购买后重试",'home.php?mod=magic',[],['alert'=>'error']);
		}
		$res=doMine();
		if(!$debugmode){
			usemagic($onemagicid, $onemagiccount);
			updatemagiclog($onemagicid, '2', '1', '0', 0, 'uid', $_G['uid']);
		}
	} elseif ($mode==2){
		if($tenmagiccount<=0){
			showmessage("道具不足，请购买后重试",'home.php?mod=magic',[],['alert'=>'error']);
		}
		$res=doMine(true);
		if(!$debugmode){
			usemagic($tenmagicid, $tenmagiccount);
			updatemagiclog($tenmagicid, '2', '1', '0', 0, 'uid', $_G['uid']);
		}
	}
	$resids=[];
	foreach($res as $value){
		if(!$debugmode) minerAction($value['action'],$_G['uid']);
		$resids[]=$value['id'];
	}
	if(!$debugmode) C::t('#v50miner#log')->add_log($_G['uid'],	implode(',',$resids),TIMESTAMP);
} else {
switch ($_GET['action']) {
	case 'log':
		$page = empty($_GET['page'])?1:intval($_GET['page']);
		if($page<1) $page=1;
		$perpage = 10;
		$start = ($page-1)*$perpage;
		$items=C::t('#v50miner#log')->query_page($_G['uid'],$start,$perpage);
		$items_new=array_map(function ($var) {
			$logrew=explode(',',$var['items']);
			$logtxt=array_map(function ($rid){
				global $rewards;
				if(!empty($rewards[$rid])){
					return $rewards[$rid]['text'];
				} else {
					return "RID$rid";
				}
			},$logrew);
			$var['mineat']=dgmdate($var['mineat']);
			$var['items']=implode(',',$logtxt);
			return $var;
		},$items);
		$multi = simplepage(count($items_new), $perpage, $page, 'plugin.php?id=v50miner:home&action=log');
		break;
	case 'chance':
		$config_chance=explode(PHP_EOL,$_G['cache']['plugin']['v50miner']['miner_cat']);
		$chance=[];
		foreach ($config_chance as $value) {
			list($id,$text)=explode('|',$value);
			$chance[$id]=['text'=>$text];
		}
		foreach ($rewards as $key=>$value) {
			$chance[$value['lvl']]['items'][]=[
				'img'=>$value['img'],
				'text'=>$value['text'],
				'chance'=>getChance($key)
			];
		}
		break;
	default:
		break;
}
}
//echo $miner_reward[randReward()];
include template('v50miner:home');