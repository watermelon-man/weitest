	<?php
/**
 * 小明跑腿模块微站定义
 *
 * @author imeepos
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

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
	// header("Access-Control-Allow-Origin:*");
	$data = array();
	$data['status'] = $status;
	$data['message'] = $message;
	$data['info'] = $info;
	die(json_encode($data));
}

define('Meepo_Debug',false);

if(Meepo_Debug){
	ini_set("display_errors", "On");
	error_reporting(E_ALL | E_STRICT);
}

M('tasks');

class Imeepos_runnerModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面
		global $_W,$_GPC;
		load()->model('mc');
    	mc_oauth_userinfo();

    	$setting = M('setting')->getValue('setting.system');
    	
    	include $this->template('index');
	}

	public function doMobilec0() {
		//这个操作被定义用来呈现 管理中心导航菜单
		// global $_W,$_GPC;
		// load()->model('mc');
  //   	mc_oauth_userinfo();
		// include $this->template('index.cache');
	}
	public function doMobileOpen(){
	    global $_W,$_GPC;
        header("Access-Control-Allow-Origin:*");
        $__do = trim($_GPC['__do']);
        $input = isset($_GPC['__input']) ? $_GPC['__input'] : array();
        die($this->router->reset()->exec($__do,$input)->getJson());
    }

    public function doWebUpdate(){
    	global $_W;
    	$file = IA_ROOT.'/addons/imeepos_runner/install.php';
        include_once $file;
	    $file = IA_ROOT.'/addons/imeepos_runner/update.php';
        include_once $file;
		$file = IA_ROOT.'/addons/imeepos_runner/update-v10.php';
		include_once $file;
		message('更新成功');
    }

    public function doWebAdmin(){
    	return $this->doWebIndex();
    }

    public function doWebDelete(){
    	global $_W;
    	pdo_delete('imeepos_runner3_member',array('uniacid'=>$_W['uniacid']));

		message('更新成功');
    }
    public function doWebClear(){
    	global $_W,$_GPC;
    	$setting = array();
    	$code = 'update.setting';
    	$setting = M('setting')->getValue($code);
    	if(empty($setting)){
    		// pdo_delete('imeepos_runner3_member',array('uniacid'=>$_W['uniacid']));ss
	    	pdo_delete('imeepos_runner3_tasks_log',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_tasks',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_detail',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_tasks_paylog',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_recive',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_moneylog',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_listenlog',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_code',array('uniacid'=>$_W['uniacid']));
	    	pdo_delete('imeepos_runner3_address',array('uniacid'=>$_W['uniacid']));
	    	$value = serialize(array('version'=>'10.0.0'));
	    	M('setting')->update($code,$value);
    	}

    }

    public function doWebUpdatev10(){
    	global $_W;
    	$file = IA_ROOT.'/addons/imeepos_runner/update-v10.php';
		include_once $file;
		
		message('更新成功');
    }

	public function doWebIndex() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
		$role = $_W['role'];
		$uid = $_W['uid'];
		$this->doWebClear();
		if(!pdo_fieldexists('imeepos_runner3_member','isadmin')){
	        pdo_query("ALTER TABLE ".tablename('imeepos_runner3_member')." ADD COLUMN `isadmin` tinyint(2) DEFAULT '0'");
	    }
	    if(!pdo_fieldexists('imeepos_runner3_member','ismanager')){
	        pdo_query("ALTER TABLE ".tablename('w')." ADD COLUMN `ismanager` tinyint(2) DEFAULT '0'");
	    }

		if($_W['isajax']){
			$openid = trim($_GPC['openid']);
			$action = trim($_GPC['action']);	
			$data = array();
			$data['code'] = 1;
			$data['info'] = array();
			$data['msg'] = '操作成功';
			if($action == 'isadmin'){
				if($role == 'founder'){
					$member = pdo_get('imeepos_runner3_member',array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
					if(empty($member)){
						$data['code'] = 0;
						$data['msg'] = '会员不保存在或已删除';
						die(json_encode($data));
					}
					if($member['isadmin'] == 1){
						pdo_update('imeepos_runner3_member',array('isadmin'=>0),array('id'=>$member['id']));
						$data['msg'] = '操作成功';
						die(json_encode($data));
					}else{
						pdo_update('imeepos_runner3_member',array('isadmin'=>1),array('id'=>$member['id']));
						$data['msg'] = '操作成功';
						die(json_encode($data));
					}
				}else{
					$data['code'] = 0;
					$data['msg'] = '权限错误';
					die(json_encode($data));
				}
			}

			if($action == 'isrunner'){
				if($_W['uid'] > 0){
					$member = pdo_get('imeepos_runner3_member',array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
					if(empty($member)){
						$data['code'] = 0;
						$data['msg'] = '会员不保存在或已删除';
						die(json_encode($data));
					}
					if($member['isrunner'] == 1){
						pdo_update('imeepos_runner3_member',array('isrunner'=>0),array('id'=>$member['id']));
						$data['msg'] = '操作成功';
						die(json_encode($data));
					}else{
						pdo_update('imeepos_runner3_member',array('isrunner'=>1),array('id'=>$member['id']));
						$data['msg'] = '操作成功';
						die(json_encode($data));
					}
				}else{
					$data['code'] = 0;
					$data['msg'] = '权限错误';
					die(json_encode($data));
				}
			}

			if($action == 'ismanager'){
				if($_W['uid'] > 0){
					$member = pdo_get('imeepos_runner3_member',array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
					if(empty($member)){
						$data['code'] = 0;
						$data['msg'] = '会员不保存在或已删除';
						die(json_encode($data));
					}
					if($member['ismanager'] == 1){
						pdo_update('imeepos_runner3_member',array('ismanager'=>0),array('id'=>$member['id']));
						$data['msg'] = '操作成功';
						die(json_encode($data));
					}else{
						pdo_update('imeepos_runner3_member',array('ismanager'=>1),array('id'=>$member['id']));
						$data['msg'] = '操作成功';
						die(json_encode($data));
					}
				}else{
					$data['code'] = 0;
					$data['msg'] = '权限错误';
					die(json_encode($data));
				}
			}

			die();
		}
		
		$page = intval($_GPC['page']);
		$page = $page > 0 ? $page : 1;
		$where = "";
		$params = array();
		if($_GPC['isadmin'] == 'on'){
			$where .= " AND isadmin = 1";
		}
		if($_GPC['ismanager'] == 'on'){
			$where .= " AND ismanager = 1";
		}
		if($_GPC['isrunner'] == 'on'){
			$where .= " AND isrunner = 1";
		}
		$list = M('member')->getList($page,$where,$params);
		include $this->template('index');
	}

	public function doWebBindQrcode(){
		global $_W,$_GPC;
		$url = $this->createMobileUrl('');
	}

	

	public function doMobileSocket(){
		global $_W,$_GPC;
		header("Content-type:text/event-stream");
		header("Access-Control-Allow-Origin:*");
		$__do = trim($_GPC['__do']);
		$input = isset($_GPC['__input']) ? $_GPC['__input'] : array();
		while(true){
			$data = $this->router->reset()->exec($__do,$input)->getJson();
			echo "\n\ndata:{$data}\n\n";
			usleep(1 * 1000000);
			die();
			@ob_flush();@flush();
		}
	}

	public function __construct(){
		global $_W,$_GPC;
		$file = IA_ROOT."/addons/imeepos_runner/core/Router.php";
		if(file_exists($file)){
			include_once $file;
			$this->router = new Router();
		}
		if($_W['os'] == 'mobile') {
			if(!empty($_W['openid'])){
				M('member')->update();
			}else{
				die('请在微信中打开或者借用授权');
			}
		}
	}

	public function doWebSetting(){
		global $_W,$_GPC;
		$code = 'system.code.tpl';
		if($_W['ispost']){
			$input = $_POST;
			foreach($input as $key=>&$in){
				$in = trim($in);
			}
			$data = serialize($input);
			M('setting')->update($code,$data);
			die(json_encode($input));
		}
		$setting = M('setting')->getValue('system.code.tpl');
		if(empty($setting)){
			$setting = array(
				'appid'=>'',
				'appkey'=>''
			);
		}
		include $this->template('setting');
	}

}