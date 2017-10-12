<?php

defined('IN_IA') or exit('Access Denied');

class My_stModuleSite extends WeModuleSite {

    public function doMobileShetuanInfo() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $id = $_GPC['id'];
        $shetuan = pdo_fetch("SELECT * FROM " . tablename('my_st') . " WHERE uniacid = '{$uniacid}' AND id = '{$id}'");
//        var_dump($shetuan);
        $students_apply = pdo_fetch("SELECT * FROM " . tablename('my_st_students') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND shetuan_id = '{$id}'");
        $user_info = pdo_fetch("SELECT * FROM " . tablename('my_st_msg') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        if (empty($user_info)) {
            message('请先填写信息', $this->createMobileUrl('UserMsg', '', 'error'));
            exit;
        } $school_lunbo = pdo_fetchall("SELECT * FROM " . tablename('my_st_school_lunbo') . " WHERE uniacid = '{$uniacid}' AND school_id = '{$user_info['school']}' ORDER BY createtime DESC");
        $shetuan_activity = pdo_fetchall("SELECT a.id,a.starttime,a.updatetime,a.activity_name,a.activity_pic,a.activity_remark,a.info,a.createtime,b.name FROM " . tablename('my_st_activity') . " a LEFT JOIN " . tablename('my_st') . " b ON a.shetuan_id = b.id" . " WHERE a.uniacid = '{$uniacid}' AND b.id = '{$id}' ORDER BY a.sort DESC,a.id DESC");
        include $this->template('shetuan');
    }

    public function doMobileActivityInfo() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $id = $_GPC['id'];
        $shetuan = pdo_fetch("SELECT * FROM " . tablename('my_st_activity') . " WHERE uniacid = '{$uniacid}' AND id = '{$id}'");
        //当前用户参加的社团信息。
        $students = pdo_fetch("SELECT * FROM " . tablename('my_st_students') . " WHERE uniacid = '{$uniacid}' AND shetuan_id = '{$shetuan['shetuan_id']}' AND openid = '{$openid}' AND status = 1");
        
        //单个社团信息
        $msg_shetuan = pdo_fetch("SELECT * FROM " . tablename('my_st') . " WHERE uniacid = '{$uniacid}' AND id = '{$shetuan['shetuan_id']}'");
        //单个用户信息
        $user_info = pdo_fetch("SELECT * FROM " . tablename('my_st_msg') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        if (empty($user_info)) {
            message('请先填写信息', $this->createMobileUrl('UserMsg', '', 'error'));
            exit;
        }
        $school_lunbo = pdo_fetchall("SELECT * FROM " . tablename('my_st_school_lunbo') . " WHERE uniacid = '{$uniacid}' AND school_id = '{$user_info['school']}' ORDER BY createtime DESC");
        $wx = pdo_fetch("SELECT * FROM " . tablename('my_st_activity_qiandao') . " WHERE uniacid = '{$uniacid}' AND activity_id = '{$id}' AND openid = '{$openid}'");
        $my_shetuan = pdo_fetchall("SELECT a.activity_id,a.createtime,a.status,b.name,b.mobile,b.nicheng,b.touxiang FROM " . tablename('my_st_activity_qiandao') . " a LEFT JOIN " . tablename('my_st_msg') . " b ON a.openid = b.openid" . " WHERE a.uniacid = '{$uniacid}' AND a.activity_id = '{$id}' ORDER BY a.createtime DESC");
        include $this->template('activity');
    }

    /**
     * 
     * @global type $_GPC
     * @global type $_W手机入团申请
     */
    public function doMobileApply() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $shetuan_id = $_GPC['shetuan_id'];
        $data['uniacid'] = $uniacid;
//        $data['uniacid'] = $uniacid;
        $data['openid'] = $openid;
        $data['shetuan_id'] = $shetuan_id;
        $data['createtime'] = time();
        //每个成员最多申请两个社团。
        $students_apply = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('my_st_students') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        if ($students_apply >= 2) {
            echo json_encode(array(
                'code' => 200,
                'message' => '每人最多申请两个社团',
                'data' => array()
            ));
            exit();
        }
        $result = pdo_insert('my_st_students', $data);
        if ($result) {
            echo json_encode(array(
                'code' => 200,
                'message' => '申请递交成功，请耐心等待管理员审核',
                'data' => array()
            ));
        } else {
            echo json_encode(array(
                'code' => 400,
                'message' => '申请失败，请联系我们',
                'data' => array()
            ));
        }
        exit();
    }

    public function doMobileQiandao() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $activity_id = $_GPC['activity_id'];
        $activity = pdo_fetch("SELECT * FROM " . tablename('my_st_activity') . " WHERE uniacid = '{$uniacid}' AND id = '{$activity_id}'");
        if (time() > $activity['updatetime']) {
            print_r("活动已结束！###OK");
            exit;
        } elseif (time() < $activity['starttime']) {
            print_r("活动未开始,请耐心等待！###OK");
            exit;
        } else {
            $activity_qiandao = pdo_fetch("SELECT * FROM " . tablename('my_st_activity_qiandao') . " WHERE uniacid = '{$uniacid}' AND activity_id = '{$activity_id}' AND openid = '{$openid}'");
            if ($activity_qiandao) {
                print_r("您已报名,无需重复报名！###OK");
                exit;
            } else {
                load()->model('mc');
                $user_id = $_W['member']['uid'];
                $siteroot = $_W['siteroot'];
                $acid = $_W['acid'];
                include IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
                $url = $siteroot . 'app/index.php?i=' . $uniacid . '&j=' . $acid . '&c=entry&activity_id=' . $activity_id . '&student_openid=' . $openid . '&user_id=' . $user_id . '&do=Check&m=my_st';
                $timewx = time();
                $root_png = '../addons/my_st/template/mobile/pic/' . $timewx . '.png';
                QRcode::png($url, $root_png, 1, 2, 2);
                if ($root_png) {
                    $data['wx_link'] = $timewx . '.png';
                } 
                $data['openid'] = $openid;
                $data['uniacid'] = $uniacid;
                $data['activity_id'] = $activity_id;
                $data['createtime'] = time();
                $result = pdo_insert('my_st_activity_qiandao', $data);
                if ($result) {
                    print_r("报名成功，稍后将在下方生成专属签到二维码。###" . $timewx . '.png');
                    exit;
                }
            }
        }
    }

    public function doMobileCheck() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $activity_id = $_GPC['activity_id'];
        $student_openid = $_GPC['student_openid'];
        $user = pdo_fetch("SELECT * FROM " . tablename('my_st_msg') . " WHERE uniacid='{$uniacid}' AND openid = '{$openid}'");
        $user_yonghu = pdo_fetch("SELECT * FROM " . tablename('my_st_msg') . " WHERE uniacid='{$uniacid}' AND openid = '{$student_openid}'");
        $activity = pdo_fetch("SELECT * FROM " . tablename('my_st_activity') . " WHERE uniacid='{$uniacid}' AND id = '{$activity_id}'");
        $shetuan = pdo_fetch("SELECT * FROM " . tablename('my_st') . " WHERE uniacid='{$uniacid}' AND id = '{$activity['shetuan_id']}'");
//        var_dump($openid,$shetuan);die();
        if ($openid != $shetuan['admin']) {
            message('抱歉您不是该社团的管理员,无法核销!', $this->createMobileUrl('Index', '', 'error'));
            exit;
        }
        $check = pdo_fetch("SELECT * FROM " . tablename('my_st_activity_qiandao') . " WHERE uniacid='{$uniacid}' AND activity_id = '{$activity_id}' AND status = 1 AND openid = '{$student_openid}'");
        if ($check) {
            message('该二维码已被核销,无需重复核销!', $this->createMobileUrl('Index', '', 'error'));
            exit;
        } else {
            $data['status'] = 1;
            $result = pdo_update('my_st_activity_qiandao', $data, array('activity_id' => $activity_id, 'uniacid' => $uniacid, 'openid' => $student_openid));
            if ($activity['classify'] == 1) {
                $data_msg['xuefen'] = $user_yonghu['xuefen'] + $activity['how_much'];
                $show = '学分';
            } else {
                load()->model('mc');
                $user_id = $_GPC['user_id'];
                $howmuch = $activity['how_much'];
                $log = array(0 => $user_id, 1 => '增加积分', 2 => 'my_st', 3 => $user_id, 4 => '', 5 => 2,);
                $result = mc_credit_update($user_id, 'credit1', $howmuch, $log);
                $data_msg['jifen'] = $user_yonghu['jifen'] + $activity['how_much'];
                $show = '社团考核分';
            }
            $rs = pdo_update('my_st_msg', $data_msg, array('uniacid' => $uniacid, 'openid' => $student_openid));
            if ($rs) {
                message('签到成功!', $this->createMobileUrl('Index', '', 'error'));
                exit;
                $templateid = $this->module['config']['template'];
                $url = '';
                $data = array('first' => array('value' => "您已成功签到此次活动,获得" . $activity['how_much'] . "个." . $show . "\n", 'color' => "#68228B",), 'keyword1' => array('value' => $user['name'], 'color' => "#FF0000",), 'keyword2' => array('value' => date('Y-m-d H:m'), 'color' => "#FF0000",), 'keyword3' => array('value' => "我们的正常工作时间是8:00-17:00\n", 'color' => "#FF0000",), 'remark' => array('value' => "如有问题请致电或直接在微信留言，我们将第一时间为您服务！", 'color' => "#ABABAB",),);
                if ($_W['account']['level'] == ACCOUNT_SERVICE_VERIFY) {
                    $account_api = WeAccount::create();
                    $status = $account_api->sendTplNotice($openid, $templateid, $data, $url);
                    $status2 = $account_api->sendTplNotice($student_openid, $templateid, $data, $url);
                    if ($status && $status2) {
                        message('核销成功', $this->createMobileUrl('Index', '', 'error'));
                        exit;
                    }
                }
            }
        }
    }

    public function doMobileMyShetuan() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $my_shetuan = pdo_fetchall("SELECT a.shetuan_id,a.createtime,a.status,b.name,b.jianjie,b.pic,b.students FROM " . tablename('my_st_students') . " a LEFT JOIN " . tablename('my_st') . " b ON a.shetuan_id = b.id" . " WHERE a.uniacid = '{$uniacid}' AND a.openid = '{$openid}' ORDER BY a.createtime DESC");
        include $this->template('my_shetuan');
    }

    public function doMobileIndex() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        load()->func('tpl');
        $user_info = pdo_fetch("SELECT * FROM " . tablename('my_st_msg') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        if (empty($user_info)) {
            message('请先填写信息', $this->createMobileUrl('UserMsg', '', 'error'));
            exit;
        }
        $title = $this->module['title'];
        $logo = $this->module['logo'];
        $school_lunbo = pdo_fetchall("SELECT * FROM " . tablename('my_st_school_lunbo') . " WHERE uniacid = '{$uniacid}' ORDER BY createtime DESC");
        $school_wenzi = pdo_fetchall("SELECT * FROM " . tablename('my_st_school_wenzi') . " WHERE uniacid = '{$uniacid}' ORDER BY createtime DESC");
        $shetuan = pdo_fetchall("SELECT * FROM " . tablename('my_st') . " WHERE uniacid = '{$uniacid}' ORDER BY sort DESC");
        $shetuan_activity = pdo_fetchall("SELECT a.id,a.starttime,a.updatetime,a.activity_name,a.activity_pic,a.activity_remark,a.info,a.createtime,b.name FROM " . tablename('my_st_activity') . " a LEFT JOIN " . tablename('my_st') . " b ON a.shetuan_id = b.id" . " WHERE a.uniacid = '{$uniacid}' ORDER BY a.sort DESC,a.id DESC");
        include $this->template('index');
    }

    public function doMobileUserCenter() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $msg = mc_oauth_userinfo();
        $pic = $msg['avatar'];
        $name = $msg['nickname'];
        $my_shetuan = pdo_fetchall("SELECT a.shetuan_id,a.createtime,a.status,b.name,b.jianjie,b.pic,b.students FROM " . tablename('my_st_students') . " a LEFT JOIN " . tablename('my_st') . " b ON a.shetuan_id = b.id" . " WHERE a.uniacid = '{$uniacid}' AND a.openid = '{$openid}' ORDER BY a.createtime DESC");
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_students') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}' AND status = 1");
        $students_info = pdo_fetch("SELECT * FROM " . tablename('my_st_msg') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        $activity_info = pdo_fetchall("SELECT a.activity_id,a.createtime , a.status, a.wx_link ,b.activity_name,b.shetuan_id FROM " . tablename('my_st_activity_qiandao') . " a LEFT JOIN " . tablename('my_st_activity') . " b ON a.activity_id = b.id " . " WHERE a.uniacid = '{$uniacid}' AND a.openid = '{$openid}' ORDER BY a.createtime DESC");
        foreach ($activity_info as $k => $item) {
            $shetuan = pdo_fetch("SELECT * FROM " . tablename('my_st') . " WHERE uniacid = '{$uniacid}' AND id = '{$item['shetuan_id']}'");
            $activity_info[$k]['st'] = $shetuan['name'];
        }
        $total_activity = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_activity_qiandao') . " WHERE uniacid = '{$uniacid}' AND openid = '{$openid}'");
        include $this->template('user_center');
    }

    public function doMobileUserMsg() {
        global $_GPC;
        global $_W;
        include $this->template('msg_input');
    }

    public function doMobileUserMsgAjax() {
        global $_GPC;
        global $_W;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $msg = mc_oauth_userinfo();
        $pic = $msg['avatar'];
        $name = $msg['nickname'];
        $data['nicheng'] = $name;
        $data['touxiang'] = $pic;
        $data['name'] = $_GPC['xingming'];
        $data['wx'] = $_GPC['answer'];
        $data['mobile'] = $_GPC['mobile'];
        $data['openid'] = $openid;
        $data['uniacid'] = $uniacid;
        $data['createtime'] = time();
        //判断是否已经添加过用户信息。
        $user = pdo_get('my_st_msg', array('uniacid' => $uniacid,'open_id'=>$openid));
        if($user){
            message('信息已经填写过了，请重新进入', $this->createMobileUrl('Index'));
        }else{
            $result = pdo_insert('my_st_msg', $data);
            if ($result) {
                print_r(1);
                exit;
            } else {
                print_r(2);
                exit;
            }
        }
        
    }

    public function doWebShetuan() {
        global $_W;
        global $_GPC;
        load()->func('tpl');
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        if ($op == 'edit') {
            $data['uniacid'] = $uniacid;
            $data['name'] = $_GPC['name'];
            $data['jianjie'] = $_GPC['jianjie'];
            $data['pic'] = $_GPC['pic'];
            $data['info'] = $_GPC['info'];
            $data['sort'] = $_GPC['sort'];
            $data['admin'] = $_GPC['admin'];
            if (checksubmit('submit')) {
                if (empty($id)) {
                    $data['createtime'] = time();
                    $res = pdo_insert('my_st', $data);
                    if ($res) {
                        message('新增成功', $this->createWebUrl('Shetuan', array('op' => 'edit')), 'success');
                    }
                } else {
                    $data['updatetime'] = time();
                    $result = pdo_update('my_st', $data, array('id' => $id));
                    if (!empty($result)) {
                        message('修改成功', $this->createWebUrl('Shetuan', array('op' => 'display')), 'success');
                    }
                }
            } else {
                unset($info);
                $info = pdo_get('my_st', array('id' => $id));
                include $this->template('shetuan');
                exit;
            }
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $edit_admin = pdo_fetchall(" SELECT * FROM " . tablename('my_st') . " WHERE uniacid = '{$uniacid}' ORDER BY sort DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('shetuan');
    }

    public function doWebshetuanDelete() {
        global $_W;
        global $_GPC;
        $del = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');

        $isEx = pdo_fetch("select * from " . tablename('my_st_activity') . " WHERE `shetuan_id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
        if ($isEx) {
            message('社团下有活动，需先删除活动', $this->createWebUrl('Shetuan', array('op' => 'display'), 'error'));
        } else {
            $result = pdo_query("DELETE FROM " . tablename('my_st') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
            if (!empty($result)) {
                message('删除成功', $this->createWebUrl('Shetuan', array('op' => 'display'), 'success'));
            }
        }
    }

    public function doWebActivity() {
        global $_W;
        global $_GPC;
        load()->func('tpl');
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        $shetuan_id = $_GPC['shetuan_id'];
        $info_school = pdo_fetch("SELECT * FROM " . tablename('my_st') . " WHERE uniacid = '{$uniacid}' and id = '{$shetuan_id}'");
        if ($op == 'edit') {
            $data['uniacid'] = $uniacid;
            $data['activity_name'] = $_GPC['activity_name'];
            $data['activity_remark'] = $_GPC['activity_remark'];
            $data['activity_pic'] = $_GPC['activity_pic'];
            $data['classify'] = $_GPC['classify'];
            $data['how_much'] = $_GPC['how_much'];
            $data['info'] = $_GPC['info'];
            $data['shetuan_id'] = $shetuan_id;
            $timelimit = $_GPC['birth'];
            $timebegin = $timelimit['start'];
            $timeover = $timelimit['end'];
            $data['starttime'] = strtotime($timebegin);
            $data['updatetime'] = strtotime($timeover);
            if (checksubmit('submit')) {
                if (empty($id)) {
                    $data['createtime'] = time();
                    $res = pdo_insert('my_st_activity', $data);
                    if ($res) {
                        message('新增成功', $this->createWebUrl('Activity', array('op' => 'edit')), 'success');
                    }
                } else {
                    $result = pdo_update('my_st_activity', $data, array('id' => $id));
                    if (!empty($result)) {
                        message('修改成功', $this->createWebUrl('Activity', array('op' => 'edit')), 'success');
                    }
                }
            } else {
                unset($info);
                $info = pdo_get('my_st_activity', array('id' => $id));
                if (empty($info)) {
                    $info['starttime'] = time();
                    $info['updatetime'] = time() + 2592000;
                } include $this->template('activity');
                exit;
            }
        } $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $edit_admin = pdo_fetchall(" SELECT * FROM " . tablename('my_st_activity') . " WHERE uniacid = '{$uniacid}' AND shetuan_id = '{$shetuan_id}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_activity') . " WHERE uniacid = '{$uniacid}' AND shetuan_id = '{$shetuan_id}'");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('activity');
    }

    public function doWebActivityDelete() {
        global $_W;
        global $_GPC;
        $del = $_GPC['id'];
        $shetuan_id = $_GPC['shetuan_id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');
        $result = pdo_query("DELETE FROM " . tablename('my_st_activity') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
        if (!empty($result)) {
            message('删除成功', $this->createWebUrl('Activity', array('op' => 'display', 'shetuan_id' => $shetuan_id), 'success'));
        } include $this->template('activity');
    }

    public function doWebStudents() {
        global $_W;
        global $_GPC;
        $uniacid = $_W['uniacid'];
        $shetuan_id = $_GPC['shetuan_id'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $student_info = pdo_fetchall("SELECT a.id,a.shetuan_id , a.createtime, a.status ,b.name,b.wx,b.mobile  FROM " . tablename('my_st_students') . " a LEFT JOIN " . tablename('my_st_msg') . " b ON a.openid = b.openid " . " WHERE a.uniacid = '{$uniacid}' AND a.shetuan_id = '{$shetuan_id}' ORDER BY a.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_students') . " WHERE uniacid = '{$uniacid}' AND shetuan_id = '{$shetuan_id}'");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('students');
    }

    public function doWebCheckStudents() {
        global $_W;
        global $_GPC;
        $uniacid = $_W['uniacid'];
        $shetuan_id = $_GPC['shetuan_id'];
        $student_id = $_GPC['student_id'];
        $data['status'] = 1;
        $result = pdo_update('my_st_students', $data, array('id' => $student_id));
        if ($result) {
            $number = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_students') . " WHERE uniacid = '{$uniacid}' AND shetuan_id = '{$shetuan_id}'");
            $data_shetuan['students'] = $number;
            $result2 = pdo_update('my_st', $data_shetuan, array('id' => $shetuan_id));
            message('审核通过', $this->createWebUrl('Students', array('shetuan_id' => $shetuan_id), 'success'));
        }
    }

    public function doWebQiandao() {
        global $_W;
        global $_GPC;
        $uniacid = $_W['uniacid'];
        $activity_id = $_GPC['activity_id'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $student_info_qiandao = pdo_fetchall("SELECT a.id,a.shetuan_id , a.activity_id, a.status ,a.createtime,b.name,b.wx,b.mobile  FROM " . tablename('my_st_activity_qiandao') . " a LEFT JOIN " . tablename('my_st_msg') . " b ON a.openid = b.openid " . " WHERE a.uniacid = '{$uniacid}' AND a.activity_id = '{$activity_id}' ORDER BY a.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_activity_qiandao') . " WHERE uniacid = '{$uniacid}' AND activity_id = '{$activity_id}'");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('qiandao');
    }

    public function doWebCheckQiandao() {
        global $_W;
        global $_GPC;
        $uniacid = $_W['uniacid'];
        $qiandao_id = $_GPC['qiandao_id'];
        $activity_id = $_GPC['activity_id'];
        $data['status'] = 1;
        $result = pdo_update('my_st_activity_qiandao', $data, array('id' => $qiandao_id));
        message('确认成功', $this->createWebUrl('Qiandao', array('activity_id' => $activity_id), 'success'));
    }

    public function doWebStudentsDelete() {
        global $_W;
        global $_GPC;
        $del = $_GPC['id'];
        $shetuan_id = $_GPC['shetuan_id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');
        $result = pdo_query("DELETE FROM " . tablename('my_st_students') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
        if (!empty($result)) {
            message('删除成功', $this->createWebUrl('Students', array('op' => 'display', 'shetuan_id' => $shetuan_id), 'success'));
        } 
        include $this->template('activity');
    }

    public function doWebLunbo() {
        global $_W;
        global $_GPC;
        load()->func('tpl');
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        if ($op == 'edit') {
            $data['uniacid'] = $uniacid;
            $data['pic'] = $_GPC['pic'];
            $data['createtime'] = time();
            if (checksubmit('submit')) {
                if (empty($id)) {
                    $res = pdo_insert('my_st_school_lunbo', $data);
                    if ($res) {
                        message('新增成功', $this->doWebUrl('Lunbo', array('op' => 'edit')), 'success');
                    }
                } else {
                    $result = pdo_update('my_st_school_lunbo', $data, array('id' => $id));
                    if (!empty($result)) {
                        message('修改成功', $this->doWebUrl('Lunbo', array('op' => 'display')), 'success');
                    }
                }
            } else {
                unset($info);
                $info = pdo_get('my_st_school_lunbo', array('id' => $id));
                include $this->template('lunbo');
                exit;
            }
        } $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $edit_admin = pdo_fetchall(" SELECT * FROM " . tablename('my_st_school_lunbo') . " WHERE uniacid = '{$uniacid}' ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_school_lunbo') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('lunbo');
    }

    public function doWebLunboDelete() {
        global $_W;
        global $_GPC;
        $del = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');
        $result = pdo_query("DELETE FROM " . tablename('my_st_school_lunbo') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
        if (!empty($result)) {
            message('删除成功', $this->createWebUrl('Lunbo', array('op' => 'display'), 'success'));
        }
    }

    public function doWebWenzi() {
        global $_W;
        global $_GPC;
        load()->func('tpl');
        $op = $_GPC['op'] ? $_GPC['op'] : 'display';
        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        if ($op == 'edit') {
            $data['uniacid'] = $uniacid;
            $data['wenzi'] = $_GPC['wenzi'];
            $data['wenzi_title'] = $_GPC['wenzi_title'];
            $data['link'] = $_GPC['link'];
            $data['pic'] = $_GPC['pic'];
            if (checksubmit('submit')) {
                if (empty($id)) {
                    $data['createtime'] = time();
                    $res = pdo_insert('my_st_school_wenzi', $data);
                    $id_result = pdo_insertid();
                    if ($res) {
                        message('新增成功', $this->doWebUrl('Wenzi', array('op' => 'edit')), 'success');
                    }
                } else {
                    $data['updatetime'] = time();
                    $result = pdo_update('my_st_school_wenzi', $data, array('id' => $id));
                    if (!empty($result)) {
                        message('修改成功', $this->doWebUrl('Wenzi', array('op' => 'display')), 'success');
                    }
                }
            } else {
                unset($info);
                $info = pdo_get('my_st_school_wenzi', array('id' => $id));
                include $this->template('wenzi');
                exit;
            }
        } $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $edit_admin = pdo_fetchall(" SELECT * FROM " . tablename('my_st_school_wenzi') . " WHERE uniacid = '{$uniacid}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_school_wenzi') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);
        include $this->template('wenzi');
    }

    public function doWebWenziDelete() {
        global $_W;
        global $_GPC;
        $del = $_GPC['id'];
        $school_id = $_GPC['school_id'];
        $uniacid = $_W['uniacid'];
        load()->func('tpl');
        $result = pdo_query("DELETE FROM " . tablename('my_st_school_wenzi') . " WHERE `id` = :id AND `uniacid`=:uniacid", array(':id' => $del, ':uniacid' => $uniacid));
        if (!empty($result)) {
            message('删除成功', $this->createWebUrl('Wenzi', array('op' => 'display', 'school_id' => $school_id), 'success'));
        }
    }

    public function doWebUser() {
        global $_W;
        global $_GPC;
        load()->func('tpl');
        $uniacid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('my_st_msg') . " WHERE uniacid = '{$uniacid}'");
        $pager = pagination($total, $pindex, $psize);
        $edit_admin = pdo_fetchall(" SELECT * FROM " . tablename('my_st_msg') . " WHERE uniacid = '{$uniacid}' ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        include $this->template('student_info');
    }

    public function doMobileRule() {
        global $_W;
        global $_GPC;
        load()->func('tpl');
        $uniacid = $_W['uniacid'];
        $rule = $this->module['config']['rule'];
        include $this->template('rule');
    }

}
