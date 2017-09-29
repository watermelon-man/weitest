<?php
global $_GPC,$_W;
// $this->checkFollow;
// $this->checkBowser;
$rid = intval($_GPC['rid']);

$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($user_agent, 'MicroMessenger') === false) {

    header("HTTP/1.1 301 Moved Permanently");
    header("Location: {$this->createMobileUrl('other',array('type'=>1,'id'=>$rid))}");
    exit();
}

if(empty($rid)){
    $data = array(
        'success' => 100,
        'msg' => "参数错误",
    );

    echo json_encode($data);
    exit;
}

$from_user = $_W['fans']['from_user'];
$avatar = $_W['fans']['tag']['avatar'];
$sex = $_W['fans']['tag']['sex'];
$nickname = $_W['fans']['nickname'];

load()->model('account');
$_W['account'] = account_fetch($_W['acid']);
$cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
$cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
if ($_W['account']['level'] != 4) {
    $from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
    $avatar = $cookie['avatar'];
    $nickname = $cookie['nickname'];
}


    $reply = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_reply')." WHERE rid='".$rid."' " );

    $fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where rid = '" . $rid . "' and from_user='" . $from_user . "'");

    if(empty($fans)){
        $data = array(
            'success' => 100,
            'msg' => "无法获取您的信息，请刷新页面",
        );

        echo json_encode($data);
        exit;
    }

    $message = trim($_GPC['message']);
    $pic = trim($_GPC['pic']);
    $identification = trim($_GPC['identification']);

if(empty($message)||empty($pic)){

        $data = array(
            'success' => 100,
            'msg' => "请填写寄存物品或上传图片",
        );

        echo json_encode($data);
        exit;
}
if(empty($identification)){
   $status=0;
    $waiter_id=0;
}else{
    $status=1;
    $waiter_id=pdo_fetchcolumn("SELECT wditer_openid FROM ".tablename('haoman_dpm_shop_wditer')." WHERE id=:id and  rid=:rid",array(':id'=>$identification,':rid'=>$rid));
}


    $tid = date('YmdHi').random(8, 1);
    $insert = array(
        'rid' => $rid,
        'uniacid' => $_W['uniacid'],
        'from_user' => $from_user,
        'fansid' => $fans['id'],
        'message' => $message,
        'pic' => $pic,
        'status' => $status,
        'waiter' => $waiter_id,
        'identification' => $identification,
        'orderid' => $tid,
        'createtime' => time(),
    );


  $stem=  pdo_insert('haoman_dpm_deposit',$insert);

  if($stem){
      $data = array(
          'success' => 1,
          'msg' => "寄存成功",
      );
  }else{
      $data = array(
          'success' => 100,
          'msg' => "寄存失败",
      );
  }


echo json_encode($data);