<?php
/**
 * 经典Windows扫雷小游戏模块微站定义
 *
 * @author delete
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Delete_saoleiModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面
		global $_W;
		$title = $this->module['config']['title'];
		$pic = $this->module['config']['pic'];
		$desc = $this->module['config']['desc'];
		include $this->template('index');
	}

}