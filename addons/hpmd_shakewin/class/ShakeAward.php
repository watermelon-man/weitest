<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CommonValStr.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/dbutility.php');

class ShakeAward 
{
    public static function WebProcessAwardOperation($op)
	{
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			$retval = null;
			if (checksubmit()) {
				if (empty($id)) {
					$retval = ShakeAward::WebCreateAward();
					if($retval == CommonValStr::$AWARD_NAME_EXIT_VAL) {
						return array('returnval' => $retval);
					}
				} else {
					$retval = ShakeAward::WebUpdateAwardById($id);
					if($retval == CommonValStr::$AWARD_NAME_EXIT_VAL) {
						return array('returnval' => $retval);
					}
				}
				return array('returnval' => $retval);
			}
			$item = DBUtility::getAwardsById($id);
			return array('item' => $item, 'returnval' => CommonValStr::$AWARD_EDITLIST_SUCCESS_VAL);
		} else if ($op == 'delete') {
			$id = trim($_GPC['id']);
			$retval = ShakeAward::WebDeleteAwardById($id);
			return array('returnval' => $retval);

		} else if ($op == 'deleteall') {
			$retval = ShakeAward::WebDeleteSelectedAwards();
			return array('returnval' => $retval);
		} else if ($op == 'detail') {
			$id = trim($_GPC['id']);
			$item = ShakeAward::WebGetAwardDetailById($id);
			return array('item' => $item, 'returnval' => CommonValStr::$AWARD_DETAIL_SUCCESS_VAL);
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;

			$list = ShakeAward::WebGetAwardPagedList();
			$total = ShakeAward::WebGetAwardTotalNum($sql);
			$pager = pagination($total, $pindex, $psize);

			if ($_GPC['add_paper'] == 1 && !empty($_GPC['paperid'])) {
				$add_paper = 1;
				session_start();
				$url = $_SESSION['last_url'];
				$paperid = intval($_GPC['paperid']);

			} else {
				$add_paper = 0;
			}
			return array('list' => $list, 'total' => $total, 'pager' => $pager, 'add_paper' => $add_paper, 'url' => $url, 'paperid' => $paperid, 'returnval' => CommonValStr::$AWARD_LIST_SUCCESS_VAL);
		}
	}
 
    private static function WebUpdateAwardById($id)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$exitid = DBUtility::getAwardsUniIdByName($id, $_GPC['name']);
		if($exitid > 0) {
			//return CommonValStr::$AWARD_NAME_EXIT_VAL;
		}

		$up = array(
			'weid' => $weid,
			'updatetime' => time(),
			'totalnum' => $_GPC['totalnum'],
			'probebility' => $_GPC['probebility'],
			'name' => $_GPC['name'],
			'description' => $_GPC['description'],
			'code' => $_GPC['code'],
			'address' => $_GPC['address'],
			'ruleid' => $_GPC['ruleid'],
			'online' => $_GPC['online'],
		);
		DBUtility::updateById(DBUtility::$table_shakewin_awards, $up, $id);

		return CommonValStr::$AWARD_EDIT_SUCCESS_VAL;
    }
    private static function WebCreateAward()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$exitid = DBUtility::getAwardsIdByName($_GPC['name']);
		if($exitid > 0) {
			//return CommonValStr::$AWARD_NAME_EXIT_VAL;
		}

		$insert = array(
			'weid' => $weid,
			'createtime' => time(),
			'totalnum' => $_GPC['totalnum'],
			'probebility' => $_GPC['probebility'],
			'name' => $_GPC['name'],
			'description' => $_GPC['description'],
			'code' => $_GPC['code'],
			'address' => $_GPC['address'],
			'ruleid' => $_GPC['ruleid'],
			'online' => $_GPC['online'],
		);
		$result = DBUtility::insert(DBUtility::$table_shakewin_awards, $insert);

		return CommonValStr::$AWARD_ADD_SUCCESS_VAL;
    }
    private static function WebDeleteAwardById($id)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		DBUtility::delete(DBUtility::$table_shakewin_awards, array("id" => $id, 'weid' => $weid));

		return CommonValStr::$AWARD_DEL_SUCCESS_VAL;
	}
    private static function WebDeleteSelectedAwards()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			DBUtility::delete(DBUtility::$table_shakewin_awards, array("id" => $id, 'weid' => $weid));
		}

		return CommonValStr::$AWARD_DEL_ALL_SUCCESS_VAL;
	}

    private static function WebGetAwardDetailById($id)
    {
		return DBUtility::getAwardsById($id);
	}

    private static function WebGetAwardPagedList()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$sql = "";
		$replywords = "";
		$params = array();
		if (!empty($_GPC['ruleid'])) {
			$sql .= ' AND a.`ruleid` = :ruleid ';
			$params[':ruleid'] = "{$_GPC['ruleid']}";
		}
		if (!empty($_GPC['name'])) {
			$sql .= ' AND a.`name` LIKE :name ';
			$params[':name'] = "%{$_GPC['name']}%";
		}

		return DBUtility::getAwardsListWithPage($sql, $pindex, $psize, $params);
	}
	
    private static function WebGetAwardTotalNum()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$sql = "";
		if (!empty($_GPC['ruleid'])) {
			$sql .= ' AND a.`ruleid` = :ruleid ';
			$params[':ruleid'] = "{$_GPC['ruleid']}";
		}
		if (!empty($_GPC['name'])) {
			$sql .= ' AND a.`name` LIKE :name ';
			$params[':name'] = "%{$_GPC['name']}%";
		}

		return DBUtility::getTotalAwardsNum($sql);		
	}

}