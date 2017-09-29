<?php
	$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($follow) || $follow['follow']==0){
		message('你想知道怎么加入么？', $rule['gzurl']);	
	}
	$member = pdo_fetch('SELECT * FROM '.tablename('hc_article_member')." WHERE ispay = 1 and uniacid = ".$uniacid." and openid = '".$openid."'");
	$id = $member['id'];
	if(empty($member)){
		//message('请先注册！', $this->createMobileUrl('register'), 'error');
		$url = $this->createMobileUrl('register');
		header("location:$url");
	}

	if($op=='display'){
		if($uid){
			$fans = mc_fetch($uid, array('credit2'));
		}
		$viplevel = pdo_fetch("select * from ".tablename('hc_article_viplevel')." where id = ".$member['viplevel']);
		//评论金额
		$commission1 = pdo_fetchcolumn("select sum(credit2) from ".tablename('hc_article_commission')." where flag = 0 and uniacid = ".$uniacid." and mid = ".$member['id']);
		$commission1 = empty($commission1) ? '0.00' : $commission1;
		//下属佣金
		$commission2 = pdo_fetchcolumn("select sum(credit2) from ".tablename('hc_article_commission')." where flag = 1 and uniacid = ".$uniacid." and mid = ".$member['id']);
		$commission2 = empty($commission2) ? '0.00' : $commission2;
	}
	
	if($op=='question'){
		$questions = pdo_fetchall("select * from ".tablename('hc_article_question')." where isopen = 1 and uniacid = ".$uniacid);
		include $this->template('question');
		exit;
	}
	
	if($op=='myqrcode'){
		$target_file = IA_ROOT.'/addons/hc_article/style/poster/memberposter/'.$uniacid."share$id.jpg";
		$isexist = 1;
		if(!file_exists($target_file)){
			$isexist = 0;
		}
		include $this->template('myqrcode');
		exit;
	}
	
	include $this->template('index');
?>