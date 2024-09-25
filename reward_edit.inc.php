<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
$rewards=C::t('#v50miner#rewards')->get_all_rewards();
if(!submitcheck('rewardsubmit')) {
    $rewardrow='';
    foreach ($rewards as $key => $value) {
        $rewardrow .= showtablerow('', array('class="td25"', '','class="td22"','','','class="td29"','class="td26"'), array(
            "<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$key\">",
            "<span>$key</span>",
            "<input type=\"text\" class=\"txt\" size=\"5\" name=\"text[$key]\" value=\"$value[text]\">",
            "<input type=\"text\" class=\"txt\" size=\"5\" name=\"lvl[$key]\" value=\"$value[lvl]\">",
            "<input type=\"text\" class=\"txt\" size=\"5\" name=\"chance[$key]\" value=\"$value[chance]\">",
            "<input type=\"text\" class=\"txt\" size=\"5\" name=\"raction[$key]\" value=\"$value[action]\">",
            "<input type=\"text\" class=\"txt\" size=\"10\" name=\"img[$key]\" value=\"$value[img]\">","<img src=\"source/plugin/v50miner/template/images/$value[img]\" />"
        ), TRUE);
    }
    echo <<<EOT
<script type="text/JavaScript">
	var rowtypedata = [
		[
			[1, '', 'td25'],
            [1,'',''],
			[1, '<input type="text" class="txt" size="2" name="newtext[]">', 'td22'],
			[1, '<input type="text" class="txt" size="5" name="newlvl[]">'],
			[1, '<input type="text" class="txt" size="5" name="newchance[]">'],
            [1, '<input type="text" class="txt" size="5" name="newraction[]">','td29'],
            [1, '<input type="text" class="txt" size="10" name="newimg[]">','td22'],
		],
	];
</script>
EOT;
showtips(<<<EOT
<li>动作参数格式：action名|参数1|参数2...</li>
<li>可用的action：medal,count,magic,none</li>
<li>奖励道具 magic|道具ID|数量</li>
<li>积分增减 count|积分字段|数量</li>
<li>颁发勋章 medal|勋章ID|替代动作</li>
<li>如medal|1|count|extcredit1|50 表示发放勋章1，若存在则增加extcredit1积分50</li>
<li>无操作 none</li>
EOT);
    showformheader("plugins&operation=config&identifier=v50miner&pmod=reward_edit");
    showtableheader('奖品编辑');
    showsubtitle(array('', 'ID','名称', '分类', '机率','动作','图片'));
    echo $rewardrow;
    echo '<tr><td></td><td colspan="4"><div><a href="###" onclick="addrow(this, 0)" class="addtr">添加新奖品</a></div></td></tr>';
    showsubmit('rewardsubmit','submit','del');
    showtablefooter();
    showformfooter();
} else {
    if($ids = dimplode($_GET['delete'])) {
        DB::query("DELETE FROM ".DB::table('v50miner_rewards')." WHERE id IN ($ids)");
    }
    if(is_array($_GET['text'])){
        foreach ($_GET['text'] as $key => $value) {
            C::t('#v50miner#rewards')->set_reward($key,
            $_GET['text'][$key],
            $_GET['lvl'][$key],
            $_GET['img'][$key],
            $_GET['chance'][$key],
            $_GET['raction'][$key]);
        }
    }
    if(is_array($_GET['newtext'])){
        foreach ($_GET['newtext'] as $key => $value) {
            C::t('#v50miner#rewards')->add_reward(
            $_GET['newtext'][$key],
            $_GET['newlvl'][$key],
            $_GET['newimg'][$key],
            $_GET['newchance'][$key],
            $_GET['newraction'][$key]);
        }
    }
    $newrewards=C::t('#v50miner#rewards')->get_all_rewards();
    save_syscache('v50rewards',$newrewards);
    cpmsg('奖品已更新', '', 'succeed');
}
