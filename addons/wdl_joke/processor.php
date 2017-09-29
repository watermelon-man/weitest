<?php
/**
 * 笑话大全模块处理程序
 *
 * @author 微多拉
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Wdl_jokeModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		
		$api = 'http://www.weiduola.cn/api/joke.php?mod=get&type=1';
		$data = file_get_contents($api);
		$data = json_decode($data);
		$settings = $this->module['config'];
		//if ($data['type'] > 1) {
		  //  $reply = '拿到一条图片'.file_get_contents($api);
		    //return $this->respText($reply);
		//} else {
		$reply = '';
		$pre = array($settings['preword1'],$settings['preword2'],$settings['preword3']);
		$pre = array_filter($pre);
		if (!empty($pre)) {
		    $reply .= $pre[array_rand($pre)]."\n\r";
		}
		
		$reply .= $data->text;
		
		$post = array($settings['postword1'],$settings['postword2'],$settings['postword3']);
		$post = array_filter($post);
		if (!empty($post)) {
		    $reply .= "\n\r".$post[array_rand($post)];
		}
		
		return $this->respText($reply);
		//}
	}
}