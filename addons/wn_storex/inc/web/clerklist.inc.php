<?php

defined('IN_IA') or exit('Access Denied');

global $_W, $_GPC;

load()->model('reply');
load()->model('module');
mload()->model('activity');
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
activity_get_coupon_type();
$user_permissions = pdo_getall('users_permission', array('uniacid' => $_W['uniacid'], 'type' => 'wn_storex', 'uid <>' => ''), '', 'uid');
$uids = !empty($user_permissions) && is_array($user_permissions) ? array_keys($user_permissions) : array();
$users_lists = array();
if (!empty($uids)) {
	$users_lists = pdo_getall('users', array('uid' => $uids), '', 'uid');
}
$current_module_permission = module_permission_fetch($this->module['name']);
if (!empty($current_module_permission)) {
	foreach ($current_module_permission as $key => $permission) {
		$permission_name[$permission['permission']] = $permission['title'];
	}
}
if (!empty($user_permissions)) {
	foreach ($user_permissions as $key => &$permission) {
		$permission['permission'] = explode('|', $permission['permission']);
		foreach ($permission['permission'] as $k => $val) {
			$permission['permission'][$val] = $permission_name[$val];
			unset($permission['permission'][$k]);
		}
		$permission['user_info'] = $users_lists[$key];
	}
	unset($permission);
}

$clerk_list = pdo_getall('storex_activity_clerks', array('uniacid' => $_W['uniacid']), '', 'uid');
$available_user = $user_permissions;
foreach ($available_user as $key => $value) {
	if (!empty($clerk_list[$key])) {
		unset($available_user[$key]);
	}
}
if ($op == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 30;
	$limit = 'ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ", {$psize}";
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('storex_activity_clerks')." WHERE uniacid = :uniacid ", array(':uniacid' => $_W['uniacid']));
	$list = pdo_fetchall("SELECT * FROM ".tablename('storex_activity_clerks')." WHERE uniacid = :uniacid {$limit}", array(':uniacid' => $_W['uniacid']));
	$uids = array(0);
	foreach ($list as $row) {
		if ($row['uid'] > 0) {
			$uids[] = $row['uid'];
		}
	}
	$uids = implode(',', $uids);
	$users = pdo_fetchall('SELECT username,uid FROM ' . tablename('users') . " WHERE uid IN ({$uids})", array(), 'uid');
	$pager = pagination($total, $pindex, $psize);
	$stores = pdo_getall('storex_activity_stores', array('uniacid' => $_W['uniacid']), array('id', 'business_name', 'branch_name'), 'id');
}
if ($op == 'checkname' && $_W['isajax']) {
	$username = trim($_GPC['username']);
	$uid = intval($_GPC['uid']);
	if (!empty($uid)) {
		$exist = pdo_fetch("SELECT * FROM ". tablename('users'). " WHERE uid <> :uid AND username = :username", array(':uid' => $uid, ':username' => trim($_GPC['username'])));
	} else {
		$exist = pdo_get('users', array('username' => $username));
	}
	if (empty($exist)) {
		message(error(1), '', 'ajax');
	} else {
		message(error(0), '', 'ajax');
	}
}
if ($op == 'post') {
	$uid = intval($_GPC['uid']);
	$user_info = user_single($uid);
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$clerk = pdo_get('storex_activity_clerks', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}
	if (checksubmit()) {
		$name = trim($_GPC['name']) ? trim($_GPC['name']) : message('店员名称不能为空');
		$mobile =  trim($_GPC['mobile']) ? trim($_GPC['mobile']) : message('手机号不能为空');
		$storeid =  intval($_GPC['storeid']) ? intval($_GPC['storeid']) : message('请选择所在门店');
		$password = trim($_GPC['password']);
		if (istrlen($password) < 8) {
			message('必须输入核销密码，且密码长度不得低于8位。');
		}
		$password_exist = pdo_get('storex_activity_clerks', array('uniacid' => $_W['uniacid'], 'password' => $password, 'id <>' => $id));
		if (!empty($password_exist)) {
			message('密码已存在，请重新输入密码');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'storeid' => $storeid,
			'name' => $name,
			'mobile' => $mobile,
			'openid' => trim($_GPC['openid']),
			'nickname' => trim($_GPC['nickname']),
			'uid' => $uid,
			'password' => $_GPC['password']
		);
		if (empty($_GPC['password'])) {
			unset($data['password']);
		}
		if (empty($clerk['id'])) {
			pdo_insert('storex_activity_clerks', $data);
		} else {
			pdo_update('storex_activity_clerks', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		}
		message('编辑店员资料成功', $this->createWeburl('clerklist'), 'success');
	}
	$stores = pdo_getall('storex_activity_stores', array('uniacid' => $_W['uniacid'], 'source' => COUPON_TYPE), array('id', 'business_name', 'branch_name'));
}

if ($op == 'verify') {
	if ($_W['isajax']) {
		$openid = trim($_GPC['openid']);
		$nickname = trim($_GPC['nickname']);
		if (!empty($openid)) {
			$exist = pdo_get('mc_mapping_fans', array('acid' => $_W['acid'], 'openid' => $openid), array('openid', 'nickname'));
		} else {
			$exist = pdo_get('mc_mapping_fans', array('nickname' => $nickname, 'acid' => $_W['acid']), array('openid', 'nickname'));
		}
		if (empty($exist)) {
			message(error(-1, '未找到对应的粉丝编号，请检查昵称或openid是否有效'), '', 'ajax');
		}
		message(error(0, $exist), '', 'ajax');
	}
}
if ($op == 'delete') {
	$id = intval($_GPC['id']);
	pdo_delete('storex_activity_clerks',array('id' => intval($_GPC['id']), 'uniacid' => $_W['uniacid']));
	message("删除成功",referer(),'success');
}
if ($op == 'switch') {
	$clerkid = intval($_GPC['id']);
	$clerk = pdo_get('storex_activity_clerks', array('id' => $clerkid, 'uniacid' => $_W['uniacid']));
	$user = user_single(array('uid' => $clerk['uid']));
	$cookie = array();
	$cookie['uid'] = $user['uid'];
	$cookie['lastvisit'] = $user['lastvisit'];
	$cookie['lastip'] = $user['lastip'];
	$cookie['hash'] = md5($user['password'] . $user['salt']);
	$compare = ver_compare(IMS_VERSION, '1.0');
	if ($compare == -1) {
		$session = base64_encode(json_encode($cookie));
	} else {
		$session = authcode(json_encode($cookie), 'encode');
	}
	isetcookie('__session', $session, 7 * 86400);
	header('Location:' . $this->createWeburl('clerkdesk', array('uniacid' => $clerk['uniacid'], 'op' => 'index')));
	exit;
}
include $this->template('clerklist');