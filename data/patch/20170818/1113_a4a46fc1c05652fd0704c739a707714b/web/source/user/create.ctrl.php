<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('user');

uni_user_permission_check('system_user_post');
$_W['page']['title'] = '添加用户 - 用户管理';
$state = uni_permission($_W['uid']);
if ($state != ACCOUNT_MANAGE_NAME_FOUNDER) {
	itoast('没有操作权限！', referer(), 'error');
}

if (checksubmit()) {
	$username = trim($_GPC['username']);
	$vice_founder_name = trim($_GPC['vice_founder_name']);
	$group_id = intval($_GPC['groupid']);
	if (!preg_match(REGULAR_USERNAME, $username)) {
		itoast('必须输入用户名，格式为 3-15 位字符，可以包括汉字、字母（不区分大小写）、数字、下划线和句点。', '', '');
	}
	if (user_check(array('username' => $username))) {
		itoast('非常抱歉，此用户名已经被注册，你需要更换注册名称！', '', '');
	}
	if (istrlen($_GPC['password']) < 8) {
		itoast('必须输入密码，且密码长度不得低于8位。', '', '');
	}
	if (trim($_GPC['password']) !== trim($_GPC['repassword'])) {
		itoast('两次密码不一致！', '', '');
	}
	if (!intval($_GPC['groupid'])) {
		itoast('请选择所属用户组', '', '');
	}
	if ($group_id > 0) {
		$group = user_group_detail_info(intval($_GPC['groupid']));
		if (empty($group)) {
			itoast('会员组不存在', '', '');
		}
	}

	$timelimit = intval($group['timelimit']);
	$timeadd = 0;
	if ($timelimit > 0) {
		$timeadd = strtotime($timelimit . ' days');
	}
	$data = array(
		'username' => $username,
		'password' => trim($_GPC['password']),
		'remark' => $_GPC['remark'],
		'groupid' => $group_id,
		'starttime' => TIMESTAMP,
		'endtime' => $timeadd,
		'founder_groupid' => intval($_GPC['founder_groupid'])
	);
	$data['owner_uid'] = user_get_uid_byname($vice_founder_name);
	if (user_is_vice_founder()) {
		$data['owner_uid'] = $_W['uid'];
	}
	$uid = user_register($data);
	if ($uid > 0) {
		unset($data);
		itoast('增加成功！', url('user/edit/' . $do, array('uid' => $uid)), 'success');
	}
	itoast('增加失败，请稍候重试或联系网站管理员解决！', '', '');
}
$groups = user_group();
template('user/create');