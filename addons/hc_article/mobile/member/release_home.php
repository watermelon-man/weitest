<?php
	load()->func('tpl');
	$active = 3;
	if($op == 'display'){
		if(checksubmit('submit')){
			
			$headurl = !empty($_GPC['headurl']) ? trim($_GPC['headurl']) : message('请上传头像');
			$http = mb_substr($headurl , 0 , 5);
			if($http != 'http:'){
				$headurl = $_W['attachurl'].$headurl;
			}
			$nickname = !empty($_GPC['nickname']) ? trim($_GPC['nickname']) : message('请输入昵称');
			$mobile = hehe($_GPC['mobile']);
			$mem = array(
				'headurl'=>$headurl,
				'nickname'=>$nickname,
				'mobile'=>$mobile,
				'remark'=>trim($_GPC['remark'])
			);
			pdo_update('hc_article_member', $mem, array('id'=>$member['id']));
			message('提交成功', $this->createMobileUrl('release_home'), 'succees');
		}
		$uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid = '{$uniacid}' and openid = '".$member['openid']."'");
		if($uid){
			$credit2 = pdo_fetchcolumn("SELECT credit2 FROM " . tablename('mc_members') . " WHERE uid = '{$uid}'");
		}
		$viplevel = pdo_fetch("select * from ".tablename('hc_article_viplevel')." where id = ".$member['viplevel']);
	}
	include $this->template('member/release_home');
?>