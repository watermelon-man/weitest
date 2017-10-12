<?php

global $_GPC, $_W;
$rid = intval($_GPC['id']);
$uniacid = $_W['uniacid'];

//网页授权借用开始（特殊代码）

$from_user = $_W['fans']['from_user'];
$avatar = $_W['fans']['tag']['avatar'];
$nickname = $_W['fans']['nickname'];

load()->model('account');
$_W['account'] = account_fetch($_W['acid']);
$cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
$cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
if ($_W['account']['level'] != 4||empty($nickname)) {
    $from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
    $avatar = $cookie['avatar'];
    $nickname = $cookie['nickname'];
}

//网页授权借用结束（特殊代码）



$img = str_replace('data:image/png;base64,', '', $_POST['params']);
$img = str_replace(' ', '+', $img);
$img = base64_decode($img);
$fileName = '../addons/haoman_dpm/qrcode/'.md5(uniqid()).'.png';
$f = fopen($fileName, 'w+');
fwrite($f, $img);
fclose($f);

//    //2017-10-06  新增测试部分
//    $logo = '../addons/haoman_dpm/images/123.png';
//    $dateF = date('Y-m-d', time());
//    $datearr = explode('-', $dateF);
//    $rPathName = 'images/' . $_W['uniacid'] . '/' . $datearr[0] . '/' . $datearr[1] . '/';
//    $pathName = IA_ROOT . '/attachment/' . $rPathName;
//    $fileName2 = uniqid() . '.png';
//    $oImgUrl = $pathName . $fileName2;
//    $rImgUrl = $rPathName . $fileName2;
//    $filename2 = $oImgUrl;
//
//    $this->get_narrow_img2(tomedia($fileName),$filename2);
//
//    $this->spliceImage($filename2,$logo,$filename2);

$insert = array(
    'rid'=>$rid,
    'uniacid'=>$uniacid,
    'from_user'=>$from_user,
    'nickname'=>$nickname,
    'avatar'=>$avatar,
    'img'=>$fileName,
//    'img2'=>$rImgUrl,
    'status'=>0,
    'createtime'=>time(),
    );

$temp = pdo_insert('haoman_dpm_shouqian',$insert);

if($temp == false){
    $data = array(
        'flag'=>100,
    );
}else{
    $data = array(
        'flag'=>1,
    );
}

echo json_encode($data);

