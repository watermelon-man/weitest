<?php
	$this->CheckCookie();
	$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($follow) || $follow['follow']==0){
		message('你想知道怎么加入么？', $rule['gzurl']);
	}
	
	$member = pdo_fetch('SELECT * FROM '.tablename('hc_article_member')." WHERE ispay = 1 and uniacid = ".$uniacid." and openid = '".$openid."'");
	if(!empty($member)){
		message('请勿重复注册！');
	}
	
	if($op=='display'){
		$viplevels = pdo_fetchall('SELECT * FROM '.tablename('hc_article_viplevel')." WHERE uniacid = ".$uniacid." order by usecredit asc");
        include $this->template('register');
		exit;
	}
	
	if($op=='pay'){
		pdo_delete('hc_article_member', array('uniacid'=>$uniacid, 'openid'=>$openid, 'ispay'=>0));
		$vipid = !empty($_GPC['vipid']) ? intval($_GPC['vipid']) : message('请选择会员等级');
		$mobile = !empty($_GPC['mobile']) ? trim($_GPC['mobile']) : message('请输入手机号码');
		if($uid){
			$fans = mc_fetch($uid, array('avatar', 'nickname'));
		}
		$sid = empty($_COOKIE[$shareid]) ? 0 : intval($_COOKIE[$shareid]);
		$viplevel = pdo_fetch('SELECT * FROM '.tablename('hc_article_viplevel')." WHERE id = ".$vipid);
		$mem = array(
			'uniacid'=>$uniacid,
			'openid'=>$openid,
			'shareid'=>$sid,
			'nickname'=>$fans['nickname'],
			'mobile'=>$mobile,
			'headurl'=>$fans['avatar'],
			'viplevel'=>$vipid,
			'articlenum'=>$viplevel['articlenum'],
			'remark'=>trim($_GPC['remark']),
			'createtime'=>time()
		);
		if(empty($viplevel['needmoney'])){
			$mem['ispay'] = 1;
			pdo_insert('hc_article_member', $mem);
			setcookie("$shareid", 0);
			message('恭喜你，注册成功！', $this->createMobileUrl('register'));
		} else {
			pdo_insert('hc_article_member', $mem);
			$mid = pdo_insertId();
			$params['tid'] = $mid;
			$params['user'] = $openid;
			$params['fee'] = $viplevel['needmoney'];
			$params['title'] = $_W['account']['name'];
			$params['ordersn'] = date('md') . random(4, 1);
			include $this->template('pay');
		}
	}
?>