<?php
/**
 * 小逗比聊天机器人模块小程序接口定义
 *
 * @author zwe10
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Liaotian_doubiModuleWxapp extends WeModuleWxapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
}