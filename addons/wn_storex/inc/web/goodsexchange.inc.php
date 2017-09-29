<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;
load()->model('mc');
mload()->model('activity');

$ops = array('post', 'delete', 'display', 'deliver', 'receiver', 'record', 'record-del', 'toggle');
$op = in_array(trim($_GPC['op']), $ops) ? trim($_GPC['op']) : 'display';

$creditnames = array(
	'credit1' => '积分',
	'credit2' => '余额'
);

if ($op == 'post') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$item = pdo_get('storex_activity_exchange', array('id' => $id, 'uniacid' => $_W['uniacid']));
		if (empty($item)) {
			message('未找到指定兑换礼品或已删除', $this->createWeburl('goodsexchange', array('op' => 'display')), 'error');
		} else {
			$item['extra'] = iunserializer($item['extra']);
		}
	} else {
		$item['starttime'] = TIMESTAMP;
		$item['endtime'] = TIMESTAMP + 6 * 86400;
	}
	if (checksubmit('submit')) {
		$data['title'] = !empty($_GPC['title']) ? trim($_GPC['title']) : message('请输入兑换名称！');
		$data['credittype'] = !empty($_GPC['credittype']) ? trim($_GPC['credittype']) : message('请选择积分类型！');
		$data['credit'] = intval($_GPC['credit']);
		if (empty($_GPC['extra']['title'])) {
			message('请输入兑换礼品的名称', '', 'error');
		}
		$data['extra'] = iserializer($_GPC['extra']);
		$data['thumb'] = trim($_GPC['thumb']);
		$data['status'] = trim($_GPC['status']);
		$data['pretotal'] = intval($_GPC['pretotal']) ? intval($_GPC['pretotal']) : message('请输入每人最大兑换次数');
		$data['total'] = intval($_GPC['total']) ? intval($_GPC['total']) : message('请输入兑换总数');
		$data['type'] = 3;
		$data['description'] = !empty($_GPC['description']) ? trim($_GPC['description']) : message('请输入兑换说明！');
		$starttime = strtotime($_GPC['datelimit']['start']);
		$endtime = strtotime($_GPC['datelimit']['end']);
		if ($endtime == $starttime) {
			$endtime = $endtime + 86399;
		}
		$data['starttime'] = $starttime;
		$data['endtime'] = $endtime;
		if (empty($id)) {
			$data['uniacid'] = $_W['uniacid'];
			pdo_insert('storex_activity_exchange', $data);
			message('添加真实物品兑换成功', $this->createWeburl('goodsexchange', array('op' => 'display')), 'success');
		} else {
			pdo_update('storex_activity_exchange', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			message('更新真实物品兑换成功', $this->createWeburl('goodsexchange', array('op' => 'display')), 'success');
		}
	}
}

if ($op == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$where = ' WHERE type = 3 AND uniacid = :uniacid ';
	$params = array(':uniacid' => $_W['uniacid']);
	$title = trim($_GPC['keyword']);
	if (!empty($title)) {
		$where .= " AND title LIKE '%{$title}%'";
	}
	$list = pdo_fetchall("SELECT * FROM " . tablename('storex_activity_exchange') . " $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_activity_exchange') . $where , $params);
	$pager = pagination($total, $pindex, $psize);
	foreach ($list as &$row) {
		$extra = iunserializer($row['extra']);
		$row['extra'] = $extra;
		$row['thumb'] = tomedia($row['thumb']);
	}
	unset($row);
}

if ($op == 'delete') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$item = pdo_get('storex_activity_exchange', array('id' => $id, 'uniacid' => $_W['uniacid']), array('id'));
	}
	if (empty($item)) {
		message('删除失败,指定兑换不存在或已删除.');
	}
	pdo_delete('storex_activity_exchange', array('id' => $id, 'uniacid' => $_W['uniacid']));
	message('删除成功', referer(), 'success');
}

//发货记录
if ($op == 'deliver') {
	$exchanges = pdo_fetchall("SELECT id, title FROM " . tablename('storex_activity_exchange') . " WHERE uniacid = :uniacid ORDER BY id DESC", array(':uniacid' => $_W['uniacid']));
	$starttime = empty($_GPC['time']['start']) ? strtotime('-6 month') : strtotime($_GPC['time']['start']);
	$endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;
	$where = " WHERE a.uniacid = :uniacid AND a.createtime >= :starttime AND a.createtime <= :endtime";
	$params = array(
			':uniacid' => $_W['uniacid'],
			':starttime' => $starttime,
			':endtime' => $endtime,
	);
	$uid = addslashes($_GPC['uid']);
	if (!empty($uid)) {
		$where .= ' AND ((a.name = :uid) or (a.mobile = :uid))';
		$params[':uid'] = $uid;
	}
	$exid = intval($_GPC['exid']);
	if (!empty($exid)) {
		$where .= " AND b.id = {$exid}";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = pdo_fetchall("SELECT a.*, b.title, b.extra, b.thumb FROM " . tablename('storex_activity_exchange_trades_shipping') . " AS a LEFT JOIN " . tablename('storex_activity_exchange') . " AS b ON a.exid = b.id " . $where . " ORDER BY tid DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_activity_exchange_trades_shipping') . " AS a LEFT JOIN " . tablename('storex_activity_exchange') . " AS b ON a.exid = b.id " . $where, $params);
	if (checksubmit('export', true)) {
		$header = array(
			'title' => '标题',
			'extra' => '兑换物品',
			'name' => '收件人',
			'createtime' => '兑换时间',
			'mobile' => '收件人电话',
			'zipcode' => '收件人邮编',
			'address' => '收件地址',
			'status' => '状态'
		);
		$html = "\xEF\xBB\xBF";
		foreach ($header as $li) {
			$html .= $li . "\t ,";
		}
		$html .= "\n";
		foreach ($list as $deliver) {
			foreach ($header as $key => $title) {
				if ($key == 'createtime') {
					$html .= date('Y-m-d', $deliver[$key]) . "\t ,";
				} elseif ($key == 'extra') {
					$extra = iunserializer($deliver[$key]);
					$html .= $extra['title'] . "\t ,";
				} elseif ($key == 'status') {
					switch ($deliver['status']) {
						case '0' :
							$status = '待发货';
							break;
						case '1' :
							$status = '待收货';
							break;
						case '2' :
							$status = '已收货';
							break;
						case '-1' :
							$status = '已关闭';
							break;
					}
					$html .= $status . "\t ,";
				} else {
					$html .= $deliver[$key] . "\t ,";
				}
			}
			$html .= "\n";
		}
		$html .= "\n";
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=会员数据.csv");
		echo $html;
		exit();
	}
	if (!empty($list)) {
		$uids = array();
		foreach ($list as $row) {
			$uids[] = $row['uid'];
		}
		$members = mc_fetch($uids, array('uid', 'nickname'));
		foreach ($list as &$row) {
			$row['extra'] = iunserializer($row['extra']);
			$row['nickname'] = $members[$row['uid']]['nickname'];
			$row['thumb'] = tomedia($row['thumb']);
		}
		unset($row);
	}
	$pager = pagination($total, $pindex, $psize);
}

if ($op == 'receiver') {
	$id = intval($_GPC['id']);
	$shipping = pdo_get('storex_activity_exchange_trades_shipping', array('uniacid' => $_W['uniacid'], 'tid' => $id));
	if (checksubmit('submit')) {
		$data = array(
			'name'=>$_GPC['realname'],
			'mobile'=>$_GPC['mobile'],
			'province'=>$_GPC['reside']['province'],
			'city'=>$_GPC['reside']['city'],
			'district'=>$_GPC['reside']['district'],
			'address'=>$_GPC['address'],
			'zipcode'=>$_GPC['zipcode'],
			'status'=>intval($_GPC['status'])
		);
		pdo_update('storex_activity_exchange_trades_shipping', $data, array('tid' => $id));
		message('更新发货人信息成功', referer(), 'success');
	}
}
if ($op == 'record') {
	$exchanges_info = pdo_getall('storex_activity_exchange', array('uniacid' => $_W['uniacid'], 'type' => '3'), array('id', 'title'));
	$starttime = empty($_GPC['time']['start']) ? strtotime('-1 month') : strtotime($_GPC['time']['start']);
	$endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']) + 86399;

	$where = " WHERE a.uniacid = :uniacid AND a.type = 3 AND a.createtime >= :starttime AND a.createtime < :endtime ";
	$params = array(
		':uniacid' => $_W['uniacid'],
		':starttime' => $starttime,
		':endtime' => $endtime,
	);
	$uid = intval($_GPC['uid']);
	if (!empty($uid)) {
		$where .= ' AND a.uid = :uid';
		$params[':uid'] = $uid;
	}
	$exid = intval($_GPC['exid']);
	if (!empty($exid)) {
		$where .= " AND b.id = {$exid}";
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = pdo_fetchall("SELECT a.*, b.title, b.extra, b.thumb FROM " . tablename('storex_activity_exchange_trades') . " AS a LEFT JOIN " . tablename('storex_activity_exchange') ." AS b ON a.exid = b.id " . " $where ORDER BY tid DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('storex_activity_exchange_trades') . " AS a LEFT JOIN " . tablename('storex_activity_exchange') . " AS b ON a.exid = b.id " . $where , $params);
	$pager = pagination($total, $pindex, $psize);
	if (!empty($list)) {
		$uids = array();
		foreach ($list as $row) {
			$uids[] = $row['uid'];
		}
		load()->model('mc');
		$members = mc_fetch($uids, array('uid', 'nickname'));
		foreach ($list as &$row) {
			$row['extra'] = iunserializer($row['extra']);
			$row['nickname'] = $members[$row['uid']]['nickname'];
			$row['thumb'] = tomedia($row['thumb']);
		}
		unset($row);
	}
}

//删除兑换记录
if ($op == 'record-del') {
	$tid = intval($_GPC['id']);
	if (empty($tid)) {
		message('没有指定的兑换记录', referer(), 'error');
	}
	pdo_delete('storex_activity_exchange_trades_shipping', array('uniacid' => $_W['uniacid'], 'tid' => $tid));
	pdo_delete('storex_activity_exchange_trades', array('uniacid' => $_W['uniacid'], 'tid' => $tid));
	message('删除兑换记录成功', referer(), 'success');
}

if ($op == 'toggle') {
	$id = intval($_GPC['id']);
	$status = intval($_GPC['status']) ? 0 : 1;
	pdo_update('storex_activity_exchange', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
	$info = intval($_GPC['status']) ? '下架成功' : '上架成功';
	message(error(0, $info), referer(), 'ajax');
}

include $this->template('goodsexchange');