<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CacheLayer.php');

class BigScreen 
{
    public static function record_shaking_fans($actid, $nickname, $headimg){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($nickname) || empty($headimg) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_shaking' . $actid;
		$subdata['openid'] = $openid;
		//$subdata['openid'] = $nickname; //���ԣ�ģ������û�ҡ���ֻ�
		$subdata['nickname'] = $nickname;
		$subdata['headimg'] = $headimg;
		$subdata['timestamp'] = time();

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			$result = array();
		    $result[] = json_encode($subdata);
			foreach($table_keys as $value){
				$key_decode = json_decode($value, true);
				if($key_decode['openid'] != $openid) {
					$result[] = $value;
				}
			}
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($result));

		} else {
			$values = array();
			$values[] = json_encode($subdata);
			cache_write_jy($tablekeysname, json_encode($values));
		}
		record_module_weid_keys($tablekeysname);
	}
	 
    public static function get_shaking_fans($actid, $timestamp){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_shaking' . $actid;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			$result = array();
			foreach($table_keys as $value){
				$key_decode = json_decode($value, true);
				if($key_decode['timestamp'] > $timestamp) {
					$result[] = $key_decode;
				}
			}
			return $result;
		} else {
			return null;
		}
	}

    public static function record_award_fans($actid, $nickname, $headimg, $name){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($nickname) || empty($headimg) || empty($name) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_award' . $actid;
		$subdata['openid'] = $openid;
		$subdata['nickname'] = $nickname;
		$subdata['headimg'] = $headimg;
		$subdata['name'] = $name;
		$subdata['timestamp'] = time();

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {

			$result = array();
		    $result[] = json_encode($subdata);
			foreach($table_keys as $value){
				$result[] = $value;
			}
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($result));

		} else {
			$values = array();
			$values[] = json_encode($subdata);
			cache_write_jy($tablekeysname, json_encode($values));
		}
		record_module_weid_keys($tablekeysname);
	}
 
    public static function get_award_fans($actid, $timestamp){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_award' . $actid;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			$result = array();
			foreach($table_keys as $value){
				$key_decode = json_decode($value, true);
				if($key_decode['timestamp'] > $timestamp) {
					$result[] = $key_decode;
				}
			}
			record_log('ShakeAward', 'txt', 'get_award_fans memcached', 'get_award_fans memcached');
			return $result;
		} else {
			//���Դ�db�л�ȡ���
			$sqlall = "SELECT openid,awardid,createtime FROM " . tablename('shakewin_fans');
			$sqlall .= "  WHERE weid = '{$_W['uniacid']}' AND ruleid=" . $actid;
			$sqlall .= " ORDER BY createtime DESC ";
			$listawards = pdo_fetchall_with_cache('shakewin_fans', $sqlall, $params);
			record_log('ShakeAward', 'txt', 'listawards memcached', json_encode($listawards));
			
			$result = array();
			$list = array();
			if(!empty($listawards)) {
				foreach ($listawards as $item) {
					if(!empty($item['openid'])) {
						$subdata['openid'] = $item['openid'];
						$sql = 'SELECT nickname,headimg  FROM ' . tablename('shakewin_wxinfo') . ' WHERE `openid`=:openid';
						$params = array();
						$params[':openid'] = $item['openid'];
						$faninfo = pdo_fetch_with_cache('shakewin_wxinfo', $sql, $params);
						if(!empty($faninfo)) {
							$subdata['nickname'] = $faninfo['nickname'];
							$subdata['headimg'] = $faninfo['headimg'];
						}
					}
					if(!empty($item['awardid'])) {
						$sql = 'SELECT name FROM ' . tablename('shakewin_awards') . ' WHERE `id`=:id';
						$params = array();
						$params[':id'] = $item['awardid'];
						$awardinfo = pdo_fetchcolumn_with_cache('shakewin_awards', $sql, $params);
						if(!empty($awardinfo)) {
							$subdata['name'] = $awardinfo;
						}

					}
					$subdata['timestamp'] = $item['createtime'];

					if($subdata['timestamp'] > $timestamp) {
						$result[] = $subdata;
					}
					$list[] = json_encode($subdata);
				}
			}
			cache_write_jy($tablekeysname, json_encode($list));
			record_log('ShakeAward', 'txt', 'get_award_fans db2', 'get_award_fans db');
			record_module_weid_keys($tablekeysname);
			return $result;
		}
	}

    public static function bigscreen_begin($actid){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulename = get_module_name();
		record_log('BigScreen', 'txt', 'modulename', $modulename);

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_be' . $actid;
		$subdata['begin'] = 1;
		$subdata['timestamp'] = time();
		$encodedata = json_encode($subdata);
		cache_write_jy($tablekeysname, $encodedata);
		record_module_weid_keys($tablekeysname);

		//ֱ��д����ݿ⣬����Ҫȷ����stopʱɾ��������¼
		$insert = array(
			'cachekey' => $tablekeysname,
			'cacheval' => $encodedata,
		);
		$result = pdo_insert_with_cache('shakewin_cached', $insert);

	}

    public static function bigscreen_stop($actid){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_be' . $actid;
		$shakingkeysname = $modulename . $weid . '_shaking' . $actid;
		$awardkeysname = $modulename . $weid . '_award' . $actid;

		cache_delete_jy($tablekeysname); //�������������
		cache_delete_jy($shakingkeysname); //���ҡ����˿��������
		cache_delete_jy($awardkeysname); //���񽱷�˿��������
		pdo_delete_with_cache("shakewin_cached", array("cachekey" => $tablekeysname));

		//�ֹͣ �Ƿ��Զ�����Ϊ���� online==0
	}

    public static function is_bigscreen_begin($actid){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_be' . $actid;
		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys) && $table_keys['timestamp'] > 0) {
			if($table_keys['begin'] == 1) {
				return true;
			} else {
				return false;
			}
		} else {
			$sql = 'SELECT cacheval FROM ' . tablename('shakewin_cached');
			$sql .= ' WHERE `cachekey` = :cachekey LIMIT 1';
			$params = array(':cachekey' => $tablekeysname);
			$cacheval = pdo_fetchcolumn_with_cache('shakewin_cached', $sql, $params);
			if(!empty($cacheval)) {
				$table_keys_db = json_decode($cacheval, true);
				if(!empty($table_keys_db) && $table_keys_db['timestamp'] > 0) {
					if($table_keys_db['begin'] == 1) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	
    public static function bigscreen_running_actid() 
	{
		$idlist = DBUtility::getAllBigScreenRuleIdList();
		if($idlist) 
		{
			foreach ($idlist as $item) 
			{
				if($item['id'] > 0 && BigScreen::is_bigscreen_begin($item['id']) == true)
				{
					return $item['id'];
				}
			}
		} else {
			return 0;
		}
		return 0;
	}

	//ҳ���߼�
    public static function WebBigScreenStartActivityList()
	{
		global $_GPC, $_W;
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$sql = "";
		$params = array();
		if (!empty($_GPC['replywords'])) {
			$sql .= ' AND `replywords` LIKE :replywords';
			$params[':replywords'] = "%{$_GPC['replywords']}%";
		}
		$listold = DBUtility::getOnlineBigscreenRuleListWithPage($sql, $pindex, $psize, $params);
		$total = DBUtility::getTotalOnlineBigscreenRuleNum($sql);
		$pager = pagination($total, $pindex, $psize);

		if ($_GPC['add_paper'] == 1 && !empty($_GPC['paperid'])) {
			$add_paper = 1;
			session_start();
			$url = $_SESSION['last_url'];
			$paperid = intval($_GPC['paperid']);

		} else {
			$add_paper = 0;
		}

		$list = array();
		if(!empty($listold)) {
			foreach ($listold as $item) {
				$subdata['id'] = $item['id'];
				$subdata['title'] = $item['title'];
				$subdata['replywords'] = $item['replywords'];
				$begin = BigScreen::is_bigscreen_begin($item['id']);
				if($begin != true) {
					$begin = 0; 
				} else {
					$begin = 1;
				}
				$subdata['started'] = $begin;
				$list[] = $subdata;
			}
		}

		return array('list' => $list,  'returnval' => CommonValStr::$BIGSCREEN_LIST_START_ACTIVITYS_SUCCESS_VAL);
	}
    public static function WebBigScreenStartStopActivity()
	{
		global $_GPC, $_W;
		$ruleid = $_GPC['ruleid'];
		$started = $_GPC['started'];

		$isbegin = BigScreen::is_bigscreen_begin($ruleid);
		if($isbegin != true) {
			$isbegin = 0; 
		} else {
			$isbegin = 1;
		}

		if($ruleid <= 0 || ($started != 0 && $started != 1)) {
			return array('returnval' => CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_FAIL_VAL);
		}

		if($started == 0 && $isbegin == 0) {
			if(BigScreen::bigscreen_running_actid() > 0) {
				return array('returnval' => CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_VAL);
			}

			$started = 1;
			BigScreen::bigscreen_begin($ruleid);
		} else if($started == 1 && $isbegin == 1) {
			$started = 0;
			BigScreen::bigscreen_stop($ruleid);
		} else {
			return array('returnval' => CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_VAL);
		}

		return array('ruleid' => $ruleid, 'started' => $started, 'returnval' => CommonValStr::$BIGSCREEN_START_STOP_ACTIVITY_SUCCESS_VAL);
	}
    public static function WebActivityBigScreenLaunch()
	{
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$ruleid = $_GPC['ruleid'];

		//���ԣ�ģ������û�ҡ���ֻ�
		/*
        for($i=0; $i<100; $i++) {
			record_shaking_fans($ruleid, '�º�' . $i, 'test_head_url');
		}
		*/

		if($ruleid <= 0) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_VAL);
		}

		$item = DBUtility::getRuleById($ruleid);
		if ($item == false || empty($item['starttime']) || empty($item['endtime'])) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
		}

		//�����ж�ȡ�Ƿ����Ļ��ʼ��ť�Ѿ����£����Ҵ���Ļ����ť��û���¡�
		record_log('BigScreen', 'txt', 'screentime', $item['screentime']);
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

		$giftnum = DBUtility::getGiftSumNumByRuleid($ruleid);
		$winfans = BigScreen::get_award_fans($ruleid, 0);
		$winfannum = count($winfans);
		record_log('Shakebigscreen', 'txt', 'winfans', json_encode($winfans));

		return array('replywords' => $item['replywords'], 'giftnum' => $giftnum, 'ruleid' => $ruleid, 'winfans' => $winfans, 'winfannum' => $winfannum, 'item' => $item, 'returnval' => CommonValStr::$BIGSCREEN_LAUNCH_ACTIVITY_SUCCESS_VAL);
	}
    public static function WebActivityBigScreenAwarders()
	{
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$ruleid = $_GPC['ruleid'];

		if($ruleid <= 0) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_VAL);
		}

		$item = DBUtility::getRuleById($ruleid);
		if ($item == false || empty($item['starttime']) || empty($item['endtime'])) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
		}

		if($item['online'] == 0) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NOT_ONLINE_VAL);
		}

		$giftnum = DBUtility::getGiftSumNumByRuleid($ruleid);
		$winfans = BigScreen::get_award_fans($ruleid, 0);
		$winfannum = count($winfans);
		record_log('Shakebigscreen', 'txt', 'winfans', json_encode($winfans));

		return array('replywords' => $item['replywords'], 'giftnum' => $giftnum, 'ruleid' => $ruleid, 'winfans' => $winfans, 'winfannum' => $winfannum, 'item' => $item, 'returnval' => CommonValStr::$WINNER_LIST_SUCCESS_VAL);
	}
    public static function WebCheckShakingFans()
	{
		global $_GPC, $_W;
		$ruleid = $_GPC['ruleid'];
		$headexitsecond = $_GPC['headexitsecond'];
		record_log('CheckShaking', 'txt', 'CheckShaking', $ruleid);

		if($ruleid <= 0) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR);
		}


		$item = DBUtility::getRuleById($ruleid);
		if ($item == false || empty($item['starttime']) || empty($item['endtime'])) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
		}

		//�����ж�ȡ�Ƿ����Ļ��ʼ��ť�Ѿ����£����Ҵ���Ļ����ť��û���¡�
		record_log('BigScreen', 'txt', 'screentime', $item['screentime']);
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

		$fans = BigScreen::get_shaking_fans($ruleid, time() - $headexitsecond);

		return array('fans' => $fans,  'returnval' => CommonValStr::$BIGSCREEN_CHECK_SHAKING_FANS_SUCCESS_VAL);
	}
    public static function WebCheckWinningFans()
	{
		global $_GPC, $_W;
		$ruleid = $_GPC['ruleid'];
		record_log('CheckWinning', 'txt', 'ruleid', $ruleid);

		if($ruleid <= 0) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR);
		}

		$item = DBUtility::getRuleById($ruleid);
		if ($item == false || empty($item['starttime']) || empty($item['endtime'])) {
			return array('returnval' => CommonValStr::$CHECK_ACTIVITY_ERR_REPLYWORDS_VAL);
		}

		//�����ж�ȡ�Ƿ����Ļ��ʼ��ť�Ѿ����£����Ҵ���Ļ����ť��û���¡�
		record_log('BigScreen', 'txt', 'screentime', $item['screentime']);
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

		$fans = BigScreen::get_award_fans($ruleid, 0);

		return array('fans' => $fans,  'returnval' => CommonValStr::$BIGSCREEN_CHECK_WINNING_FANS_SUCCESS_VAL);
	}


}