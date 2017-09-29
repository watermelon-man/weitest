<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CommonValStr.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/dbutility.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/ShakeAuthorize.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/Bigscreen.php');

class ShakeMobile 
{
    public static function CheckActivity($APPID)
	{
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		$avatar = $_W['fans']['avatar'];
		$nickname = $_W['fans']['nickname'];
		$sex = $_W['fans']['sex'];
		$weid = $_W['uniacid'];
		$replywords = $_GPC['replywords'];

		if (empty($openid)) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_CARE_VAL);
		}

		if (empty($replywords)) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
		} else {
			default_cache_expiretime(DBUtility::$table_shakewin_rules, " replywords='" . $replywords . "' and ");
			$item = DBUtility::getRuleByReplyWord($replywords);
			if ($item == false || empty($item['starttime']) || empty($item['endtime'])) {
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
			}

			if($item['screentime'] == 1) {
				$begin = BigScreen::is_bigscreen_begin($item['id']);
				if($begin != true) {
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL);
				}
			} else {
				$now = time();
				if($now < $item['starttime']) {
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL);
				}
				if($now > $item['endtime']) {
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ALREADY_END_VAL);
				}
			}

			if($item['online'] == 0) {
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_VAL);
			}
		}

		$exitid = DBUtility::getWeixOpenid();
		record_log('developer', 'txt', 'exitid', $exitid);

		if(empty($exitid)) {
			if(empty($avatar) || empty($nickname)) {
				ShakeAuthorize::GetFansInfo($replywords, $APPID); 
				record_log('developer', 'txt', 'fansQueryInfo1', json_encode($_W['fans']));
			} else {
				$insert = array(
					'openid' => $openid,
					'weid' => $weid,
					'createtime' => time(),
					'nickname' => $nickname,
					'sex' => $sex,
					'headimg' => $avatar,
				);
				DBUtility::insert(DBUtility::$table_shakewin_wxinfo, $insert);
			}
		}

		$shaketimeswin = 5;
		if($item['shaketimeswin'] > 0) {
			$shaketimeswin = $item['shaketimeswin'];
		}
		
		return array('shaketimeswin' => $shaketimeswin, 'replywords' => $replywords, 'item' => $item, 'returnval' => CommonValStr::$CHECK_ACTIVITY_SUCCESS_VAL);
	}
 
    public static function ShakeAward()
	{
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		$weid = $_W['uniacid'];
		$replywords = $_GPC['replywords'];
		$shaketimes = $_GPC['shaketimes'];
		record_log('ShakeAward', 'txt', 'replywords shaketimes', $replywords . ' : ' . $shaketimes);

		if (empty($openid)) {
			record_log('ShakeAward', 'txt', 'empty openid', $openid);
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_CARE_VAL);
		}

		if (empty($replywords)) {
			record_log('ShakeAward', 'txt', 'empty replywords', $replywords);
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
		} else {
			default_cache_expiretime(DBUtility::$table_shakewin_rules, " replywords='" . $replywords . "' and ");

			$item = DBUtility::getRuleByReplyWord($replywords);

			if ($item == false || empty($item['id']) || empty($item['starttime']) || empty($item['endtime'])) {
				record_log('ShakeAward', 'txt', 'empty item', json_encode($item));
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
			}

			if($item['screentime'] == 1) {
				$begin = BigScreen::is_bigscreen_begin($item['id']);
				if($begin != true) {
					record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_NOT_START_VAL begin', ' begin:' . $begin);
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL);
				}
			} else {
				$now = time();
				if($now < $item['starttime']) {
					record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_NOT_START_VAL starttime', $now . ' starttime:' . $item['starttime']);
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_START_VAL);
				}
				if($now > $item['endtime']) {
					record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_ALREADY_END_VAL endtime', $now . ' endtime:' . $item['endtime']);
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ALREADY_END_VAL);
				}
			}

			if($item['online'] == 0) {
				record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_NOT_ONLINE_VAL online:' . $item['online']);
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_VAL);
			}

			$exitinfo = DBUtility::getWeixUsrInfo();
			record_log('ShakeAward', 'txt', 'nickname', $exitinfo['nickname'] . ' : ' . $exitinfo['headimg']);

			if ($exitinfo == false || empty($exitinfo['nickname']) ||  empty($exitinfo['headimg'])) {
				record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_NOT_USRINFO_VAL exitinfo', json_encode($exitinfo));
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_USRINFO_VAL);
			}

			BigScreen::record_shaking_fans($item['id'], $exitinfo['nickname'], $exitinfo['headimg']);
			if($item['minawardinterval'] > 0) {
				$adfans = BigScreen::get_award_fans($item['id'], 0);
				if($adfans && count($adfans) > 0) {
					if($adfans[0]['timestamp'] + $item['minawardinterval'] > time()) {
						record_log('ShakeAward', 'txt', 'BIGSCREEN_AWARD_MINTIME_LIMIT_SUCCESS_VAL', CommonValStr::$BIGSCREEN_AWARD_MINTIME_LIMIT_SUCCESS_STR);
						return array('returnval' => CommonValStr::$BIGSCREEN_AWARD_MINTIME_LIMIT_SUCCESS_VAL);
					}
				}
			}

			if($item['awardtimesday'] > 0) {
				$begintoday = mktime(0,0,0,date('m'), date('d'), date('Y'));
				$wherestr_daytt = ' ruleid=' . $item['id'] . ' AND ' . " openid='" . $openid . "' AND weid=" . $weid . " AND createtime > " . $begintoday;
				$daytt = table_count_num_withcache(DBUtility::$table_shakewin_fans, $wherestr_daytt);
				record_log('ShakeAward', 'txt', 'daytt', 'daytt: ' . $daytt . ' awardtimesday: ' . $item['awardtimesday']);
				if($daytt >= $item['awardtimesday']) {
					record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_DAYTOTAL_REACH_VAL', 'CHECK_ACTIVITY_DAYTOTAL_REACH_VAL');
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_DAYTOTAL_REACH_VAL);
				}
			} else {
				record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_DAYTOTAL_NONE_VAL', 'CHECK_ACTIVITY_DAYTOTAL_NONE_VAL');
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_DAYTOTAL_NONE_VAL);
			}

			if($item['awardtimestotal'] > 0 && $item['starttime'] > 0) {
				if($item['awarddiffact'] == 0) {
					//�ҳ��Ѿ����õĻ���ų�
					$notonlinesql = " ";
					$onlinerules = DBUtility::getNotOnlineRuleIds();
					if(!empty($onlinerules)) {
						foreach($onlinerules as &$value){
							$notonlinesql .= ' AND ruleid !=' . $value['id'];
						}
					} 

					$wherestr_usrtt = " openid='" . $openid . "' AND weid=" . $weid . $notonlinesql;
				} else {
					$wherestr_usrtt = ' ruleid=' . $item['id'] . ' AND ' . " openid='" . $openid . "' AND weid=" . $weid;
				}
				record_log('ShakeAward', 'txt', 'wherestr_usrtt', $wherestr_usrtt);
				$usrtt = table_count_num_withcache(DBUtility::$table_shakewin_fans, $wherestr_usrtt);
				record_log('ShakeAward', 'txt', 'usrtt', 'usrtt: ' . $usrtt . ' awardtimestotal: ' . $item['awardtimestotal']);
				if($usrtt >= $item['awardtimestotal']) {
					record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_TOTAL_REACH_VAL', 'CHECK_ACTIVITY_TOTAL_REACH_VAL');
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_TOTAL_REACH_VAL);
				}
			} else {
				record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_TOTAL_NONE_VAL', 'CHECK_ACTIVITY_TOTAL_NONE_VAL');
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_TOTAL_NONE_VAL);
			}

			if($item['awardtotal'] > 0 && $item['starttime'] > 0) {
				$wherestr_alltt = ' ruleid=' . $item['id'] . ' AND ' . " weid=" . $weid . " AND createtime > " . $item['starttime'];
				$alltt = table_count_num_withcache(DBUtility::$table_shakewin_fans, $wherestr_alltt);
				record_log('ShakeAward', 'txt', 'alltt', 'alltt: ' . $alltt . ' awardtotal: ' . $item['awardtotal']);
				if($alltt >= $item['awardtotal']) {
					record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_REACH_VAL', 'CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_REACH_VAL');
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_REACH_VAL);
				}
			} else {
				record_log('ShakeAward', 'txt', 'CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_NONE_VAL', 'CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_NONE_VAL');
				return array('returnval' => CommonValStr::$CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_NONE_VAL);
			}

			record_log('ShakeAward', 'txt', 'before lock', 'before lock');
			$write_key = 'shakewin_fans_lock' . $weid;
			$lock = new CacheLock($write_key);
			$lock->lock();

			try
			{
				$win = false;
				$winid = 0;
				$winnum = 0;
				$winname = null;
				$listawards = DBUtility::getOnlineAwardsByRuleId($item['id']);

				mt_srand((double) microtime() * 1000000);
				$randnum = mt_rand(0, 100);
				if (!empty($listawards)) {
					foreach ($listawards as $row) {
						if(!empty($row['probebility'])){
							//�齱�߼�
							if($win == 1 || $row['probebility'] > 100 || $row['probebility'] <= 0) {
								continue;
							}
							$bordnum = 100 - $row['probebility'];
							record_log('ShakeAward', 'txt', 'randnum', 'randnum: ' . $randnum . ' bordnum:' . $bordnum);
							if($randnum > $bordnum) {
								$win = true;
								$winid = $row['id'];
								$winnum = $row['totalnum'];
								$winname = $row['name'];
								record_log('ShakeAward', 'txt', 'winid', 'winid: ' . $winid);
								break;
							}
						}
					}
				}

				if($win == true && $winid > 0) {
					record_log('ShakeAward', 'txt', 'get award!', 'after lock');
					$update = array(
						'totalnum' => $winnum - 1,
					);
					DBUtility::updateById(DBUtility::$table_shakewin_awards, $update, $winid);

					$insert = array(
						'openid' => $openid,
						'weid' => $weid,
						'createtime' => time(),
						'awardid' => $winid,
						'ruleid' => $item['id'],
						'awardstatus' => 1, //���н�δ����
						'shaketimes' => $shaketimes,
					);
					DBUtility::insert(DBUtility::$table_shakewin_fans, $insert);
					record_log('ShakeAward', 'txt', 'insert', json_encode($insert));
					inc_table_count('shakewin_fans', $wherestr_daytt, 1);
					inc_table_count('shakewin_fans', $wherestr_usrtt, 1);
					inc_table_count('shakewin_fans', $wherestr_alltt, 1);

					BigScreen::record_award_fans($item['id'], $exitinfo['nickname'], $exitinfo['headimg'], $winname);

					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_WIN_REMINDER_VAL, 'awardname' => $winname);
				} else {
					return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOTWIN_REMINDER_VAL);
				}

			}
			catch(Exception $ex)
			{
				$lock->unlock();
				record_log('Error', 'txt', 'doMobileShakeAward Exception', $ex->getMessage());
				die(json_encode(array("success" => 0, "msg" => CommonValStr::$CHECK_ACTIVITY_NOMORE_AWARDS_STR)));
			}

			$lock->unlock();
			record_log('ShakeAward', 'txt', 'after lock', 'after lock');
		}

	}
}