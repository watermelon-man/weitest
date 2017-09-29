<?php
/**
 * shakewin模块微站定义
 *
 * @author yh
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
define("SHAKE_WIN_DIR", "hpmd_shakewin");
define("COMMON_DIR", "common_jy");

error_reporting(E_ERROR);
ini_set('error_log','../addons/hpmd_shakewin/error.txt');

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/FileLock.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CommonValStr.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/DBUtility.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/ShakeAuthorize.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/ShakeMobile.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/ShakeActivity.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/ShakeAward.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/ShakeWinner.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/Bigscreen.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/Exporter.php');

class Hpmd_shakewinModuleSite extends WeModuleSite {
	public function doMobileShakeAward() {
		global $_GPC, $_W;

		$opresult = ShakeMobile::ShakeAward();
		if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_CARE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_CARE_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_START_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ALREADY_END_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ALREADY_END_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_USRINFO_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_USRINFO_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_DAYTOTAL_REACH_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_DAYTOTAL_REACH_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_DAYTOTAL_NONE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_DAYTOTAL_NONE_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_TOTAL_REACH_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_TOTAL_REACH_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_TOTAL_NONE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_TOTAL_NONE_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_REACH_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_REACH_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_NONE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_NONE_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOMORE_AWARDS_STR) {
			die(json_encode(array("success" => 0, "msg" => CommonValStr::$CHECK_ACTIVITY_NOMORE_AWARDS_STR)));
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOTWIN_REMINDER_VAL) {
			$data = array(
				'success' => 0,
				'msg' => CommonValStr::$CHECK_ACTIVITY_NOTWIN_REMINDER_STR,
			);
			echo json_encode($data);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_WIN_REMINDER_VAL) {
			$data = array(
				'success' => 1,
				'msg' => CommonValStr::$CHECK_ACTIVITY_WIN_REMINDER_STR,
				'awardname' => $opresult['awardname'],
			);
			echo json_encode($data);
		}     

	}
	public function doMobileMyAwards() {
		global $_GPC, $_W;
		$replywords = $_GPC['replywords'];
		$ruleid = $_GPC['ruleid'];

		$opresult = ShakeWinner::MobileMyWinPagedList();
		if($opresult) {
			$list = $opresult['list'];
			$nickname = $opresult['nickname'];
			$exchangestarttime = date('Y-m-d h:i',$opresult['exchangestarttime']);
			$exchangeendtime = date('Y-m-d h:i',$opresult['exchangeendtime']);
		}
		$account = account_fetch($_W['uniacid']);
		if(!empty($replywords)) {
			$rule = DBUtility::getRuleByReplyWord($replywords);
		}
		include $this->template('myawards');
	}
	private function getPowerKey(){
		global $_GPC, $_W;
		$account = account_fetch($_W['uniacid']);

		if(!empty($_W['account']['oauth']['key']) && !empty($_W['account']['oauth']['secret'])) {
			$key = $_W['account']['oauth']['key'];
		} else {
			$key = $account['key'];
		}
		return $key;
	}
	private function getPowerSecret(){
		global $_GPC, $_W;
		$account = account_fetch($_W['uniacid']);

		if(!empty($_W['account']['oauth']['key']) && !empty($_W['account']['oauth']['secret'])) {
			$secret = $_W['account']['oauth']['secret'];
		} else {
			$secret = $account['secret'];
		}
		return $secret;
	}
	public function doMobileIndex() {
		global $_GPC, $_W;
		
		$opresult = ShakeMobile::CheckActivity($this->getPowerKey());
		if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_CARE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_CARE_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_START_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ALREADY_END_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ALREADY_END_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_STR);
		}  else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_SUCCESS_VAL) {
			$shaketimeswin = $opresult['shaketimeswin'];
			$account = account_fetch($_W['uniacid']);
			$replywords = $opresult['replywords'];
			$item = $opresult['item'];
			include $this->template('yaoyiyao');
		} 
	}
	public function doMobileGetUsrInfo() {
		global $_GPC, $_W;
		
		$opresult = ShakeAuthorize::GetUsrInfo($this->getPowerKey(), $this->getPowerSecret());
		if ($opresult['returnval'] == CommonValStr::$GET_USRINFO_FAIL_VAL) {
			echo "<script type='text/javascript'>alert('".CommonValStr::$GET_USRINFO_FAIL_STR."');</script>";
		} else if ($opresult['returnval'] == CommonValStr::$GET_USRINFO_SUCCESS_VAL) {
			$jmpurl = $this->createMobileUrl('Index', array('replywords' => $opresult['replywords']));
			record_log('care', 'txt', 'insert: ', $jmpurl);
			echo "<script type='text/javascript'>window.location.href=\"" . $jmpurl . "\";</script>";
		}
	}
	public function doMobileYaoyiyao() {
		message(CommonValStr::$OUTSIDE_LINK_REMIND_STR);
	}
	public function doWebClearCache() {
		return ShakeActivity::WebClearActivityCache();
	}
	public function doWebShakeawards() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];
		$ruleid = $_GPC['ruleid'];

		load()->func('tpl');
		$opresult = ShakeAward::WebProcessAwardOperation($op);
		if ($opresult['returnval'] == CommonValStr::$AWARD_NAME_EXIT_VAL) {
			message(CommonValStr::$AWARD_NAME_EXIT_STR, $this->createWebUrl('Shakeawards'), "success");
		} else if ($opresult['returnval'] == CommonValStr::$AWARD_EDIT_SUCCESS_VAL || 
				   $opresult['returnval'] == CommonValStr::$AWARD_ADD_SUCCESS_VAL) {
			message(CommonValStr::$AWARD_EDIT_SUCCESS_STR, $this->createWebUrl('Shakeawards'), "success");
		} else if ($opresult['returnval'] == CommonValStr::$AWARD_EDITLIST_SUCCESS_VAL) {
			$item = $opresult['item'];
			include $this->template('awardsedit');
		} else if ($opresult['returnval'] == CommonValStr::$AWARD_DEL_SUCCESS_VAL) {
			message(CommonValStr::$AWARD_DEL_SUCCESS_STR, $this->createWebUrl('Shakeawards'), "success");
		} else if ($opresult['returnval'] == CommonValStr::$AWARD_DEL_ALL_SUCCESS_VAL) {
			$data = array(
				'errno' => 0,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$AWARD_DETAIL_SUCCESS_VAL) {
			$item = $opresult['item'];
			include $this->template('awardsdetail');
		} else if ($opresult['returnval'] == CommonValStr::$AWARD_LIST_SUCCESS_VAL) {
			$list = $opresult['list'];
			$total = $opresult['total'];
			$pager = $opresult['pager'];
			$add_paper = $opresult['add_paper'];
			$url = $opresult['url'];
			$paperid = $opresult['paperid'];
			include $this->template('awardslist');
		} 
		
	}
	public function doWebShakerule() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];
		//record_log('developer', 'txt', 'doWebShakerule weid', $weid);
		//record_log('developer', 'txt', 'doWebShakerule op', $op);

		$opresult = ShakeActivity::WebProcessActivityOperation($op);
		if ($opresult['returnval'] == CommonValStr::$REPLY_WORDS_EXIT_VAL) {
			message(CommonValStr::$REPLY_WORDS_EXIT_STR, $this->createWebUrl('Shakerule'), "success");
		} else if ($opresult['returnval'] == CommonValStr::$ACTIVITY_EDIT_SUCCESS_VAL || 
				   $opresult['returnval'] == CommonValStr::$ACTIVITY_ADD_SUCCESS_VAL) {
			message(CommonValStr::$ACTIVITY_SAVE_SUCCESS_STR, $this->createWebUrl('Shakerule'), "success");
		} else if ($opresult['returnval'] == CommonValStr::$ACTIVITY_EDITLIST_SUCCESS_VAL) {
			$item = $opresult['item'];
			$allcachekeys = $opresult['allcachekeys'];
			include $this->template('ruleedit');
		} else if ($opresult['returnval'] == CommonValStr::$ACTIVITY_DEL_SUCCESS_VAL) {
			message(CommonValStr::$ACTIVITY_DEL_SUCCESS_STR, $this->createWebUrl('Shakerule'), "success");
		} else if ($opresult['returnval'] == CommonValStr::$ACTIVITY_DEL_ALL_SUCCESS_VAL) {
			$data = array(
				'errno' => 0,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$ACTIVITY_COPY_SUCCESS_VAL || 
			       $opresult['returnval'] == CommonValStr::$ACTIVITY_AWARDS_COPY_SUCCESS_VAL) {
			$data = array(
				'errno' => 0,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$ACTIVITY_DETAIL_SUCCESS_VAL) {
			$item = $opresult['item'];
			include $this->template('ruledetail');
		} else if ($opresult['returnval'] == CommonValStr::$ACTIVITY_LIST_SUCCESS_VAL) {
			$list = $opresult['list'];
			$total = $opresult['total'];
			$pager = $opresult['pager'];
			$add_paper = $opresult['add_paper'];
			$url = $opresult['url'];
			$paperid = $opresult['paperid'];
			include $this->template('rulelist');
		} 
		
	}
	
	public function doWebShakewinner() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];

		$opresult = ShakeWinner::WebProcessWinnerOperation($op);
		if ($opresult['returnval'] == CommonValStr::$WINNER_DETAIL_SUCCESS_VAL) {
			$faninfo = $opresult['faninfo'];
			$replywords = $opresult['replywords'];
			$statusstr = $opresult['statusstr'];
			$awardinfo = $opresult['awardinfo'];
			include $this->template('awardfandetail');
		} else if ($opresult['returnval'] == CommonValStr::$WINNER_LIST_SUCCESS_VAL) {
			$list = $opresult['list'];
			$total = $opresult['total'];
			$pager = $opresult['pager'];
			$add_paper = $opresult['add_paper'];
			$url = $opresult['url'];
			$paperid = $opresult['paperid'];
			include $this->template('awardfanlist');
		} 
	}
	public function doWebShakebigscreen() {
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];

		$opresult = BigScreen::WebBigScreenStartActivityList();
		if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_LIST_START_ACTIVITYS_SUCCESS_VAL) {
			$list = $opresult['list'];
			include $this->template('bigscreenstart');
		} 
	}
	public function doWebStartStopAct() {
		global $_GPC, $_W;

		$opresult = BigScreen::WebBigScreenStartStopActivity();
		if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_FAIL_VAL) {
			$data = array(
				'errno' => -1,
				'msg' => CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_FAIL_STR,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_VAL) {
			$data = array(
				'errno' => -1,
				'msg' => CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_STR,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_VAL) {
			$data = array(
				'errno' => -1,
				'msg' => CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_STR,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_SUCCESS_VAL) {
			$data = array(
				'errno' => 0,
				'started' => $opresult['started'],
				'url' => $this->createWebUrl('BigScreenStart', array('ruleid' => $opresult['ruleid'])),
			);
			echo json_encode($data);
		} 
	}
	public function doWebBigScreenStart() {
		global $_GPC, $_W;

		$opresult = BigScreen::WebActivityBigScreenLaunch();
		if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_START_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ALREADY_END_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ALREADY_END_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_STR);
		} else if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_LAUNCH_ACTIVITY_SUCCESS_VAL) {
			$ruleid = $opresult['ruleid'];
			$winfans = $opresult['winfans'];
			$winfannum = $opresult['winfannum'];
			$replywords = $opresult['replywords'];
			$giftnum = $opresult['giftnum'];
			$item = $opresult['item'];
			if(empty($item['backgrdpic'])) {
				$backgrdpic = '../addons/hpmd_shakewin/template/images/112.jpg';
			} else {
				$backgrdpic = $_W['siteroot'] . 'attachment/' . $item['backgrdpic'];
			}
			include $this->template('bigscreen');
		}     
	}
	public function doWebBigScreenAwarders() {
		global $_GPC, $_W;

		$opresult = BigScreen::WebActivityBigScreenAwarders();
		if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_START_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_ALREADY_END_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_ALREADY_END_STR);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_VAL) {
			message(CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_STR);
		} else if ($opresult['returnval'] == CommonValStr::$WINNER_LIST_SUCCESS_VAL) {
			$ruleid = $opresult['ruleid'];
			$winfans = $opresult['winfans'];
			$winfannum = $opresult['winfannum'];
			$replywords = $opresult['replywords'];
			$giftnum = $opresult['giftnum'];
			$item = $opresult['item'];
			if(empty($item['backgrdpic'])) {
				$backgrdpic = '../addons/hpmd_shakewin/template/images/114.jpg';
			} else {
				$backgrdpic = $_W['siteroot'] . 'attachment/' . $item['backgrdpic'];
			}
			include $this->template('bigscreenawarder');
		} 

	}
	public function doWebCheckShaking() {
		global $_GPC, $_W;
		
		$opresult = BigScreen::WebCheckShakingFans();
		if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR) {
			$data = array(
				'errno' => 1,
				'error' => CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_CHECK_SHAKING_FANS_SUCCESS_VAL) {
			$data = array(
				'errno' => 0,
				'msg' => json_encode($opresult['fans']),
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL) {
			$data = array(
				'errno' => 2,
				'msg' => CommonValStr::$CHECK_ACTIVITY_NOT_START_STR,
			);
			echo json_encode($data);
		}
	}
	public function doWebCheckWinning() {
		global $_GPC, $_W;
		
		$opresult = BigScreen::WebCheckWinningFans();
		if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR) {
			$data = array(
				'errno' => 1,
				'error' => CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR,
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$BIGSCREEN_CHECK_WINNING_FANS_SUCCESS_VAL) {
			$data = array(
				'errno' => 0,
				'msg' => json_encode($opresult['fans']),
			);
			echo json_encode($data);
		} else if ($opresult['returnval'] == CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL) {
			$data = array(
				'errno' => 2,
				'msg' => CommonValStr::$CHECK_ACTIVITY_NOT_START_STR,
			);
			echo json_encode($data);
		}
	}
	public function doWebBigScreenWaiter() {
		/*$opresult = BigScreen::WebActivityBigScreenAwarders();
		$item = $opresult['item'];
		if(empty($item['backgrdpic'])) {
			$backgrdpic = '../addons/hpmd_shakewin/template/images/114.jpg';
		} else {
			$backgrdpic = $_W['siteroot'] . 'attachment/' . $item['backgrdpic'];
		}*/
		include $this->template('bigscreenwaiter');
	}
	public function doWebCheckRunningAct() {
		global $_GPC, $_W;

		record_log('developer', 'txt', 'doWebCheckRunningAct', 'enter');
		$ruleid = BigScreen::bigscreen_running_actid();
		if($ruleid > 0) {
			$data = array(
				'errno' => 1,
				'url' => $this->createWebUrl('BigScreenStart', array('ruleid' => $ruleid)),
			);
			echo json_encode($data);
		} else {
			$data = array(
				'errno' => 0,
			);
			echo json_encode($data);
		}
	}
	public function doWebExportFans() {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		Exporter::ExportFansToCsv();
	}
}