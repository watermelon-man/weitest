<?php
	$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($follow) || $follow['follow']==0){
		message('你想知道怎么加入么？', $rule['gzurl']);	
	}
	$member = pdo_fetch("select * from ".tablename('hc_monkey_member')." where uniacid = ".$uniacid." and openid = '".$openid."'");
	if(empty($member)){
		message('请先点亮8字才能领取优惠券哦', $this->createMobileUrl('index'));
	}

	if($op=='display'){
		$mycoupon = pdo_fetch("select * from ".tablename('hc_monkey_takecoupon')." where uniacid = ".$uniacid." and mid = ".$member['id']);
		if(!empty($mycoupon)){
			$coupon = pdo_fetch("select * from ".tablename('hc_monkey_coupon')." where id = ".$mycoupon['couponid']);
			if($mycoupon['status']==1){
				$flag = 3;
			} else {
				$time = time();
				if($time < $coupon['starttime']){
					$flag = 0;
				}
				if($time >= $coupon['starttime'] && $time < $coupon['endtime']){
					$flag = 1;
				}
				if($time >= $coupon['endtime']){
					$flag = 2;
				}
			}
		} else {
			message('你还没领取优惠券哦', $this->createMobileUrl('index'));
		}
	}
	
	if($op=='detail'){
		$couponid = intval($_GPC['couponid']);
		if($couponid){
			$coupon = pdo_fetch("select * from ".tablename('hc_monkey_coupon')." where id = ".$couponid);
		}
		include $this->template('coupon_detail');
		exit;
	}
	
	if($op=='cost'){
		$password = trim($_GPC['password']);
		$couponid = intval($_GPC['couponid']);
		if($couponid){
			$coupon = pdo_fetch("select password from ".tablename('hc_monkey_coupon')." where id = ".$couponid." and isopen = 1");
			if($password == $coupon['password']){
				pdo_update('hc_monkey_takecoupon', array('status'=>1), array('mid'=>$member['id'], 'couponid'=>$couponid));
				echo 1;
				exit;
			} else {
				echo 0;
				exit;
			}
		}
		echo 0;
		exit;
	}
	
	include $this->template('coupon');
?>