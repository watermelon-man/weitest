<?php
	if($op == 'display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM " . tablename('hc_article_article') . " WHERE uniacid = '{$uniacid}' ORDER BY createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_article_article') . " WHERE uniacid = '{$uniacid}'");
		$pager = pagination($total, $pindex, $psize);
		$types = pdo_fetchall("SELECT id, title FROM " . tablename('hc_article_type') . " WHERE uniacid = '{$uniacid}'");
		$type = array();
		foreach($types as $t){
			$type[$t['id']] = $t['title'];
		}
		$members = pdo_fetchall("SELECT id, nickname FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$uniacid}'");
		$member = array();
		foreach($members as $m){
			$member[$m['id']] = $m['nickname'];
		}
	}

	if($op == 'post'){
		$id = intval($_GPC['id']);
		if(checksubmit('submit')){
			$title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('请输入文章标题');
			$datelimit = $_GPC['datelimit'];
			$data = array(
				'uniacid' => $uniacid,
				'title' => $title,
				'mid' => intval($_GPC['mid']),
				'typeid' => intval($_GPC['typeid']),
				'author' => trim($_GPC['author']),
				'sharethumb' => trim($_GPC['sharethumb']),
				'description' => trim($_GPC['description']),
				'content' => htmlspecialchars_decode(trim($_GPC['content'])),
				'visitnum' => intval($_GPC['visitnum']),
				'sharenum' => intval($_GPC['sharenum']),
				'allmoney' => intval($_GPC['allmoney']),
				//'paytype' => intval($_GPC['paytype']),
				'eachmoney' => intval($_GPC['eachmoney']),
				'starttime' => strtotime($datelimit['start']),
				'endtime' => strtotime($datelimit['end']),
				'isopen' => intval($_GPC['isopen']),
				'isindex' => intval($_GPC['isindex']),
			);
			if(is_array($_GPC['thumbs'])){
				$data['thumbs'] = serialize($_GPC['thumbs']);
			}
			if (!empty($id)) {
				pdo_update('hc_article_article', $data, array('id' => $id));
			} else {
				$data['paytype'] = intval($_GPC['paytype']);
				$data['isfree'] = intval($_GPC['isfree']);
				$data['createtime'] = time();
				if($data['mid']){
					$member = pdo_fetch("SELECT openid, articlenum, viplevel FROM " . tablename('hc_article_member') . " WHERE id = '{$data['mid']}'");
					$usecredit = 0;
					if(!$data['isfree']){
						if($member['articlenum']>0){
							$update_articlenum = 1;
						} else {
							if(!empty($member['openid'])){
								$uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$uniacid}' and openid = '".$member['openid']."'");
								if($uid){
									$credit2 = pdo_fetchcolumn("SELECT credit2 FROM " . tablename('mc_members') . " WHERE uid = '{$uid}'");
									$usecredit = pdo_fetchcolumn("SELECT usecredit FROM " . tablename('hc_article_viplevel') . " WHERE id = '{$member['viplevel']}'");
									if($credit2 < $usecredit){
										message('该会员的余额不足支付发表该文章');
									} else {
										$update_credit2 = 1;
									}
								} else {
									message('该会员未关注公众号');
								}
							} else {
								message('非法访问');
							}
						}
					}
					$allmoney = 0;
					if(!$data['paytype']){
						if(!empty($member['openid'])){
							$uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$uniacid}' and openid = '".$member['openid']."'");
							if($uid){
								$credit2 = pdo_fetchcolumn("SELECT credit2 FROM " . tablename('mc_members') . " WHERE uid = '{$uid}'");
								if($credit2 < $data['allmoney']+$usecredit){
									message('该会员的余额不足支付文章总金额');
								} else {
									$allmoney = $data['allmoney'];
								}
							} else {
								message('该会员未关注公众号');
							}
						} else {
							message('非法访问');
						}
					}
					if($update_articlenum==1){
						pdo_update('hc_article_member', array('articlenum'=>$member['articlenum']-1), array('id'=>$data['mid']));
					}
					if($update_credit2==1 && !empty($uid)){
						$totalmoney = $allmoney + $usecredit;
						if(intval($totalmoney)){
							pdo_update('mc_members', array('credit2'=>$credit2-$totalmoney), array('uid'=>$uid));
						}
					}
				}
				pdo_insert('hc_article_article', $data);
			}
			message('更新成功！', $this->createWebUrl('article', array('op' => 'display')), 'success');
		}
		$article = pdo_fetch("select * from " . tablename('hc_article_article') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $uniacid));
		$types = pdo_fetchall("SELECT * FROM " . tablename('hc_article_type') . " WHERE uniacid = '{$uniacid}' ORDER BY displayorder DESC");
		$members = pdo_fetchall("SELECT * FROM " . tablename('hc_article_member') . " WHERE uniacid = '{$uniacid}' ORDER BY createtime DESC");
		if(empty($article)){
			$article['isopen'] = 1;
		}
		$piclist = unserialize($article['thumbs']);
	}
	
	if($op == 'delete') {
		$id = intval($_GPC['id']);
		pdo_delete('hc_article_article', array('id' => $id));
		message('删除成功！', $this->createWebUrl('article', array('op' => 'display')), 'success');
	}
	
	if($op=='holdmoney'){
		$mid = intval($_GPC['mid']);
		if($mid){
			$openid = pdo_fetchcolumn("SELECT openid FROM " . tablename('hc_article_member') . " WHERE id = '{$mid}'");
			if(!empty($openid)){
				$uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$uniacid}' and openid = '".$openid."'");
				if($uid){
					$credit2 = pdo_fetchcolumn("SELECT credit2 FROM " . tablename('mc_members') . " WHERE uid = '{$uid}'");
					echo $credit2;
					exit;
				} else {
					echo 0;
					exit;
				}
				
			} else {
				echo 0;
				exit;
			}
		} else {
			echo 0;
			exit;
		}
	}
	
	include $this->template('web/article');
?>