<?php
	$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($follow) || $follow['follow']==0){
		message('你想知道怎么加入么？', $rule['gzurl']);
	}
	$member = pdo_fetch('SELECT * FROM '.tablename('hc_article_member')." WHERE ispay = 1 and uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($member)){
		message('请先注册！', $this->createMobileUrl('register'), 'error');
	}
	
	if($op=='withdraw'){
		if($uid){
			$fans = mc_fetch($uid, array('credit2'));
		}
		if(checksubmit('submit')){
			$credit2 = intval($_GPC['credit2']);
			if($uid){
				if($fans>=$credit2){
					mc_credit_update($uid, 'credit2', -$credit2, array('0'=>'', '1'=>$member['nickname'].'提现记录'));
					$withdraw = array(
						'uniacid'=>$uniacid,
						'mid'=>$member['id'],
						'credit2'=>$credit2,
						'flag'=>0,
						'createtime'=>time()
					);
					pdo_insert('hc_article_withdraw', $withdraw);
				} else {
					message('提现余额不足');
				}
			}
			message('提现成功', $this->createMobileUrl('withdraw', array('op'=>'withdraw', 'opp'=>'log')));
		}
		$withdraws = pdo_fetchall("select * from ".tablename('hc_article_withdraw')." where flag = 0 and uniacid = ".$uniacid." and mid = ".$member['id']." order by createtime desc");
		include $this->template('withdraw');
		exit;
	}
	
	if($op=='input'){
		if($uid){
			$fans = mc_fetch($uid, array('credit2'));
		}
		if(checksubmit('submit')){
			$credit2 = intval($_GPC['credit2']);
			if($credit2){
				$params['tid'] = $member['id'].'/';
				$params['user'] = $openid;
				$params['fee'] = $credit2;
				$params['title'] = $_W['account']['name'];
				$params['ordersn'] = date('md').random(4, 1);
				include $this->template('pay');
				exit;
			}
		}
		$inputs = pdo_fetchall("select * from ".tablename('hc_article_withdraw')." where flag = 1 and uniacid = ".$uniacid." and mid = ".$member['id']." order by createtime desc");
		include $this->template('inputmoney');
		exit;
	}
?>