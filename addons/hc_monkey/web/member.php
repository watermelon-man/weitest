<?php
	if ($op == 'display') {
		$couponid = intval($_GPC['couponid']);
		if($couponid){
			$takemids = pdo_fetchall("select distinct mid from ".tablename('hc_monkey_takecoupon')." where uniacid = ".$uniacid." and couponid = ".$couponid);
			$takemid = '';
			foreach($takemids as $t){
				$takemid = $takemid.$t['mid'].',';
			}
			$takemid = '('.trim($takemid, ',').')';
		}
		if($_GPC['opp']=='sort'){
			$sort = array(
				'nickname'=>trim($_GPC['nickname']),
				'mobile'=>trim($_GPC['mobile'])
			);
			$members = pdo_fetchall("SELECT * FROM " . tablename('hc_monkey_member') . " WHERE nickname like '%".$sort['nickname']."%' and mobile like '%".$sort['mobile']."%' and uniacid = '{$_W['uniacid']}' ORDER BY createtime DESC");
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			if(empty($takemids)){
				$members = pdo_fetchall("SELECT * FROM " . tablename('hc_monkey_member') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
				$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_monkey_member') . " WHERE uniacid = '{$uniacid}'");
			} else {
				$members = pdo_fetchall("SELECT * FROM " . tablename('hc_monkey_member') . " WHERE id in ".$takemid." and uniacid = '{$_W['uniacid']}' ORDER BY createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
				$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_monkey_member') . " WHERE id in ".$takemid." and uniacid = '{$uniacid}'");
			}
			$pager = pagination($total, $pindex, $psize);
		}
	} elseif ($op == 'post') {
		$id = intval($_GPC['id']);
		$member = pdo_fetch("SELECT * FROM " . tablename('hc_monkey_member') . " WHERE id = '$id'");
		if(checksubmit('submit')) {
			$headimgurl = !empty($_GPC['headimgurl']) ? trim($_GPC['headimgurl']) : message('请上传头像');
			$http = mb_substr($headimgurl , 0 , 5);
			if($http != 'http:'){
				$headimgurl = $_W['attachurl'].$headimgurl;
			}
			$nickname = !empty($_GPC['nickname']) ? trim($_GPC['nickname']) : message('请输入昵称');
			//$mobile = hehe($_GPC['mobile']);
			$mobile = $_GPC['mobile'];
			$mem = array(
				'headimgurl'=>$headimgurl,
				'nickname'=>$nickname,
				'mobile'=>$mobile,
				'word_num'=>intval($_GPC['word_num']),
				'score'=>intval($_GPC['score']),
				'remark'=>trim($_GPC['remark'])
			);
			pdo_update('hc_monkey_member', $mem, array('id'=>$id));
			message('提交成功', $this->createWebUrl('member'), 'succees');
		}
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		pdo_delete('hc_monkey_member', array('id' => $id));
		message('删除成功！', $this->createWebUrl('member', array('op' => 'display')), 'success');
	}
	include $this->template('web/member');
?>