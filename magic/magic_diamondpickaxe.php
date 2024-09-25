<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class magic_diamondpickaxe {

    var $version = '1.0';
    var $name = 'diamondpickaxe_name';
    var $description = 'diamondpickaxe_desc';
    var $price = '10';
    var $weight = '10';
    var $copyright = 'litwak913';
    var $magic = array();
	var $parameters = array();
    function getsetting(&$magic) {
	}

	function setsetting(&$magicnew, &$parameters) {
	}
    function usesubmit() {
	}

	function show() {
		echo '<script>window.href=window.location.href="plugin.php?id=v50miner:home"</script>';
	}

	function buy() {
	}
}