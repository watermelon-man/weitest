<?php
//删除老菜单 增加新菜单

pdo_update('modules',array('isrulefields'=>1),array('name'=>'imeepos_runner'));

$sql = "SELECT * FROM ".tablename('modules_bindings')." WHERE module = :module AND entry = :entry AND do = :do";

$params = array(':module'=>'imeepos_runner',':entry'=>'menu',':do'=>'index');
$item = pdo_fetch($sql,$params);

if(empty($item)){
    $data = array();
    $data['module'] = 'imeepos_runner';
    $data['entry'] = 'menu';
    $data['title'] = '管理跑腿';
    $data['do'] = 'index';
    $data['direct'] = 0;
    $data['displayorder'] = 0;
    pdo_insert('modules_bindings',$data);
}

$sql = "SELECT * FROM ".tablename('modules_bindings')." WHERE module =:module AND entry =:entry";
$params = array(':module'=>'imeepos_runner',':entry'=>'menu');
$list = pdo_fetchall($sql,$params);

foreach($list as $li){
    if($li['do'] == 'index'){

    }else{
        pdo_delete('modules_bindings',array('eid'=>$li['eid']));
    }
}

$sql = "SELECT * FROM ".tablename('modules_bindings')." WHERE module=:module AND entry=:entry";
$params = array(':module'=>'imeepos_runner',':entry'=>'cover');
foreach($list as $li){
    if($li['do'] == 'index'){

    }else{
        pdo_delete('modules_bindings',array('eid'=>$li['eid']));
    }
}

//pdo_query("DELETE * FROM ".tablename('modules_bindings')." WHERE do != 'admin' AND module = 'imeepos_runner'");

if(!pdo_fieldexists('imeepos_runner3_member','hash')){
	pdo_query("ALTER TABLE ".tablename('imeepos_runner3_member')." ADD COLUMN `hash` varchar(32) DEFAULT ''");
}
if(!pdo_indexexists('imeepos_runner3_member','INDEX_HASH')){
    pdo_query("ALTER TABLE ".tablename('imeepos_runner3_member')." ADD INDEX INDEX_HASH ( `hash` )");
}
if(!pdo_fieldexists('imeepos_runner3_member','card_image3')){
	pdo_query("ALTER TABLE ".tablename('imeepos_runner3_member')." ADD COLUMN `card_image3` varchar(320) DEFAULT ''");
}
if(!pdo_fieldexists('imeepos_runner3_tasks','hash')){
	pdo_query("ALTER TABLE ".tablename('imeepos_runner3_tasks')." ADD COLUMN `hash` varchar(32) DEFAULT ''");
}
if(!pdo_fieldexists('imeepos_runner3_tasks','lat')){
	pdo_query("ALTER TABLE ".tablename('imeepos_runner3_tasks')." ADD COLUMN `lat` int(11) DEFAULT '0'");
}
if(!pdo_fieldexists('imeepos_runner3_tasks','lng')){
	pdo_query("ALTER TABLE ".tablename('imeepos_runner3_tasks')." ADD COLUMN `lng` int(11) DEFAULT '0'");
}

if(!pdo_fieldexists('imeepos_runner3_message','task_id')){
	pdo_query("ALTER TABLE ".tablename('imeepos_runner3_message')." ADD COLUMN `task_id` int(11) DEFAULT '0'");
}
if(!pdo_indexexists('imeepos_runner3_tasks','INDEX_HASH')){
    pdo_query("ALTER TABLE ".tablename('imeepos_runner3_tasks')." ADD INDEX INDEX_HASH ( `hash` )");
}
if(!pdo_fieldexists('imeepos_runner3_detail','duration')){
    pdo_query("ALTER TABLE ".tablename('imeepos_runner3_detail')." ADD COLUMN `duration` int(11) DEFAULT '0'");
}
if(!pdo_fieldexists('imeepos_runner3_tasks','message')){
    pdo_query("ALTER TABLE ".tablename('imeepos_runner3_tasks')." ADD COLUMN `message` varchar(320) DEFAULT ''");
}

if(!pdo_fieldexists('imeepos_runner3_tasks','dianfu')){
    pdo_query("ALTER TABLE ".tablename('imeepos_runner3_tasks')." ADD COLUMN `dianfu` tinyint(2) DEFAULT '0'");
}
