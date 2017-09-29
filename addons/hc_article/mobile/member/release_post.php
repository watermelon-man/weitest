<?php
	load()->func('tpl');
	$active = 1;
	if($op == 'display'){
		$id = intval($_GPC['id']);
		if($_GPC['opp']=='post'){
			$title = !empty($_GPC['title']) ? trim($_GPC['title']) : message('请输入文章标题');
			$datelimit = $_GPC['datelimit'];
			$data = array(
				'uniacid' => $uniacid,
				'title' => $title,
				'mid' => intval($member['id']),
				'typeid' => intval($_GPC['typeid']),
				'sharethumb' => trim($_GPC['sharethumb']),
				'description' => trim($_GPC['description']),
				'content' => htmlspecialchars_decode(trim($_GPC['content'])),
				'allmoney' => intval($_GPC['allmoney']),
				//'paytype' => intval($_GPC['paytype']),
				'eachmoney' => intval($_GPC['eachmoney']),
				'starttime' => strtotime($datelimit['start']),
				'endtime' => strtotime($datelimit['end']),
				'isopen' => intval($_GPC['isopen']),
			);
			if(is_array($_GPC['thumbs'])){
				$data['thumbs'] = serialize($_GPC['thumbs']);
			}
			if (!empty($id)) {
				pdo_update('hc_article_article', $data, array('id' => $id));
			} else {
				$data['paytype'] = 0;
				$data['isfree'] = 0;
				$data['createtime'] = time();
				if($data['mid']){
					$usecredit = 0;
					if(1){
						if($member['articlenum']>0){
							$update_articlenum = 1;
						} else {
							if(!empty($member['openid'])){
								$uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$uniacid}' and openid = '".$member['openid']."'");
								if($uid){
									$credit2 = pdo_fetchcolumn("SELECT credit2 FROM " . tablename('mc_members') . " WHERE uid = '{$uid}'");
									$usecredit = pdo_fetchcolumn("SELECT usecredit FROM " . tablename('hc_article_viplevel') . " WHERE id = '{$member['viplevel']}'");
									if($credit2 < $usecredit){
										message('余额不足支付发表该文章');
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
					if(1){
						if(!empty($member['openid'])){
							$uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$uniacid}' and openid = '".$member['openid']."'");
							if($uid){
								$credit2 = pdo_fetchcolumn("SELECT credit2 FROM " . tablename('mc_members') . " WHERE uid = '{$uid}'");
								if($credit2 < $data['allmoney']+$usecredit){
									message('余额不足支付文章总金额');
								} else {
									$allmoney = $data['allmoney'];
								}
							} else {
								message('未关注公众号');
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
			message('更新成功！', $this->createMobileUrl('release_list', array('op' => 'display')), 'success');
		}
		$article = pdo_fetch("select * from " . tablename('hc_article_article') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $uniacid));
		$types = pdo_fetchall("SELECT * FROM " . tablename('hc_article_type') . " WHERE uniacid = '{$uniacid}' ORDER BY displayorder DESC");
		if(empty($article)){
			$article['isopen'] = 1;
		}
		$piclist = unserialize($article['thumbs']);
	}
	include $this->template('member/release_post');
?>