<?php
$sql = "CREATE TABLE IF NOT EXISTS ".tablename('iweite_sxbm_setting')."(
  `tid` int(10) NOT NULL AUTO_INCREMENT,
  `weid` int(4) DEFAULT '0',
  `followurl` mediumtext,
  `share` mediumtext,
  `guanzhu` mediumtext,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

pdo_query($sql);

$sql = "CREATE TABLE IF NOT EXISTS ".tablename('iweite_sxbm')." (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(350) DEFAULT NULL,
  `username` varchar(300) DEFAULT NULL,
  `dateline` varchar(350) DEFAULT NULL,
  `content` mediumtext,
  `weid` int(4) DEFAULT '0',
  KEY `tid` (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

pdo_query($sql);

?>