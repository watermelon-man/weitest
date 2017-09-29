<?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/hc_article/responser.php';
require_once IA_ROOT . '/addons/hc_article/wechatutil.php';

class hc_articleModuleProcessor extends WeModuleProcessor {
	
	public function respond() {
       	global $_W;

		$uniacid = $_W['uniacid'];
		$message = $this->message;
		$openid = $message['from'];
		$member = pdo_fetch("select * from ".tablename('hc_article_member')." where ispay = 1 and uniacid = ".$uniacid." and openid = '".$openid."'");
		$id = $member['id'];
		if(!intval($id)){
			return $this->respText('您还未注册成为会员，请先注册后再来获取专属名片！');
		}
		$resp = new QRResponser();
		if($member['ischange'] == 1){
			$resp->aa($openid);
			//$this->testJumpUrl();
			return;
		}
		if(time()-$member['media_time']>60*60*20*3){
			$resp->aa($openid);
			//$this->testJumpUrl();
			return;
		}
		if(empty($member['media_id'])){
			$target_file = IA_ROOT.'/addons/hc_article/style/poster/memberposter/'.$uniacid."share$id.jpg";
			if(!file_exists($target_file)){
				$resp->aa($openid);
				//$this->testJumpUrl();
			} else {
				$resp = new QRResponser();
				$media_id = $resp->uploadImage($target_file);
				pdo_update('hc_article_member', array('media_id'=>$media_id, 'ischange'=>0, 'mediatime'=>time()), array('id'=>$id));
				$ret = $resp->sendImage($openid, $media_id);
			}
		} else {
			return $this->respImage($member['media_id']);	
		} 
    }
	
	private function testJumpUrl(){
		global $_W;
		//异步执行
		$host = $_SERVER['HTTP_HOST'];
		
		$fp = fsockopen($host, 80, $errno, $errstr, 1);
		if (!$fp) { 
			WeUtility::logging("fsockopen错误", $out);
			echo "$errstr ($errno)<br />\n";
		} else {
			$url = "/app/".$this->createMobileUrl('RunTask', array('from_user'=>$this->message['from']));
			$out = "GET $url  / HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n\r\n";
			WeUtility::logging("来到fwrite", $out);
			fwrite($fp, $out);
			/*忽略执行结果
			while (!feof($fp)) {
				echo fgets($fp, 128);
			}*/
			fclose($fp);
			$this->responseEmptyMsg();
		}
	}
	
	public function responseEmptyMsg() {
		global $_W;
		WeUtility::logging("responseEmptyMsg", "sdfsdfsdfsdf");
		ob_clean();
		ob_start();
		echo '';
		ob_flush();
		ob_end_flush();
		exit(0);
	}
}
