<?php
	$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($follow) || $follow['follow']==0){
		message('你想知道怎么加入么？', $rule['gzurl']);	
	}
	$member = pdo_fetch("select * from ".tablename('hc_monkey_member')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($member)){
		$this->CheckCookie();
	}
	if($op=='display'){
		$success_content = array();
		for($i=0; $i<8; $i++){
			$success_content[$i] = mb_substr($rule['success_content'], $i, 1, 'utf-8');
		}
	}
	
	if($op=='getuser'){
		$data['success'] = 1;
		$data['data'] = $member;
		echo json_encode($data);
		exit;
	}
	
	if($op=='submitscore'){
		$word_num = intval($_GPC['peachNum']);
		if($word_num <= $member['word_num']){
			$word_num = $member['word_num'];
		}
		$score = intval($_GPC['goodCollect']);
		if($score <= $member['score']){
			$score = $member['score'];
		}
		$mem = array(
			'word_num'=>$word_num,
			'score'=>$score
		);
		pdo_update('hc_monkey_member', $mem, array('id'=>$member['id']));
		echo 1;
		exit;
	}
	
	if($op=='submitphone'){
		$mobile = trim($_GPC['phone']);
		$mem = array(
			'mobile'=>$mobile,
		);
		pdo_update('hc_monkey_member', $mem, array('id'=>$member['id']));
		$mycoupon = pdo_fetch("select * from ".tablename('hc_monkey_takecoupon')." where uniacid = ".$uniacid." and mid = ".$member['id']);
		if(empty($mycoupon)){
			$coupons = pdo_fetchall("select * from ".tablename('hc_monkey_coupon')." where starttime <= ".time()." and endtime > ".time()." and isopen = 1 and total > 0 and uniacid = ".$uniacid);
			if(empty($coupons)){
				//优惠券已领完
				$data['success'] = 0;
				echo json_encode($data);
				exit;
			} else {
				$coupons_num = sizeof($coupons);
				$coupon = $coupons[rand(0, $coupons_num-1)];
				$insert = array(
					'uniacid'=>$uniacid,
					'couponid'=>$coupon['id'],
					'mid'=>$member['id'],
					'nickname'=>$member['nickname'],
					'mobile'=>$mobile,
					'status'=>0,
					'createtime'=>time()
				);
				pdo_insert('hc_monkey_takecoupon', $insert);
				pdo_update('hc_monkey_coupon', array('total'=>$coupon['total']-1), array('id'=>$coupon['id']));
				$data['success'] = 1;
				$data['title'] = $coupon['title'];
				$data['description'] = $coupon['description'];
				echo json_encode($data);
				exit;
			}
		} else {
			$coupon = pdo_fetch("select title, description from ".tablename('hc_monkey_coupon')." where id = ".$mycoupon['couponid']);
			$data['success'] = 1;
			$data['title'] = $coupon['title'];
			$data['description'] = $coupon['description'];
			echo json_encode($data);
			exit;
		}
	}
	
	include $this->template('index');
?>