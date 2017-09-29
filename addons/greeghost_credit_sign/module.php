<?php
/**
 * 会员签到模块定义
 *
 * @author greeghost
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Greeghost_credit_signModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		
		if(checksubmit()) {
			
			$data = $_GPC['data'];

			$data['random'] = intval($data['random']);
			$data['score'] = intval($data['score']);
			$data['score1'] = intval($data['score1']);
			$data['score2'] = intval($data['score2']);
			$data['sign_start_time'] = empty($data['sign_start_time']) ? "00:00" : $data['sign_start_time'];
			$data['continue_day'] = intval($data['continue_day']);
			$data['continue_score'] = intval($data['continue_score']);
			$data['share_score'] = intval($data['share_score']);
			$data['share_limit'] = intval($data['share_limit']);
			$data['description'] = htmlspecialchars_decode($data['description']);

			if($data['random'] == 0 && $data['score'] == ''){
				message('请输入固定签到积分');
			}
			if($data['random'] == 1 && ($data['score1'] == '' || $data['score2'] == '')){
				message('请输入随机积分范围');
			}

			if (!$this->saveSettings($data)) {
				message('设置失败','','error');
			} else {
				message('设置成功','','success');
			}
		}
	
		load()->func('tpl');
		include $this->template('setting');
	}

}