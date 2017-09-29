<?php
/**
 * 山顶冒险[驽马小游戏]模块小程序接口定义
 *
 * @author yofung168
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Numagame_sdmxModuleWxapp extends WeModuleWxapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
}