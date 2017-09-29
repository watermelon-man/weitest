<?php
/**
 * 手写笔墨模块
 *
 * @author 微特网络
 * @url http://www.iweite.com
 */
defined('IN_IA') or exit('Access Denied');
define('RES', '../addons/iweite_sxbm/template/themes');
class Iweite_sxbmModuleSite extends WeModuleSite {
	public $modulename = 'iweite_sxbm';
	public function __construct() {
	}
	
	public function doWeblist() {
		global $_W, $_GPC;
		checklogin();
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
		if ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$item = pdo_fetch("SELECT tid FROM " . tablename($this->modulename) . " WHERE tid = '$id'");
			if (empty($item)) {
				message('抱歉，不存在或是已经被删除！', $this->createWebUrl('list', array('op' => 'display')), 'error');
			}
			pdo_delete($this->modulename, array('tid' => $id), 'OR');
			message('删除成功！', $this->createWebUrl('list', array('op' => 'display')), 'success');
		} elseif ($operation == 'deleteall') {
			$rowcount = 0;
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				if (!empty($id)) {
					pdo_delete($this->modulename, array('tid' => $id, 'weid' => $_W['uniacid']));
					$rowcount++;
				}
			}
			echo "删除成功";
			exit;
		} elseif ($operation == 'display') {
			$_W['page']['title'] = '数据列表';
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;
			$params = array();
			$condition = " where weid={$weid}";
			$order_condition = " ORDER BY tid DESC ";
			$sql = 'SELECT * FROM ' . tablename($this->modulename) . $condition . $order_condition . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$items = pdo_fetchall($sql, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename) . $condition . $order_condition, $params);
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('list');
	}
	public function doWebsetting() {
		global $_W, $_GPC;
		checklogin();
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
		$id = intval($_GPC['id']);
		$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  weid=$weid");
		$setting_share = json_decode($setting["share"], true);
		$setting_yd = json_decode($setting["guanzhu"], true);
		if (checksubmit('submit')) {
			if (empty($_GPC['followurl'])) {
				message('抱歉，请输入诱导关注连接！');
			}
			if (strpos($_GPC['share_image'], 'http') === false) {
				$share_image = $_W['attachurl'] . $_GPC['share_image'];
			} else {
				$share_image = $_GPC['share_image'];
			}
			if (strpos($_GPC['yd_image'], 'http') === false) {
				$yd_image = $_W['attachurl'] . $_GPC['yd_image'];
			} else {
				$yd_image = $_GPC['yd_image'];
			}
			$share = '{"share_title":"' . $_GPC['share_title'] . '","share_desc":"' . $_GPC['share_desc'] . '","share_url":"' . $_GPC['share_url'] . '","share_image":"' . $share_image . '"}';
			$guanzhu = '{"yd_appid":"' . $_GPC['yd_appid'] . '","yd_secret":"' . $_GPC['yd_secret'] . '"}';
			$data = array('weid' => intval($weid), 'followurl' => $_GPC['followurl'], 'share' => $share, 'guanzhu' => $guanzhu);
			if (!empty($id)) {
				pdo_update($this->modulename . '_setting', $data, array('weid' => $weid));
			} else {
				pdo_insert($this->modulename . '_setting', $data);
			}
			message('更新成功！', $this->createWebUrl('setting', array('op' => 'display')), 'success');
		}
		include $this->template('set');
	}
	public function doMobileIndex() {
		global $_GPC, $_W;
		$weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			header("HTTP/1.1 301 Moved Permanently");
			message('本页面仅支持微信访问!非微信浏览器禁止浏览!', '', 'error');
			exit();
		}
		$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));
		$guanzhu = json_decode($setting["guanzhu"], true);
		$appid = $guanzhu['yd_appid'];
		$secret = $guanzhu['yd_secret'];
		$share = json_decode($setting["share"], true);
		$share_images = $_W['siteroot'] . "addons/iweite_sxbm/icon.jpg";
		
		$share_image = empty($share['share_image']) ? $share_images : $share['share_image'];
		$share_title = empty($share['share_title']) ? "手写笔墨" : $share['share_title'];
		$share_desc = empty($share['share_desc']) ? "以手代笔，写字做画，就是这么有格调！" : $share['share_desc'];
		$share_url = empty($share['share_url']) ? $_W['siteroot'] . 'app' . str_replace("./", "/", $this->createMobileUrl('index', array(), true)) : $share['share_url'];
		///无权限，借用总设置
		if (empty($_W['fans']['nickname'])) {
			if ($_W['account']['level'] < 4 && (empty($appid) || empty($secret))) {
				$userinfo = mc_oauth_userinfo();
				$openid = $userinfo["openid"];
				$opname = $userinfo["nickname"];
				$opface = $userinfo["headimgurl"];
			} else {
				///自定义借全授权
				$appUrl = $this->createMobileUrl('auth2');
				$appUrl = substr($appUrl, 2);
				$redirect_uri = $_W['siteroot'] . "app/" . $appUrl;
				$scope = 'snsapi_userinfo'; //snsapi_base为只获取OPENID,snsapi_userinfo为获取头像和昵称
				$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($redirect_uri) . "&response_type=code&scope=" . $scope . "&state=1#wechat_redirect";
				header("location: $oauth2_code");
				exit;
			}
		} else { ///有权限直接用
			$openid = $_W['fans']['openid'];
			$opname = $_W['fans']["nickname"];
			$opface = $_W['fans']["tag"]["avatar"];
		}
		if (empty($openid)) {
			message('公众号无法获取用户信息', '', 'error');
		}
		if (empty($opname)) {
			message('公众号无法获取用户信息', '', 'error');
		}
		$upload = $this->createMobileUrl('upload');
		$show = $this->createMobileUrl('show');
		include $this->template('index');
	}
	public function doMobileAuth2() {
		global $_GPC;
		global $_W;
		load()->func('communication');
		$code = $_GPC['code'];
		$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));
		$guanzhu = json_decode($setting["guanzhu"], true);
		$appid = $guanzhu['yd_appid'];
		$secret = $guanzhu['yd_secret'];
		$oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
		$content = ihttp_get($oauth2_code);
		$token = @json_decode($content['content'], true);
		if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
			echo '<h1>获取微信公众号授权' . $code . '失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'] . '<h1>';
			exit();
		}
		$openid = $token['openid'];
		if ($userinfo['subscribe'] == 0) {
			$userinfo = $this->getUserInfo($openid, $token['access_token']);
		}
		if (empty($userinfo) || !is_array($userinfo) || empty($userinfo['openid']) || empty($userinfo['nickname'])) {
			echo '<h1>获取微信公众号授权失败[无法取得userinfo], 请稍后重试！ 公众平台返回原始数据为: <br />' . $userinfo['meta'] . '<h1>';
			die;
		}
		$opface = $userinfo['headimgurl'];
		$opname = $userinfo['nickname'];
		$upload = $this->createMobileUrl('upload');
		$show = $this->createMobileUrl('show');
		include $this->template('index');
	}
	public function getUserInfo($openid, $ACCESS_TOKEN = '') {
		if ($ACCESS_TOKEN == '') {
			load()->classs('weixin.account');
			$accObj = WeixinAccount::create($_W['account']['acid']);
			$ACCESS_TOKEN = $accObj->fetch_token();
			$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $ACCESS_TOKEN . '&openid=' . $openid . '&lang=zh_CN';
		} else {
			$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $ACCESS_TOKEN . '&openid=' . $openid . '&lang=zh_CN';
		}
		$json = ihttp_get($url);
		$userInfo = @json_decode($json['content'], true);
		return $userInfo;
	}
	public function doMobileUpload() {
		global $_GPC, $_W;
		$weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
		$txt = !empty($_GPC['txt']) ? $_GPC['txt'] : '';
		$openid = !empty($_GPC['openid']) ? $_GPC['openid'] : '';
		if (empty($_GPC['txt'])) {
			message('操作数据失败！', '', 'error');
		}
		if (empty($_GPC['openid'])) {
			message('操作数据失败！', '', 'error');
		}
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false) {
			echo '{"sta":100,"msg":"信息添加成功"}';
			exit();
		}
		$data = array('weid' => intval($weid), 'content' => $_GPC['txt'], 'openid' => $openid, 'dateline' => time());
		pdo_insert($this->modulename, $data);
		$id = pdo_insertid();
		echo '{"sta":200,"id":"' . $id . '"}';
		exit;
	}
	public function doMobileShow() {
		global $_GPC, $_W;
		$weid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['weid']);
		$id = !empty($_GPC['id']) ? intval($_GPC['id']) : 0;
		if (empty($id)) {
			message('操作数据失败！', '', 'error');
		}
		$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename) . " WHERE tid = '$id'");
		if (empty($item)) {
			message('操作数据失败！', '', 'error');
		}
		$item["content"] = str_replace("&quot;", "\"", $item["content"]);
		$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE weid = :weid ", array(':weid' => $_W['uniacid']));
		
		$followur = $setting["followurl"];
		$share = json_decode($setting["share"], true);
		$share_images = $_W['siteroot'] . "addons/iweite_sxbm/icon.jpg";
		$share_image = empty($share['share_image']) ? $share_images : $share['share_image'];
		$share_title = empty($share['share_title']) ? "手写笔墨" : $share['share_title'];
		$share_desc = empty($share['share_desc']) ? "以手代笔，写字做画，就是这么有格调！" : $share['share_desc'];
		$share_url = empty($share['share_url']) ? $_W['siteroot'] . 'app' . str_replace("./", "/", $this->createMobileUrl('show', array('id' => $item["tid"]), true)) : $share['share_url'];
		include $this->template('show');
	}
}
