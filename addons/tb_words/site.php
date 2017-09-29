<?php
/**
 * 提笔忘字模块微站定义
 *
 * @author tengbang
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Tb_wordsModuleSite extends WeModuleSite {
	public function doMobileIndex(){
	global $_W,$_GPC;
		$item = pdo_fetch("SELECT * FROM ".tablename('tb_words_manage')." WHERE `uniacid`=:uniacid ",array(':uniacid'=>$_W['uniacid']));
		if($item['check']==0){
			message("正在进入，请稍候...",$this->createMobileUrl('get'),'success');
		}
		$code = $_GET['code'];
	if(empty($code)){
		$appid = $_W['account']['key'];
		$appkey = $_W['account']['secret'];
		$url = urlencode($_W['siteurl']);
  		$send_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$url."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
  		header('Location: '.$send_url, true, 301);
}else{
	$code = $_GET['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$_W['account']['key'].'&secret='.$_W['account']['secret'].'&code='.$code.'&grant_type=authorization_code';		
		$data = iget($url);//iget这方法在下面
		$jsonData = json_decode($data,true);
		$openid = $jsonData['openid'];
		$result = mc_fansinfo($openid);
		if($result['follow'] == 0){
			message("你还没有关注公众号，关注后才能玩！",$item['url'],"info");
		}else{
				message('请稍候...',$this->createMobileUrl('get'),'success');			
		}
}
	}
public function doMobileGet(){
$root = $_W['siteurl'];
		$item = pdo_fetch("SELECT * FROM ".tablename('tb_words_manage')." WHERE `uniacid`=:uniacid ",array(':uniacid'=>$_W['uniacid']));
		include $this->template('index');
}

	public function doWebManage() {
		global $_W,$_GPC;
		if(checksubmit()){
			$data['url'] = $_GPC['url'];
			$data['name'] = $_GPC['name'];
			$data['check'] = $_GPC['check'];
			if(empty($_GPC['getid'])){			
			$res =pdo_insert('tb_words_manage',array('url'=>$data['url'],'check'=>$data['check'],'name'=>$data['name'],'uniacid'=>$_W['uniacid']));
			if($res){
				message('操作成功',$this->createWebUrl('manage'),'success');
			}else{
				message('操作失败,请重试！','','error');
			}
		}else{
			$res = pdo_update('tb_words_manage',array('url'=>$data['url'],'check'=>$data['check'],'name'=>$data['name']),array('id'=>$_GPC['getid'],'uniacid'=>$_W['uniacid']));
			if($res){
message('操作成功',$this->createWebUrl('manage'),'success');
			}else if($res==0){
message('你未做任何改变',$this->createWebUrl('manage'),'success');
			}else{
				message('操作失败,请重试！','','error');
			}
		}
		}
		$item = pdo_fetch("SELECT * FROM ".tablename('tb_words_manage')." WHERE `uniacid`=:uniacid ",array(':uniacid'=>$_W['uniacid']));
		include $this->template('manage');
	}

}

//通过curl  get方式获取内容
function iget($url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在	
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	if (curl_errno($curl)) {
		return curl_error($curl);
	}
	return $data;
}