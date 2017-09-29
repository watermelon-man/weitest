<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('recharge_add', 'recharge_pay', 'card_recharge', 'recharge_record');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'error';

mload()->model('card');
check_params();
$uid = mc_openid2uid($_W['openid']);

if ($op == 'card_recharge') {
	$card_member_info = pdo_get('storex_mc_card_members', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']), array('id'));
	$card_setting = card_setting_info();
	$recharge_lists = array();
	$recharge_lists['type'] = $type = $_GPC['type'];
	if (!empty($card_member_info)) {
		if (!empty($type)) {
			if ($type == 'nums') {
				$nums_recharge = $card_setting['params']['cardNums'] ? $card_setting['params']['cardNums'] : array();
				if ($nums_recharge['params']['nums_status'] == 1) {
					$recharge_lists = $nums_recharge['params']['nums'];
				}
			} elseif ($type == 'times') {
				$times_recharge = $card_setting['params']['cardTimes'] ? $card_setting['params']['cardTimes'] : array();
				if ($times_recharge['params']['times_status'] == 1) {
					$recharge_lists = $times_recharge['params']['times'];
				}
			}
		} else {
			$card_recharge = $card_setting['params']['cardRecharge'] ? $card_setting['params']['cardRecharge'] : array();
			if ($card_recharge['params']['recharge_type'] == 1) {
				$recharge_lists = $card_recharge['params']['recharges'];
			}
		}
	}
	message(error(0, $recharge_lists), '', 'ajax');
}

if ($op == 'recharge_add') {
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : 'credit';
	$fee = floatval($_GPC['fee']);
	if (empty($fee) || $fee <= 0) {
		message(error(-1, '请输入正确金额'), '', 'ajax');
	}
	$card_setting = card_setting_info();
	$recharge_record = array(
		'uid' => $uid,
		'openid' => $_W['openid'],
		'uniacid' => $_W['uniacid'],
		'tid' => date('YmdHi') . random(8, 1),
		'fee' => $fee,
		'createtime' => TIMESTAMP,
		'status' => 0,
	);
	if ($type == 'credit') {
		$back= floatval($_GPC['back']);
		$backtype = trim($_GPC['backtype']);
		if ($backtype == 0 || $backtype == 1) {
			$card_recharge = $card_setting['params']['cardRecharge'];
			$recharge_lists = array();
			if ($card_recharge['params']['recharge_type'] == 1) {
				$recharge_lists = $card_recharge['params']['recharges'];
				foreach ($recharge_lists as $key => $value) {
					if ($value['backtype'] == $backtype) {
						if ($value['condition'] == $fee) {
							$back = $value['back'];
							break;
						}
					}
				}
			}
		}
		$recharge_record['type'] = $type;
		$recharge_record['tag'] = $back;
		$recharge_record['backtype'] = $backtype;
	} elseif ($type == 'card_nums') {
		$card_nums = $card_setting['params']['cardNums'];
		$recharge_lists = array();
		$add_num = 0;
		if ($card_nums['params']['nums_status'] == 1) {
			$recharge_lists = $card_nums['params']['nums'];
			foreach ($recharge_lists as $key => $value) {
				if ($value['recharge'] == $fee) {
					$add_num = $value['num'];
					break;
				}
			}
		}
		$recharge_record['type'] = $type;
		$recharge_record['tag'] = $add_num;
		$recharge_record['backtype'] = 2;
	} elseif ($type == 'card_times') {
		$card_times = $card_setting['params']['cardTimes'];
		$recharge_lists = array();
		$add_times = 0;
		if ($card_times['params']['times_status'] == 1) {
			$recharge_lists = $card_times['params']['times'];
			foreach ($recharge_lists as $key => $value) {
				if ($value['recharge'] == $fee) {
					$add_times = $value['time'];
					break;
				}
			}
		}
		$recharge_record['type'] = $type;
		$recharge_record['tag'] = $add_times;
		$recharge_record['backtype'] = 2;
	}
	if ($type == 'card_nums' || $type == 'card_times') {
		if (empty($recharge_record['tag'])) {
			message(error(-1, '充值金额错误'), '', 'ajax');
		}
	}
	if (!pdo_insert('mc_credits_recharge', $recharge_record)) {
		message(error(-1, '创建充值订单失败'), '', 'ajax');
	}
	$recharge_id = pdo_insertid();
	message(error(0, $recharge_id), '', 'ajax');
}

if ($op == 'recharge_pay') {
	$charge_record = pdo_get('mc_credits_recharge', array('id' => intval($_GPC['id'])));
	if ($charge_record['type'] == 'credit') {
		$title = '万能小店余额充值';
	} elseif ($charge_record['type'] == 'card_nums') {
		$title = '万能小店会员卡次数充值';
	} elseif ($charge_record['type'] == 'card_times') {
		$title = '万能小店会员卡时间充值';
	}
	$params = array(
		'tid' => $charge_record['tid'],
		'title' => $title,
		'fee' => $charge_record['fee'],
		'user' => $uid
	);
	$pay_info = $this->pay($params, $mine);
	message(error(0, $pay_info), '', 'ajax');
}

if($op == 'recharge_record') {
	$period = $_GPC['period'];
	$starttime = ($period == '1') ? date('Ym01') : date('Ym01', strtotime(1 * $period . "month"));
	$endtime = date('Ymd', strtotime("$starttime + 1 month - 1 day"));
	$setting = card_setting_info();
	$recharge_info = array();
	$recharge_info['card'] = pdo_get('storex_mc_card_members', array('uniacid' => $_W['uniacid'], 'uid' => $uid));
	$type = trim($_GPC['type']);
	if ($type == 'nums') {
		$recharge_info['text'] = $setting['nums_text'];
	} elseif ($type == 'times') {
		$recharge_info['text'] = $setting['times_text'];
	}
	$where = ' WHERE uniacid = :uniacid AND uid = :uid AND type = :type AND addtime >= :starttime AND addtime <= :endtime';
	$params = array(
		':uniacid' => $_W['uniacid'],
		':uid' => $uid,
		':type' => $type,
		':starttime' => strtotime($starttime),
		':endtime' => strtotime($endtime)
	);
	$recharge_info['record'] = pdo_fetchall('SELECT * FROM ' . tablename('storex_mc_card_record') . $where . ' ORDER BY id DESC ', $params);
	message(error(0, $recharge_info), '', 'ajax');
}


