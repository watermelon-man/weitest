<?php
    $item = pdo_fetch('SELECT * FROM ' . tablename('hc_article_poster') . ' WHERE uniacid=:uniacid limit 1', array(':uniacid' => $uniacid));
    $id = $item['id'];
    if (checksubmit('submit')) {
        $data = array(
			'uniacid' => $uniacid,
			'title' => $_GPC['title'],
			'keyword' => $_GPC['keyword'],
			'bg' => $_GPC['bg'],
			'data' => htmlspecialchars_decode($_GPC['data']),
			'waittext' => $_GPC['waittext'],
			'createtime' => time(),
		);
		if($item['data']!=htmlspecialchars_decode($_GPC['data']) || $item['bg']!=$_GPC['bg']){
			$members = pdo_fetchall('SELECT id FROM ' . tablename('hc_article_member') . ' WHERE uniacid=:uniacid', array(':uniacid' => $uniacid));
			foreach($members as $m){
				pdo_update('hc_article_member', array('ischange'=>1), array('id' => $m['id']));
			}
		}
        if (!empty($id)) {
            pdo_update('hc_article_poster', $data, array('id' => $id, 'uniacid' => $uniacid));
        } else {
            pdo_insert('hc_article_poster', $data);
            $id = pdo_insertid();
        }
        $rule = pdo_fetch('select * from ' . tablename('rule') . ' where uniacid=:uniacid and module=:module and name=:name limit 1', array(':uniacid' => $uniacid, ':module' => 'hc_article', ':name' => $data['title']));
		if (empty($rule)) {
            $rule_data = array('uniacid' => $uniacid, 'name' => $data['title'], 'module' => 'hc_article', 'displayorder' => 0, 'status' => 1);
            pdo_insert('rule', $rule_data);
            $rid = pdo_insertid();
            $keyword_data = array('uniacid' => $uniacid, 'rid' => $rid, 'module' => 'hc_article', 'content' => $data['keyword'], 'type' => 1, 'displayorder' => 0, 'status' => 1);
            pdo_insert('rule_keyword', $keyword_data);
        } else {
            pdo_update('rule_keyword', array('content' => $data['keyword']), array('rid' => $rule['id']));
            pdo_update('rule', array('name' => $data['title']), array('id' => $rule['id']));
        }
        message('更新海报成功！', $this->createWebUrl('memberposter', array('op' => 'display')), 'success');
    }
	if (!empty($item)) {
        $data = json_decode(str_replace('&quot;', '\'', $item['data']), true);
    }
	
	include $this->template('web/memberposter');
?>