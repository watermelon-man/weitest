<?php
/**
 * 风-报名模块定义
 *
 * @author feng
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
include 'inc/function/function.php';
class Feng_baomingModule extends WeModule {
	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			$dat = array(
				"succtpl" => textareaarr($_GPC['succtpl']),
				"succ_tplid"=>trim($_GPC['succ_tplid']),
				'fxtitle'=>$_GPC['fxtitle'],
				'fxdes'=>$_GPC['fxdes'],
				'fxpic'=>$_GPC['fxpic'],
				'fxlink'=>$_GPC['fxlink'],
				"glopenid" => $_GPC['glopenid'],
				'activname'=>$_GPC['activname'],
				'content'=>$_GPC['content'],
				'bmdate'=>$_GPC['bmdate']
			);
            if($this->saveSettings($dat)){
                message('保存成功', 'refresh');
            }
		}
		//解析数组字符串
		$settings['succtpl'] = arrtotextarea($settings['succtpl']);
		include $this->template('setting');
	}
	
	

}