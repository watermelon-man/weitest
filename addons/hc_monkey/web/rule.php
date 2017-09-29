<?php
	if($op=='display'){
		$rule = pdo_fetch('SELECT * FROM '.tablename('hc_monkey_rule')." WHERE uniacid = ".$uniacid);
		$id = $rule['id'];
		if (checksubmit('submit')) {
			
			$rule = array(
				'uniacid'=>$uniacid,
				'index_content'=>htmlspecialchars_decode(trim($_GPC['index_content'])),
				'success_content'=>trim($_GPC['success_content']),
				'get_thumb'=>trim($_GPC['get_thumb']),
				'gzurl'=>trim($_GPC['gzurl']),
				'share_title'=>trim($_GPC['share_title']),
				'share_content'=>trim($_GPC['share_content']),
				'share_thumb'=>trim($_GPC['share_thumb']),
			);
			if($id){
				pdo_update('hc_monkey_rule', $rule, array('id'=>$id));
			} else {
				$rule['createtime'] = time();
				pdo_insert('hc_monkey_rule', $rule);
			}
			
			message('提交成功！', $this->createWebUrl('rule'), 'success');
		}
	}
	include $this->template('web/rule');
?>