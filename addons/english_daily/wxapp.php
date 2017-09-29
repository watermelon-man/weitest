<?php
/**
 * 每日一句英语模块小程序接口定义
 *
 * @author 北极以北
 * @url http://bbs.we7.cc
 */
defined('IN_IA') or exit('Access Denied');

class English_dailyModuleWxapp extends WeModuleWxapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
}