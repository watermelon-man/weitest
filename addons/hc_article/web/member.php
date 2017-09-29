<?php
	if ($op == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$members = pdo_fetchall("SELECT * FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$uniacid}'");
		$pager = pagination($total, $pindex, $psize);
		$viplevels = pdo_fetchall("SELECT * FROM " . tablename('hc_article_viplevel') . " WHERE uniacid = '{$_W['uniacid']}'");
		$viplevel = array();
		foreach($viplevels as $v){
			$viplevel[$v['id']] = $v['title'];
		}
		include $this->template('web/member');
	} elseif ($op == 'post') {
		$id = intval($_GPC['id']);
		$member = pdo_fetch("SELECT * FROM " . tablename('hc_article_member') . " WHERE id = '$id'");
		$uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$uniacid}' and openid = '".$member['openid']."'");
		if($uid){
			$credit2 = pdo_fetchcolumn("SELECT credit2 FROM " . tablename('mc_members') . " WHERE uid = '{$uid}'");
		}
		if(checksubmit('submit')) {
			$headurl = !empty($_GPC['headurl']) ? trim($_GPC['headurl']) : message('请上传头像');
			$http = mb_substr($headurl , 0 , 5);
			if($http != 'http:'){
				$headurl = $_W['attachurl'].$headurl;
			}
			$nickname = !empty($_GPC['nickname']) ? trim($_GPC['nickname']) : message('请输入昵称');
			$mobile = hehe($_GPC['mobile']);
			$mem = array(
				'headurl'=>$headurl,
				'nickname'=>$nickname,
				'mobile'=>$mobile,
				'viplevel'=>$_GPC['viplevel'],
				'articlenum'=>intval($_GPC['articlenum']),
				'remark'=>trim($_GPC['remark'])
			);
			$credit2 = intval($_GPC['credit2']);
			if($credit2){
				if($uid){
					pdo_update('mc_members', array('credit2'=>$credit2), array('uid'=>$uid));
				}
			}
			pdo_update('hc_article_member', $mem, array('id'=>$id));
			message('提交成功', $this->createWebUrl('member'), 'succees');
		}
		$viplevels = pdo_fetchall("SELECT * FROM " . tablename('hc_article_viplevel') . " WHERE uniacid = '{$uniacid}'");
		include $this->template('web/member');
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		pdo_delete('hc_article_member', array('id' => $id));
		message('删除成功！', $this->createWebUrl('member', array('op' => 'display')), 'success');
	}
?>