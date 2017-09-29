<?php
/**
 * 历史今天模块处理程序
 *
 * @author mytime
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Mytime_lsjtModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		return $this->respText($this->geturl());
	}

	function geturl(){
		$m = date('m',time());
		$d = date('d',time());
		$ch = curl_init();
	    $url = 'http://api.avatardata.cn/HistoryToday/LookUp?key=4c8e5faf913547d4bb637647927ba46e&yue='.$m.'&ri='.$d.'&type=2&page=1';
	    // 添加apikey到header
	    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL , $url);
	    $notice = curl_exec($ch);
		curl_close($ch);
		$res = json_decode($notice,true);
		
		foreach ($res['result'] as $k => $v) {
			$str .= $v['year'].'年'.$v['month'].'月'.$v['day'].'日:'.$v['title']."\n";
		}
		
		return $str;
	}
}