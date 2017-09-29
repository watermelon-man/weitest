<?php
	if ($op == 'display') {
		if (!empty($_GPC['displayorder'])) {
			foreach ($_GPC['displayorder'] as $id => $displayorder) {
				pdo_update('hc_article_question', array('displayorder' => $displayorder), array('id' => $id));
			}
			message('排序更新成功！', $this->createWebUrl('question', array('op' => 'display')), 'success');
		}
		$questions = pdo_fetchall("SELECT * FROM " . tablename('hc_article_question') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
		include $this->template('web/question');
	} elseif ($op == 'post') {
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$question = pdo_fetch("SELECT * FROM " . tablename('hc_article_question') . " WHERE id = '$id'");
		} else {
			$question = array(
				'displayorder' => 0,
				'isopen' => 1
			);
		}
		if (checksubmit('submit')) {
			if (empty($_GPC['title'])) {
				message('请输入标题！');
			}
			$data = array(
				'uniacid' => $_W['uniacid'],
				'title' => $_GPC['title'],
				'displayorder' => intval($_GPC['displayorder']),
				'content' => trim($_GPC['content']),
				'isopen' => intval($_GPC['isopen']),
				'createtime'=>time()
			);
			if (!empty($id)) {
				pdo_update('hc_article_question', $data, array('id' => $id));
			} else {
				pdo_insert('hc_article_question', $data);
			}
			message('提交成功！', $this->createWebUrl('question', array('op' => 'display')), 'success');
		}
		include $this->template('web/question');
	} elseif ($op == 'delete') {
		$id = intval($_GPC['id']);
		pdo_delete('hc_article_question', array('id' => $id));
		message('删除成功！', $this->createWebUrl('question', array('op' => 'display')), 'success');
	}
?>