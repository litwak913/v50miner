<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<SQL
CREATE TABLE IF NOT EXISTS cdb_v50miner_rewards (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `text` varchar (128) NOT NULL DEFAULT '',
  `lvl` varchar(64) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '', 
  `chance` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `action` varchar(255) NOT NULL DEFAULT '', 
  PRIMARY KEY (`id`)
) ENGINE=INNODB;
CREATE TABLE IF NOT EXISTS cdb_v50miner_logs (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `items` text NOT NULL DEFAULT '',
  `mineat` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=INNODB;
SQL;

runquery($sql);

$finish = TRUE;

?>