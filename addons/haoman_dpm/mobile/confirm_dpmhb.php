<?php
global $_GPC, $_W;
$rid = intval($_GPC['id']);
$token = $_GPC['token'];

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


//$reply = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_bpreply')." WHERE rid='".$rid."' " );

$fashb = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_hb_setting')." WHERE rid='".$rid."' " );
$codes =10;
$fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where rid = '" . $rid . "' and from_user='" . $from_user . "'");
$bp = pdo_fetch("select is_img,isbp,isds,bp_pay,bp_pay2,bp_listword,bp_keyword,ishb,isvo,isbb,is_mf,is_gift from " . tablename('haoman_dpm_bpreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));

$codes =$bp['bp_pay2'];

//是否是管理员判断
$isAdmin =0;
$admin = pdo_fetch("select id,free_times,uses_times,set_hb from " . tablename('haoman_dpm_bpadmin') . "  where admin_openid=:admin_openid and status=0 and rid=:rid", array(':admin_openid' => $from_user,':rid'=>$rid));
if($admin){
    $isAdmin =1;//1表示是管理员，0表示不是
}
$hb_num = $_GPC['pbtime'];
$hb_moneys = $_GPC['bppic'];
$messages = empty($_GPC['message'])?'恭喜发财，大吉大利':$_GPC['message'];
$desk = $_GPC['desk'];
$pay_type = $_GPC['type'];
$new_type =$_GPC['new_type'];

if($new_type==2){
    //新版发红包
    $hb_num = $_GPC['num'] ;
    $hb_moneys = $_GPC['total'];
    $messages = empty($_GPC['content'])?'恭喜发财，大吉大利':$_GPC['content'];
    $desk = $_GPC['hb_type'];
    $paytype = $_GPC['paytype'];
}
if(empty($nickname)){
    $nickname = trim($_GPC['nickname']);
}
if(empty($nickname) || empty($avatar)||$avatar=='/0'){
    $nickname = $fans['nickname'];
    if($fans['avatar']&&$fans['avatar']!='/0'){
        $avatar = tomedia($fans['avatar']);
    }else{
        $avatar = '../addons/haoman_dpm/images/item8.jpg';
    }

}
if($fashb['hb_minmoney']==0){
    $fashb['hb_minmoney']=1;
}
if($hb_moneys<$fashb['hb_minmoney']){
    $data = array(

        'success' => 100,
        'msg' => "红包金额最少".$fashb['hb_minmoney']."元",
    );

    echo json_encode($data);
    exit;
}

if($hb_moneys>$fashb['hb_manmoney']&&$fashb['hb_manmoney']!=0){
    $data = array(

        'success' => 100,
        'msg' => "红包金额最大".$fashb['hb_manmoney']."元",
    );

    echo json_encode($data);
    exit;
}
if($hb_num<1){
    $data = array(

        'success' => 100,
        'msg' => "红包数量最少1个",
    );

    echo json_encode($data);
    exit;
}
    if($hb_moneys*100/$hb_num<1){
        $data = array(

            'success' => 100,
            'msg' => "每个红包最小金额0.01元!!",
        );

        echo json_encode($data);
        exit;
    }
if($fashb['isfanshb']!=1){
    $data = array(

        'success' => 100,
        'msg' => "未开启发红包模式!!",
    );

    echo json_encode($data);
    exit;
}



$tid = date('YmdHi').random(8, 1);

$hb_money = $hb_moneys+($hb_moneys*$fashb['counter'])/100;//支付费用加手续费


$hb_money=sprintf("%.2f",$hb_money);
$fhb_type = 0;
    if($paytype==1){
        if($hb_money>$fans['totalnum']/100){
            $data = array(
                'success' => 100,
                'msg' => "账户余额不足!!",
            );

            echo json_encode($data);
            exit;
        }else{
            $fhb_type =1;
        }
    }

$result = pdo_insert('haoman_dpm_pay_order', array(
    'uniacid' => $_W['uniacid'],
    'transid'=>$tid,
    'from_user' => $from_user,
    'avatar' => $avatar,
    'nickname' => $nickname,
    'bptime' => $hb_num,
    'message' => $messages,
    'wordimg' => $desk,
    'pay_total' => $hb_moneys,
    'pay_ip' => $_W['clientip'],
    'from_realname' => $fashb['counter'],//手续费
    'rid' => $rid,
    'status' => 1,
    'pay_addr' => $hb_money,
    'fansid' => $fhb_type,
    'isadmin' => 0,
    'pay_type' => 4,
    'createtime' => time(),
));



if (empty($result)) {
    $data = array(

        'success' => 100,
        'msg' => "红包发送失败",
    );
    echo json_encode($data);
    exit;

}else{
    $orderid = pdo_insertid();



    if($isAdmin==1&&$admin['set_hb']==1){

        if($admin['free_times']-$admin['uses_times']>0||$admin['free_times']==0){
            $update = array();
            $update['status'] = 2;
            $update['paytime'] = TIMESTAMP;
            $transid = $tid;
            $update['orderid'] = 2;
            $update['isadmin'] = 1;
            $update['pay_total'] = $hb_money;

            $ress =  $this->modify($transid,$update);

            $data = array(
                'code' => 10,
                'success' => 2,
                'isAdmin'=>1,
                'msg' => "提交红包支付成功",
            );
//        $ret = pdo_update('haoman_dpm_guest', array('type'=>$item_list['type']+1), array('id'=>$item_list['id']));
            echo json_encode($data);
            exit;
        }
        if($admin['free_times']-$admin['uses_times']<=0&&$admin['free_times']!=0){
            $isAdmin==0;
        }
    }

    if($fhb_type==1){
        //账户余额发红包非管理员
            $update = array();
            $update['status'] = 2;
            $update['paytime'] = TIMESTAMP;
            $transid = $tid;
            $update['orderid'] = 2;
            $update['isadmin'] = 0;
            $update['pay_total'] = $hb_money;

            $ress =  $this->modify($transid,$update);

            $data = array(
                'code' => 10,
                'success' => 2,
                'isAdmin'=>1,
                'msg' => "提交红包支付成功",
            );
    //        $ret = pdo_update('haoman_dpm_guest', array('type'=>$item_list['type']+1), array('id'=>$item_list['id']));
            echo json_encode($data);
            exit;
    }


    if($token=='onBridge'&&$codes==1){
        $data = array('fee' => floatval($hb_money), 'uniacid' => $_W['uniacid'], 'ordersn' => date('YmdHi').random(8, 1), 'openid' => $from_user, 'nickname' => $nickname, 'status' => 0, 'title' => "大屏幕红包费用", 'xq' => '微信支付', 'addtime' => date('Y-m-d H:i:s', time()));
        $params = array('tid' => $tid, 'ordersn' => $tid, 'title' => "大屏幕红包费用", 'user' => $from_user, 'fee' => floatval($hb_money), 'module' => 'haoman_dpm',);
        $log = pdo_get('core_paylog', array('uniacid' => $_W['uniacid'], 'module' => $params['module'], 'tid' => $params['tid']));
        if (empty($log)) {
            $log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['member']['uid'], 'module' => $params['module'], 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
            pdo_insert('core_paylog', $log);
        }


        $params = base64_encode(json_encode($params));
    }
    $data = array(
        'code' => $codes,
        'success' => 1,
        'params' => $params,
        'arr' => $data,
        'pay_type' => 4,
        'orderid' => $orderid,
        'tid' => $tid,
        'pay_money' => $hb_money,
        'msg' => "红包发送成功",
        'isAdmin' => 0,
    );

    echo json_encode($data);
    exit;
}