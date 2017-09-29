<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CommonValStr.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/dbutility.php');

class ShakeActivity 
{
    public static function WebProcessActivityOperation($op)
	{
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			$retval = null;
			if (checksubmit()) {
				if (empty($id)) {
					$retval = ShakeActivity::WebCreateActivity();
					if($retval == CommonValStr::$REPLY_WORDS_EXIT_VAL) {
						return array('returnval' => $retval);
					}
				} else {
					$retval = ShakeActivity::WebUpdateActivityById($id);
					if($retval == CommonValStr::$REPLY_WORDS_EXIT_VAL) {
						return array('returnval' => $retval);
					}
				}
				return array('returnval' => $retval);
			}
			$item = DBUtility::getRuleById($id);
			$allcachekeys = num_module_weid_keys();
			return array('item' => $item, 'allcachekeys' => $allcachekeys, 'returnval' => CommonValStr::$ACTIVITY_EDITLIST_SUCCESS_VAL);
		}   else if ($op == 'delete') {
			$id = trim($_GPC['id']);
			$retval = ShakeActivity::WebDeleteActivityById($id);
			return array('returnval' => $retval);
		} else if ($op == 'deleteall') {
			$retval = ShakeActivity::WebDeleteSelectedActivitys();
			return array('returnval' => $retval);
		} else if ($op == 'copyall') {
			$retval = ShakeActivity::WebCopyAlldActivitys();
			return array('returnval' => $retval);
		} else if ($op == 'copyactaward') {
			$retval = ShakeActivity::WebCopyAlldActivitysWithAwards();
			return array('returnval' => $retval);
		} else if ($op == 'detail') {
			$id = trim($_GPC['id']);
			$item = ShakeActivity::WebGetActivityDetailById($id);
			return array('item' => $item, 'returnval' => CommonValStr::$ACTIVITY_DETAIL_SUCCESS_VAL);
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;

			$list = ShakeActivity::WebGetActivityPagedList();
			$total = ShakeActivity::WebGetActivityTotalNum();

			$pager = pagination($total, $pindex, $psize);

			if ($_GPC['add_paper'] == 1 && !empty($_GPC['paperid'])) {
				$add_paper = 1;
				session_start();
				$url = $_SESSION['last_url'];
				$paperid = intval($_GPC['paperid']);
			} else {
				$add_paper = 0;
			}

			return array('list' => $list, 'total' => $total, 'pager' => $pager, 'add_paper' => $add_paper, 'url' => $url, 'paperid' => $paperid, 'returnval' => CommonValStr::$ACTIVITY_LIST_SUCCESS_VAL);
		}
	}
 
    public static function WebClearActivityCache()
    {
		global $_GPC, $_W;
		if (empty($_W['uniacid'])) {
			record_log('developer', 'txt', 'WebClearActivityCache', '0');
			return '0';
		}
		DBUtility::clearActivityCache();
		return '1';
	}

    private static function WebUpdateActivityById($id)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$exitid = DBUtility::getRuleIdByReplyWordExceptId($_GPC['replywords'], $id);
		if($exitid > 0) {
			return CommonValStr::$REPLY_WORDS_EXIT_VAL;
		}

		$up = array(
			'weid' => $weid,
			'replywords' => $_GPC['replywords'],
			'updatetime' => time(),
			'starttime' => strtotime($_GPC['datelimitsc']['start']),
			'endtime' => strtotime($_GPC['datelimitsc']['end']),
			'exchangestarttime' => strtotime($_GPC['datelimitex']['start']),
			'exchangeendtime' => strtotime($_GPC['datelimitex']['end']),
			'cachesecond' => $_GPC['cachesecond'],
			'awardtimestotal' => $_GPC['awardtimestotal'],
			'shaketimeswin' => $_GPC['shaketimeswin'],
			'awardtimesday' => $_GPC['awardtimesday'],
			'awardtotal' => $_GPC['awardtotal'],
			'awarddiffact' => $_GPC['awarddiffact'],
			'minawardinterval' => $_GPC['minawardinterval'],
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'online' => $_GPC['online'],
			'xyrestrict' => $_GPC['xyrestrict'],
			'latitude' => $_GPC['latitude'],
			'longitude' => $_GPC['longitude'],
			'xyradius' => $_GPC['xyradius'],
			'xydescript' => $_GPC['xydescript'],
			'screentime' => $_GPC['screentime'],
			'ballspeed' => $_GPC['ballspeed'],
			'autodirection' => $_GPC['autodirection'],
			'directtime' => $_GPC['directtime'],
			'checkshaketime' => $_GPC['checkshaketime'],
			'checkwintime' => $_GPC['checkwintime'],
			'headexitsecond' => $_GPC['headexitsecond'],
			'backgrdpic' => $_GPC['backgrdpic'],
			'showpic' => $_GPC['showpic'],
			'enableshare' => $_GPC['enableshare'],
			'sharetitle' => $_GPC['sharetitle'],
			'sharedesc' => $_GPC['sharedesc'],
			'sharelink' => $_GPC['sharelink'],
			'sharepic' => $_GPC['sharepic'],
		);
		DBUtility::updateById(DBUtility::$table_shakewin_rules, $up, $id);
		 
		if(!empty($_GPC['showpic'])) {
			copy('../attachment/'.$_GPC['showpic'], '../addons/hpmd_shakewin/template/images/113.jpg');
		} else {
			copy('../addons/hpmd_shakewin/template/images/112.jpg', '../addons/hpmd_shakewin/template/images/113.jpg');
		}
		
		return CommonValStr::$ACTIVITY_EDIT_SUCCESS_VAL;
    }
    private static function WebCreateActivity()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$exitid = DBUtility::getRuleIdByReplyWord($_GPC['replywords']);
		if($exitid > 0) {
			return CommonValStr::$REPLY_WORDS_EXIT_VAL;
		}

		$insert = array(
			'weid' => $weid,
			'replywords' => $_GPC['replywords'],
			'createtime' => time(),
			'starttime' => strtotime($_GPC['datelimitsc']['start']),
			'endtime' => strtotime($_GPC['datelimitsc']['end']),
			'exchangestarttime' => strtotime($_GPC['datelimitex']['start']),
			'exchangeendtime' => strtotime($_GPC['datelimitex']['end']),
			'cachesecond' => $_GPC['cachesecond'],
			'awardtimestotal' => $_GPC['awardtimestotal'],
			'shaketimeswin' => $_GPC['shaketimeswin'],
			'awardtimesday' => $_GPC['awardtimesday'],
			'awardtotal' => $_GPC['awardtotal'],
			'awarddiffact' => $_GPC['awarddiffact'],
			'minawardinterval' => $_GPC['minawardinterval'],
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'online' => $_GPC['online'],
			'xyrestrict' => $_GPC['xyrestrict'],
			'latitude' => $_GPC['latitude'],
			'longitude' => $_GPC['longitude'],
			'xyradius' => $_GPC['xyradius'],
			'xydescript' => $_GPC['xydescript'],
			'screentime' => $_GPC['screentime'],
			'ballspeed' => $_GPC['ballspeed'],
			'autodirection' => $_GPC['autodirection'],
			'directtime' => $_GPC['directtime'],
			'checkshaketime' => $_GPC['checkshaketime'],
			'checkwintime' => $_GPC['checkwintime'],
			'headexitsecond' => $_GPC['headexitsecond'],
			'backgrdpic' => $_GPC['backgrdpic'],
			'showpic' => $_GPC['showpic'],
			'enableshare' => $_GPC['enableshare'],
			'sharetitle' => $_GPC['sharetitle'],
			'sharedesc' => $_GPC['sharedesc'],
			'sharelink' => $_GPC['sharelink'],
			'sharepic' => $_GPC['sharepic'],
		);
		$result = DBUtility::insert(DBUtility::$table_shakewin_rules, $insert);

		if(!empty($_GPC['showpic'])) {
			copy('../attachment/'.$_GPC['showpic'], '../addons/hpmd_shakewin/template/images/113.jpg');
		} else {
			copy('../addons/hpmd_shakewin/template/images/112.jpg', '../addons/hpmd_shakewin/template/images/113.jpg');
		}
				
		return CommonValStr::$ACTIVITY_ADD_SUCCESS_VAL;
    }
    private static function WebDeleteActivityById($id)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$id = intval($_GPC['id']);
		
		DBUtility::delete(DBUtility::$table_shakewin_awards, array("ruleid" => $id, 'weid' => $weid));
		DBUtility::delete(DBUtility::$table_shakewin_fans, array("ruleid" => $id, 'weid' => $weid));
		DBUtility::delete(DBUtility::$table_shakewin_rules, array("id" => $id, 'weid' => $weid));
		return CommonValStr::$ACTIVITY_DEL_SUCCESS_VAL;
	}
    private static function WebDeleteSelectedActivitys()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			DBUtility::delete(DBUtility::$table_shakewin_awards, array("ruleid" => $id, 'weid' => $weid));
			DBUtility::delete(DBUtility::$table_shakewin_fans, array("ruleid" => $id, 'weid' => $weid));
			DBUtility::delete(DBUtility::$table_shakewin_rules, array("id" => $id, 'weid' => $weid));
		}
		return CommonValStr::$ACTIVITY_DEL_ALL_SUCCESS_VAL;
	}
    private static function WebCopyAlldActivitys()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			$item = DBUtility::getRuleById($id);
			if(!empty($item) && !empty($item['replywords'])) {
				$insert = array(
					'weid' => $item['weid'],
					'replywords' => $item['replywords'] . time(),
					'createtime' => time(),
					'starttime' => $item['starttime'],
					'endtime' => $item['endtime'],
					'exchangestarttime' => $item['exchangestarttime'],
					'exchangeendtime' => $item['exchangeendtime'],
					'cachesecond' => $item['cachesecond'],
					'awardtimestotal' => $item['awardtimestotal'],
					'shaketimeswin' => $item['shaketimeswin'],
					'awardtimesday' => $item['awardtimesday'],
					'awardtotal' => $item['awardtotal'],
					'awarddiffact' => $item['awarddiffact'],
					'minawardinterval' => $item['minawardinterval'],
					'title' => $item['title'],
					'description' => $item['description'],
					'online' => $item['online'],
					'xyrestrict' => $item['xyrestrict'],
					'latitude' => $item['latitude'],
					'longitude' => $item['longitude'],
					'xyradius' => $item['xyradius'],
					'xydescript' => $item['xydescript'],
					'screentime' => $item['screentime'],
					'ballspeed' => $item['ballspeed'],
					'autodirection' => $item['autodirection'],
					'directtime' => $item['directtime'],
					'checkshaketime' => $item['checkshaketime'],
					'checkwintime' => $item['checkwintime'],
					'headexitsecond' => $item['headexitsecond'],
					'backgrdpic' => $item['backgrdpic'],
					'showpic' => $_GPC['showpic'],
					'enableshare' => $_GPC['enableshare'],
					'sharetitle' => $_GPC['sharetitle'],
					'sharedesc' => $_GPC['sharedesc'],
					'sharelink' => $_GPC['sharelink'],
					'sharepic' => $_GPC['sharepic'],
				);
				$result = DBUtility::insert(DBUtility::$table_shakewin_rules, $insert);
			}
		}
		return CommonValStr::$ACTIVITY_COPY_SUCCESS_VAL;
	}

    private static function WebCopyAlldActivitysWithAwards()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			$item = DBUtility::getRuleById($id);
			if(!empty($item) && !empty($item['replywords'])) {
				$indexdiff = time();
				$insert = array(
					'weid' => $item['weid'],
					'replywords' => $item['replywords'] . $indexdiff,
					'createtime' => time(),
					'starttime' => $item['starttime'],
					'endtime' => $item['endtime'],
					'exchangestarttime' => $item['exchangestarttime'],
					'exchangeendtime' => $item['exchangeendtime'],
					'cachesecond' => $item['cachesecond'],
					'awardtimestotal' => $item['awardtimestotal'],
					'shaketimeswin' => $item['shaketimeswin'],
					'awardtimesday' => $item['awardtimesday'],
					'awardtotal' => $item['awardtotal'],
					'awarddiffact' => $item['awarddiffact'],
					'minawardinterval' => $item['minawardinterval'],
					'title' => $item['title'],
					'description' => $item['description'],
					'online' => $item['online'],
					'xyrestrict' => $item['xyrestrict'],
					'latitude' => $item['latitude'],
					'longitude' => $item['longitude'],
					'xyradius' => $item['xyradius'],
					'xydescript' => $item['xydescript'],
					'screentime' => $item['screentime'],
					'ballspeed' => $item['ballspeed'],
					'autodirection' => $item['autodirection'],
					'directtime' => $item['directtime'],
					'checkshaketime' => $item['checkshaketime'],
					'checkwintime' => $item['checkwintime'],
					'headexitsecond' => $item['headexitsecond'],
					'backgrdpic' => $item['backgrdpic'],
					'showpic' => $_GPC['showpic'],
					'enableshare' => $_GPC['enableshare'],
					'sharetitle' => $_GPC['sharetitle'],
					'sharedesc' => $_GPC['sharedesc'],
					'sharelink' => $_GPC['sharelink'],
					'sharepic' => $_GPC['sharepic'],
				);
				$result = DBUtility::insert(DBUtility::$table_shakewin_rules, $insert);
				$insertid = pdo_insertid();

				if($insertid > $id) {
					$awardsitems = DBUtility::getAwardsByRuleId($id);
					if(!empty($awardsitems)) {
						foreach($awardsitems as &$value){
							$insert = array(
								'weid' => $value['weid'],
								'createtime' => time(),
								'totalnum' => $value['totalnum'],
								'probebility' => $value['probebility'],
								'name' => $value['name'],
								'description' => $value['description'],
								'code' => $value['code'] . $indexdiff,
								'address' => $value['address'],
								'ruleid' => $insertid,
								'online' => $value['online'],
							);
							$result = DBUtility::insert(DBUtility::$table_shakewin_awards, $insert);
						}
					} 
				}
			}
		}
		return CommonValStr::$ACTIVITY_AWARDS_COPY_SUCCESS_VAL;

	}

    private static function WebGetActivityDetailById($id)
    {
		return DBUtility::getRuleById($id);
	}

    private static function WebGetActivityPagedList()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$sql = "";
		$params = array();
		if (!empty($_GPC['replywords'])) {
			$sql .= ' AND `replywords` LIKE :replywords';
			$params[':replywords'] = "%{$_GPC['replywords']}%";
		}
		return DBUtility::getRuleListWithPage($sql, $pindex, $psize, $params);
	}
	
    private static function WebGetActivityTotalNum()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$sql = "";
		$params = array();
		if (!empty($_GPC['replywords'])) {
			$sql .= ' AND `replywords` LIKE :replywords';
			$params[':replywords'] = "%{$_GPC['replywords']}%";
		}
		return DBUtility::getTotalRuleNum($sql);
	}




}