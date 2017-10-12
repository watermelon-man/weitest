<?php
global $_GPC, $_W;

checklogin();
$uniacid = $_W['uniacid'];
load()->model('reply');
load()->func('tpl');
$sql = "uniacid = :uniacid and `module` = :module";
$params = array();
$params[':uniacid'] = $_W['uniacid'];
$params[':module'] = 'haoman_dpm';

$list = reply_search($sql, $params);
foreach($list as $lists){
	$rid= $lists['id'];
}


$addad = pdo_fetchall("select * from " . tablename('haoman_dpm_bgitems') . "where uniacid= :uniacid order by `id` desc",array(':uniacid'=>$uniacid));

include $this->template('bp_bgup');