<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CommonValStr.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/dbutility.php');

class ShakeWinner 
{
    public static function WebProcessWinnerOperation($op)
	{
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		if ($op == 'status') {
			$id = trim($_GPC['id']);
			ShakeWinner::WebUpdateWinnerStatusById($id);

			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;

			$total = ShakeWinner::WebGetWinnerTotalNum();
			$list = ShakeWinner::WebGetWinnerPagedList();

			$pager = pagination($total, $pindex, $psize);
			if ($_GPC['add_paper'] == 1 && !empty($_GPC['paperid'])) {
				$add_paper = 1;
				session_start();
				$url = $_SESSION['last_url'];
				$paperid = intval($_GPC['paperid']);

			} else {
				$add_paper = 0;
			}

			return array('list' => $list, 'total' => $total, 'pager' => $pager, 'add_paper' => $add_paper, 'url' => $url, 'paperid' => $paperid, 'returnval' => CommonValStr::$WINNER_LIST_SUCCESS_VAL);
		} else if ($op == 'detail') {
			$id = trim($_GPC['id']);
			return ShakeWinner::WebGetWinnerDetailById($id);
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;

			$total = ShakeWinner::WebGetWinnerTotalNum();
			$list = ShakeWinner::WebGetWinnerPagedList();

			$pager = pagination($total, $pindex, $psize);
			if ($_GPC['add_paper'] == 1 && !empty($_GPC['paperid'])) {
				$add_paper = 1;
				session_start();
				$url = $_SESSION['last_url'];
				$paperid = intval($_GPC['paperid']);

			} else {
				$add_paper = 0;
			}

			return array('list' => $list, 'total' => $total, 'pager' => $pager, 'add_paper' => $add_paper, 'url' => $url, 'paperid' => $paperid, 'returnval' => CommonValStr::$WINNER_LIST_SUCCESS_VAL);
		}
	}
 
    private static function WebUpdateWinnerStatusById($id)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$up = array(
			'awardstatus' => 2,
		);
		DBUtility::updateById(DBUtility::$table_shakewin_fans, $up, $id);

		return CommonValStr::$WINNER_EDIT_STATUS_SUCCESS_VAL;
    }
    private static function WebUpdateWinnerById($id)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];


		return CommonValStr::$AWARD_EDIT_SUCCESS_VAL;
    }
    private static function WebCreateWinner()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		return CommonValStr::$AWARD_ADD_SUCCESS_VAL;
    }
    private static function WebDeleteWinnerById($id)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

	
		return CommonValStr::$AWARD_DEL_SUCCESS_VAL;
	}
    private static function WebDeleteSelectedWinners()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];


		return CommonValStr::$AWARD_DEL_ALL_SUCCESS_VAL;
	}

    private static function WebGetWinnerDetailById($id)
    {

		$item = DBUtility::getWinFansById($id);
		
		if(!empty($item['openid'])) {
			$faninfo = DBUtility::getWeixUsrInfoByOpenid($item['openid']);
		}

		if(!empty($item['awardid'])) {
			$awardinfo = DBUtility::getAwardsById($item['awardid']);
			record_log('developer', 'txt', 'doWebShakewinner ruleid', $awardinfo['ruleid']);
			if(!empty($awardinfo['ruleid'])) {
				$replywords = DBUtility::getReplyWordById($awardinfo['ruleid']);
			}
		}

		if($item['awardstatus'] == CommonValStr::$WINNER_STATUS_NOTWIN_VAL) {
			$statusstr = CommonValStr::$WINNER_STATUS_NOTWIN_STR;
		} else if($item['awardstatus'] == CommonValStr::$WINNER_STATUS_WIN_NOTSEND_VAL) {
			$statusstr = CommonValStr::$WINNER_STATUS_WIN_NOTSEND_STR;
		} else if($item['awardstatus'] == CommonValStr::$WINNER_STATUS_WIN_SEND_VAL) {
			$statusstr = CommonValStr::$WINNER_STATUS_WIN_SEND_STR;
		} 

		return array('awardinfo' => $awardinfo, 'faninfo' => $faninfo, 'replywords' => $replywords, 'statusstr' => $statusstr, 'returnval' => CommonValStr::$WINNER_DETAIL_SUCCESS_VAL);
	}

    private static function WebGetWinnerPagedList()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		if ($_GPC['ruleid'] > 0) {
			$sql = " AND ruleid=" . $_GPC['ruleid'];
		}
		$params = array();

		$listawards = DBUtility::getWinFansWithPage($sql, $pindex, $psize, $params);
		$list = array();
		if(!empty($listawards)) {
			foreach ($listawards as $item) {
				$subdata['id'] = $item['id'];
				$subdata['awardstatus'] = $item['awardstatus'];
				$subdata['createtime'] = $item['createtime'];

				if(!empty($item['openid'])) {
					$faninfo = DBUtility::getWeixUsrInfoByOpenid($item['openid']);
					if(!empty($faninfo)) {
						$subdata['nickname'] = $faninfo['nickname'];
						$subdata['headimg'] = $faninfo['headimg'];
					}
				}
				if(!empty($item['awardid'])) {
					$awardinfo = DBUtility::getAwardsById($item['awardid']);
					if(!empty($awardinfo)) {
						$subdata['name'] = $awardinfo['name'];
						$subdata['ruleid'] = $awardinfo['ruleid'];
						$subdata['code'] = $awardinfo['code'];
					}

				}
				if ($_GPC['ruleid'] > 0 && $item['ruleid'] != $_GPC['ruleid']) {
					continue;
				}
				if (!empty($_GPC['nickname']) && stristr($subdata['nickname'], $_GPC['nickname']) === FALSE && stristr($subdata['name'], $_GPC['nickname']) === FALSE) {
					continue;
				}
				if(!empty($item['ruleid'])) {
					$replywords = DBUtility::getReplyWordById($item['ruleid']);
					if(!empty($replywords)) {
						$subdata['replywords'] = $replywords;
					}
				}
				$list[] = $subdata;
			}
		}

		return $list;
	}
	
    public static function MobileMyWinPagedList()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		if ($_GPC['ruleid'] > 0) {
			$sql = " AND ruleid=" . $_GPC['ruleid'] . " AND openid='" . $openid . "'";
		}
		$params = array();

		$listawards = DBUtility::getWinFansWithPage($sql, $pindex, $psize, $params);
		$list = array();
		$nickname = null;
		if(!empty($listawards)) {
			foreach ($listawards as $item) {
				$subdata['id'] = $item['id'];
				$subdata['awardstatus'] = $item['awardstatus'];
				$subdata['createtime'] = $item['createtime'];

				if(!empty($item['openid'])) {
					$faninfo = DBUtility::getWeixUsrInfoByOpenid($item['openid']);
					if(!empty($faninfo)) {
						$nickname = $faninfo['nickname'];
						$subdata['nickname'] = $faninfo['nickname'];
						$subdata['headimg'] = $faninfo['headimg'];
					}
				}
				if(!empty($item['awardid'])) {
					$awardinfo = DBUtility::getAwardsById($item['awardid']);
					if(!empty($awardinfo)) {
						$subdata['name'] = $awardinfo['name'];
						$subdata['ruleid'] = $awardinfo['ruleid'];
						$subdata['code'] = $awardinfo['code'];
					}

				}
				if ($_GPC['ruleid'] > 0 && $item['ruleid'] != $_GPC['ruleid']) {
					continue;
				}
				if (!empty($_GPC['nickname']) && stristr($subdata['nickname'], $_GPC['nickname']) === FALSE && stristr($subdata['name'], $_GPC['nickname']) === FALSE) {
					continue;
				}
				$list[] = $subdata;
			}
		}

		$currule = DBUtility::getRuleById($_GPC['ruleid']);

		return array('list' => $list, 'nickname' => $nickname, 'exchangestarttime' => $currule['exchangestarttime'], 'exchangeendtime' => $currule['exchangeendtime']);
	}

    private static function WebGetWinnerTotalNum()
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		if ($_GPC['ruleid'] > 0) {
			$sql = " AND ruleid=" . $_GPC['ruleid'];
		}
		return DBUtility::getTotalWinFansNum($sql);
	}

}