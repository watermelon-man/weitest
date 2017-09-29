<?php
global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$id = intval($_GPC['id']);
$uniacid = $_W['uniacid'];
$credit1 = $_W['member']['credit1'];
//
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MicroMessenger') === false) {

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: {$this->createMobileUrl('other',array('type'=>1,'id'=>$rid))}");
        exit();
    }

    //网页授权借用开始

    load()->model('account');
    $_W['account'] = account_fetch($_W['acid']);
    $cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
    $cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
    if ($_W['account']['level'] != 4) {
        $from_user = $cookie['openid'];
        $avatar = $cookie['avatar'];
        $nickname = $cookie['nickname'];
    }else{
        $from_user = $_W['fans']['from_user'];
        $avatar = $_W['fans']['tag']['avatar'];
        $nickname = $_W['fans']['nickname'];
    }

    $code = $_GPC['code'];
    $urltype = '';
    if (empty($from_user) || empty($avatar) || empty($nickname)) {
        if (!is_array($cookie) || !isset($cookie['avatar']) || !isset($cookie['openid']) || !isset($cookie['nickname'])) {
            $userinfo = $this->get_UserInfo($rid, $code, $urltype);
            $nickname = $userinfo['nickname'];
            $avatar = $userinfo['headimgurl'];
            $from_user = $userinfo['openid'];
        } else {
            $avatar = $cookie['avatar'];
            $nickname = $cookie['nickname'];
            $from_user = $cookie['openid'];
        }
    }

    //网页授权借用结束




    $page_from_user = base64_encode(authcode($from_user, 'ENCODE'));


if (empty($rid)||empty($id)) {
	message('抱歉，参数错误！', '', 'error');//调试代码
}
$op = $_GPC['op'];

if ($op == '2') {
    $orderId = intval($_GPC['orderid']);

    if (empty($orderId)) {
        $data = array(
            'success' => 100,
            'msg' => '寄存订单不存在或已经被删除',
        );

        echo json_encode($data);
        exit;

    }else{
        $dele_opder = pdo_fetch("select id from " . tablename('haoman_dpm_deposit') . " where id=:id and rid = :rid and uniacid = :uniacid and status=3  ORDER BY id desc",array(':rid'=>$rid,':id'=>$orderId,'uniacid'=>$uniacid));
       if($dele_opder){

           pdo_update('haoman_dpm_deposit', array('status'=>4,'closetime'=>time()), array('id'=>$dele_opder['id']));

//           pdo_delete('haoman_dpm_pay_order', array('id' => $dele_opder['id']));

           $data = array(
               'success' => 1,
               'msg' => '寄存订单已经成功删除',
           );

           echo json_encode($data);
           exit;
       }else{
           $data = array(
               'success' => 100,
               'msg' => '寄存订单不存在或已经被删除了',
           );

           echo json_encode($data);
           exit;
       }

    }

}elseif ($op==1){
    $orderId = intval($_GPC['orderid']);
    $comnet = $_GPC['comnet'];

    if(empty($orderId)){
        $data = array(
            'success' => 100,
            'msg' => '寄存订单不存在或已经被删除',
        );

        echo json_encode($data);
        exit;
    }
    if(empty($comnet)){
        $data = array(
            'success' => 100,
            'msg' => '位置不能为空',
        );

        echo json_encode($data);
        exit;
    }

    $dele_opder = pdo_fetch("select id from " . tablename('haoman_dpm_deposit') . " where id=:id and rid = :rid and uniacid = :uniacid and status=1  ORDER BY id desc",array(':rid'=>$rid,':id'=>$orderId,'uniacid'=>$uniacid));
    if($dele_opder){
        pdo_update('haoman_dpm_deposit', array('status'=>2,'closetime'=>time(),'address'=>$comnet), array('id'=>$dele_opder['id']));

//           pdo_delete('haoman_dpm_pay_order', array('id' => $dele_opder['id']));

        $data = array(
            'success' => 1,
            'msg' => '寄存取回成功',
        );

        echo json_encode($data);
        exit;
    }else{
        $data = array(
            'success' => 100,
            'msg' => '寄存取回失败',
        );

        echo json_encode($data);
        exit;
    }


}elseif ($op==3){
    //送达
    $orderId = intval($_GPC['orderid']);

    if (empty($orderId)) {
        $data = array(
            'success' => 100,
            'msg' => '订单不存在或已经被删除',
        );

        echo json_encode($data);
        exit;

    }else{
        $dele_opder = pdo_fetch("select id from " . tablename('haoman_dpm_deposit') . " where id=:id and rid = :rid and uniacid = :uniacid and status=2  ORDER BY id desc",array(':rid'=>$rid,':id'=>$orderId,'uniacid'=>$uniacid));
        if($dele_opder){

            pdo_update('haoman_dpm_deposit', array('status'=>3,'closetime'=>time(),'waiter2'=>'用户确认'), array('id'=>$dele_opder['id']));

//           pdo_delete('haoman_dpm_pay_order', array('id' => $dele_opder['id']));

            $data = array(
                'success' => 1,
                'msg' => '确认成功！',
            );

            echo json_encode($data);
            exit;
        }else{
            $data = array(
                'success' => 100,
                'msg' => '寄存订单不存在或已经被删除了',
            );

            echo json_encode($data);
            exit;
        }

    }

}


$reply = pdo_fetch("select * from " . tablename('haoman_dpm_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$yyy = pdo_fetch("select isyyy from " . tablename('haoman_dpm_yyyreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$vote = pdo_fetch("select vote_status from " . tablename('haoman_dpm_newvote_set') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$photo = pdo_fetch("select * from " . tablename('haoman_dpm_photo_setting') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$punishment = pdo_fetch("select is_punishment from " . tablename('haoman_dpm_punishment') . " where rid = :rid and uniacid=:uniacid ", array(':rid'=>$rid,':uniacid'=>$_W['uniacid']));
$custom = pdo_fetchall("select * from " . tablename('haoman_dpm_custom') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$shop = pdo_fetch("select shop_status from " . tablename('haoman_dpm_shop_setting') . " where rid = :rid and uniacid=:uniacid ", array(':rid'=>$rid,':uniacid'=>$_W['uniacid']));

if(ISCUSTOM == 1 && CUSTOM_VERSION == 'DS'){
    $dsreply = pdo_fetch("select * from " . tablename('haoman_dpm_ds_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
}
if(ISCUSTOM == 1 && CUSTOM_VERSION == 'ZNL'){
    $znlreply = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_znl_reply')." WHERE rid='".$rid."' " );
}

$fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where id =:id and  rid = :rid and from_user=:from_user",array(':id'=>$id,':rid'=>$rid,':from_user'=>$from_user));

if(empty($fans)){
    message('抱歉，参数错误！！', '', 'error');//调试代码
}

$order = pdo_fetchall("select * from " . tablename('haoman_dpm_deposit') . " where rid = :rid and uniacid = :uniacid and from_user =:from_user and fansid=:fansid and status!=4 ORDER BY id desc",array(':rid'=>$rid,':from_user'=>$from_user,'uniacid'=>$uniacid,':fansid'=>$fans['id']));

    $jssdk = new JSSDK();
    $package = $jssdk->GetSignPackage();

    include $this->template('deposit');

