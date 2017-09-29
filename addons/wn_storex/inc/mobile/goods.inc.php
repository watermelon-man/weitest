<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
$ops = array('display', 'exchange', 'mine', 'detail', 'confirm');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'error';

load()->model('mc');
mload()->model('activity');
mload()->model('card');

$uid = mc_openid2uid($_W['openid']);

$profile = mc_fetch($_W['member']['uid']);
//真实物品列表
if ($op == 'display') {
	$goods_lists = pdo_fetchall('SELECT id, title, extra, thumb, type, credittype, endtime, description, credit FROM ' . tablename('storex_activity_exchange') . ' WHERE uniacid = :uniacid AND type = :type AND endtime > :endtime AND status = 1 ORDER BY endtime ASC ', array(':uniacid' => $_W['uniacid'], ':type' => 3, ':endtime' => TIMESTAMP));
	foreach ($goods_lists as &$list) {
		$list['thumb'] = tomedia($list['thumb']);
		$list['extra'] = iunserializer($list['extra']);
		if (!is_array($list['extra'])) {
			$list['extra'] = array();
		}
	}
	unset($list);
	message(error(0, $goods_lists), '', 'ajax');
}
//兑换过程
if ($op == 'exchange') {
	$id = intval($_GPC['id']); 
	$shipping_data = array(
		'name' => trim($_GPC['username']),
		'mobile' => trim($_GPC['mobile']),
		'zipcode' => trim($_GPC['zipcode']),
		'province' => trim($_GPC['province']),
		'city' => trim($_GPC['city']),
		'district' => trim($_GPC['district']),
		'address' => trim($_GPC['address']),
	);
	foreach ($shipping_data as $val) {
		if (empty($val)) {
			message(error(-1, '请填写收货人信息'), '', 'ajax');
		}
	}
	$creditnames = array(
		'credit1' => '积分',
		'credit2' => '余额'
	);
	$goods = activity_get_exchange_info($id, $_W['uniacid']);
	if (empty($goods)) {
		message(error(-1, '没有指定的礼品兑换'), '', 'ajax');
	}
	$credit = mc_credit_fetch($uid, array($goods['credittype']));
	if ($credit[$goods['credittype']] < $goods['credit']) {
		message(error(-1, "{$creditnames[$goods['credittype']]}不足"), '', 'ajax');
	}
	$ret = activity_user_get_goods($uid, $id);
	if (is_error($ret)) {
		message($ret, '', 'ajax');
	}
	pdo_update('storex_activity_exchange_trades_shipping', $shipping_data, array('tid' => $ret));
	mc_credit_update($uid, $goods['credittype'], -1 * $goods['credit'], array($uid, '礼品兑换:' . $goods['title'] . ' 消耗 ' . $creditnames[$goods['credittype']] . ':' . $goods['credit']));
	//微信通知
	if ($goods['credittype'] == 'credit1') {
		mc_notice_credit1($_W['openid'], $uid, -1 * $goods['credit'], '兑换礼品消耗积分');
	} else {
		mc_notice_credit2($_W['openid'], $uid, -1 * $goods['credit'], 0, '线上消费，兑换礼品');
	}
	message(error(0, '兑换成功'), '', 'ajax');
}
//收获地址
if ($op == 'detail') {
	$tid = intval($_GPC['tid']);//收货人信息id
	$id = intval($_GPC['id']);
	$goods_info = pdo_get('storex_activity_exchange', array('id' => $id));
	$goods_info['reside'] = $goods_info['total'] - $goods_info['num'];
	$goods_info['exp_date'] = '有效期:' . date('Y.m.d', $goods_info['starttime']) . '-' . date('Y.m.d', $goods_info['endtime']);
	$goods_info['description'] = htmlspecialchars_decode($goods_info['description']);
	$goods_info['thumb'] = tomedia($goods_info['thumb']);
	$goods_info['extra'] = iunserializer($goods_info['extra']);
	if (empty($tid)) {
		$address_data = pdo_get('mc_member_address', array('uniacid' => $_W['uniacid'], 'uid' => $uid));
	} else {
		$address_data = pdo_get('storex_activity_exchange_trades_shipping', array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'tid' => $tid));
		$address_data['username'] = $address_data['name'];
	}
	$exchange_info['goods'] = $goods_info;
	$exchange_info['address'] = $address_data;
	message(error(0, $exchange_info), '', 'ajax');
}
//我的实物
if ($op == 'mine') {
	$lists = pdo_fetchall("SELECT a.*, b.id AS gid, b.title, b.extra, b.thumb, b.type, b.credittype, b.endtime, b.description, b.credit FROM " . tablename('storex_activity_exchange_trades_shipping') . " AS a LEFT JOIN " . tablename('storex_activity_exchange') . " AS b ON a.exid = b.id WHERE a.uid = :uid ORDER BY a.status", array(':uid' => $uid));
	foreach ($lists as &$list) {
		$list['thumb'] = tomedia($list['thumb']);
		$list['extra'] = iunserializer($list['extra']);
		if(!is_array($list['extra'])) {
			$list['extra'] = array();
		}
	}
	unset($list);
	message(error(0, $lists), '', 'ajax');
}
//确认收货
if ($op == 'confirm') {
	if ($_W['isajax'] && $_W['ispost']) {
		$tid = intval($_GPC['tid']);
		$shipping_info = pdo_get('storex_activity_exchange_trades_shipping', array('tid' => $tid, 'uid' => $uid), array('tid', 'status'));
		if (empty($shipping_info)) {
			message(error(-1,'订单信息不存在'), '', 'ajax');
		}
		if ($shipping_info['status'] == 1) {
			pdo_update('storex_activity_exchange_trades_shipping', array('status' => 2), array('uid' => $uid, 'tid' => $tid));
			message(error(0, '确认收货成功'), '', 'ajax');
		} else {
			if ($shipping_info['status'] == 0) {
				$message = '该商品待发货';
			} elseif ($shipping_info['status'] == 2) {
				$message = '订单已完成';
			} elseif ($shipping_info['status'] == -1) {
				$message = '订单已关闭';
			}
			message(error(-1, $message), '', 'ajax');
		}
	}
}