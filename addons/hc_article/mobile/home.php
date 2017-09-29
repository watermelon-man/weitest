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
		$gzs = pdo_fetchall("select * from ".tablename('hc_article_gz')." where uniacid = ".$uniacid." and mid = ".$member['id']);
		$myarticlenum = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_article_article')." WHERE mid = ".$member['id']." and uniacid = '{$uniacid}' ORDER BY createtime DESC");
		$sharenum = 0;
		$gznum = 0;
		foreach($gzs as $g){
			if($g['isgz']==1){
				$gznum++;
			}
			if($g['isshare']==1){
				$sharenum++;
			}
		}
	}
	
	if($op=='my'){
		$opp = $_GPC['opp'];
		if($opp=='myarticle'){
			$title = '我的文章';
			$articles = pdo_fetchall("SELECT id, title, description FROM " . tablename('hc_article_article')." WHERE mid = ".$member['id']." and uniacid = '{$uniacid}' ORDER BY createtime DESC");
		}
		if($opp=='myshare'){
			$title = '我的分享';
			$my = pdo_fetchall("select * from ".tablename('hc_article_gz')." where isshare = 1 and uniacid = ".$uniacid." and mid = ".$member['id']);
		}
		if($opp=='mygz'){
			$my = pdo_fetchall("select * from ".tablename('hc_article_gz')." where isgz = 1 and uniacid = ".$uniacid." and mid = ".$member['id']);
			$title = '我的关注';
		}
		if(!empty($my)){
			$article_ids = '';
			foreach($my as $m){
				$article_ids = $article_ids.$m['aid'].',';
			}
			$article_ids = '('.trim($article_ids, ',').')';
			$articles = pdo_fetchall("SELECT id, title, description FROM " . tablename('hc_article_article')." WHERE id in ".$article_ids." and uniacid = '{$uniacid}' ORDER BY createtime DESC");
		}
		
		include $this->template('myhome');
		exit;
	}
	
	include $this->template('home');
?>