<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$cacheok=loadcache('v50rewards');
if(empty($_G['cache']['v50rewards'])){
	$rewards=C::t('#v50miner#rewards')->get_all_rewards();
	save_syscache('v50rewards',$rewards);
} else {
	$rewards=$_G['cache']['v50rewards'];
}
$leng = 1;
$chance=[0];
foreach ($rewards as $item) {
	$leng+=$item['chance'];
	$chance[]=$item['chance'];
}
$savecat=explode(PHP_EOL,$_G['cache']['plugin']['v50miner']['miner_save_cat']);
$savecat=array_map(function($val){return trim($val);},$savecat);
//var_export($miner_chance);
function getChance($id){
	global $rewards,$leng;
	return number_format($rewards[$id]['chance']/$leng*100,2).'%';
}
function doMine($ten=false){
	function checkLvl($arr){
		global $savecat;
		foreach ($arr as $val) {
			if(in_array($val['lvl'],$savecat)){
				return true;
			}
		}
		return false;
	}
	global $rewards,$chance;
	$result=[];
	if($ten){
		for ($i=0; $i < 9; $i++) { 
			$key=randReward($chance);
			$result[]=$rewards[$key];
		}
		if(checkLvl($result)){
			$key=randReward($chance);
			$result[]=$rewards[$key];
		} else {
			$save_rewards=array_filter($rewards,function($item){
				global $savecat;
				return in_array($item['lvl'],$savecat);
			});
			$save_chance=[];
			foreach ($save_rewards as $item) {
				$save_chance[]=$item['chance'];
			}
			$key=randReward($save_chance);
			$result[]=array_slice($save_rewards,$key,1)[0];
		}
	} else {
		$key=randReward($chance);
		$result[]=$rewards[$key];
	}
	return $result;
}
function randReward($arr) {
	$calcleng=1;
	foreach ($arr as $chance) {
		$calcleng+=$chance;
	}
	foreach ($arr as $key => $chance) {
		$mtrand=mt_rand()/mt_getrandmax();
		$random=floor($mtrand*$calcleng);
		if($random<$chance){
			return $key;
		} else {
			$calcleng-=$chance;
		}
	}
}
function minerAction($str,$uid){
	$argarr=explode('|',$str);
	$args=array_slice($argarr,1);
	call_user_func('minerAction_'.$argarr[0],$uid,$args);
}
function minerAction_none($uid,$args){
	return;
}
function minerAction_magic($uid,$args){
	if(C::t('common_member_magic')->count_magic($uid, $args[0])) {
		C::t('common_member_magic')->increase($uid, $args[0], array('num' => $args[1]), false, true);
	} else {
		C::t('common_member_magic')->insert(array(
			'uid' => $uid,
			'magicid' => $args[0],
			'num' => $args[1]
		));
	}
}
function minerAction_count($uid,$args){
	global $_G;
	if($_GET['domine']==1){
		$magicid=$_G['cache']['plugin']['v50miner']['miner_magic_one'];
	} else if($_GET['domine']==2) {
		$magicid=$_G['cache']['plugin']['v50miner']['miner_magic_ten'];
	}
	updatemembercount($uid, array($args[0] => $args[1]), 1, 'MRC',$magicid);
}
function minerAction_medal($uid,$args){
	$medalid=$args[0];
	$mymedals = C::t('common_member_medal')->count_by_uid_medalid($uid,$medalid);
	if ($mymedals!=0){
		$new_action=array_splice($args,1);
		if(!empty($new_action)){
			minerAction(implode('|',$new_action),$uid);
		}
		return true;
	};
	$medal = C::t('forum_medal')->fetch($medalid);
    $memberfieldforum = C::t('common_member_field_forum')->fetch($uid);
    $usermedal = $memberfieldforum;
    unset($memberfieldforum);
    $medalnew = $usermedal['medals'] ? $usermedal['medals']."\t".$medal['medalid'] : $medal['medalid'];
    C::t('common_member_field_forum')->update($uid, array('medals' => $medalnew));
	C::t('common_member_medal')->insert(array('uid' => $uid, 'medalid' => $medal['medalid']), 0, 1);
	C::t('forum_medallog')->insert(array(
        'uid' => $uid,
        'medalid' => $medalid,
        'type' => $medal['type'],
        'dateline' => TIMESTAMP,
        'expiration' => 0,
        'status' => 0,
    ));
    return true;
}