<?php
global $_GPC,$_W;
// $this->checkFollow;
// $this->checkBowser;
$rid = intval($_GPC['rid']);
$openid = $_GPC['openid'];

//$user_agent = $_SERVER['HTTP_USER_AGENT'];
//if (strpos($user_agent, 'MicroMessenger') === false) {
//
//    header("HTTP/1.1 301 Moved Permanently");
//    header("Location: {$this->createMobileUrl('other',array('type'=>1,'id'=>$rid))}");
//    exit();
//}
if(empty($rid)){
    $data = array(
        'code' => 1,
        'msg' => '参数错误',
        'data' => '',
    );

    echo json_encode($data);
    exit;
}
if(empty($openid)){
    $data = array(
        'code' => 1,
        'msg' => '未获取到openid',
        'data' => '',
    );

    echo json_encode($data);
    exit;

}

load()->classs('weixin.account');
load()->func('communication');
$tokens = WeAccount::token();

if(empty($tokens)){
    $data = array(
        'code' => 1,
        'msg' => '未获取到access_token',
        'data' => '',
    );

    echo json_encode($data);
    exit;

}
//$tokenUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $accessToken . "&openid=" . $openid . "&lang=zh_CN";

//$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$tokens}";
$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$tokens}&openid={$openid}&lang=zh_CN";
$content = ihttp_get($url);
//$userInfo = @json_decode($content['content'], true);
//$res = json_decode($this->httpGet($url));
//$userInfo = $content['content'];
$userInfo = @json_decode($content['content'], true);
    $nickname = $userInfo['nickname'];
    $avatar = $userInfo['headimgurl'];
    $sex = $userInfo['sex'];

    $tems =  pdo_update('haoman_dpm_fans', array('nickname' => $nickname, 'avatar' => $avatar,'sex'=>$sex), array('rid'=>$rid,'from_user' => $userInfo['openid']));

    $cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
    $cookie = array("nickname" => $userInfo['nickname'],'avatar'=>$userInfo['headimgurl'],'openid'=>$userInfo['openid'],'sex'=>$userInfo['sex']);
    setcookie($cookieid, base64_encode(json_encode($cookie)), time() + 3600 * 24 * 365);

    if($tems){
        $data = array(
            'code' => 2,
            'msg' => '同步成功',
            'data' => '',
        );

        echo json_encode($data);
        exit;
    }else{
        $data = array(
            'code' => 1,
            'msg' => '同步失败，错误码：'.$userInfo['errcode'],
            'data' => $userInfo['errcode'],
        );

        echo json_encode($data);
        exit;
    }
