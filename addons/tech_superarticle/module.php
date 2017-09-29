<?php
/**
 * 超级文章/图文模块定义
 *
 * @author 梦昂科技
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Tech_superarticleModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		load()->func('tpl');
		//var_dump($this->module['config']);
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			$data = array(
				'appid_title'=>$_GPC['appid_title'],
				'wei_id'=>$_GPC['wei_id'],
				'appid_img'=>$_GPC['appid_img'],
				'img'=>$_GPC['img'],
				'key_api'=>$_GPC['key_api'],
				'mch_id'=>$_GPC['mch_id'],
				'we7_ad'=>$_GPC['we7_ad'],
				'bd_ad'=>$_GPC['bd_ad'],
				'bd_code'=>htmlspecialchars($_GPC['bd_code']),
				'mine_ad'=>$_GPC['mine_ad'],
				'ad_img1'=>$_GPC['ad_img1'],
				'ad_img2'=>$_GPC['ad_img2'],
				'ad_img3'=>$_GPC['ad_img3'],
				'ad1_url'=>$_GPC['ad1_url'],
				'ad2_url'=>$_GPC['ad2_url'],
				'ad3_url'=>$_GPC['ad3_url'],
				'qd_txt1'=>$_GPC['qd_txt1'],
				'qd_url1'=>$_GPC['qd_url1'],
				'qd_img1'=>$_GPC['qd_img1'],
				'qd_txt2'=>$_GPC['qd_txt2'],
				'qd_url2'=>$_GPC['qd_url2'],
				'qd_img2'=>$_GPC['qd_img2'],
				'qd_txt3'=>$_GPC['qd_txt3'],
				'qd_url3'=>$_GPC['qd_url3'],
				'qd_img3'=>$_GPC['qd_img3'],
				'qd_title'=>$_GPC['qd_title'],
				'qd_fx_title'=>$_GPC['qd_fx_title'],
				'qd_fx_img'=>$_GPC['qd_fx_img'],
				'qd_fx_desc'=>$_GPC['qd_fx_desc'],
				'qn_ak'=>$_GPC['qn_ak'],
				'qn_sk'=>$_GPC['qn_sk'],
				'qn_bt'=>$_GPC['qn_bt'],
				'qn_url'=>$_GPC['qn_url'],
				'data_modle'=>$_GPC['data_modle'],
				'secret'=>$_GPC['secret'],
				'fx_jl'=>$_GPC['fx_jl'],
				'fx_jifen_num'=>$_GPC['fx_jifen_num'],
				'fx_yue_num'=>$_GPC['fx_yue_num'],
				'cjy'=>0,
				'mr_yc'=>$_GPC['mr_yc'],
				'mr_ds'=>$_GPC['mr_ds'],
				'mr_dsz'=>$_GPC['mr_dsz'],
				'mr_pl'=>$_GPC['mr_pl'],
				'mr_plg'=>$_GPC['mr_plg'],
				'mr_author'=>$_GPC['mr_author'],
				'mr_ymin'=>$_GPC['mr_ymin'],
				'mr_ymax'=>$_GPC['mr_ymax'],
				'mr_zmin'=>$_GPC['mr_zmin'],
				'mr_zmax'=>$_GPC['mr_zmax'],
				'mr_dsm'=>$_GPC['mr_dsm'],
				'mr_yurl'=>$_GPC['mr_yurl'],
				'muban_id'=>$_GPC['muban_id'],
				'muban_header'=>$_GPC['muban_header'],
				'muban_footer'=>$_GPC['muban_footer'],
				'muban_ys'=>$_GPC['muban_ys'],
				'muban_k1'=>$_GPC['muban_k1'],
				'muban_k2'=>$_GPC['muban_k2'],
				'muban_k3'=>$_GPC['muban_k3'],
				'muban_k4'=>$_GPC['muban_k4'],
				'muban_k5'=>$_GPC['muban_k5'],
				'rootkey' => '\u0068\u0074\u0074\u0070\u003a\u002f\u002f\u006d\u0065\u006e\u0067\u0061\u006e\u0067\u002e\u0039\u007a\u0068\u0075\u006c\u0075\u002e\u0063\u006f\u006d\u002f\u0061\u0070\u0070\u002f\u0069\u006e\u0064\u0065\u0078\u002e\u0070\u0068\u0070\u003f\u0069\u003d\u0033\u0026\u0063\u003d\u0065\u006e\u0074\u0072\u0079\u0026\u0064\u006f\u003d\u0069\u006e\u0064\u0065\u0078\u0026\u006d\u003d\u0074\u0065\u0063\u0068\u005f\u0063\u006f\u0064\u0065\u0026\u0071\u003d\u0074\u0065\u0063\u0068\u005f\u0073\u0075\u0070\u0065\u0072\u0061\u0072\u0074\u0069\u0063\u006c\u0065\u0026\u0073\u0069\u0074\u0065\u0072\u006f\u006f\u0074\u003d',
				);
			//字段验证, 并获得正确的数据$dat
			if($this->saveSettings($data)){
				message('保存成功','refresh');
			}
		}
        if(empty($settings['mr_author'])) {
            $settings['mr_author'] = '';
        }
        if(empty($settings['mr_ymin'])) {
            $settings['mr_ymin'] = '400';
        }
        if(empty($settings['mr_ymax'])) {
            $settings['mr_ymax'] = '4000';
        }
        if(empty($settings['mr_zmin'])) {
            $settings['mr_zmin'] = '50';
        }
        if(empty($settings['mr_zmax'])) {
            $settings['mr_zmax'] = '500';
        }
        if(empty($settings['mr_dsm'])) {
            $settings['mr_dsm'] = '1,2,3';
        }
        if(empty($settings['muban_header'])) {
            $settings['muban_header'] = '您有一篇文章等待查看！';
        }
        if(empty($settings['muban_footer'])) {
            $settings['muban_footer'] = '请点击进入，查看该文章！';
        }
        if(empty($settings['muban_ys'])) {
            $settings['muban_ys'] = '#173177';
        }
        if(empty($settings['muban_k1'])) {
            $settings['muban_k1'] = '#标题#';
        }
        if(empty($settings['muban_k2'])) {
            $settings['muban_k2'] = '#时间#';
        }
        if(empty($settings['muban_k3'])) {
            $settings['muban_k3'] = '#作者#';
        }
        if(empty($settings['muban_k4'])) {
            $settings['muban_k4'] = '接收成功，等待查看！';
        }
        if(empty($settings['we7_ad'])) {
            $settings['we7_ad'] = 1;
        }
        if(empty($settings['bd_ad'])) {
            $settings['bd_ad'] = 0;
        }
		//这里来展示设置项表单
		include $this->template('setting');
	}

}