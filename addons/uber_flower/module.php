<?php
/**
 * 微步互动 Uber qq1476708765
 */
defined('IN_IA') or exit('Access Denied');
require_once "define.php";
require_once "model.php";
class Uber_FlowerModule extends WeModule
{
	public $table_reply = 'uber_flower_reply';
    public $table_fans = 'uber_flower_fans';
    public $table_share = 'uber_flower_share';
	public $table_friend = 'uber_flower_friend';
	public $table_award = 'uber_flower_award';
	public $table_merchant = 'uber_flower_merchant';
	
	public function fieldsFormDisplay($rid = 0)
	{
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE uniacid='{$_W[uniacid]}' AND rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$reply['starttime'] = date("Y-m-d  H:i", $reply['starttime']);
			$reply['endtime'] = date("Y-m-d  H:i", $reply['endtime']);
		}
		$style_default = ($this->module['config']['style'])?$this->module['config']['style']:'default';
		//echo $style_default;exit;
		if (empty($reply['prize'])) {
			$prizes = array(array('kw' => 1, 'level' => '一等奖', 'name' => "CK中性香水", 'num' => '1', 'rate' => '20', 'bio' => 'CK中性香水一份', 'mchid' => 0, 'img' => UBER_RES . '/'.$style_default.'/images/default_prize1.png'), array('kw' => 2, 'level' => '二等奖', 'name' => "暖风机", 'num' => '10', 'rate' => '30', 'bio' => '暖风机一个', 'mchid' => 0, 'img' => UBER_RES . '/'.$style_default.'/images/default_prize2.png'), array('kw' => 3, 'level' => '三等奖', 'name' => "加湿器", 'num' => '100', 'rate' => '50', 'bio' => '加湿器一只', 'mchid' => 0, 'img' => UBER_RES . '/'.$style_default.'/images/default_prize3.png'));
		} else {
			$prizes = iunserializer($reply['prize']);
		}
		if (!$reply['adimgurl']) {
			$reply['adimgurl'] = UBER_RES . '/'.$style_default.'/images/ad.jpg';
		}
		if (!$reply['coverurl']) {
			$reply['coverurl'] = UBER_RES . '/'.$style_default.'/images/front/cover.jpg';
		}
		if (!$reply['bgurl']) {
			$reply['bgurl'] = UBER_RES . '/'.$style_default.'/images/front/bg.jpg';
		}
		if (!$reply['loopbgurl']) {
			$reply['loopbgurl'] = UBER_RES . '/'.$style_default.'/images/front/loopbg.png';
		}
		if (!$reply['selfurl']) {
			$reply['selfurl'] = UBER_RES . '/'.$style_default.'/images/front/self.png';
		}
		if (!$reply['targeturl']) {
			$reply['targeturl'] = UBER_RES . '/'.$style_default.'/images/front/target.png';
		}
		if (!$reply['targetbgurl']) {
			$reply['targetbgurl'] = UBER_RES . '/'.$style_default.'/images/front/targetbg.png';
		}
		if (!$reply['gamesuccess']) {
			$reply['gamesuccess'] = UBER_RES . '/'.$style_default.'/tp/images/flower/luck.png';
		}
		if (!$reply['gamefail']) {
			$reply['gamefail'] = UBER_RES . '/'.$style_default.'/tp/images/flower/bad.png';
		}
		if (!$reply['showusernum']) {
			$reply['showusernum'] = 100;
		}
		if (!$reply['gametime']) {
			$reply['gametime'] = 30;
		}
		if (!$reply['gamelevel']) {
			$reply['gamelevel'] = 3;
		}
		if (!isset($reply['isprofile'])) {
			$reply['isprofile'] = 1;
		}
		load()->func('tpl');
		include $this->template('form');
	}
	public function fieldsFormValidate($rid = 0)
	{
		global $_GPC, $_W;
		if ($_GPC['prize_rate']) {
			$ratetotle = array_sum($_GPC['prize_rate']);
			if ($ratetotle != 100) {
				return '请检查您设置的奖品概率总和是否为100%';
			}
		}
		if ($_GPC['playtimes'] > 0 && $_GPC['playtimes'] > 0) {
			if ($_GPC['playtimes'] < $_GPC['everydaytimes']) {
				return '每天游戏次数不能大于总游戏次数！';
			}
		}
		if (strtotime($_GPC['starttime']) > strtotime($_GPC['endtime'])) {
			return '活动结束时间应该大于活动开始时间！';
		}
		return '';
	}
	public function fieldsFormSubmit($rid)
	{
		global $_GPC, $_W;
		load()->func('tpl');
		$id = intval($_GPC['id']);
		$_profile = array('umobile' =>1, 'uname' =>1, 'uaddress' =>intval($_GPC['uaddress']), 'uqq' =>intval($_GPC['uqq']));
		if(is_array($_profile)){
		$profile = iserializer($_profile);
		}
		$data = array('rid' => $rid, 'uniacid' => $_W['uniacid'], 'title' => $_GPC['title'], 'starttime' => strtotime($_GPC['starttime']), 'endtime' => strtotime($_GPC['endtime']), 'adimgurl' => $_GPC['adimgurl'], 'adurl' => $_GPC['adurl'], 'share_title' => $_GPC['share_title'], 'share_image' => $_GPC['share_image'], 'share_url' => $_GPC['share_url'], 'share_desc' => $_GPC['share_desc'], 'copyright' => htmlspecialchars_decode($_GPC['copyright']), 'shownum' => $_GPC['shownum'], 'exchange' => $_GPC['exchange'], 'isfollow' => $_GPC['isfollow'], 'followurl' => $_GPC['followurl'], 'playtimes' => intval($_GPC['playtimes']), 'everydaytimes' => intval($_GPC['everydaytimes']), 'ruletext' => htmlspecialchars_decode($_GPC['ruletext']), 'awardtext' => $_GPC['awardtext'], 'isprofile' => intval($_GPC['isprofile']), 'profile' => $profile, "gametime" => intval($_GPC['gametime']), "gamelevel" => intval($_GPC['gamelevel']), "showusernum" => intval($_GPC['showusernum']), "daysharenum" => intval($_GPC['daysharenum']), "mode" => intval($_GPC['mode']), "shareawardnum" => intval($_GPC['shareawardnum']), 'bgurl' => $_GPC['bgurl'], 'coverurl' => $_GPC['coverurl'], 'qrcode' => $_GPC['qrcode'], 'qrcodetext' => $_GPC['qrcodetext'], 'bgmusic' => $_GPC['bgmusic'], 'status' => 1, 'createtime' => TIMESTAMP,'loopbgurl' => $_GPC['loopbgurl'],'selfurl' => $_GPC['selfurl'],'targeturl' => $_GPC['targeturl'],'targetbgurl' => $_GPC['targetbgurl'],'profilemsg' => $_GPC['profilemsg'],'gamesuccess' => $_GPC['gamesuccess'],'gamefail' => $_GPC['gamefail'],);
		$data['prize'] = '';
		if ($_GPC['prize']) {
			$prizes = array();
			foreach ($_GPC['prize'] as $key => $prize) {
				if ($prize != '') {
					$prizes[] = array('kw' => $_GPC['prize_kw'][$key], 'level' => $_GPC['prize_level'][$key], 'name' => $_GPC['prize_name'][$key], 'num' => $_GPC['prize_num'][$key], 'rate' => $_GPC['prize_rate'][$key], 'bio' => $_GPC['prize'][$key], 'mchid' => $_GPC['prize_mchid'][$key], 'img' => $_GPC['prize_img'][$key]);
				}
			}
			$data['prize'] = empty($prizes) ? '' : iserializer($prizes);
		}
		if (empty($id)) {
			pdo_insert($this->table_reply, $data);
		} else {
			pdo_update($this->table_reply, $data, array('id' => $id));
		}
		return true;
	}
	public function ruleDeleted($rid)
	{
		pdo_delete($this->table_reply, array('rid' => $rid));
		pdo_delete($this->table_fans, array('rid' => $rid));
		pdo_delete($this->table_award, array('rid' => $rid));
		pdo_delete($this->table_friend, array('rid' => $rid));
		pdo_delete($this->table_share, array('rid' => $rid));
		return true;
	}
	public function settingsDisplay($settings)
	{
		global $_W, $_GPC;
		$styles = array();
		$dir = IA_ROOT . "/addons/uber_flower/template/mobile/";
		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != ".." && $file != ".") {
					if (is_dir($dir . "/" . $file)) {
						$styles[] = $file;
					}
				}
			}
			closedir($handle);
		}
		if (checksubmit()) {
			$dat = array('style' => $_GPC['style'], 'editor' => intval($_GPC['editor']), 'ismerchant' => intval($_GPC['ismerchant']));
			if (!$this->saveSettings($dat)) {
				message('配置参数保存失败', referer(), 'error');
			} else {
				message('配置参数更新成功！', referer(), 'success');
			}
		}
		include $this->template('settings');
	}
}