<?php
	$active = 2;
	if($op == 'display'){
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall("SELECT * FROM " . tablename('hc_article_article') . " WHERE mid = ".$member['id']." and uniacid = '{$uniacid}' ORDER BY createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('hc_article_article') . " WHERE mid = ".$member['id']." and uniacid = '{$uniacid}'");
		$pager = pagination($total, $pindex, $psize);
		$types = pdo_fetchall("SELECT id, title FROM " . tablename('hc_article_type') . " WHERE uniacid = '{$uniacid}'");
		$type = array();
		foreach($types as $t){
			$type[$t['id']] = $t['title'];
		}
	}
	
	if($op == 'delete') {
		$id = intval($_GPC['id']);
		pdo_delete('hc_article_article', array('id' => $id));
		message('删除成功！', $this->createMobileUrl('release_list', array('op' => 'display')), 'success');
	}
	
	include $this->template('member/release_list');
?>