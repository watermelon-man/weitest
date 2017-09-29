<?php
	$coupons = pdo_fetchall("select * from ".tablename('hc_monkey_coupon')." where uniacid = ".$uniacid);
	$coupon = array();
	foreach($coupons as $c){
		$coupon[$c['id']] = $c['title'];
	}
	if ($op == 'display') {
		if($_GPC['opp']=='sort'){
			$sort = array(
				'nickname'=>trim($_GPC['nickname']),
				'mobile'=>trim($_GPC['mobile'])
			);
			$takecoupons = pdo_fetchall("SELECT * FROM " . tablename('hc_monkey_takecoupon') . " WHERE nickname like '%".$sort['nickname']."%' and mobile like '%".$sort['mobile']."%' and uniacid = '{$_W['uniacid']}' ORDER BY createtime DESC");
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$takecoupons = pdo_fetchall("SELECT * FROM " . tablename('hc_monkey_takecoupon') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_monkey_takecoupon') . " WHERE uniacid = '{$uniacid}'");
			$pager = pagination($total, $pindex, $psize);
		}
	} elseif ($op == 'post') {
		$id = intval($_GPC['id']);
		$takecoupon = pdo_fetch("SELECT * FROM " . tablename('hc_monkey_takecoupon') . " WHERE id = '$id'");
		if(checksubmit('submit')) {
			$tcoupon = array(
				'status'=>intval($_GPC['status'])
			);
			pdo_update('hc_monkey_takecoupon', $tcoupon, array('id'=>$id));
			message('提交成功', $this->createWebUrl('cost'), 'succees');
		}
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		pdo_delete('hc_monkey_takecoupon', array('id' => $id));
		message('删除成功！', $this->createWebUrl('cost', array('op' => 'display')), 'success');
	}
	include $this->template('web/cost');
?>