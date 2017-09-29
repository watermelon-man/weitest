<?php
	$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($follow) || $follow['follow']==0){
		message('你想知道怎么加入么？', $rule['gzurl']);
	}
	$member = pdo_fetch('SELECT * FROM '.tablename('hc_article_member')." WHERE ispay = 1 and uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($member)){
		message('请先注册！', $this->createMobileUrl('register'), 'error');
	}
	
	if($op=='display'){
		
	}
	
	if($op=='address'){
		if($_GPC['opp']=='add_address'){
			include $this->template('add_address');
			exit;
		}
		include $this->template('address');
		exit;
	}
	
	if($op=='checkreally'){
		include $this->template('checkreally');
		exit;
	}
	
	if($op=='bank'){
		if($_GPC['opp']=='add_bank'){
			include $this->template('add_bank');
			exit;
		}
		include $this->template('bank');
		exit;
	}
	
	if($op=='submitnickname'){
		$nickname = trim($_GPC['nickname']);
		if(empty($nickname)){
			echo 0;
			exit;
		} else {
			pdo_update('hc_article_member', array('nickname'=>$nickname), array('id'=>$member['id']));
			echo 1;
			exit;
		}
	}
	
	include $this->template('memberinfo');
?>