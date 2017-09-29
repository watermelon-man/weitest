<?php
defined('IN_IA') or exit('Access Denied');
class hc_monkeyModuleSite extends WeModuleSite {
	
	public function __mobile($f_name){
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$uid = $_W['member']['uid'];
		$openid = $_W['openid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$rule = pdo_fetch('SELECT * FROM '.tablename('hc_monkey_rule')." WHERE uniacid = ".$uniacid);
		include_once  'mobile/'.strtolower(substr($f_name,8)).'.php';
	}
	
	public function __web($f_name){
		global $_W,$_GPC;
		checklogin();
		$uniacid = $_W['uniacid'];
		load()->func('tpl');
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		include_once  'web/'.strtolower(substr($f_name,5)).'.php';
	}
	
	//首页
	public function doMobileIndex(){
		$this->__mobile(__FUNCTION__);
	}
	
	//优惠券
	public function doMobileCoupon(){
		$this->__mobile(__FUNCTION__);
	}
	
	
//以下为后台管理＝＝＝＝＝＝＝＝＝＝＝＝＝＝
	
	//粉丝管理
	public function doWebMember(){
		$this->__web(__FUNCTION__);
	}
	
	//规则管理
	public function doWebRule(){
		$this->__web(__FUNCTION__);
	}
	
	//优惠券管理
	public function doWebCoupon(){
		$this->__web(__FUNCTION__);
	}
	
	//消费管理
	public function doWebCost(){
		$this->__web(__FUNCTION__);
	}
	
	
	public function doMobileUserinfo() {
		global $_GPC,$_W;
		$uniacid = $_W['uniacid'];//当前公众号ID
		load()->func('communication');
		//用户不授权返回提示说明
		if ($_GPC['code']=="authdeny"){
		    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('index', array(), true);
			header("location:$url");
			exit('authdeny');
		}
		//高级接口取未关注用户Openid
		if (isset($_GPC['code'])){
		    //第二步：获得到了OpenID
		    $appid = $_W['account']['key'];
		    $secret = $_W['account']['secret'];
			$serverapp = $_W['account']['level'];	
			if ($serverapp!=4) {
				$cfg = $this->module['config'];
			    $appid = $cfg['appid'];
			    $secret = $cfg['secret'];
				if(empty($appid) || empty($secret)){
					return ;
				}
			}
			$state = $_GPC['state'];
			//1为关注用户, 0为未关注用户
			//查询活动时间
			$code = $_GPC['code'];
		    $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
		    $content = ihttp_get($oauth2_code);
		    $token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
				echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				exit;
			}

		    $openid = $token['openid'];
			//再次查询是否为关注用户
			$profile = pdo_fetch("select * from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
			//关注用户直接获取信息	
			if ($profile['follow']==1){
			    $state = 1;
			}else{
				//未关注用户跳转到授权页
				$url = $_W['siteroot'].'app/'.$this->createMobileUrl('userinfo', array(), true);
				$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
				//header("location:$oauth2_code");
			}
			//未关注用户和关注用户取全局access_token值的方式不一样
			
			$access_token = $token['access_token'];
			$oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
			
			//使用全局ACCESS_TOKEN获取OpenID的详细信息			
			$content = ihttp_get($oauth2_url);
			$info = @json_decode($content['content'], true);
			if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {
				echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
				exit;
			}
			if(!empty($_W['member']['uid'])){
				$row = array(
					'uniacid' => $uniacid,
					'nickname'=>$info['nickname'],
					'avatar'=>$info['headimgurl'],
					'realname'=>$info['nickname']
				);
				pdo_update('mc_members', $row, array('uid'=>$_W['member']['uid']));	
			} else {
				$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $uniacid));
				$row = array(
					'uniacid' => $uniacid,
					'nickname'=>$info['nickname'],
					'avatar'=>$info['headimgurl'],
					'realname'=>$info['nickname'],
					'groupid' => $default_groupid,
					'email'=>random(32).'@we7.cc',
					'salt'=>random(8),
					'createtime'=>time()
				);
				pdo_insert('mc_members', $row);
				$user['uid'] = pdo_insertid();
				//$fan = mc_fansinfo($openid);
				//pdo_update('mc_mapping_fans', array('uid'=>$user['uid']), array('fanid'=>$fan['fanid']));
				pdo_update('mc_mapping_fans', array('uid'=>$user['uid']), array('openid'=>$openid, 'uniacid'=>$uniacid));
				_mc_login($user);
			}
			$member = pdo_fetch("select * from ".tablename('hc_monkey_member')." where uniacid = ".$uniacid." and openid = '".$openid."'");
			if(empty($member)){
				$mem = array(
					'uniacid'=>$uniacid,
					'openid'=>$openid,
					'headimgurl'=>$info['headimgurl'],
					'nickname'=>$info['nickname'],
					'createtime'=>time()
				);
				pdo_insert('hc_monkey_member', $mem);
			}
			$url = $this->createMobileUrl('index');
			//die('<script>location.href = "'.$url.'";</script>');
			header("location:$url");
			exit;
		}else{
			echo '<h1>网页授权域名设置出错!</h1>';
			exit;
		}
	}
	
	private function CheckCookie() {
		global $_W;
		//return;
		$appid = $_W['account']['key'];
		$secret = $_W['account']['secret'];
		//是否为高级号
		$serverapp = $_W['account']['level'];
		if ($serverapp!=4) {
			$cfg = $this->module['config'];
			$appid = $cfg['appid'];
			$secret = $cfg['secret'];
			if(empty($appid) || empty($secret)){
				return;
			}
		}
		//借用的
		$url = $_W['siteroot'].'app/'.$this->createMobileUrl('userinfo', array(), true);
		$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
		//exit($oauth2_code);
		header("location:$oauth2_code");
		exit;
	}
}

function hehe($mobile){
	if(preg_match("/1[3458]{1}\d{9}$/",$mobile)){
		//$mphone = substr($mobile,3,5);
		//$lphone = str_replace($mphone,"*****",$mobile);
		return $mobile;
		//return $lphone;
	} else {
		message('电话号码格式不正确');
	}
}
