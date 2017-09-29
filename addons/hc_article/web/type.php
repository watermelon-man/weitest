<?php
	if($op == 'display'){
		if(checksubmit('submit')){
			if(!empty($_GPC['displayorder'])){
				foreach($_GPC['displayorder'] as $key=>$d){
					pdo_update('hc_article_type', array('displayorder'=>$d), array('id'=>$_GPC['id'][$key]));
				}
				message('批量更新排序成功！', $this->createWebUrl('type', array('op' => 'display')), 'success');
			}
		}
		$list = pdo_fetchall("SELECT * FROM " . tablename('hc_article_type') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
	} elseif ($op == 'post'){
		$id = intval($_GPC['id']);
		if(checksubmit('submit')){
			$data = array(
				'uniacid' => $_W['uniacid'],
				'title' => trim($_GPC['title']),
				'isopen' => intval($_GPC['isopen']),
				'displayorder' => intval($_GPC['displayorder'])
			);

			if (!empty($id)) {
				pdo_update('hc_article_type', $data, array('id' => $id));
			} else {
				pdo_insert('hc_article_type', $data);
				$id = pdo_insertid();
			}
			message('更新成功！', $this->createWebUrl('type', array('op' => 'display')), 'success');
		}
		$type = pdo_fetch("select * from " . tablename('hc_article_type') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $_W['uniacid']));
		if(empty($type)){
			$type['isopen'] = 1;
		}
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		$type = pdo_fetch("SELECT id  FROM " . tablename('hc_article_type') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
		if (empty($type)) {
			message('抱歉，该分类不存在或是已经被删除！', $this->createWebUrl('type', array('op' => 'display')), 'error');
		}
		pdo_delete('hc_article_type', array('id' => $id));
		message('删除成功！', $this->createWebUrl('type', array('op' => 'display')), 'success');
	} else {
		message('请求方式不存在');
	}
	include $this->template('web/type');
?>