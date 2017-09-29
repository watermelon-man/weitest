<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('user');

$dos = array('display', 'check_display', 'check_pass', 'recycle_display', 'recycle_delete','recycle_restore', 'recycle', 'vice_founder');
$do = in_array($do, $dos) ? $do: 'display';

$_W['page']['title'] = '用户列表 - 用户管理';
$founders = explode(',', $_W['config']['setting']['founder']);

if (in_array($do, array('display', 'recycle_display', 'check_display', 'vice_founder'))) {
	switch ($do) {
		case 'check_display':
			uni_user_permission_check('system_user_check');
			$condition = ' WHERE u.status = 1 ';
			break;
		case 'recycle_display':
			uni_user_permission_check('system_user_recycle');
			$condition = ' WHERE u.status = 3 ';
			break;
		case 'vice_founder':
			$condition = ' WHERE u.founder_groupid = ' . ACCOUNT_MANAGE_GROUP_VICE_FOUNDER;
			break;
		default:
			uni_user_permission_check('system_user');
			$condition = ' WHERE u.status = 2 AND u.founder_groupid = 0';
			break;
	}
	if (user_is_vice_founder()) {
		$condition .= ' AND u.owner_uid = ' . $_W['uid'];
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$params = array();
	if (!empty($_GPC['username'])) {
		$condition .= " AND u.username LIKE :username";
		$params[':username'] = "%{$_GPC['username']}%";
	}
	$sql = 'SELECT u.*, p.avatar FROM ' . tablename('users') .' AS u LEFT JOIN ' . tablename('users_profile') . ' AS p ON u.uid = p.uid '. $condition . " LIMIT " . ($pindex - 1) * $psize .',' .$psize;
	$users = pdo_fetchall($sql, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('users') .' AS u '. $condition, $params);
	$pager = pagination($total, $pindex, $psize);
	$system_module_num = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('modules') . "WHERE type = :type AND issystem = :issystem", array(':type' => 'system',':issystem' => 1));
	foreach ($users as &$user) {
		$user['avatar'] = !empty($user['avatar']) ? $user['avatar'] : './resource/images/nopic-user.png';
		if (empty($user['endtime'])) {
			$user['endtime'] = '永久有效';
		} else {
			if ($user['endtime'] <= TIMESTAMP) {
				$user['endtime'] = '服务已到期';
			} else {
				$user['endtime'] = date('Y-m-d', $user['endtime']);
			}
		}

		$user['founder'] = user_is_founder($user['uid']);
		$user['uniacid_num'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('uni_account_users')." WHERE uid = :uid", array(':uid' => $user['uid']));

		$user['module_num'] =array();
		$group = pdo_get('users_group', array('id' => $user['groupid']));
		$user_role = user_is_founder($user['uid']);
		if ($user_role) {
			$user['maxaccount'] = '不限';
		}
		if (!empty($group)) {
			if (empty($user_role)) {
				$user['maxaccount'] = $group['maxaccount'];
			}
			$user['groupname'] = $group['name'];
			$package = iunserializer($group['package']);
			$group['package'] = uni_groups($package);
			foreach ($group['package'] as $modules) {
				if (is_array($modules['modules'])) {
					foreach ($modules['modules'] as  $module) {
						$user['module_num'][] = $module['name'];
					}
				}
			}
		}

		$user['module_num'] = array_unique($user['module_num']);
		$user['module_nums'] = count($user['module_num']) + $system_module_num;
	}
	unset($user);
	$usergroups = user_group();
	template('user/display');
}

if (in_array($do, array('recycle', 'recycle_delete', 'recycle_restore', 'check_pass'))) {
	switch ($do) {
		case 'check_pass':
			uni_user_permission_check('system_user_check');
			break;
		case 'recycle':
		case 'recycle_delete':
		case 'recycle_restore':
			uni_user_permission_check('system_user_recycle');
			break;
	}
	$uid = intval($_GPC['uid']);
	$uid_user = user_single($uid);
	if (in_array($uid, $founders)) {
		itoast('访问错误, 无法操作站长.', url('user/display'), 'error');
	}
	if (empty($uid_user)) {
		exit('未指定用户,无法删除.');
	}
	switch ($do) {
		case 'check_pass':
			$data = array('status' => 2);
			pdo_update('users', $data , array('uid' => $uid));
			itoast('更新成功！', referer(), 'success');
			break;
		case 'recycle':			user_delete($uid, true);
			itoast('更新成功！', referer(), 'success');
			break;
		case 'recycle_delete':			user_delete($uid);
			itoast('删除成功！', referer(), 'success');
			break;
		case 'recycle_restore':
			$data = array('status' => 2);
			pdo_update('users', $data , array('uid' => $uid));
			itoast('启用成功！', referer(), 'success');
			break;
	}
}