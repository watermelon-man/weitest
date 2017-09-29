<?php
	if($op=='delete'){
		$id = intval($_GPC['id']);
		if($id){
			pdo_delete('hc_article_viplevel', array('id'=>$id));
			echo 1;
			exit;
		}
		echo 0;
		exit;
	}
	
	if($op=='display'){
		//$theone = pdo_fetch("SELECT * FROM ".tablename('hc_article_rule')." WHERE uniacid = ".$uniacid);
		$viplevels = pdo_fetchall('SELECT * FROM '.tablename('hc_article_viplevel')." WHERE uniacid = ".$uniacid." order by needmoney asc");
		$rule = pdo_fetch('SELECT * FROM '.tablename('hc_article_rule')." WHERE uniacid = ".$uniacid);
		$id = $rule['id'];
		if (checksubmit('submit')) {
			if(!empty($_GPC['title'])){
				$time = time();
				foreach($_GPC['title'] as $key=>$v){
					$sharelevel = array(
						'uniacid'=>$uniacid,
						'title'=>$v,
						'needmoney'=>intval($_GPC['needmoney'][$key]),
						'articlenum'=>intval($_GPC['articlenum'][$key]),
						'usecredit'=>intval($_GPC['usecredit'][$key]),
						'createtime'=>$time
					);
					if(intval($_GPC['slid'][$key])){
						pdo_update('hc_article_viplevel', $sharelevel, array('id'=>$_GPC['slid'][$key]));
					} else {
						pdo_insert('hc_article_viplevel', $sharelevel);
					}
				}
			}
			
			$rule = array(
				'uniacid'=>$uniacid,
				'notice'=>trim($_GPC['notice']),
				'gzurl'=>trim($_GPC['gzurl']),
				'morehz'=>trim($_GPC['morehz']),
				'register_bg'=>trim($_GPC['register_bg']),
				'index_logo'=>trim($_GPC['index_logo']),
				'commission1'=>intval($_GPC['commission1']),
				'commission2'=>intval($_GPC['commission2']),
				'commission3'=>intval($_GPC['commission3']),
			);
			if($id){
				pdo_update('hc_article_rule', $rule, array('id'=>$id));
			} else {
				$rule['createtime'] = time();
				pdo_insert('hc_article_rule', $rule);
			}
			
			message('提交成功！', $this->createWebUrl('rule'), 'success');
		}
	}
	include $this->template('web/rule');
?>