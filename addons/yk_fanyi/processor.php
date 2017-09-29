<?php
/**
 * 有道翻译（整句翻译）模块处理程序
 *
 * @author yunke
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Yk_fanyiModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		$keyword = preg_replace('/(@|\s)/','',$content);
		$keyword= urlencode($keyword);
		$url = 'http://fanyi.youdao.com/openapi.do?keyfrom=yunke2016&key=91010191&type=data&doctype=json&version=1.1&q='.$keyword;
		load()->func('communication');
		$dat = file_get_contents($url);
		$resp1=json_decode($dat,true);
		$trans = $resp1["translation"]['0'];
		if(empty($trans)) $trans="请缩小句子长度，再翻译";
		
		
		return $this->respText($trans);
	}
}