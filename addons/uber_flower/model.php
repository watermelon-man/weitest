<?php

//调试打印函数
function printr($var){
	echo "<pre>";print_r($var);exit;
}
//调试日志函数
function xdebug($log,$path=''){
	if(empty($path))$path = MODULE_ROOT."/xdebug.log";
    file_put_contents($path, var_export($log, true).PHP_EOL, FILE_APPEND);
}
function getWxConfig() {
		global $_W,$_GPC;
		$data['appId'] = $_W['account']['jssdkconfig']['appId'];
		$data['timestamp'] = $_W['account']['jssdkconfig']['timestamp'];
		$data['nonceStr'] = $_W['account']['jssdkconfig']['nonceStr'];
		$data['signature'] = $_W['account']['jssdkconfig']['signature'];
		return $data;
}
function toImgUrl($img = '') {
    global $_W;
    if (empty($img)) {
        return "";
    }
    if (substr($img, 0, 6) == 'avatar') {
        return $_W['siteroot'] . "resource/image/avatar/" . $img;
    }
    if (substr($img, 0, 8) == './themes') {
        return $_W['siteroot'] . $img;
    }
    if (substr($img, 0, 1) == '.') {
        return $_W['siteroot'] . substr($img, 2);
    }
    if (substr($img, 0, 5) == 'http:') {
        return $img;
    }
    return $_W['attachurl'] . $img;
}
function tomediaurl($imgurl){
	if(stripos($imgurl,'addons',true)!==false)return $_W['siteroot'] . substr($imgurl, 2);
	return tomedia($imgurl);
}
function getAvgCredit($rid,$mchid,$mode=0,$limit=5){
	global $_W;
	$credit_arr = pdo_fetchall("select credit from " . tablename('uber_flower_fans') . " WHERE uniacid= :uniacid AND rid= :rid AND mchid= :mchid ORDER BY credit ASC,createtime DESC LIMIT {$limit}", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':mchid' => $mchid));	
	if($mode==1)$credit_arr = pdo_fetchall("select totalcredit as avgcredit from " . tablename('uber_flower_fans') . " WHERE uniacid= :uniacid AND rid= :rid AND mchid= :mchid ORDER BY totalcredit ASC,createtime DESC LIMIT  {$limit}", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':mchid' => $mchid));
	foreach($credit_arr as $key=>&$credit){
		$credit_arr[$key] = $credit['credit'];
	}
	unset($credit);
	$avgcredit = array_sum($credit_arr);
	$avgcredit = $avgcredit/$limit;
	return round($avgcredit,1);
	
}