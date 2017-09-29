<?php
$sql = "
DROP TABLE IF EXISTS  `ims_imeepos_runner3_adv`;
CREATE TABLE `ims_imeepos_runner3_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `title` varchar(64) DEFAULT '',
  `image` varchar(300) DEFAULT '',
  `time` int(11) DEFAULT '0',
  `link` varchar(320) DEFAULT '',
  `status` tinyint(2) DEFAULT '0',
  `position` varchar(32) DEFAULT '',
  `openid` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `INDEX_OPENID` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_announcement`;
CREATE TABLE `ims_imeepos_runner3_announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `title` varchar(32) DEFAULT '',
  `link` varchar(320) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_buy`;
CREATE TABLE `ims_imeepos_runner3_buy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `freight` float(10,2) DEFAULT '0.00',
  `title` varchar(132) DEFAULT '',
  `buyprovince` varchar(32) DEFAULT '',
  `buycity` varchar(32) DEFAULT '',
  `province` varchar(32) DEFAULT '',
  `city` varchar(32) DEFAULT '',
  `address` varchar(132) DEFAULT '',
  `receivelon` varchar(32) DEFAULT '',
  `receivelat` varchar(32) DEFAULT '',
  `expectedtime` int(11) DEFAULT '0',
  `buyaddress` varchar(132) DEFAULT '',
  `sendlon` varchar(32) DEFAULT '',
  `sendlat` varchar(32) DEFAULT '',
  `other` varchar(320) DEFAULT '',
  `distance` int(11) DEFAULT '0',
  `taskid` int(11) DEFAULT '0',
  `limit_time` int(11) DEFAULT '0',
  `receiveaddress` varchar(132) DEFAULT '',
  `receivedetail` varchar(320) DEFAULT '',
  `receivemobile` varchar(32) DEFAULT '',
  `message` varchar(640) DEFAULT '',
  `receiverealname` varchar(32) DEFAULT '',
  `dianfu` tinyint(2) DEFAULT '0',
  `goodscost` decimal(10,2) DEFAULT '0.00',
  `goodstitle` varchar(32) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_category_setting`;
CREATE TABLE `ims_imeepos_runner3_category_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) DEFAULT '',
  `setting` text,
  `uniacid` int(11) DEFAULT '0',
  `category_id` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_category_field`;
CREATE TABLE `ims_imeepos_runner3_category_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `category_id` int(11) DEFAULT '0',
  `type` varchar(32) DEFAULT '',
  `title` varchar(32) DEFAULT '',
  `placeholder` varchar(32) DEFAULT '',
  `warning` varchar(32) DEFAULT '',
  `need` tinyint(2) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `options` varchar(800) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_category`;
CREATE TABLE `ims_imeepos_runner3_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(32) NOT NULL,
  `displayorder` int(11) unsigned NOT NULL DEFAULT '0',
  `desc` varchar(320) NOT NULL,
  `icon` varchar(320) NOT NULL,
  `task_num` int(11) unsigned NOT NULL DEFAULT '0',
  `setting` text NOT NULL,
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_UNIACID` (`uniacid`),
  KEY `IDX_DISPLAYORDER` (`displayorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_category_field_data`;
CREATE TABLE `ims_imeepos_runner3_category_field_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `field_id` int(11) DEFAULT '0',
  `task_id` int(11) DEFAULT '0',
  `value` varchar(320) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_category_task`;
CREATE TABLE `ims_imeepos_runner3_category_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `status` tinyint(2) DEFAULT '0',
  `total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_citys`;
CREATE TABLE `ims_imeepos_runner3_citys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `title` varchar(64) DEFAULT '',
  `lat` varchar(32) DEFAULT '',
  `lng` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_code`;
CREATE TABLE `ims_imeepos_runner3_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(32) DEFAULT '',
  `code` varchar(32) DEFAULT '',
  `time` int(11) DEFAULT '0',
  `content` varchar(320) DEFAULT '',
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_detail`;
CREATE TABLE `ims_imeepos_runner3_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `taskid` int(11) DEFAULT '0',
  `goodsweight` float(10,2) DEFAULT '0.00',
  `goodscost` float(10,2) DEFAULT '0.00',
  `goodsname` varchar(64) DEFAULT '',
  `sendprovince` varchar(32) DEFAULT '',
  `sendcity` varchar(32) DEFAULT '',
  `sendaddress` varchar(132) DEFAULT '',
  `receiveprovince` varchar(32) DEFAULT '',
  `receivecity` varchar(32) DEFAULT '',
  `receiveaddress` varchar(132) DEFAULT '',
  `pickupdate` int(11) DEFAULT '0',
  `sendlon` varchar(64) DEFAULT '',
  `sendlat` varchar(64) DEFAULT '',
  `receivelon` varchar(64) DEFAULT '',
  `receivelat` varchar(64) DEFAULT '',
  `distance` int(11) DEFAULT '0',
  `dataTimeValue` int(11) DEFAULT '0',
  `time` tinyint(2) DEFAULT '0',
  `base_fee` float(10,2) DEFAULT '0.00',
  `fee` float(10,2) DEFAULT '0.00',
  `total` float(10,2) DEFAULT '0.00',
  `small_money` float(10,2) DEFAULT '0.00',
  `senddetail` varchar(64) DEFAULT '',
  `receivedetail` varchar(320) DEFAULT '',
  `receivemobile` varchar(32) DEFAULT '',
  `receiverealname` varchar(32) DEFAULT '',
  `message` varchar(640) DEFAULT '',
  `images` varchar(1000) DEFAULT '',
  `float_distance` float(10,2) DEFAULT '0.00',
  `duration` varchar(120) DEFAULT '',
  `sendrealname` varchar(32) DEFAULT '',
  `sendmobile` varchar(64) DEFAULT '',
  `total_num` int(11) NOT NULL DEFAULT '1',
  `duration_value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_goods`;
CREATE TABLE `ims_imeepos_runner3_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `name` varchar(320) DEFAULT '',
  `weight` varchar(128) DEFAULT '',
  `price` decimal(10,2) DEFAULT '0.00',
  `detail` varchar(320) DEFAULT '',
  `create_at` int(11) DEFAULT '0',
  `class_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_idauth`;
CREATE TABLE `ims_imeepos_runner3_idauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `cardno` varchar(32) DEFAULT '',
  `code` int(11) DEFAULT '0',
  `birthday` varchar(32) DEFAULT '',
  `sex` varchar(32) DEFAULT '',
  `name` varchar(32) DEFAULT '',
  `address` varchar(64) DEFAULT '',
  `openid` varchar(64) DEFAULT '',
  `uid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_image`;
CREATE TABLE `ims_imeepos_runner3_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `src` varchar(300) DEFAULT NULL,
  `code` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_listenlog`;
CREATE TABLE `ims_imeepos_runner3_listenlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `taskid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_member`;
CREATE TABLE `ims_imeepos_runner3_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `uniacid` int(11) unsigned NOT NULL,
  `status` tinyint(2) unsigned NOT NULL,
  `groupid` int(11) unsigned NOT NULL,
  `time` int(11) DEFAULT NULL,
  `openid` varchar(64) DEFAULT NULL,
  `online` tinyint(2) DEFAULT '0',
  `nickname` varchar(32) DEFAULT '',
  `avatar` varchar(320) DEFAULT NULL,
  `gender` tinyint(2) DEFAULT '0',
  `city` varchar(32) DEFAULT '',
  `provice` varchar(32) DEFAULT '',
  `realname` varchar(32) DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `xinyu` int(11) DEFAULT '0',
  `isrunner` tinyint(2) DEFAULT '0',
  `card_image1` varchar(320) DEFAULT '',
  `card_image2` varchar(320) DEFAULT '',
  `cardnum` varchar(64) DEFAULT '',
  `lat` varchar(64) DEFAULT '',
  `lng` varchar(64) DEFAULT '',
  `oauth_code` varchar(64) DEFAULT '',
  `level_id` int(11) DEFAULT '0',
  `hash` varchar(32) DEFAULT '',
  `description` varchar(320) DEFAULT '''''',
  `desc` varchar(320) DEFAULT '',
  `forbid` int(4) DEFAULT '0',
  `card_image3` varchar(320) DEFAULT '',
  `forbid_time` int(11) DEFAULT '0',
  `isadmin` tinyint(2) NOT NULL DEFAULT '0',
  `ismanager` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `INDEX_OPENID` (`openid`),
  KEY `INDEX_UNIACID` (`uniacid`),
  KEY `INDEX_ISRUNNER` (`isrunner`),
  KEY `INDEX_HASH` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_message`;
CREATE TABLE `ims_imeepos_runner3_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(2) DEFAULT '0',
  `title` varchar(320) DEFAULT '',
  `link` varchar(320) DEFAULT '',
  `task_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_moneylog`;
CREATE TABLE `ims_imeepos_runner3_moneylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `reciveid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `fee` float(10,2) DEFAULT '0.00',
  `openid` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_navs`;
CREATE TABLE `ims_imeepos_runner3_navs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `title` varchar(32) DEFAULT '',
  `link` varchar(320) DEFAULT '',
  `icon_on` varchar(320) DEFAULT '',
  `icon_off` varchar(320) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `position` varchar(32) DEFAULT '',
  `do` varchar(32) DEFAULT '',
  `action` varchar(32) DEFAULT '',
  `ido` varchar(32) DEFAULT '',
  `icon` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_paylog`;
CREATE TABLE `ims_imeepos_runner3_paylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `tid` varchar(64) DEFAULT '',
  `time` int(11) DEFAULT '0',
  `setting` text,
  `status` tinyint(2) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `fee` float(10,2) DEFAULT '0.00',
  `type` varchar(32) DEFAULT '',
  `taskid` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_recive`;
CREATE TABLE `ims_imeepos_runner3_recive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `taskid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `fee` float(10,2) DEFAULT '0.00',
  `update_time` int(11) DEFAULT '0',
  `status` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `INDEX_OPENID` (`openid`),
  KEY `INDEX_UNIACID` (`uniacid`),
  KEY `INDEX_TASKID` (`taskid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_reminds`;
CREATE TABLE `ims_imeepos_runner3_reminds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned DEFAULT '0',
  `taskid` int(11) unsigned DEFAULT '0',
  `openid` varchar(64) DEFAULT NULL,
  `to_openid` varchar(64) DEFAULT NULL,
  `content` text,
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(2) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `reply` text,
  PRIMARY KEY (`id`),
  KEY `INDEX_OPENID` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_runner_level`;
CREATE TABLE `ims_imeepos_runner3_runner_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `displayorder` int(11) DEFAULT '0',
  `title` varchar(32) DEFAULT '',
  `xinyu` int(11) DEFAULT '0',
  `icon` varchar(320) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_service_message`;
CREATE TABLE `ims_imeepos_runner3_service_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) unsigned DEFAULT '0',
  `taskid` int(11) unsigned DEFAULT '0',
  `openid` varchar(64) DEFAULT NULL,
  `content` text,
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(2) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `reply` text,
  PRIMARY KEY (`id`),
  KEY `INDEX_OPENID` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_setting`;
CREATE TABLE `ims_imeepos_runner3_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `code` varchar(32) DEFAULT '',
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_shop`;
CREATE TABLE `ims_imeepos_runner3_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `lat` varchar(32) DEFAULT '',
  `lng` varchar(32) DEFAULT '',
  `poiaddress` varchar(320) DEFAULT '',
  `poiname` varchar(128) DEFAULT '',
  `cityname` varchar(128) DEFAULT '',
  `desc` varchar(320) DEFAULT '',
  `realname` varchar(32) DEFAULT '',
  `mobile` varchar(32) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(2) DEFAULT '0',
  `image` varchar(320) DEFAULT '',
  `card_image1` varchar(320) DEFAULT '',
  `register_num` varchar(32) DEFAULT '0',
  `title` varchar(64) DEFAULT '',
  `logo` varchar(320) DEFAULT '',
  `card_image2` varchar(320) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `INDEX_OPENID` (`openid`),
  KEY `INDEX_UNIACID` (`uniacid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_star`;
CREATE TABLE `ims_imeepos_runner3_star` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `taskid` int(11) DEFAULT '0',
  `from_openid` varchar(64) DEFAULT '',
  `to_openid` varchar(64) DEFAULT '',
  `star` int(11) DEFAULT '0',
  `type` tinyint(4) DEFAULT '0',
  `content` varchar(1000) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_tasks_paylog`;
CREATE TABLE `ims_imeepos_runner3_tasks_paylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `create_time` int(11) DEFAULT '0',
  `tid` varchar(64) DEFAULT '',
  `tasks_id` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `type` varchar(32) DEFAULT '',
  `fee` decimal(10,2) DEFAULT '0.00',
  `status` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_tasks_log`;
CREATE TABLE `ims_imeepos_runner3_tasks_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `taskid` int(11) DEFAULT '0',
  `openid` varchar(64) DEFAULT '',
  `content` varchar(1000) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `lat` varchar(32) DEFAULT '',
  `lng` varchar(32) DEFAULT '',
  `status` tinyint(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `taskid` (`taskid`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_tasks`;
CREATE TABLE `ims_imeepos_runner3_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `status` tinyint(2) DEFAULT '1',
  `create_time` int(11) DEFAULT '0',
  `cityid` int(11) DEFAULT '0',
  `media_id` varchar(132) DEFAULT '',
  `openid` varchar(64) DEFAULT '',
  `desc` text,
  `total` float(10,2) DEFAULT '0.00',
  `small_money` float(10,2) DEFAULT '0.00',
  `limit_time` int(11) DEFAULT '0',
  `address` varchar(320) DEFAULT '',
  `city` varchar(32) DEFAULT '',
  `type` tinyint(4) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `code` varchar(64) DEFAULT '',
  `qrcode` text,
  `read_num` int(11) DEFAULT '0',
  `share_num` int(11) DEFAULT '0',
  `listen_num` int(11) DEFAULT '0',
  `message` varchar(320) DEFAULT '',
  `media_src` varchar(320) DEFAULT 'divider',
  `payType` varchar(32) DEFAULT 'divider',
  `voice_time` int(11) DEFAULT '0',
  `hash` varchar(32) DEFAULT 'divider',
  PRIMARY KEY (`id`),
  KEY `INDEX_OPENID` (`openid`),
  KEY `INDEX_UNIACID` (`uniacid`),
  KEY `INDEX_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_tpl`;
CREATE TABLE `ims_imeepos_runner3_tpl` (
  `id` varchar(32) DEFAULT '',
  `name` varchar(32) DEFAULT '',
  `params` text,
  `data` text,
  `setting` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS  `ims_imeepos_runner3_tpl_data`;
CREATE TABLE `ims_imeepos_runner3_tpl_data` (
  `name` varchar(32) DEFAULT '',
  `title` varchar(32) DEFAULT '',
  `uniacid` int(11) DEFAULT '0',
  `html_content` text,
  `data` text,
  `create_time` int(11) DEFAULT '0',
  `pageinfo` text,
  `pagetype` varchar(32) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

";

pdo_query($sqls);

