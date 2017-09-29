<?php
/**
 * 每日一句英语模块微站定义
 *
 * @author 北极以北
 * @url http://bbs.we7.cc
 */
defined('IN_IA') or exit('Access Denied');

class English_dailyModuleSite extends WeModuleSite {
	public function doMobileIndex() {
		global $_W;
		$list = pdo_fetchall("select id,time,content,note,picture from ".tablename($this->modulename.'_content') . " where uniacid={$_W['uniacid']} order by id desc limit 7");
		// var_dump($list);exit;
		include $this->template('index');
	}
}