<?php
/**
 * 小明跑腿模块微站定义
 * 
 * 
 *
 * @author imeepos
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

// $sql = "SELECT * FROM ".tablename('modules_bindings')." WHERE module = :module AND entry = :entry AND do = :do";
// $params = array(':module'=>'imeepos_runner',':entry'=>'cover',':do'=>'runner');
// $item = pdo_fetch($sql,$params);
// if(!empty($item)){
// 	pdo_delete('modules_bindings',array('module'=>'imeepos_runner','entry'=>'cover','do'=>'runner'));
// }

// $sql = "SELECT * FROM ".tablename('modules_bindings')." WHERE module = :module AND entry = :entry AND do = :do";
// $params = array(':module'=>'imeepos_runner',':entry'=>'cover',':do'=>'tasks');
// $item = pdo_fetch($sql,$params);

// if(empty($item)){
// 	$data = array('module'=>'imeepos_runner','entry'=>'cover','do'=>'tasks','title'=>'任务大厅','direct'=>1);
// 	pdo_insert('modules_bindings',$data);
// }

// load()->func('logging');
// 


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

if(!function_exists('A')){
	function A($name){
		static $model = array();
		if(empty($model[$name])) {
			include IA_ROOT.'/addons/imeepos_runner/api/'.$name.'.php';
			$model[$name] = new $name();
		}
		return $model[$name];
	}
}

function J($status = -1,$message="获取数据失败",$info=array()){
	header("Access-Control-Allow-Origin:*");
	$data = array();
	$data['status'] = -1;
	$data['message'] = $message;
	$data['info'] = $info;
	die(json_encode($data));
}

M('paylog');


// ini_set("display_errors", "On");
// error_reporting(E_ALL | E_STRICT);
// 
defined('Meepo_Debug',true);


class Imeepos_runnerModuleSite extends WeModuleSite {
	
	public $modulename = 'imeepos_runner';
	public $pluginname = '';

	public function doMobileUpload(){
		global $_W,$_GPC;

		$act = trim($_GPC['act']);
		if($act == 'uploadImage'){
			$input = $_GPC['__input'];
			$serviceId = $input['serviceId'];
			//上传到服务器
			$oauth_account = WeAccount::create($_W['account']['oauth']);
			$media = array(
	            'type'=>'image',
	            'media_id'=>$serviceId
	        );
        	$file = $oauth_account->downloadMedia($media);
        	$file = tomedia($file);
        	J('0','上传成功',$file);
		}
	}

	public function doMobileWebapp(){
		global $_W,$_GPC;

		include $this->template('webapp/index');
	}
	public function doMobileTask_api(){
		global $_W,$_GPC;

		$act = trim($_GPC['act']);
		if($act == 'index'){
			$where = " AND t.uniacid = :uniacid ";
			$params = array(':uniacid'=>$_W['uniacid']);
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			$psize = 10;
			$sql = "SELECT t.*,m.avatar as avatar,m.nickname as nickname,m.realname as realname ,m.mobile as mobile,m.xinyu as xinyu FROM ".tablename('imeepos_runner3_tasks')." as t LEFT JOIN ".tablename('imeepos_runner3_member')." as m ON t.openid = m.openid WHERE 1 {$where} ORDER BY t.create_time DESC,m.xinyu DESC limit ".(($page-1)*$psize).",".$psize;
			$list = pdo_fetchall($sql,$params);

			J(0,'获取成功',$list);
		}

		if($act == 'search'){
			$key = trim($_GPC['key']);
			$where = " AND isrunner = :isrunner AND uniacid = :uniacid AND nickname like '%{$key}%' OR realname like '%{$key}%' OR mobile like '%{$key}%'";
			$params = array(':isrunner'=>'1',':uniacid'=>$_W['uniacid']);
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			$psize = 10;
			$sql = "SELECT * FROM ".tablename('imeepos_runner3_tasks')." WHERE 1 {$where} ORDER BY create_time DESC limit ".(($page-1)*$psize).",".$psize;
			$list = pdo_fetchall($sql,$params);
			J(0,'获取成功',$list);
		}

		if($act == 'detail'){
			$where = "";
			$params = array();
			$sql = "SELECT * FROM ".tablename('imeepos_runner3_tasks')." WHERE 1 {$where} limit 1";
			$item = pdo_fetch($sql,$parmas);
			J(0,'获取成功',$item);
		}

		J();
	}

	public function doMobilecategory_api(){
		global $_W,$_GPC;
		header("Access-Control-Allow-Origin:*");
		$act = trim($_GPC['act']);
		if($act == 'index'){
			$psize = 10;
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			$openid = $this->getOpenid();
			$list = M('category')->getList($page,$where,$params);
			J(0,'获取成功',$list['list']);
		}
	}

	public function getOpenid(){
		global $_W;
		if(Meepo_Debug){
			$openid = $_W['openid'] ? $_W['openid'] : 'ojrmQt9r91gWieJeM3Zz7hAPIlaU';
		}else{
			$openid = $_W['openid'];
		}
		return $openid;
	}

	public function doMobileHome_address_api(){
		global $_W,$_GPC;
		header("Access-Control-Allow-Origin:*");
		$act = trim($_GPC['act']);
		$openid = $this->getOpenid();
		if($act == 'index'){
			$psize = 10;
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			

			$type = isset($_GPC['type']) ? intval($_GPC['type']) : '0';

			$sql = "SELECT * FROM ".tablename('imeepos_runner3_address')." WHERE openid = :openid AND uniacid = :uniacid AND type = :type limit ".(($page-1)*$psize).",".$psize;
			$params = array(':openid'=>$openid,':uniacid'=>$_W['uniacid'],':type'=>$type);
			$list = pdo_fetchall($sql,$params);

			J(0,'获取成功',$list);
		}
		if($act == 'me_add'){
			$input = $_GPC['__input'];
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['openid'] = $openid;
			$data['poiaddress'] = $input['poiaddress'];
			$data['poiname'] = $input['poiname'];
			$data['cityname'] = $input['cityname'];
			$data['realname'] = $input['realname'];
			$data['mobile'] = $input['mobile'];
			$data['lat'] = $input['latlng']['lat'];
			$data['lng'] = $input['latlng']['lng'];
			$data['create_at'] = time();
			$data['type'] = 0;
			M('address')->update($data);
			J(0,'获取成功',$input);
		}
		if($act == 'friend_add'){
			$input = $_GPC['__input'];
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['openid'] = $openid;
			$data['poiaddress'] = $input['poiaddress'];
			$data['poiname'] = $input['poiname'];
			$data['cityname'] = $input['cityname'];
			$data['realname'] = $input['realname'];
			$data['mobile'] = $input['mobile'];
			$data['lat'] = $input['latlng']['lat'];
			$data['lng'] = $input['latlng']['lng'];
			$data['create_at'] = time();
			$data['type'] = 1;
			M('address')->update($data);
			J(0,'获取成功',$input);
		}
		J();
	}
	public function doMobileGoods_api(){
		global $_W,$_GPC;
		$act = trim($_GPC['act']);
		$openid = $this->getOpenid();
		if($act == 'index'){
			$psize = 10;
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			

			$type = isset($_GPC['type']) ? intval($_GPC['type']) : '0';

			$sql = "SELECT * FROM ".tablename('imeepos_runner3_goods')." WHERE openid = :openid AND uniacid = :uniacid limit ".(($page-1)*$psize).",".$psize;
			$params = array(':openid'=>$openid,':uniacid'=>$_W['uniacid']);
			$list = pdo_fetchall($sql,$params);

			J(0,'获取成功',$list);
		}
		if($act == 'me_add'){
			$input = $_GPC['__input'];
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			$data['openid'] = $openid;
			$data['name'] = $input['name'];
			$data['weight'] = $input['weight'];
			$data['price'] = $input['price'];
			$data['detail'] = $input['detail'];
			$data['create_at'] = time();
			$data['class_id'] = 0;
			M('goods')->update($data);
			J(0,'获取成功',$input);
		}
		J();
	}
	public function doMobileAdv_api(){
		global $_W,$_GPC;
		$act = trim($_GPC['act']);
		if($act == 'index'){
			$where = " AND uniacid = :uniacid AND position = :position";
			$params = array(':uniacid'=>$_W['uniacid'],':position'=>'adv');
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			$psize = 10;
			$sql = "SELECT * FROM ".tablename('imeepos_runner3_adv')." WHERE 1 {$where} ORDER BY time DESC limit ".(($page-1)*$psize).",".$psize;
			$list = pdo_fetchall($sql,$params);
			foreach ($list as &$li) {
				$li['image'] = tomedia($li['image']);
			}
			unset($li);
			J(0,'获取成功',$list);
		}

	}

	public function doMobileMember_api(){
		global $_W,$_GPC;
		$act = trim($_GPC['act']);
		$openid = $this->getOpenid();
		if($act == 'index'){
			$psize = 10;
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			
			$where = "";
			$params = array();
			$list = M('member')->getList($page,$where,$params);
			J(0,'获取成功',$list['list']);
		}
		if($act == 'update_location'){
			$input = $_GPC['__input'];
			$member = M('member')->getInfo($openid);
			if(empty($input['cityname'])){
				$member['city'] = $input['cityname'];
			}
			$member['lat'] = $input['latlng']['lat'];
			$member['lng'] = $input['latlng']['lng'];
			M('member')->update_or_insert($member);
			J(0,'更新成功',$member);
		}
		if($act == 'myinfo'){
			$info = M('member')->getInfo($openid);
			if(empty($info)){
				$info = array();
				$info['openid'] = $openid;
			}
			J(0,'获取成功',$info);
		}
		if($act == 'detail'){
			$openid = trim($_GPC['openid']);
			$info = M('member')->getInfo($openid);
			J(0,'获取成功 ',$info);
		}
		J();
	}
	//跑腿员api
	public function doMobilerunner_api(){
		global $_W,$_GPC;

		$act = trim($_GPC['act']);

		$openid = $this->getOpenid();

		if($act == 'index'){
			$where = " AND isrunner = :isrunner AND uniacid = :uniacid ";
			$params = array(':isrunner'=>'1',':uniacid'=>$_W['uniacid']);
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			$psize = 10;
			$sql = "SELECT * FROM ".tablename('imeepos_runner3_member')." WHERE 1 {$where} ORDER BY time DESC limit ".(($page-1)*$psize).",".$psize;
			$list = pdo_fetchall($sql,$params);
			J(0,'获取成功',$list);
		}

		if($act == 'search'){
			$key = trim($_GPC['key']);
			$where = " AND isrunner = :isrunner AND uniacid = :uniacid AND nickname like '%{$key}%' OR realname like '%{$key}%' OR mobile like '%{$key}%'";
			$params = array(':isrunner'=>'1',':uniacid'=>$_W['uniacid']);
			$page = intval($_GPC['page']);
			$page = $page > 0 ? $page : 1;
			$psize = 10;
			$sql = "SELECT * FROM ".tablename('imeepos_runner3_member')." WHERE 1 {$where} ORDER BY time DESC limit ".(($page-1)*$psize).",".$psize;
			$list = pdo_fetchall($sql,$params);
			J(0,'获取成功',$list);
		}

		if($act == 'detail'){
			$where = "";
			$params = array();
			$sql = "SELECT * FROM ".tablename('imeepos_runner3_member')." WHERE 1 {$where} limit 1";
			$item = pdo_fetch($sql,$parmas);
			J(0,'获取成功',$item);
		}

		if($act == 'recive_log'){

		}

		J();
	}

	public function doMobileapi(){
	    global $_W,$_GPC;
	    header("Access-Control-Allow-Origin:*");
	    $_GPC['do'] = 'api';
	    $act = trim($_GPC['act']);
		$api = trim($_GPC['api']);
		A(''.$api)->$act();
	}

	// public function doWebapi(){
	// 	global $_W,$_GPC;
	// 	$_GPC['do'] = 'api';
	// 	$act = trim($_GPC['act']);
	// 	$api = trim($_GPC['api']);
	// 	$act = 'web'.$act;
	// 	A(''.$api)->$act();
	// }
	
	// public function doMobiletest(){
	//     global $_W,$_GPC;
	//     $_GPC['do'] = 'test';
	// 	$code = '1234';
	// 	$mobile = '13140415408';
	// 	$member = M('member')->getInfo($_W['openid']);
	// 	$site = WeUtility::createModuleSite('imeepos_opensms');
	// 	$site->sendSmsCode($code,$mobile,$member);
	// }
	
	// public function doMobileopen(){
	//     global $_W,$_GPC;
	//     $_GPC['do'] = 'open';
	//     $act = trim($_GPC['act']);
	// 	$data = array();
	// 	$input = $_GPC;
	// 	$data['goodsweight'] = floatval($input['goodsweight']);
	// 	$data['goodscost'] = floatval($input['goodscost']);
	// 	$data['goodsname'] = trim($input['goodstitle']);

	// 	$data['sendprovince'] = '';
	// 	$data['sendcity'] = trim($input['sendaddress']['city']);
	// 	$data['sendaddress'] = trim($input['sendaddress']['title']);
	// 	$data['senddetail'] = trim($input['sendaddress']['detail']);

	// 	$data['sendrealname'] = trim($input['sendaddress']['realname']);
	// 	$data['sendmobile'] = trim($input['sendaddress']['mobile']);

	// 	$data['receiveprovince'] = '';
	// 	$data['receivecity'] = trim($input['receiveaddress']['city']);
	// 	$data['receiveaddress'] = trim($input['receiveaddress']['title']);
	// 	$data['receivedetail'] = trim($input['receiveaddress']['detail']);
	// 	$data['receiverealname'] = trim($input['receiveaddress']['realname']);
	// 	$data['receivemobile'] = trim($input['receiveaddress']['mobile']);

	// 	$data['message'] = trim($input['message']);
	// 	$data['sendlon'] = floatval($input['sendaddress']['lng']);
	// 	$data['sendlat'] = floatval($input['sendaddress']['lat']);

	// 	$data['receivelon'] = floatval($input['receiveaddress']['lng']);
	// 	$data['receivelat'] = floatval($input['receiveaddress']['lat']);

	// 	$images = $input['thumbs'];
	// 	$imgs = array();
	// 	foreach ($images as $img){
	// 		$imgs[] = M('image')->createImage($img['src']);
	// 	}
	// 	$data['images'] = serialize($imgs);

	// 	if(!empty($_GPC['distance'])){
	// 		//距离 米
	// 		$data['distance'] = intval($_GPC['distance']);
	// 		$data['float_distance'] = floatval($data['distance']/1000);
	// 	}else{
	// 		$url = "http://apis.map.qq.com/ws/distance/v1/?mode=driving&from={$data['sendlat']},{$data['sendlon']}&to={$data['receivelat']},{$data['receivelon']}&output=json&key=4MHBZ-JVL35-WLMII-Q3NME-3Z2G2-PKBJJ";
	// 		load()->func('communication');
	// 		$content = ihttp_get($url);
	// 		$content = @json_decode($content['content'], true);

	// 		if($content['status'] == '373'){
	// 			$result = array();
	// 			$result['result'] = 0;
	// 			$result['message'] = $content['message']."，最大距离为10公里";
	// 			die(json_encode($result));
	// 		}
	// 		$content = $content['result']['elements'][0];
	// 		$data['distance'] = intval(intval($content['distance'])/1000);
	// 		$data['float_distance'] = floatval(intval($content['distance'])/1000);
	// 	}

	// 	if(!empty($_GPC['duration'])){
	// 		$data['duration'] = intval($_GPC['duration']);
	// 	}
	// 	$data['pickupdate'] = strtotime(trim($input['datatime']['value']));

	// 	if(!empty($input['datatime']['value'])){
	// 		$data['dataTimeValue'] = strtotime(trim($input['datatime']));//预约取
	// 	}else{
	// 		$code = 'plugin_setting';
	// 		$item = M('setting')->getByCode($code);
	// 		$plugin = iunserializer($item['value']);
	// 		$hour = floatval($plugin['limit_time']);
	// 		$data['dataTimeValue'] = time() + intval(60*60*$hour);
	// 	}

	// 	$data['time'] = intval($input['time']);

	// 	$data['small_money'] = floatval($input['small_money']);

	// 	if($data['small_money'] < 0){
	// 		$data['small_money'] = abs($data['small_money']);
	// 	}

	// 	//计算费用
	// 	$set = M('setting')->getValue('divider_set');

	// 	//判断是否在起步价内
	// 	$distance = floatval($data['distance']);
	// 	$max_distance = floatval($set['start_km']);

	// 	$distance = $distance/1000;
	// 	$price = 0;
	// 	$start_price = floatval($set['start_fee']);
	// 	if($distance > $max_distance){
	// 		$chao_distance = $distance - $max_distance;
	// 		$limit_km_km = floatval($set['limit_km_km']);
	// 		if($limit_km_km >0){
	// 			$chao = ceil($chao_distance / $limit_km_km);
	// 		}else{
	// 			$chao = 0;
	// 		}
	// 		$limit_km_fee = floatval($set['limit_km_fee']);
	// 		$price += floatval($chao * $limit_km_fee);
	// 	}

	// 	$max_goodsweight = floatval($set['start_kg']);
	// 	$goodsweight = floatval($data['goodsweight']);

	// 	if($goodsweight > $max_goodsweight){
	// 		$chao_goodsweight = $goodsweight - $max_goodsweight;
	// 		$limit_kg_kg = floatval($set['limit_kg_kg']);
	// 		if($limit_kg_kg >0){
	// 			$chao = $chao_goodsweight / $limit_kg_kg;
	// 		}else{
	// 			$chao = 0;
	// 		}
	// 		$limit_kg_fee = floatval($set['limit_kg_fee']);
	// 		$price += round($chao * $limit_kg_fee);
	// 	}

	// 	$data['base_fee'] = $start_price;
	// 	$data['fee'] = $price;
	// 	$data['total'] = $start_price + $price + $data['small_money'];

	// 	$now = time();

	// 	$month=intval(date('m'));
	// 	$day=intval(date('d'));
	// 	$year=intval(date('Y'));

	// 	$start_time = intval($set['start_time']);
	// 	$start_time = mktime($start_time,0,0,$month,$day,$year);
	// 	$end_time = intval($set['end_time']);
	// 	$end_time = mktime($end_time,0,0,$month,$day,$year);

	// 	$time_fee = (1+floatval($set['time_fee'])/100);

	// 	if($now >= $start_time && $now <= $end_time){

	// 	}else{
	// 		$data['base_fee'] = $data['base_fee'];
	// 		$data['fee'] = $data['fee'];
	// 		$data['total'] = $data['total'] * $time_fee;
	// 	}


	// 	$detail = $data;

	// 	$text = "";

	// 	if(empty($data['time'])){
	// 		//及时取

	// 		$text .="及时取";
	// 		if(!empty($data['small_money'])){
	// 			$text .= "(加急):";
	// 		}
	// 		if(!empty($data['goodsname'])){
	// 			$text .= "商品名称:".$data['goodsname'];
	// 		}
	// 		if(!empty($data['goodscost'])){
	// 			$text .= ",价值：".$data['goodscost']."元";
	// 		}
	// 		if(!empty($data['goodsweight'])){
	// 			$text .= ",".$data['goodsweight']."公斤";
	// 		}
	// 		if(!empty($data['distance'])){
	// 			$text .= ",总路程".($data['float_distance'])."公里";
	// 		}
	// 		$text .= '从'.$data['sendaddress'].'送到'.$data['receiveaddress'].",赏金：".$data['total']."元";
	// 	}else{
	// 		$text .="预约取";
	// 		if(!empty($data['small_money'])){
	// 			$text .= "(加急):";
	// 		}
	// 		if(!empty($data['goodsname'])){
	// 			$text .= "商品名称:".$data['goodsname'];
	// 		}
	// 		if(!empty($data['goodscost'])){
	// 			$text .= ",价值：".$data['goodscost']."元";
	// 		}
	// 		if(!empty($data['goodsweight'])){
	// 			$text .= ",".$data['goodsweight']."公斤";
	// 		}
	// 		if(!empty($data['distance'])){
	// 			$text .= ",总路程".($data['float_distance'])."公里";
	// 		}
	// 		$text .= '从'.$data['sendaddress'].'送到'.$data['receiveaddress'].",赏金：".$data['total']."元";
	// 	}

	// 	$acc = WeAccount::create();
	// 	if(!empty($text)){
	// 		$url = "http://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&spd=5&text=".urlencode($text);
	// 		$img = array();
	// 		$data = file_get_contents($url);
	// 		$type = 'mp3';
	// 		$filename = "audios/imeepos_runner/".time()."_".random(6).".".$type;
	// 		if(file_write($filename,$data)){
	// 			$result = $acc->uploadMedia($filename,'voice');
	// 			$img['media_id'] = $result['media_id'];
	// 		}
	// 	}

	// 	$tasks = array();
	// 	$tasks['uniacid'] = $_W['uniacid'];
	// 	$tasks['openid'] = $_W['openid'];
	// 	$tasks['create_time'] = time();
	// 	$tasks['desc'] = $text;
	// 	$tasks['city'] = trim($input['city']['title']);

	// 	$tasks['media_id'] = $img['media_id'];
	// 	$tasks['status'] = 0;
	// 	$tasks['type'] = intval($detail['time']);

	// 	$tasks['total'] = $detail['total'];
	// 	$tasks['small_money'] = $detail['small_money'];
	// 	$tasks['address'] = $detail['receiveaddress'];


	// 	$tasks['message'] = $detail['message'];
	// 	$tasks['limit_time'] = $detail['dataTimeValue'];
	// 	pdo_insert('imeepos_runner3_tasks',$tasks);

	// 	$detail['taskid'] = pdo_insertid();
	// 	$code = random(4,true);
	// 	$codetask = array();
	// 	$codetask['code'] = $code;
	// 	$qrcode = 'imeepos_runner'.md5($code.$tasks['create_time']);
	// 	$codetask['qrcode'] = $qrcode;
	// 	pdo_update('imeepos_runner3_tasks',$codetask,array('id'=>$detail['taskid']));

	// 	pdo_insert('imeepos_runner3_detail',$detail);

	// 	//插入订单记录
	// 	$paylog = array();
	// 	$paylog['fee'] = $detail['total'];
	// 	$paylog['tid'] = "U".time().random(6,true);
	// 	$paylog['uniacid'] = $_W['uniacid'];
	// 	$paylog['setting'] = iserializer(array('taskid'=>$detail['taskid']));
	// 	$paylog['status'] = 0;
	// 	$paylog['openid'] = $_W['openid'];
	// 	$paylog['time'] = time();
	// 	$paylog['type'] = 'post_task';

	// 	pdo_insert('imeepos_runner3_paylog',$paylog);
	// 	$tid = pdo_insertid();

	// 	//imeepos_runner3_detail
	// 	$result = array();
	// 	$result['result'] = 0;
	// 	$result['paylog'] = $paylog;
	// 	$result['media_id'] = $img['media_id'];
	// 	if(empty($distance)){
	// 		$result['message'] = '总费用：'.$detail['total']."元";
	// 	}else{
	// 		$result['message'] = '总路程：'.$distance.'公里，总费用：'.$detail['total']."元";
	// 	}

	// 	$member = M('member')->getInfo($_W['openid']);
	// 	$content = "【".$member['nickname']."】,成功发布此任务！";
	// 	$data = array();
	// 	$data['uniacid'] = $_W['uniacid'];
	// 	$data['openid'] = $_W['openid'];
	// 	$data['create_time'] = time();
	// 	$data['taskid'] = $detail['taskid'];
	// 	$data['content'] = $content;
	// 	$data['lat'] = $detail['receivelat'];
	// 	$data['lng'] = $detail['receivelon'];
	// 	M('tasks_log')->update($data);

	// 	//新订单后台提醒
	// 	$data = array();
	// 	$data['uniacid'] = $_W['uniacid'];
	// 	$data['create_time'] = time();
	// 	$data['status'] = 0;
	// 	$data['title'] = "【".$member['nickname']."】成功提交任务";
	// 	$data['link'] = '';
	// 	M('message')->update($data);

	// 	$detail['distance'] = $distance;
	// 	$result['tid'] = $tid;
	// 	$result['credit'] = $detail['total'];

	// 	die(json_encode($result));
	// }
	
	public function doMobileopensuccess(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'opensuccess';
	    $params = $_GPC['params'];
		$params = unserialize(json_decode($params));
		M('paylog')->payResult($params);
		$data = array();
		$data['status'] = 0;
	    die(json_encode($data));
	}
	
	/***
	 * 后台任务管理
	 */
	public function doWebcategory_task(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'category_task';
		$category_id = intval($_GPC['category_id']);
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('category_task')->update($data);
	            message('保存成功',$this->createWebUrl('category_task',array('category_id'=>$category_id)),'success');
	        }
	        $item = M('category_task')->getInfo($id);
	        include $this->template('category_task_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('category_task')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('category_task')->getList($page,$where);
	    include $this->template('category_task');
	}
	/**
	 * 米波网络学院 分类的前台实现
	 * 分类列表页
	 */
	public function doMobilecategory_lsit(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'category_lsit';
		$title = '';

		$categorys = M('category')->getList(1);
	    include $this->template('category_lsit');
	}

	public function doMobilePost(){
		$this->doMobilePlugin('template','post_index');
	}

	public function doMobilePlugin($mp = '',$mdo=''){
		global $_W,$_GPC;
		$mp = !empty($mp)?$mp:trim($_GPC['mp']);
		$mdo = !empty($mdo)?$mdo:trim($_GPC['mdo']);
		$_GPC['mp'] = $mp;
		$_GPC['mdo'] = $mdo;

		if(empty($mp)){
			message('插件错误','',error);
		}
		if(empty($mdo)){
			message('插件错误','',error);
		}
		$this->getMobilePlugin($mp,$mdo);
		exit();
	}

	/**
	 * 分类详情页 -- 任务表单
	 */
	public function doMobilecategory_post(){
	    global $_W,$_GPC;

	    $_GPC['do'] = 'category_post';
		$category_id = intval($_GPC['category_id']);

		$category = M('category')->getInfo($category_id);

		if(empty($category)){
			$message = '所选分类不存在或已删除';
			$url = $this->createMobileUrl('category_lsit');
			include $this->template('error');
			exit();
		}
		$fileds = M('category_field')->getall(array('category_id'=>$category_id));

		$fileds_result = array();
		foreach ($fileds as $f){
			$fileds_result[$f['id']] = $f;
		}

		if($_W['ispost']){
			$data = array();
			$post = $_POST;

			//插入任务表
			$task = array();
			$task['uniacid'] = $_W['uniacid'];
			$task['openid'] = $_W['openid'];
			$task['status'] = 0;//没有支付
			$task['create_time'] = time();

			$task_post = M('category_task')->update($task);
			$total = 0;
			foreach ($post as $key => $field){
				if($fileds_result[$key]['type'] == 'image'){
					$images = array();
					foreach ($field as $f){
						$images[] = M('image')->createImage($f);
					}
					$data = array();
					$data['uniacid'] = $_W['uniacid'];
					$data['create_time'] = time();
					$data['field_id'] = $key;
					$data['value'] = serialize($images);
					$data['task_id'] = $task_post['id'];

					M('category_field_data')->update($data);
				}else if($fileds_result[$key]['type'] == 'address'){
					$data = array();
					$data['uniacid'] = $_W['uniacid'];
					$data['create_time'] = time();
					$data['field_id'] = $key;
					$data['value'] = serialize($field);
					$data['task_id'] = $task_post['id'];
					M('category_field_data')->update($data);
				}else if ($fileds_result[$key]['type'] == 'fee'){
					//判断价格到底符合要求不
					$fee = floatval($field);
					if($fee <= 0){
						$data = array();
						$data['status'] = 0;
						$data['message'] = "价格不能小于0";
						die(json_encode($data));
					}
					//总的费用
					$total = $total + $fee;

					$data = array();
					$data['uniacid'] = $_W['uniacid'];
					$data['create_time'] = time();
					$data['field_id'] = $key;
					$data['value'] = $field;
					$data['task_id'] = $task_post['id'];
					M('category_field_data')->update($data);
				}else{
					$data = array();
					$data['uniacid'] = $_W['uniacid'];
					$data['create_time'] = time();
					$data['field_id'] = $key;
					$data['value'] = trim($field);
					$data['task_id'] = $task_post['id'];
					M('category_field_data')->update($data);
				}
			}

			//
			$task_post['total'] = $total;
			M('category_task')->update($task_post);

			$data = array();
			$data['status'] = 1;
			$data['message'] = "任务提交成功";
			$data['task_id'] = $task_post['id'];
			die(json_encode($data));
		}

	    include $this->template('category_post');
	}

	/**
	 * 订单确认页
	 */
	public function doMobilecategory_confirm(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'category_confirm';
		$task_id = intval($_GPC['task_id']);

		$task = M('category_task')->getInfo($task_id);

		if(empty($task)){
			$message = '任务不存在或已删除';
			$url = $this->createMobileUrl('category_lsit');
			include $this->template('error');
			exit();
		}

		$paylog = array();
		$paylog['fee'] = $task['total'];
		$paylog['tid'] = "U".time().random(6,true);
		$paylog['uniacid'] = $_W['uniacid'];
		$paylog['setting'] = iserializer(array('id'=>$task['id']));
		$paylog['status'] = 0;
		$paylog['openid'] = $_W['openid'];
		$paylog['time'] = time();
		$paylog['type'] = 'post_category_task';

		pdo_insert('imeepos_runner3_paylog',$paylog);

		$params = array();
		$params['tid'] = $paylog['tid'];
		$params['ordersn'] = $paylog['tid'];
		$params['fee'] = floatval($paylog['fee']);
		$params['title'] = "发布任务";
		$params['user'] = $_W['openid'];

		$this->pay($params,array());
		exit();
	}

	/**
	 * 订单 支付页
	 */
	/*public function doMobilecategory_pay(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'category_pay';
	    
	    include $this->template('category_pay');
	}*/

	public function doWebannouncement(){
		global $_W,$_GPC;
		$_GPC['do'] = 'announcement';
		if ($_GPC['act'] == 'edit') {
			$id = intval($_GPC['id']);
			if($_W['ispost']){
				$data = array();
				$data['uniacid'] = $_W['uniacid'];
				$data['title'] = trim($_GPC['title']);
				$data['displayorder'] = intval($_GPC['displayorder']);
				$data['link'] = trim($_GPC['link']);
				$data['create_time'] = time();
				if(!empty($id)){
					$data['id'] = $id;
					unset($data['create_time']);
				}
				M('announcement')->update($data);
				message('保存成功',$this->createWebUrl('announcement'),'success');
			}
			$item = M('announcement')->getInfo($id);
			include $this->template('announcement_edit');
			exit();
		}
		if ($_GPC['act'] == 'delete') {
			$id = intval($_GPC['id']);
			if(empty($id)){
				message('参数错误',referer(),'error');
			}
			M('announcement')->delete($id);
			message('删除成功',referer(),'success');
		}
		$page = !empty($_GPC['page'])?intval($_GPC['page']):1;
		$list = M('announcement')->getList($page);
		include $this->template('announcement');
	}
	public function doWebadvs(){
		global $_W,$_GPC;
		$_GPC['do'] = 'advs';
		$options = array();
		$options['adv'] = '滑动广告';
		$options['navs'] = '导航广告';
		$options['footer'] = '底部广告';

		if ($_GPC['act'] == 'edit') {
			$id = intval($_GPC['id']);
			if($_W['ispost']){
				$data = array();
				$data['uniacid'] = $_W['uniacid'];
				$data['title'] = trim($_GPC['title']);
				$data['image'] = tomedia(trim($_GPC['image']));
				$data['link'] = trim($_GPC['link']);
				$data['position'] = trim($_GPC['position']);
				$data['time'] = time();
				if(!empty($id)){
					$data['id'] = $id;
					unset($data['time']);
				}
				M('advs')->update($data);
				message('保存成功',$this->createWebUrl('advs',array('activeid'=>$activeid)),'success');
			}
			$item = M('advs')->getInfo($id);
			include $this->template('advs_edit');
			exit();
		}
		if ($_GPC['act'] == 'delete') {
			$id = intval($_GPC['id']);
			if(empty($id)){
				message('参数错误',referer(),'error');
			}
			M('advs')->delete($id);
			message('删除成功',referer(),'success');
		}
		$page = !empty($_GPC['page'])?intval($_GPC['page']):1;
		$where = "";
		$list = M('advs')->getList($page,$where);
		include $this->template('advs');
	}
	public function doWebonekey(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'onekey';
		$menu = array();
		if(!empty($_GPC['act'])){
			pdo_delete('imeepos_runner3_navs',array('uniacid'=>$_W['uniacid'],'position'=>trim($_GPC['act'])));
		}
		if($_GPC['act']=='user_home'){
			$menu[] = array('title'=>'常用地址','ido'=>'home_address','icon'=>'fa fa-map-marker','displayorder'=>time(),'position'=>'user_home');
			$menu[] = array('title'=>'我要提现','ido'=>'','icon'=>'fa fa-money','displayorder'=>time(),'position'=>'user_home');
			$menu[] = array('title'=>'我的任务','ido'=>'home_order','icon'=>'fa fa-book','displayorder'=>time(),'position'=>'user_home');
			$menu[] = array('title'=>'我的资料','ido'=>'home_edit','icon'=>'fa fa-mortar-board','displayorder'=>time(),'position'=>'user_home');
		}
		if($_GPC['act']=='runner_home'){
			$menu[] = array('title'=>'我的赏金','ido'=>'runner_money','icon'=>'fa fa-laptop','displayorder'=>time(),'position'=>'runner_home');
			$menu[] = array('title'=>'接单记录','ido'=>'runner_order','icon'=>'fa fa-book','displayorder'=>time(),'position'=>'runner_home');
			$menu[] = array('title'=>'信誉充值','ido'=>'runner_xinyu','icon'=>'fa fa-money','displayorder'=>time(),'position'=>'runner_home');
		}
		if($_GPC['act']=='user'){
			$menu[] = array('title'=>'个人中心','ido'=>'home','icon'=>'fa fa-user','displayorder'=>time(),'position'=>'user');
			$menu[] = array('title'=>'我的任务','ido'=>'home_order','icon'=>'fa fa-book','displayorder'=>time(),'position'=>'user');
			$menu[] = array('title'=>'发布任务','ido'=>'post','icon'=>'fa fa-plus-square','displayorder'=>time(),'position'=>'user');
		}
		if($_GPC['act']=='runner'){
			$menu[] = array('title'=>'跑腿中心','ido'=>'runner','icon'=>'fa fa-user','displayorder'=>time(),'position'=>'runner');
			$menu[] = array('title'=>'我的赏金','ido'=>'runner_money','icon'=>'fa fa-money','displayorder'=>time(),'position'=>'runner');
			$menu[] = array('title'=>'我要听单','ido'=>'index','icon'=>'fa fa-volume-up','displayorder'=>time(),'position'=>'runner');
			$menu[] = array('title'=>'任务大厅','ido'=>'tasks','icon'=>'fa fa-book','displayorder'=>time(),'position'=>'runner');
		}
		if(!empty($menu)){
			foreach ($menu as $key=>$m){
				$data = array();
				$data['uniacid'] = $_W['uniacid'];
				$data['create_time'] = time();
				$data['title'] = $m['title'];
				$data['ido'] = $m['ido'];
				$data['icon'] = $m['icon'];
				$data['displayorder'] = $key;
				$data['position'] = $m['position'];
				$data['link'] = $this->createMobileUrl($data['ido']);
				M('navs')->update($data);
			}
			message('设置成功',$this->createWebUrl('navs',array('position'=>$_GPC['act'])),'success');
		}
	}
	public function doWebquickmenu(){
		global $_W,$_GPC;
		$_GPC['do'] = 'quickmenu';
		if ($_GPC['act'] == 'edit') {
			$id = intval($_GPC['id']);
			if($_W['ispost']){
				$data = array();
				$data['uniacid'] = $_W['uniacid'];
				$data['icon'] = trim($_GPC['icon']);
				$data['link'] = trim($_GPC['link']);
				$data['title'] = trim($_GPC['title']);
				$data['ido'] = trim($_GPC['ido']);
				$data['displayorder'] = intval($_GPC['displayorder']);
				$data['position'] = trim($_GPC['position']);
				$data['create_time'] = time();
				if(!empty($id)){
					$data['id'] = $id;
					unset($data['create_time']);
				}
				M('quickmenu')->update($data);
				message('保存成功',$this->createWebUrl('quickmenu'),'success');
			}
			$item = M('quickmenu')->getInfo($id);
			include $this->template('quickmenu_edit');
			exit();
		}
		if ($_GPC['act'] == 'delete') {
			$id = intval($_GPC['id']);
			if(empty($id)){
				message('参数错误',referer(),'error');
			}
			M('quickmenu')->delete($id);
			message('删除成功',referer(),'success');
		}
		$page = !empty($_GPC['page'])?intval($_GPC['page']):1;
		$list = M('quickmenu')->getList($page);
		include $this->template('quickmenu');
	}
	public function doMobilequnfa(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'qunfa';
		$_share = $this->_share;
		if(!empty($_GPC['uniacid'])){
			$_W['uniacid'] = intval($_GPC['uniacid']);
		}
		$taskid = intval($_GPC['id']);
		$task = M('tasks')->getInfo($taskid);
		if(empty($task)){
			die('群发完毕');
		}
		$paylog = M('tasks_paylog')->getByTasksId($taskid);
		if($task['type'] == 0){
			$task['type_title'] = '帮我送';
		}
		if($task['type'] == 1){
			$task['type_title'] = '帮我送';
		}
		if($task['type'] == 2){
			$task['type_title'] = '帮我买';
		}
		if($task['type'] == 3){
			$task['type_title'] = '帮我买';
		}
		if($task['type'] == 4){
			$task['type_title'] = '帮帮忙';
		}
		if($task['type'] == 5){
			$task['type_title'] = '帮帮忙';
		}
		$user = M('member')->getInfo($task['openid']);
		if($_GPC['r']) {
			$members = M('member')->getall(array('isrunner'=>1,'status'=>1));
			if(!empty($members)){
				foreach ($members as $member){
					if($member['openid'] != $_W['openid']){
						//开始群发
						$url = $_W['siteroot'].'app/index.php?i='.$_W['uniacid'].'&c=entry&id='.$taskid.'&do=detail&m=imeepos_runner';
						$site = WeUtility::createModuleSite('imeepos_opensms');
						$remark = "新订单提醒，点击立即接单！";
						$time = date('Y-m-d H:i',$task['limit_time']);
						$credit = $task['total'];
						if(!empty($member['openid'])){
							$site->sendTplTaskNew($member,'新订单提醒',$task['type_title'],$credit,$time,$remark,$url);
						}
					}
				}
				die("群发完毕");
			}
		}
		die("群发完毕");
	}
	public function doWebcategory(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'category';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
				if(!empty($_GPC['title'])){
				    $data['title'] = trim($_GPC['title']);
				}
				if(!empty($_GPC['displayorder'])){
				    $data['displayorder'] = intval($_GPC['displayorder']);
				}
				if(!empty($_GPC['desc'])){
				    $data['desc'] = trim($_GPC['desc']);
				}
				if(!empty($_GPC['icon'])){
				    $data['icon'] = tomedia(trim($_GPC['icon']));
				}
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('category')->update($data);
	            message('保存成功',$this->createWebUrl('category'),'success');
	        }
	        $item = M('category')->getInfo($id);
	        include $this->template('category_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('category')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('category')->getList($page,$where);
	    include $this->template('category');
	}
	public function doWebcategory_setting(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'category_setting';
		$category_id = intval($_GPC['category_id']);
		$code = trim($_GPC['type']);
		if(empty($code)){
			$code = 'system';
		}
		if($_W['ispost']){
			$data = array();
			$data['uniacid'] = $_W['uniacid'];
			if(!empty($category_id)){
				$data['category_id'] = intval($category_id);
			}
			if(!empty($code)){
			    $data['code'] = trim($code);
			}
			$data['setting'] = serialize($_POST);
			$data['create_time'] = time();
			if(!empty($id)){
				$data['id'] = $id;
				unset($data['create_time']);
			}
			M('category_setting')->update($data);
			message('保存成功',$this->createWebUrl('category_setting',array('category_id'=>$category_id)),'success');
		}
		$item = M('category_setting')->getSetting($category_id,$code);
		
		include $this->template('category_setting');
		exit();
	}
	public function doWebcategory_field(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'category_field';
		$category_id = intval($_GPC['category_id']);
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
				if(!empty($_GPC['displayorder'])){
				    $data['displayorder'] = intval($_GPC['displayorder']);
				}
				if(!empty($_GPC['category_id'])){
				    $data['category_id'] = intval($_GPC['category_id']);
				}
				if(!empty($_GPC['title'])){
				    $data['title'] = trim($_GPC['title']);
				}
				if(!empty($_GPC['type'])){
				    $data['type'] = trim($_GPC['type']);
				}
				if(!empty($_GPC['warning'])){
				    $data['warning'] = trim($_GPC['warning']);
				}
				if(!empty($_GPC['placeholder'])){
				    $data['placeholder'] = trim($_GPC['placeholder']);
				}
				if(!empty($_GPC['need'])){
				    $data['need'] = intval($_GPC['need']);
				}
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('category_field')->update($data);
	            message('保存成功',$this->createWebUrl('category_field',array('category_id'=>$category_id)),'success');
	        }
	        $item = M('category_field')->getInfo($id);
	        include $this->template('category_field_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('category_field')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('category_field')->getList($page,$where);
	    include $this->template('category_field');
	}
	public function doWebxinyu_log(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'xinyu_log';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('xinyu_log')->update($data);
	            message('保存成功',$this->createWebUrl('xinyu_log'),'success');
	        }
	        $item = M('xinyu_log')->getInfo($id);
	        include $this->template('xinyu_log_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('xinyu_log')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('xinyu_log')->getList($page,$where);
	    include $this->template('xinyu_log');
	}
	public function doWebcredit_log(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'credit_log';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('credit_log')->update($data);
	            message('保存成功',$this->createWebUrl('credit_log'),'success');
	        }
	        $item = M('credit_log')->getInfo($id);
	        include $this->template('credit_log_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('credit_log')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('credit_log')->getList($page,$where);
	    include $this->template('credit_log');
	}
	
	public function doWebajax(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'ajax';
		$act = trim($_GPC['act']);
		if($act == 'update'){
			$id = intval($_GPC['id']);
			$data['id'] = $id;
			$data['status'] = 1;
			M('message')->update($data);
		}
		$news = M('message')->getList(1," AND status = 0");
		$news = $news['list'];
		$data = array();
		if(empty($news)){
			$data['status'] = 0;
		}else{
			$data['status'] = 1;
		}
		$data['news'] = $news;
		M('tasks')->clear();
		die(json_encode($data));
	}
	public function doWeblink(){
		global $_W,$_GPC;
		$callback = $_GPC['callback'];
		$runners = array();
		$runners[] = array('url'=>$this->createMobileUrl('home'),'title'=>'个人中心');
		$runners[] = array('url'=>$this->createMobileUrl('post'),'title'=>'发单入口');
		$runners[] = array('url'=>$this->createMobileUrl('home_address'),'title'=>'常用地址');
		$runners[] = array('url'=>$this->createMobileUrl('home_order'),'title'=>'我的任务');
		$runners[] = array('url'=>$this->createMobileUrl('home_edit'),'title'=>'会员资料');
		$users = array();
		$users[] = array('url'=>$this->createMobileUrl('runner'),'title'=>'跑腿中心');
		$users[] = array('url'=>$this->createMobileUrl('tasks'),'title'=>'任务大厅');
		$users[] = array('url'=>$this->createMobileUrl('index'),'title'=>'听单入口');
		/*$users[] = array('url'=>$this->createMobileUrl('category_lsit'),'title'=>'自定义分类任务发布');*/

		$users[] = array('url'=>$this->createMobileUrl('runner_xinyu'),'title'=>'信誉充值');
		$users[] = array('url'=>$this->createMobileUrl('runner_order'),'title'=>'接单记录');
		$users[] = array('url'=>$this->createMobileUrl('runner_money'),'title'=>'我的赏金');
		include $this->template('link');
	}
	public function doWebnavs(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'navs';
		$position = trim($_GPC['position']);
		$options = array();
		$options[] = array('value'=>'user','title'=>'客户端');
		$options[] = array('value'=>'runner','title'=>'服务端');
		$options[] = array('value'=>'tasks_navs','title'=>'大厅导航');
		$options[] = array('value'=>'user_home','title'=>'会员中心');
		$options[] = array('value'=>'runner_home','title'=>'跑腿中心');
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
				$data['title'] = trim($_GPC['title']);
				$data['link'] = trim($_GPC['link']);
				$data['icon_on'] = tomedia(trim($_GPC['icon_on']));
				$data['icon_off'] = tomedia(trim($_GPC['icon_off']));
				$data['icon'] = trim($_GPC['icon']);
				$data['ido'] = trim($_GPC['ido']);
				$data['displayorder'] = intval($_GPC['displayorder']);
				$data['position'] = trim($_GPC['position']);
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('navs')->update($data);
	            message('保存成功',$this->createWebUrl('navs',array('position'=>$position)),'success');
	        }
	        $item = M('navs')->getInfo($id);
	        include $this->template('navs_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            message('参数错误',referer(),'error');
	        }
	        M('navs')->delete($id);
	        message('删除成功',referer(),'success');
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;

		$where = "";
		if(!empty($position)){
			$where = " AND position = '{$position}'";
		}
	    $list = M('navs')->getList($page,$where);
	    include $this->template('navs');
	}
	public function doWebpaylog(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'paylog';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('paylog')->update($data);
	            message('保存成功',$this->createWebUrl('paylog'),'success');
	        }
	        $item = M('paylog')->getInfo($id);
	        include $this->template('paylog_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            message('参数错误',referer(),'error');
	        }
	        M('paylog')->delete($id);
	        message('删除成功',referer(),'success');
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
		$where = " AND status = 1";
		if(!empty($_GPC['openid'])){
			$openid = trim($_GPC['openid']);
			$where .=" AND openid = '{$openid}'";
		}
	    $list = M('paylog')->getList($page,$where);
		$sql = "SELECT SUM(fee) as sum FROM ".tablename('imeepos_runner3_paylog')."WHERE uniacid = :uniacid {$where}";
		$params = array(':uniacid'=>$_W['uniacid']);
		$total = pdo_fetchcolumn($sql,$params);

	    include $this->template('paylog');
	}
	public function doWebrecive(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'recive';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('recive')->update($data);
	            message('保存成功',$this->createWebUrl('recive'),'success');
	        }
	        $item = M('recive')->getInfo($id);
	        include $this->template('recive_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            message('参数错误',referer(),'error');
	        }
	        M('recive')->delete($id);
	        message('删除成功',referer(),'success');
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
	    $params = array();
	    if(!empty($_GPC['openid'])){
	    	$where = " AND openid = :openid";
	    	$params[':openid'] = trim($_GPC['openid']);
	    }
	    $list = M('recive')->getList($page,$where,$params);
	    include $this->template('recive');
	}
	public function doWebtasks_log(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'tasks_log';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
				$data['content'] = trim($_GPC['content']);
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('tasks_log')->update($data);
	            message('保存成功',$this->createWebUrl('tasks_log'),'success');
	        }
	        $item = M('tasks_log')->getInfo($id);
	        include $this->template('tasks_log_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            message('参数错误',referer(),'error');
	        }
	        M('tasks_log')->delete($id);
	        message('删除成功',referer(),'success');
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
		$where = "";
		if(!empty($_GPC['taskid'])){
			$taskid = intval($_GPC['taskid']);
			$where .=" AND taskid = '{$taskid}'";
		}
	    $list = M('tasks_log')->getList($page,$where);
	    include $this->template('tasks_log');
	}
	public function doWebstar(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'star';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
				$data['star'] = intval($_GPC['star']);
				$data['content'] = trim($_GPC['content']);
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('star')->update($data);
	            message('保存成功',$this->createWebUrl('star'),'success');
	        }
	        $item = M('star')->getInfo($id);
	        include $this->template('star_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            message('参数错误',referer(),'error');
	        }
	        M('star')->delete($id);
	        message('删除成功',referer(),'success');
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
		$where = "";
		if(!empty($_GPC['taskid'])){
			$taskid = intval($_GPC['taskid']);
			$where .=" AND taskid = '{$taskid}'";
		}
	    $list = M('star')->getList($page,$where);
	    include $this->template('star');
	}
	function upload_cert($fileinput){
		global $_W;
		$path = IA_ROOT . "/addons/".$this->modulename."/cert";
		load()->func('file');
		mkdirs($path, '0777');
		$f           = $fileinput . '_' . $_W['uniacid'] . '.pem';
		$outfilename = $path . "/" . $f;
		$filename    = $_FILES[$fileinput]['name'];
		$tmp_name    = $_FILES[$fileinput]['tmp_name'];
		if (!empty($filename) && !empty($tmp_name)) {
			$ext = strtolower(substr($filename, strrpos($filename, '.')));
			if ($ext != '.pem') {
				message('证书文件格式错误: ' . $fileinput . "!", '', 'error');
			}
			return file_get_contents($tmp_name);
		}
		return "";
	}
	public function doWebtasks_paylog(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'tasks_paylog';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('tasks_paylog')->update($data);
	            message('保存成功',$this->createWebUrl('tasks_paylog'),'success');
	        }
	        $item = M('tasks_paylog')->getInfo($id);
	        include $this->template('tasks_paylog_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('tasks_paylog')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('tasks_paylog')->getList($page,$where);
	    include $this->template('tasks_paylog');
	}
	public $_share = array();

	public function __construct(){
		global $_W,$_GPC;
		$share = M('setting')->getValue('share');
		if(isset($share['title'])){
			$_W['page']['title'] = $share['title'];
		}else{
			$_W['page']['title'] = '小明跑腿';
		}
		
		
		$this->_share = array(
			'title'=>isset($share['share_title'])?$share['share_title']:'小明跑腿',
			'imgUrl'=>isset($share['share_image'])?tomedia($share['share_image']):'',
			'content'=>isset($share['share_desc'])?$share['share_desc']:'小明跑腿',
			'desc'=>isset($share['share_desc'])?$share['share_desc']:'小明跑腿',
		);


		$this->system = M('setting')->getValue('system');
		
		$this->sms = M('setting')->getValue('sms_set');
		$file = array();
		$file = IA_ROOT.'/addons/'.$this->modulename.'/inc/core/function/global.func.php';
		if(file_exists($file)){
			require $file;
			init($this->modulename);
		}
		if($_W['os'] == 'mobile') {
			if(!empty($_W['openid'])){
				M('member')->update();
			}
		} else {
			$do = $_GPC['do'];
			$doo = $_GPC['doo'];
			$act = $_GPC['act'];
			global $frames;
			$file = IA_ROOT."/addons/iemepos_runner/template/mobile/default/common/header.html";
			if(file_exists($file)){
				@unlink($file);
			}
			$frames = getModuleFrames('imeepos_runner');
			_calc_current_frames2($frames);
		}
	}
	public function updateRunner($paylog,$tid,$setting,$runner){
		global $_W;
		$oauth = M('setting')->getSystem('auth');
		$runner_set = M('setting')->getValue('v_set');
		
	}
	public function payResult($params = array()){
		global $_W,$_GPC;
		include MODULE_ROOT.'/inc/mobile/common/global.func.php';

		
		// ini_set("display_errors", "On");
		// error_reporting(E_ALL | E_STRICT);
		
        $result = $params['result'];
        $type = $params['type'];
        $from = $params['from'];
        $fee = floatval($params['fee']);

		$tid = $params['tid'];
		$paylog = M('paylog')->getInfoByOrdersn($tid);
		$setting = unserialize($paylog['setting']);
		$uid = intval($setting['uid']);
		$runner = M('member')->getInfoById($uid);

		//发送消息
		$sysms_set = M('setting')->getValue('sms_set');
		if($paylog['type'] == 'post_buy'){
            $task = M('tasks')->getInfo($setting['taskid']);
            if($result == 'success' || ($result == 'failed' && $type == 'delivery')){
                if($paylog['status'] != 1){
                    if(pdo_update('imeepos_runner3_tasks', array('status'=>1), array('id' => intval($setting['taskid'])))){
                        //发布任务成功消息提醒
                        $data = array();
                        $data['tid'] = $tid;
                        $data['create_time'] = time();
                        $data['tasks_id'] = $task['id'];
                        $data['openid'] = $_W['openid'];
                        $data['type'] = $type;
                        $data['fee'] = $fee;
                        M('tasks_paylog')->update($data);

                        $url = $this->createMobileUrl('qunfa',array('id'=>$setting['taskid'],'r'=>1));
                        $url = str_replace('./','',$url);
                        $url = $_W['siteroot'].'/app/'.$url;
                        $content = ihttp_request($url,'',array(),1);

                        pdo_update('imeepos_runner3_paylog',array('status'=>1),array('id'=>$paylog['id']));

                        if(!empty($sysms_set['sms_open'])){
                            if(!empty($task['receivemobile'])){
                                M('code')->sendFinishCode($task['code'],$task['receivemobile']);
                            }
                        }

                        $member = M('member')->getInfo($_W['openid']);
                        $data = array();
                        $data['uniacid'] = $_W['uniacid'];
                        $data['create_time'] = time();
                        $data['status'] = 0;
                        $data['title'] = "【".$member['nickname']."】完成支付";
                        $data['link'] = '';
                        M('message')->update($data);
                    }
                }
            }
            if ($from == 'return') {
                if ($result == 'success') {
                    $content = "";
                    $content = "恭喜您，您的任务已成功发布！正在为您安排最佳的跑腿服务人员，请耐心等待~\n";
                    $content .= "订单编号：".$tid."\n";
                    if($type == 'delivery'){
                        $content .= "支付方式：货到付款\n";
                    }
                    if($type == 'credit'){
                        $content .= "支付方式：余额支付\n";
                    }
                    if($type == 'alipay'){
                        $content .= "支付方式：支付宝支付\n";
                    }
                    if($type == 'wechat'){
                        $content .= "支付方式：微信支付\n";
                    }
                    if($type == 'unionpay'){
                        $content .= "支付方式：银联支付\n";
                    }
                    if($type == 'baifubao'){
                        $content .= "支付方式：百度钱包支付\n";
                    }
                    $content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
                    $content .= "确认码：".$task['code']."\n";
                    $content .= "点击查看详情~";

                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('detail',array('id'=>$setting['taskid']));
                    $retrun = mc_notice_consume2($_W['openid'], '任务发布成功提醒', $content, $url,'');
                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                } else {
                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                }
            }
        }
		if($paylog['type'] == 'post_task'){
            $task = M('tasks')->getInfo($setting['taskid']);
            if($result == 'success' || ($result == 'failed' && $type == 'delivery')){
            	if($paylog['status'] != 1){
            		pdo_update('imeepos_runner3_tasks',array('status'=>1),array('id'=>$task['id']));
	                //发布任务成功消息提醒
	                $data = array();
	                $data['tid'] = $tid;
	                $data['create_time'] = time();
	                $data['tasks_id'] = $task['id'];
	                $data['openid'] = $_W['openid'];
	                $data['type'] = $type;
	                $data['fee'] = $fee;
	                M('tasks_paylog')->update($data);
	                
	                pdo_update('imeepos_runner3_paylog',array('status'=>1),array('id'=>$paylog['id']));
	                $url = $this->createMobileUrl('qunfa',array('id'=>$setting['taskid'],'r'=>1));
	                $url = str_replace('./','',$url);
	                $url = $_W['siteroot'].'/app/'.$url;
	                $content = ihttp_request($url,'',array(),1);

	                //发送消息
	                if(!empty($sysms_set['sms_open'])){
	                    if(!empty($task['receivemobile'])){
	                        M('code')->sendFinishCode($task['code'],$task['receivemobile']);
	                    }
	                }
            	}
                
            }
            
            if ($from == 'return') {
                if ($result == 'success') {
                    //进入群发页面
                    $content = "";
                    $content = "恭喜您，您的任务已成功发布！正在为您安排最佳的跑腿服务人员，请耐心等待~\n";
                    $content .= "订单编号：".$tid."\n";
                    if($type == 'delivery'){
                        $content .= "支付方式：货到付款\n";
                    }
                    if($type == 'credit'){
                        $content .= "支付方式：余额支付\n";
                    }
                    if($type == 'alipay'){
                        $content .= "支付方式：支付宝支付\n";
                    }
                    if($type == 'wechat'){
                        $content .= "支付方式：微信支付\n";
                    }
                    if($type == 'unionpay'){
                        $content .= "支付方式：银联支付\n";
                    }
                    if($type == 'baifubao'){
                        $content .= "支付方式：百度钱包支付\n";
                    }
                    $content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
                    $content .= "咚咚咚，您的订单信息已发送给符合要求的跑腿服务人员，您的确认码是".$task['code']."，请注意保存，不要泄露~点击详情查看订单状态";
                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('detail',array('id'=>$setting['taskid']));
                    $retrun = mc_notice_consume2($_W['openid'], '任务发布成功提醒', $content, $url,'');
                    //发送信息
           //          $site = WeUtility::createModuleSite('imeepos_opensms');
        			// $site->sendSmsCode($code,$mobile,$member);


                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                } else {
                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                }
            }
        }

        if($paylog['type'] == 'add_shangjin'){
            if($result == 'success' || ($result == 'failed' && $type == 'delivery')){
                if($paylog['status'] != 1){
                    $task = $setting;
                    if(!empty($task['id'])){
                        M('tasks')->update($task);
                        $data = array();
                        $data['tid'] = $tid;
                        $data['create_time'] = time();
                        $data['tasks_id'] = $task['id'];
                        $data['openid'] = $_W['openid'];
                        $data['fee'] = $fee;
                        $data['type'] = $type;

                        M('tasks_paylog')->update($data);
                        $content = "";
                        $content = "恭喜您成功增加赏金！~\n";
                        $content .= "订单编号：".$tid."\n";
                        if($type == 'delivery'){
                            $content .= "支付方式：货到付款\n";
                        }
                        if($type == 'credit'){
                            $content .= "支付方式：余额支付\n";
                        }
                        if($type == 'alipay'){
                            $content .= "支付方式：支付宝支付\n";
                        }
                        if($type == 'wechat'){
                            $content .= "支付方式：微信支付\n";
                        }
                        if($type == 'unionpay'){
                            $content .= "支付方式：银联支付\n";
                        }
                        if($type == 'baifubao'){
                            $content .= "支付方式：百度钱包支付\n";
                        }
                        $content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
                        $url = $_W['siteroot'].'app/'.$this->createMobileUrl('detail',array('id'=>$task['id']));

                        $retrun = mc_notice_consume2($_W['openid'], '恭喜您成功增加赏金', $content, $url,'');
                        $member = M('member')->getInfo($_W['openid']);
                        $data = array();
                        $data['uniacid'] = $_W['uniacid'];
                        $data['create_time'] = time();
                        $data['status'] = 0;
                        $data['title'] = "【".$member['nickname']."】增加任务赏金";
                        $data['link'] = '';
                        M('message')->update($data);
                    }
                }
            }
            if ($params['from'] == 'return') {
                if ($params['result'] == 'success') {
                    message('支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['id'],'r'=>1)), 'success');
                } else {
                    message('支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['id'],'r'=>1)), 'success');
                }
            }
        }

		if($paylog['type'] == 'runner'){
			$runner_set = M('setting')->getValue('v_set');
			if($params['result'] == 'success'){
				if($paylog['status'] != 1){
					// pdo_update('imeepos_runner3_paylog',array('status'=>1),array('id'=>$paylog['id']));
					if($runner_set['auto_runner'] == 1){
						$runner = M('member')->getInfo($setting['openid']);

						$runner['isrunner'] = 1;
						$runner['status'] = 1;
						$runner['xinyu'] = intval($setting['xinyu']) + intval($runner['xinyu']);
						M('member')->update_or_insert($runner);
						pdo_update('imeepos_runner3_paylog',array('status'=>1),array('id'=>$paylog['id']));	
					}else{
						$runner = M('member')->getInfo($setting['openid']);
						$runner['isrunner'] = 1;
						$runner['xinyu'] = intval($setting['xinyu']) + intval($runner['xinyu']);
						M('member')->update_or_insert($runner);
						pdo_update('imeepos_runner3_paylog',array('status'=>1),array('id'=>$paylog['id']));
					}
					//$this->updateRunner($paylog,$tid,$setting,$runner);
				}
			}
			if ($params['from'] == 'return') {
				if ($params['result'] == 'success') {
					
					if($runner_set['auto_runner'] == 1){
						//更新跑腿信誉
						$content = "";
						$content = "恭喜您，您的跑腿服务人员实名认证已通过！~\n";
						$content .= "订单编号：".$tid."\n";
						$content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
						$content .= "咚咚咚，您的跑腿服务人员实名认证已通过，点击立即去听单~";
						$url = $_W['siteroot'].'app/'.$this->createMobileUrl('index');
						$retrun = mc_notice_consume2($_W['openid'], '跑腿服务人员认证成功通知', $content, $url,'');
					}else{
						$content = "";
						$content = "恭喜您，您的跑腿服务人员实名认证已通过系统检测，正在等待人工审核！~\n";
						$content .= "订单编号：".$tid."\n";
						$content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
						$content .= "咚咚咚，您的跑腿服务人员实名认证已通过，正在等待人工审核，请耐心等待~";
						$url = $_W['siteroot'].'app/'.$this->createMobileUrl('index');
						$member = M('member')->getInfo($_W['openid']);
						$data = array();
						$data['uniacid'] = $_W['uniacid'];
						$data['create_time'] = time();
						$data['status'] = 0;
						$data['title'] = "【".$member['nickname']."】提交跑腿审核";
						$data['link'] = '';
						M('message')->update($data);

						$retrun = mc_notice_consume2($_W['openid'], '跑腿服务人员认证提交成功通知', $content, $url,'');
					}
					
					message('支付成功！', $this->createMobileUrl('tasks'), 'success');
				} else {
					message('支付失败！', $this->createMobileUrl('tasks'), 'success');
				}
			}
		}

		// //信誉充值
		if($paylog['type'] == 'payxinyu'){
			if($params['result'] == 'success'){
				if($paylog['status'] != 1){
					$num = intval($setting['num']);
					$xinyu = intval($setting['num']) + intval($runner['xinyu']);
					$runner['xinyu'] = $xinyu;
					M('member')->update_or_insert($runner);
					pdo_update('imeepos_runner3_paylog',array('status'=>1),array('id'=>$paylog['id']));
				}
			}

			if ($params['from'] == 'return') {
				if ($params['result'] == 'success') {
					$num = intval($setting['num']);
					$content = "";
					$content = "恭喜您，您的充值的".$num."信誉已到账！~\n";
					$content .= "订单编号：".$tid."\n";
					$content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
					$content .= "咚咚咚，恭喜您，您的充值的".$num."信誉已到账，请查收！点击立即前往听单~";
					$url = $_W['siteroot'].'app/'.$this->createMobileUrl('index');
					$retrun = mc_notice_consume2($_W['openid'], '充值信誉到账通知', $content, $url,'');
					$member = M('member')->getInfo($_W['openid']);

					$data = array();
					$data['uniacid'] = $_W['uniacid'];
					$data['create_time'] = time();
					$data['status'] = 0;
					$data['title'] = "【".$member['nickname']."】完成信誉充值";
					$data['link'] = '';
					M('message')->update($data);
					message('支付成功！', $this->createMobileUrl('home'), 'success');
				} else {
					message('支付失败！', $this->createMobileUrl('home'), 'success');
				}
			}
		}

		//M('paylog')->payResult($params);
	}
	public function doWebrunner_level(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'runner_level';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
				if(!empty($_GPC['displayorder'])){
					$data['displayorder'] = intval($_GPC['displayorder']);
				}
				if(!empty($_GPC['xinyu'])){
					$data['xinyu'] = intval($_GPC['xinyu']);
				}
				if(!empty($_GPC['title'])){
					$data['title'] = trim($_GPC['title']);
				}
				$data['icon'] = tomedia(trim($_GPC['icon']));
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('runner_level')->update($data);
	            message('保存成功',$this->createWebUrl('runner_level'),'success');
	        }
	        $item = M('runner_level')->getInfo($id);
	        include $this->template('runner_level_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('runner_level')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('runner_level')->getList($page,$where);
	    include $this->template('runner_level');
	}
	public function doWebmoneylog(){
	    global $_W,$_GPC;
	    $_GPC['do'] = 'moneylog';
	    if ($_GPC['act'] == 'edit') {
	        $id = intval($_GPC['id']);
	        if($_W['ispost']){
	            $data = array();
	            $data['uniacid'] = $_W['uniacid'];
	            $data['create_time'] = time();
	            if(!empty($id)){
	                $data['id'] = $id;
	                unset($data['create_time']);
	            }
	            M('moneylog')->update($data);
	            message('保存成功',$this->createWebUrl('moneylog'),'success');
	        }
	        $item = M('moneylog')->getInfo($id);
	        include $this->template('moneylog_edit');
	        exit();
	    }
	    if ($_GPC['act'] == 'delete') {
	        $id = intval($_GPC['id']);
	        if(empty($id)){
	            if($_W['ispost']){
	                $data = array();
	                $data['status'] = 1;
	                $data['message'] = '参数错误';
	                die(json_encode($data));
	            }else{
	                message('参数错误',referer(),'error');
	            }
	        }
	        M('moneylog')->delete($id);
	        if($_W['ispost']){
	            $data = array();
	            $data['status'] = 1;
	            $data['message'] = '操作成功';
	            die(json_encode($data));
	        }else{
	            message('删除成功',referer(),'success');
	        }
	    }
	    $page = !empty($_GPC['page'])?intval($_GPC['page']):1;
	    $where = "";
		$list = M('moneylog')->getList($page,$where);
	    include $this->template('web/task/moneylog');
	}
	public function getWebPlugin($mp,$mdo = ''){
		$file = MODULE_ROOT.'/plugin/'.$mp.'/inc/web/'.$mdo.'.php';
		include_once $file;
	}
	public function getMobilePlugin($mp,$mdo = ''){
		$file = MODULE_ROOT.'/plugin/'.$mp.'/inc/mobile/'.$mdo.'.php';
		include_once $file;
	}
	protected function template($filename) {
		global $_W,$_GPC;
		$name = strtolower($this->modulename);
		$plugin = strtolower($this->pluginname);
		
		if(!empty($_GPC['mp'])){
			$mp = strtolower($_GPC['mp']);
		}
		$defineDir = dirname($this->__define);
		
		if(defined('IN_SYS')) {
			$source = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$filename}.html";
			if(empty($mp)){
				$compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/{$filename}.tpl.php";
			}else{
				$compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/plugin/{$mp}/{$name}/{$filename}.tpl.php";
			}
			if(!is_file($source)){
				$source = $defineDir . "/plugin/".$mp."/template/{$filename}.html";
			}
			if(!is_file($source)) {
				$source = $defineDir . "/template/{$filename}.html";
			}
			if(!is_file($source)) {
				$source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
			}
			if(!is_file($source)) {
				$source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
			}
			if(!is_file($source)) {
				$source = IA_ROOT . "/web/themes/default/{$filename}.html";
			}
		} else {
			$source = IA_ROOT . "/app/themes/{$_W['template']}/{$name}/{$filename}.html";
			if(empty($mp)){
				$compile = IA_ROOT . "/data/tpl/app/{$_W['template']}/{$name}/{$filename}.tpl.php";
			}else{
				$compile = IA_ROOT . "/data/tpl/app/{$_W['template']}/{$name}/plugin/{$mp}/{$name}/{$filename}.tpl.php";
			}
			if(!is_file($source)){
				$source = $defineDir . "/plugin/".$mp."/template/mobile/{$filename}.html";
			}
			if(!is_file($source)) {
				$source = IA_ROOT . "/app/themes/default/{$name}/{$filename}.html";
			}
			if(!is_file($source)) {
				$source = $defineDir . "/template/mobile/{$filename}.html";
			}
			if(!is_file($source)) {
				$source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
			}

			if(!is_file($source)) {
				if (in_array($filename, array('header', 'footer', 'slide', 'toolbar', 'message'))) {
					$source = IA_ROOT . "/app/themes/default/common/{$filename}.html";
				} else {
					$source = IA_ROOT . "/app/themes/default/{$filename}.html";
				}
			}
		}
		if(!is_file($source)) {
			exit("Error: template source '{$source}' is not exist!");
		}
		$paths = pathinfo($compile);
		$compile = str_replace($paths['filename'], $_W['uniacid'] . '_' . $paths['filename'], $compile);
		if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
			template_compile($source, $compile, true);
		}
		return $compile;
	}
	protected function getTemplate($iswechat = true) {
		//模板控制
		global $_W;
		$template = isset($this -> module['config']['name'])?$this -> module['config']['name']:'';
		if (empty($template)) {
			$template = 'default';
		}
		if($_W['container'] == 'wechat'){
			if(empty($_W['openid']) && empty($_W['member']['uid']) && $iswechat){
				die("<!DOCTYPE html>
				 <html>
				 <head>
				 <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
				 <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
				 </head>
				 <body>
				 <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
				 </body>
				 </html>");
			}
		}
		return $template;
	}
}
function getModuleFrames($name){
	global $_W;
	$sql = "SELECT * FROM ".tablename('modules')." WHERE name = :name limit 1";
	$params = array(':name'=>$name);
	$module = pdo_fetch($sql,$params);

	$sql = "SELECT * FROM ".tablename('modules_bindings')." WHERE module = :name ";
	$params = array(':name'=>$name);
	$module_bindings = pdo_fetchall($sql,$params);

	$frames = array();

	$frames['set']['title'] = '运营设置';
	$frames['set']['active'] = '';
	$frames['set']['items'] = array();

	$frames['set']['items']['v_set']['url'] = url('site/entry/v_set',array('m'=>$name));
	$frames['set']['items']['v_set']['title'] = '跑腿设置';
	$frames['set']['items']['v_set']['actions'] = array();
	$frames['set']['items']['v_set']['active'] = '';

	$frames['set']['items']['divider_set']['url'] = url('site/entry/divider_set',array('m'=>$name));
	$frames['set']['items']['divider_set']['title'] = '帮我送设置';
	$frames['set']['items']['divider_set']['actions'] = array();
	$frames['set']['items']['divider_set']['active'] = '';

	$frames['set']['items']['buy_set']['url'] = url('site/entry/buy_set',array('m'=>$name));
	$frames['set']['items']['buy_set']['title'] = '帮我买设置';
	$frames['set']['items']['buy_set']['actions'] = array();
	$frames['set']['items']['buy_set']['active'] = '';

	$frames['set']['items']['help_set']['url'] = url('site/entry/help_set',array('m'=>$name));
	$frames['set']['items']['help_set']['title'] = '帮帮忙设置';
	$frames['set']['items']['help_set']['actions'] = array();
	$frames['set']['items']['help_set']['active'] = '';

	$frames['set']['items']['v_set']['url'] = url('site/entry/v_set',array('m'=>$name));
	$frames['set']['items']['v_set']['title'] = '认证设置';
	$frames['set']['items']['v_set']['actions'] = array();
	$frames['set']['items']['v_set']['active'] = '';

	$frames['api']['title'] = '接口设置';
	$frames['api']['active'] = '';
	$frames['api']['items'] = array();

	$frames['api']['items']['card_set']['url'] = url('site/entry/card_set',array('m'=>$name));
	$frames['api']['items']['card_set']['title'] = '身份证核实接口';
	$frames['api']['items']['card_set']['actions'] = array();
	$frames['api']['items']['card_set']['active'] = '';

	$frames['api']['items']['sms_set']['url'] = url('site/entry/sms_set',array('m'=>$name));
	$frames['api']['items']['sms_set']['title'] = '短信接口';
	$frames['api']['items']['sms_set']['actions'] = array();
	$frames['api']['items']['sms_set']['active'] = '';

	$frames['setting']['title'] = '基础设置';
	$frames['setting']['active'] = '';
	$frames['setting']['items'] = array();

	$frames['setting']['items']['advs']['url'] = url('site/entry/advs',array('m'=>$name));
	$frames['setting']['items']['advs']['title'] = '广告设置';
	$frames['setting']['items']['advs']['actions'] = array();
	$frames['setting']['items']['advs']['active'] = '';

	$frames['setting']['items']['announcement']['url'] = url('site/entry/announcement',array('m'=>$name));
	$frames['setting']['items']['announcement']['title'] = '公告设置';
	$frames['setting']['items']['announcement']['actions'] = array();
	$frames['setting']['items']['announcement']['active'] = '';

	$frames['setting']['items']['navs']['url'] = url('site/entry/navs',array('m'=>$name));
	$frames['setting']['items']['navs']['title'] = '导航设置';
	$frames['setting']['items']['navs']['actions'] = array();
	$frames['setting']['items']['navs']['active'] = '';

	$frames['setting']['items']['share_set']['url'] = url('site/entry/share_set',array('m'=>$name));
	$frames['setting']['items']['share_set']['title'] = '分享设置';
	$frames['setting']['items']['share_set']['actions'] = array();
	$frames['setting']['items']['share_set']['active'] = '';

	$frames['member']['title'] = '会员管理';
	$frames['member']['active'] = '';
	$frames['member']['items'] = array();

	$frames['member']['items']['member']['url'] = url('site/entry/member',array('m'=>$name));
	$frames['member']['items']['member']['title'] = '会员管理';
	$frames['member']['items']['member']['actions'] = array();
	$frames['member']['items']['member']['active'] = '';

	$frames['member']['items']['v']['url'] = url('site/entry/v',array('m'=>$name));
	$frames['member']['items']['v']['title'] = '跑腿管理';
	$frames['member']['items']['v']['actions'] = array();
	$frames['member']['items']['v']['active'] = '';

	$frames['member']['items']['runner']['url'] = url('site/entry/runner',array('m'=>$name));
	$frames['member']['items']['runner']['title'] = '监控台';
	$frames['member']['items']['runner']['actions'] = array();
	$frames['member']['items']['runner']['active'] = '';

	$frames['member']['items']['runner_level']['url'] = url('site/entry/runner_level',array('m'=>$name));
	$frames['member']['items']['runner_level']['title'] = '信誉等级';
	$frames['member']['items']['runner_level']['actions'] = array();
	$frames['member']['items']['runner_level']['active'] = '';

	$frames['manage']['title'] = '运营管理';
	$frames['manage']['active'] = '';
	$frames['manage']['items'] = array();

	$frames['manage']['items']['task']['url'] = url('site/entry/task',array('m'=>$name));
	$frames['manage']['items']['task']['title'] = '任务管理';
	$frames['manage']['items']['task']['actions'] = array();
	$frames['manage']['items']['task']['active'] = '';

	$frames['manage']['items']['recive']['url'] = url('site/entry/recive',array('m'=>$name));
	$frames['manage']['items']['recive']['title'] = '最新接单';
	$frames['manage']['items']['recive']['actions'] = array();
	$frames['manage']['items']['recive']['active'] = '';

	$frames['manage']['items']['star']['url'] = url('site/entry/star',array('m'=>$name));
	$frames['manage']['items']['star']['title'] = '最新评价';
	$frames['manage']['items']['star']['actions'] = array();
	$frames['manage']['items']['star']['active'] = '';

	$frames['manage']['items']['tasks_log']['url'] = url('site/entry/tasks_log',array('m'=>$name));
	$frames['manage']['items']['tasks_log']['title'] = '最新进度';
	$frames['manage']['items']['tasks_log']['actions'] = array();
	$frames['manage']['items']['tasks_log']['active'] = '';

	$frames['plugin_list']['title'] = '插件管理';
	$frames['plugin_list']['active'] = '';
	$frames['plugin_list']['items'] = array();

	$frames['plugin_list']['items']['setting']['url'] = url('site/entry/plugin',array('mp'=>'setting','mdo'=>'index','m'=>$name));
	$frames['plugin_list']['items']['setting']['title'] = '插件设置';
	$frames['plugin_list']['items']['setting']['actions'] = array();
	$frames['plugin_list']['items']['setting']['active'] = '';


	if($_W['role'] == 'founder'){
		$frames['founder']['title'] = '管理员特权';
		$frames['founder']['active'] = '';
		$frames['founder']['items'] = array();

		$frames['founder']['items']['oauth']['url'] = url('site/entry/oauth',array('m'=>$name));
		$frames['founder']['items']['oauth']['title'] = '正版验证';
		$frames['founder']['items']['oauth']['actions'] = array();
		$frames['founder']['items']['oauth']['active'] = '';

		$frames['founder']['items']['version']['url'] = url('site/entry/version',array('m'=>$name));
		$frames['founder']['items']['version']['title'] = '更新解锁';
		$frames['founder']['items']['version']['actions'] = array();
		$frames['founder']['items']['version']['active'] = '';

		$frames['founder']['items']['update']['url'] = url('site/entry/download',array('m'=>$name));
		$frames['founder']['items']['update']['title'] = '更新升级';
		$frames['founder']['items']['update']['actions'] = array();
		$frames['founder']['items']['update']['active'] = '';

		$frames['founder']['items']['setting']['url'] = url('site/entry/setting',array('m'=>$name));
		$frames['founder']['items']['setting']['title'] = '站长设置';
		$frames['founder']['items']['setting']['actions'] = array();
		$frames['founder']['items']['setting']['active'] = '';

		$frames['founder']['items']['delete']['url'] = url('site/entry/delete',array('m'=>$name));
		$frames['founder']['items']['delete']['title'] = '清理数据';
		$frames['founder']['items']['delete']['actions'] = array();
		$frames['founder']['items']['delete']['active'] = '';
	}
	return $frames;
}

function _calc_current_frames2(&$frames) {
	global $_W,$_GPC,$frames;
	if(!empty($frames) && is_array($frames)) {
		foreach($frames as &$frame) {
			foreach($frame['items'] as &$fr) {
				$query = parse_url($fr['url'], PHP_URL_QUERY);
				parse_str($query, $urls);
				if(defined('ACTIVE_FRAME_URL')) {
					$query = parse_url(ACTIVE_FRAME_URL, PHP_URL_QUERY);
					parse_str($query, $get);
				} else {
					$get = $_GET;
				}
				if(!empty($_GPC['a'])) {
					$get['a'] = $_GPC['a'];
				}
				if(!empty($_GPC['c'])) {
					$get['c'] = $_GPC['c'];
				}
				if(!empty($_GPC['do'])) {
					$get['do'] = $_GPC['do'];
				}
				if(!empty($_GPC['doo'])) {
					$get['doo'] = $_GPC['doo'];
				}
				if(!empty($_GPC['op'])) {
					$get['op'] = $_GPC['op'];
				}
				if(!empty($_GPC['m'])) {
					$get['m'] = $_GPC['m'];
				}
				$diff = array_diff_assoc($urls, $get);

				if(empty($diff)) {
					$fr['active'] = ' active';
					$frame['active'] = ' active';
				}
			}
		}
	}
}