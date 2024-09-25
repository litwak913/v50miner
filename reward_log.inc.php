<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
require_once libfile("miner_func",'plugin/v50miner');
$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$perpage = 10;
$start = ($page-1)*$perpage;
if($_GET['uid']){
    $items=C::t('#v50miner#log')->query_page(intval($_GET['uid']),$start,$perpage);
    $count=C::t('#v50miner#log')->count_user(intval($_GET['uid']));
} else {
    $items=C::t('#v50miner#log')->query_page_all($start,$perpage);
    $count=C::t('#v50miner#log')->count();
}
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
$luid = array();
foreach($items_new as $item) {
    $luid[$item['uid']] = $item['uid'];
}
$users=C::t('common_member')->fetch_all($luid);
$multi = multi($count, $perpage, $page, ADMINSCRIPT."?action=plugins&operation=config&identifier=v50miner&pmod=reward_log",0,3);
showtableheader('', 'fixpadding');
showtablerow('class="header"', array('class="td24"','class="td24"',''), array(
    '用户',
    '时间',
    '奖品'
));
foreach($items_new as $item){
    showtablerow('', array('class="bold"'), array($users[$item['uid']]['username'].'(UID:'.$item['uid'].')',$item['mineat'],$item['items']));
}
$uidsearch='<input type="text" class="txt marginleft10" name="uid" placeholder="UID" value="'.$_GET['uid'].'" /><input type="submit" class="btn" value="搜索" />';
showtablefooter();
showformheader("plugins&operation=config&identifier=v50miner&pmod=reward_log");
showtableheader('', 'fixpadding');
showsubmit('','submit','','',$multi.$uidsearch);
showtablefooter();
showformfooter();

