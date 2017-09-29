<?php
	$psize = 5;
	$total = 0;
	if($op=='display'){
		$time = time();
		$advs = pdo_fetchall("SELECT * FROM " . tablename('hc_article_adv') . " WHERE enabled = 1 and uniacid = '{$uniacid}' ORDER BY displayorder DESC");
		$types = pdo_fetchall("SELECT id, title FROM " . tablename('hc_article_type') . " WHERE isopen = 1 and uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC limit 3");
		$typeid = intval($_GPC['typeid']);
		$style = intval($_GPC['style']);
		if(!$typeid){
			$style = 1;
			$typeid = $types[0]['id'];
		}
		$articles = pdo_fetchall("SELECT * FROM " . tablename('hc_article_article') . " WHERE typeid = ".$typeid." and isopen = 1 and isindex = 1 and starttime <= ".$time." and endtime > ".$time." and uniacid = '{$uniacid}' ORDER BY createtime DESC limit ".$psize);
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_article_article') . " WHERE isopen = 1 and isindex = 1 and starttime <= ".$time." and endtime > ".$time." and uniacid = '{$uniacid}'");
		$members = pdo_fetchall("SELECT id, nickname, headurl FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$uniacid}'");
		$member = array();
		foreach($members as $m){
			$member['nickname'][$m['id']] = $m['nickname'];
			$member['headurl'][$m['id']] = $m['headurl'];
		}
		$article_ids = '';
		foreach($articles as $key=>$a){
			$article_ids = $article_ids.$a['id'].',';
			$piclist[$key] = array();
			$urls = unserialize($a['thumbs']);
			if(is_array($urls)) {
				foreach($urls as $p){
					$piclist[$key][] = is_array($p)?$p['attachment']:$p;
				}
			}
		}
		if(!empty($articles)){
			$article_ids = '('.trim($article_ids, ',').')';
			$gzs = pdo_fetchall("select * from ".tablename('hc_article_gz')." where id in ".$article_ids." and isgz = 1 and uniacid = ".$uniacid." order by createtime asc");
			$gz = array();
			foreach($gzs as $g){
				$gz[$g['id']] = $g['mid'];
			}
		}
		$visitnums = pdo_fetchall("select count(id) as visitnum, aid from ".tablename('hc_article_gz')." where uniacid = ".$uniacid." group by aid");
		$visitnum = array();
		foreach($visitnums as $v){
			$visitnum[$v['aid']] = $v['visitnum'];
		}
	}
	
	if($op=='loadmore'){
		$members = pdo_fetchall("SELECT id, nickname, headurl FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$uniacid}'");
		$member = array();
		foreach($members as $m){
			$member['nickname'][$m['id']] = $m['nickname'];
			$member['headurl'][$m['id']] = $m['headurl'];
		}
		$psize1 = intval($_GPC['psize']);
		$psize1 = $psize + $psize1;
		$time = time();
		$typeid = intval($_GPC['typeid']);
		if(!$typeid){
			$typeid = $types[0]['id'];
		}
		$articles = pdo_fetchall("select * from ".tablename('hc_article_article')." where typeid = ".$typeid." and isopen = 1 and isindex = 1 and uniacid = ".$uniacid." and starttime <= ".$time." and endtime > ".$time." order by createtime desc limit ".$psize1.",".$psize);
		$visitnums = pdo_fetchall("select count(id) as visitnum, aid from ".tablename('hc_article_gz')." where uniacid = ".$uniacid." group by aid");
		$visitnum = array();
		foreach($visitnums as $v){
			$visitnum[$v['aid']] = $v['visitnum'];
		}
		$article_ids = '';
		foreach($articles as $key=>$a){
			$article_ids = $article_ids.$a['id'].',';
			$piclist[$key] = array();
			$urls = unserialize($a['thumbs']);
			if(is_array($urls)) {
				foreach($urls as $p){
					$piclist[$key][] = is_array($p)?$p['attachment']:$p;
				}
			}
		}
		if(!empty($articles)){
			$article_ids = '('.trim($article_ids, ',').')';
			$gzs = pdo_fetchall("select * from ".tablename('hc_article_gz')." where id in ".$article_ids." and isgz = 1 and uniacid = ".$uniacid." order by createtime asc");
			$gz = array();
			foreach($gzs as $g){
				$gz[$g['id']] = $g['mid'];
			}
		}
		if(!empty($articles)){
			$json['psize'] = $psize1;
			foreach($articles as $key=>$a){
				$articles[$key]['url'] = $this->createMobileurl('detail',array('id'=>$a['id']));
				$articles[$key]['headurl'] = !empty($a['mid']) ? $member['headurl'][$a['mid']] : $a['author'];
				$articles[$key]['nickname'] = !empty($a['mid']) ? $member['nickname'][$a['mid']] : $a['author'];
				$articles[$key]['starttime'] = date('Y-m-d', $a['starttime']);
				$articles[$key]['thumbs'] = '';
				if(!empty($piclist[$key])){
					$thumbs_html = '';
					foreach($piclist[$key] as $p){
						$thumbs_html = $thumbs_html.'<img class="lazy-load" src="'.tomedia($p).'" data-original="'.tomedia($p).'" width="25%" alt="'.$a['title'].'">';
					}
					$articles[$key]['thumbs'] = $thumbs_html;
				}
				$gzs_html = '';
				foreach($gzs as $k=>$g){
					if($g['aid']==$a['id']){
						$gzs_html = $gzs_html.'<span><img class="lazy-load" src="'.$member['headurl'][$gz[$g['id']]].'" data-original="'.$member['headurl'][$gz[$g['id']]].'" width="24"></span>';
						unset($gzs[$k]);
					}
				}
				$articles[$key]['gzs'] = $gzs_html;
				$articles[$key]['endtime'] = haha($a['endtime']);
				$articles[$key]['visitnum'] = $a['visitnum'] + $visitnum[$a['id']];
				
			}
			$json['articles'] = $articles;
			echo json_encode($json);
		} else {
			$json['articles'] = 0;
			echo json_encode($json);
		}
		exit;
	}
	
	include $this->template('article_index');
?>