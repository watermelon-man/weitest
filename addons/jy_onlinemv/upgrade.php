<?php
if(!pdo_tableexists('ims_jy_onlinemv_camilo')){
    $sql ="CREATE TABLE ".tablename('ims_jy_onlinemv_camilo')." (
      	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      	`weid` int(11) unsigned NOT NULL,
		`cmname` varchar(255) NOT NULL,
		`cmprice` varchar(255) NOT NULL,
		`createtime` varchar(100) NOT NULL,
		`updatetime` varchar(100) NOT NULL,
		`status` tinyint(4) unsigned NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    pdo_query($sql);
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'monthprice')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `monthprice` float(11,2) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'yearprice')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `yearprice` float(11,2) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'monthly')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `monthly` tinyint(3) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'package')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `package` tinyint(3) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'pack')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `pack` tinyint(3) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'unitprice')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `unitprice` tinyint(3) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'buynow')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `buynow` varchar(255) NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'topbanner')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `topbanner` varchar(255) NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_setting', 'camilo')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_setting')." ADD `camilo` tinyint(3) unsigned NOT NULL;");
}

if(!pdo_fieldexists('jy_onlinemv_video', 'vprice')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_video')." ADD `vprice` float(11,2) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_order', 'paytype')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_order')." ADD `paytype` tinyint(4) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_order', 'paymethod')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_order')." ADD `paymethod` tinyint(4) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_order', 'paystat')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_order')." ADD `paystat` tinyint(4) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_records', 'paytype')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_records')." ADD `paytype` tinyint(4) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_records', 'paymethod')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_records')." ADD `paymethod` tinyint(4) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_records', 'paystat')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_records')." ADD `paystat` tinyint(4) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_records', 'buytime')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_records')." ADD `buytime` varchar(255) NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_records', 'paytype')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_records')." ADD `paytype` tinyint(4) unsigned NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_records', 'camiloprice')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_records')." ADD `camiloprice` varchar(255) NOT NULL ;");
}

if(!pdo_fieldexists('jy_onlinemv_records', 'camiloname')) {
	pdo_query("ALTER TABLE ".tablename('jy_onlinemv_records')." ADD `camiloname` varchar(255) NOT NULL ;");
}