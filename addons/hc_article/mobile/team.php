<?php
	$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($follow) || $follow['follow']==0){
		message('你想知道怎么加入么？', $rule['gzurl']);
	}
	$member = pdo_fetch('SELECT * FROM '.tablename('hc_article_member')." WHERE ispay = 1 and uniacid = ".$uniacid." and openid = '".$openid."'");
	$id = $member['id'];
	if(empty($member)){
		message('请先注册！', $this->createMobileUrl('register'), 'error');
	}
	
	if($op=='display'){
		$memslevel = array();
		$lowmemsid = '('.$id.')';
		for($i=0; $i<=2; $i++){
			$memslevel[$i] = pdo_fetchall("select * from ".tablename('hc_article_member')." where ispay = 1 and shareid in ".$lowmemsid." ORDER BY id DESC");
			$lowmemsid = '';
			if(!empty($memslevel[$i])){
				foreach($memslevel[$i] as $f){
					$lowmemsid = $lowmemsid.$f['id'].',';
				}
				$lowmemsid = '('.trim($lowmemsid, ',').')';
			} else {
				unset($memslevel[$i]);
				break;
			}
			if($i==2){
				break;
			}
		}
	}
	
	if($op=='more'){
		$level = intval($_GPC['level']);
		if($level==0){
			$memslevel = pdo_fetchall("select * from ".tablename('hc_article_member')." where ispay = 1 and shareid = ".$id." ORDER BY id DESC");
		} else {
			$lowmemsid = $_GPC['lowmemsid'];
			$memslevel = pdo_fetchall("select * from ".tablename('hc_article_member')." where ispay = 1 and id in ".$lowmemsid." ORDER BY id DESC");
		}
	}
	
	include $this->template('team');
?>