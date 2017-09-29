<?php
/**
 * 微擎小程序模板模块微站定义
 *
 * @author 微擎团队
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class We7_wxappdemoModuleSite extends WeModuleSite {
	public function doWebHome() {
		include $this->template('home');
	}
}