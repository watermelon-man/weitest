<?php
/**
 * 小逗比聊天机器人模块处理程序
 *
 * @author zwe10
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Liaotian_doubiModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		$url = "http://api.douqq.com/?key=R1dyWj1zT0YxVjZRMCtSQ1JQTj1tM2FvMlJJQUFBPT0&msg=".urlencode($content);
		$data= file_get_contents($url);
        return $this->respText($data);

		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
	}
}