<?php
/**
 * 随机回复模块小程序接口定义
 *
 * @author tyzy313481929
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Random_textModuleWxapp extends WeModuleWxapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
}