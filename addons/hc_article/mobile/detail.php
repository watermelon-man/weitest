<?php
	$member = pdo_fetch('SELECT * FROM '.tablename('hc_article_member')." WHERE ispay = 1 and uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($member)){
		$url = $this->createMobileUrl('register');
		header("location:$url");
	}
	
	if($op=='report'){
		include $this->template('report');
		exit;
	}
	
	if($op=='release'){
		if(empty($member['codetime']) || time()-$member['codetime'] > 3600){
			$authcode = random(6,1);
			pdo_update('hc_article_member', array('authcode'=>$authcode, 'codetime'=>time()), array('id'=>$member['id']));
		} else {
			$authcode = $member['authcode'];
		}
		//echo $_W['siteroot'].'app/'.$this->createMobileUrl('release_index');
		include $this->template('releaseedit');
		exit;
	}
	$psize = 5;
	$total = 0;
	if($op=='display'){
		$id = intval($_GPC['id']);
		$time = time();
		$article = pdo_fetch("select * from " . tablename('hc_article_article') . " where isopen = 1 and starttime <= ".$time." and endtime > ".$time." and id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $uniacid));
		if(empty($article)){
			message('该活动已失效');
		}
		$gzs = pdo_fetchall("select * from ".tablename('hc_article_gz')." where uniacid = ".$uniacid." and aid = ".$article['id']);
		$visitnum = $article['visitnum'];
		$sharenum = $article['sharenum'];
		$gznum = 0;
		foreach($gzs as $g){
			$visitnum++;
			if($g['isgz']==1){
				$gznum++;
			}
			if($g['isshare']==1){
				$sharenum++;
			}
		}
		if($article['mid']){
			$articlemember = pdo_fetch("SELECT id, nickname, headurl FROM " . tablename('hc_article_member') . " WHERE id = ".$article['mid']);
		}
		$gz = pdo_fetch("select isgz, isshare from ".tablename('hc_article_gz')." where uniacid = ".$uniacid." and mid = ".$member['id']." and aid = ".$article['id']);
		if(empty($gz)){
			$gz = array(
				'uniacid'=>$uniacid,
				'mid'=>$member['id'],
				'aid'=>$article['id'],
				'isgz'=>0,
				'createtime'=>time()
			);
			pdo_insert('hc_article_gz', $gz);
		}
		$comments = pdo_fetchall("select * from ".tablename('hc_article_comment')." where uniacid = ".$uniacid." and aid = ".$id." order by createtime asc limit ".$psize);
		$total = pdo_fetchcolumn("select count(id) from ".tablename('hc_article_comment')." where uniacid = ".$uniacid." and aid = ".$id);
		$members = pdo_fetchall("SELECT id, nickname, headurl FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$uniacid}'");
		$member = array();
		foreach($members as $m){
			$member['nickname'][$m['id']] = $m['nickname'];
			$member['headurl'][$m['id']] = $m['headurl'];
		}
		
		include $this->template('detail');
	}
	
	if($op=='loadmore'){
		$id = intval($_GPC['id']);
		$members = pdo_fetchall("SELECT id, nickname, headurl FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$uniacid}'");
		$member = array();
		foreach($members as $m){
			$member['nickname'][$m['id']] = $m['nickname'];
			$member['headurl'][$m['id']] = $m['headurl'];
		}
		$psize1 = intval($_GPC['psize']);
		$psize1 = $psize + $psize1;
		$comments = pdo_fetchall("select * from ".tablename('hc_article_comment')." where uniacid = ".$uniacid." and aid = ".$id." order by createtime asc limit ".$psize1.",".$psize);
		if(!empty($comments)){
			$json['psize'] = $psize1;
			foreach($comments as $key=>$a){
				$comments[$key]['headurl'] = !empty($a['mid']) ? $member['headurl'][$a['mid']] : '';
				$comments[$key]['nickname'] = !empty($a['mid']) ? $member['nickname'][$a['mid']] : '';
				$comments[$key]['createtime'] = date('Y-m-d H:i:s', $a['createtime']);
			}
			$json['comments'] = $comments;
			echo json_encode($json);
		} else {
			$json['comments'] = 0;
			echo json_encode($json);
		}
		exit;
	}
	
	if($op=='isgz'){
		$aid = intval($_GPC['aid']);
		$isgz = intval($_GPC['isgz']);
		if($aid){
			if($isgz==1){
				pdo_update('hc_article_gz', array('isgz'=>1), array('mid'=>$member['id'], 'aid'=>$aid));
				echo 1;
				exit;
			} else {
				pdo_update('hc_article_gz', array('isgz'=>0), array('mid'=>$member['id'], 'aid'=>$aid));
				echo 0;
				exit;
			}
			
		} else {
			echo -1;
			exit;
		}
	}
	
	if($op=='issupport'){
		$aid = intval($_GPC['aid']);
		if($aid){
			$time = time();
			$article = pdo_fetch("select * from " . tablename('hc_article_article') . " where isopen = 1 and starttime <= ".$time." and endtime > ".$time." and id=:id and uniacid=:uniacid limit 1", array(":id" => $aid, ":uniacid" => $uniacid));
			if(empty($article)){
				echo -1;
				exit;
			}
			if(!empty($_GPC['content'])){
				$comments = pdo_fetchall("select id from ".tablename('hc_article_comment')." where uniacid = ".$uniacid." and mid = ".$member['id']." and aid = ".$aid);
				$comment = array(
					'uniacid'=>$uniacid,
					'mid'=>$member['id'],
					'aid'=>$aid,
					'content'=>trim($_GPC['content']),
					'createtime'=>time()
				);
				pdo_insert('hc_article_comment', $comment);
				if(empty($comments)){
					if($article['allmoney']-$article['eachmoney']>=0){
						pdo_update('hc_article_article', array('allmoney'=>$article['allmoney']-$article['eachmoney']), array('id'=>$aid));
						mc_credit_update($uid, 'credit2', $article['eachmoney'], array('0'=>'', '1'=>$member['nickname'].'评论'.$article['title'].'获得金额'));
						$commission = array(
							'uniacid'=>$uniacid,
							'mid'=>$member['id'],
							'shareid'=>0,
							'aid'=>$aid,
							'credit2'=>$article['eachmoney'],
							'flag'=>0,
							'createtime'=>time()
						);
						pdo_insert('hc_article_commission', $commission);
						//评论得金额
						echo 1;
						exit;
					} else {
						//普通评论
						echo 2;
						exit;
					}
				} else {
					echo 2;
					exit;
				}
				
			} else {
				echo 0;
				exit;
			}
		} else {
			echo -1;
			exit;
		}
	}
	
	if($op=='share'){
		$aid = intval($_GPC['aid']);
		if($aid){
			pdo_update('hc_article_gz', array('isshare'=>1), array('mid'=>$member['id'], 'aid'=>$aid));
		}
		echo 1;
		exit;
	}
?>