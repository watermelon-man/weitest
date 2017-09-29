<?php 
if(!function_exists('getCode')){
	function getCode(){
	
	}
}
if(!function_exists('init')) {
	function init($modelname)
	{
		$model = IA_ROOT . '/addons/' . $modelname . '/inc/core/model.php';
		if (file_exists($model)) {
			include_once $model;
		}
		$route = IA_ROOT . '/addons/' . $modelname . '/inc/core/route.php';
		if (file_exists($route)) {
			include_once $route;
		}
		$install = IA_ROOT . '/addons/' . $modelname . '/inc/core/install.php';
		if (file_exists($install)) {
			include_once $install;
		}
	}
}

load()->model('mc');
if(!function_exists('mc_notice_consume2')){
	function mc_notice_consume2($openid, $title, $content, $url = '',$thumbs='') {
		global $_W;
		$acc = mc_notice_init2();
		$status = 0;
		if(is_error($acc)) {
			return false;
		}
		if($_W['account']['level'] == 3){
			$status = mc_notice_custom_news($openid, $title, $content,$url,$thumbs);
		}
		if($_W['account']['level'] == 4) {
			$status = mc_notice_public($openid, $title, $_W['account']['name'], $content);
			if(is_error($status)){
				$status = mc_notice_custom_news($openid, $title, $content,$url,$thumbs);
			}
		}
		if(is_error($status)){
			$status = mc_notice_custom_news($openid, $title, $content,$url,$thumbs);
		}
		return $status;
	}
}
if(!function_exists('json')){
	function json($status='1',$message='系统错误',$data = array()){
		$json = array();
		$json['status'] = $status;
		$json['message'] = $message;
		$json['data'] = $data;
		die(json_encode($json));
	}
}

if(!function_exists('mc_notice_init2')){
	function mc_notice_init2() {
		global $_W;
		if(empty($_W['account'])) {
			$_W['account'] = uni_fetch($_W['uniacid']);
		}
		if(empty($_W['account'])) {
			return error(-1, '创建公众号操作类失败');
		}
		if($_W['account']['level'] < 3) {
			return error(-1, '公众号没有经过认证，不能使用模板消息和客服消息');
		}
		$acc = WeAccount::create();
		if(is_null($acc)) {
			return error(-1, '创建公众号操作对象失败');
		}
		return $acc;
	}
}

if(!function_exists('mc_notice_custom_news')){
	function mc_notice_custom_news($openid, $title, $content,$url,$thumb) {
		global $_W;
		$acc = mc_notice_init2();
		if(is_error($acc)) {
			return error(-1, $acc['message']);
		}
		$fans = pdo_fetch('SELECT salt,acid,openid FROM ' . tablename('mc_mapping_fans') . ' WHERE uniacid = :uniacid AND openid = :openid', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
	
		$row = array();
		$row['title'] = urlencode($title);
		$row['description'] = urlencode($content);
		!empty($thumb) && ($row['picurl'] = tomedia($thumb));
	
		if(strexists($url, 'http://') || strexists($url, 'https://')) {
			$row['url'] = $url;
		} else {
	
			$pass['time'] = TIMESTAMP;
			$pass['acid'] = $fans['acid'];
			$pass['openid'] = $fans['openid'];
			$pass['hash'] = md5("{$fans['openid']}{$pass['time']}{$fans['salt']}{$_W['config']['setting']['authkey']}");
			$auth = base64_encode(json_encode($pass));
			$vars = array();
			$vars['__auth'] = $auth;
			if(empty($url)){
				$vars['forward'] = base64_encode($url);
			}
	
	
			$row['url'] =  $_W['siteroot'] . 'app/' . murl('auth/forward', $vars);
		}
		$news[] = $row;
		$send['touser'] = trim($openid);
		$send['msgtype'] = 'news';
		$send['news']['articles'] = $news;
		$status = $acc->sendCustomNotice($send);
		return $status;
	}
}

if(!function_exists('M')){
	function M($name){
		static $model = array();
		if(empty($model[$name])) {
			include IA_ROOT.'/addons/imeepos_runner/inc/core/model/'.$name.'.mod.php';
			$model[$name] = new $name();
		}
		return $model[$name];
	}
}

if(!function_exists('P')){
	function P($name){
		static $plugin = array();
		if(empty($plugin[$name])) {
			include IA_ROOT.'/addons/imeepos_runner/plugin/'.$name.'/model.php';
			$p = $name.'Plugin';
			$plugin[$name] = new $p();
		}
		return $plugin[$name];
	}
}