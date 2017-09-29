<?php
	if($op == 'display'){
		if(checksubmit('submit')){
			if(!empty($_GPC['displayorder'])){
				foreach($_GPC['displayorder'] as $key=>$d){
					pdo_update('hc_article_adv', array('displayorder'=>$d), array('id'=>$_GPC['id'][$key]));
				}
				message('批量更新排序成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
			}
		}
		$list = pdo_fetchall("SELECT * FROM " . tablename('hc_article_adv') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
	} elseif ($op == 'post'){
		$id = intval($_GPC['id']);
		if(checksubmit('submit')){
			$data = array(
				'uniacid' => $_W['uniacid'],
				'link' => $_GPC['link'],
				'enabled' => intval($_GPC['enabled']),
				'displayorder' => intval($_GPC['displayorder'])
			);
			if (!empty($_GPC['thumb'])) {
				$data['thumb'] = $_GPC['thumb'];
				//file_delete($_GPC['thumb-old']);
			}

			if (!empty($id)) {
				pdo_update('hc_article_adv', $data, array('id' => $id));
			} else {
				pdo_insert('hc_article_adv', $data);
				$id = pdo_insertid();
			}
			message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
		}
		$adv = pdo_fetch("select * from " . tablename('hc_article_adv') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $_W['uniacid']));
		if(empty($adv)){
			$adv['enabled'] = 1;
		}
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		$adv = pdo_fetch("SELECT id  FROM " . tablename('hc_article_adv') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
		if (empty($adv)) {
			message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
		}
		pdo_delete('hc_article_adv', array('id' => $id));
		message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
	} else {
		message('请求方式不存在');
	}
	include $this->template('web/adv');
?>