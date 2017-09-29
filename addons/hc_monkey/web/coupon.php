<?php
	if($op=='display'){
		if(checksubmit('submit')){
			$id = $_GPC['id'];
			$listorder = $_GPC['listorder'];
			foreach($id as $key=>$i){
				pdo_update('hc_monkey_coupon', array('listorder'=>intval($listorder[$key])), array('id'=>$i));
			}
			message('批量更新成功！', $this->createWebUrl('coupon'), 'success');
		}
		$coupons = pdo_fetchall("select * from ".tablename('hc_monkey_coupon')." where uniacid = ".$uniacid." order by listorder desc");
	}

	if($op=='post'){
		$id = $_GPC['id'];
		if(checksubmit('submit')){
			$title = !empty($_GPC['title']) ? $_GPC['title'] : message('请填写标题！');
			$description = !empty($_GPC['description']) ? $_GPC['description'] : message('请填写描述！');
			$password = !empty($_GPC['password']) ? $_GPC['password'] : message('请输入消费密码');
			$datelimit = $_GPC['datelimit'];
			$coupon = array(
				'uniacid'=>$uniacid,
				'title'=>$title,
				'description'=>htmlspecialchars_decode($description),
				'password'=>$password,
				'mobile'=>trim($_GPC['mobile']),
				'detail'=>htmlspecialchars_decode(trim($_GPC['detail'])),
				'total'=>intval($_GPC['total']),
				'listorder'=>intval($_GPC['listorder']),
				'starttime'=>strtotime($datelimit['start']),
				'endtime'=>strtotime($datelimit['end']),
				'isopen'=>$_GPC['isopen'],
			);
			if(intval($id)){
				pdo_update('hc_monkey_coupon', $coupon, array('id'=>$id));
			} else {
				$coupon['createtime'] = time();
				pdo_insert('hc_monkey_coupon', $coupon);
			}
			message('提交成功！', $this->createWebUrl('coupon'), 'success');
		}
		if(intval($id)){
			$coupon = pdo_fetch("select * from ".tablename('hc_monkey_coupon')." where id = ".$id);
		} else {
			$coupon = array(
				'starttime'=>time(),
				'endtime'=>time()+3600*24*7,
				'isopen'=>1
			);
		}
		include $this->template('web/coupon_post');
		exit;
	}
	
	if($op=='delete'){
		$id = $_GPC['id'];
		pdo_delete('hc_monkey_coupon', array('id'=>$id));
		message('删除成功！', $this->createWebUrl('coupon'), 'success');
	}
	
	include $this->template('web/coupon_list');

?>