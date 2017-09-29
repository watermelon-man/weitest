<?php
defined('IN_IA') or exit('Access Denied');

global $_GPC, $_W;


$rid = intval($_GPC['rid']);
$id = intval($_GPC['id']);
$uniacid = $_W['uniacid'];
$credit1 = $_W['member']['credit1'];

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

    $reply = pdo_fetch("select * from " . tablename('haoman_dpm_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
    $yyy = pdo_fetch("select isyyy from " . tablename('haoman_dpm_yyyreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
    $vote = pdo_fetch("select vote_status from " . tablename('haoman_dpm_newvote_set') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
    $photo = pdo_fetch("select is_phone from " . tablename('haoman_dpm_photo_setting') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
    $punishment = pdo_fetch("select is_punishment from " . tablename('haoman_dpm_punishment') . " where rid = :rid and uniacid=:uniacid ", array(':rid'=>$rid,':uniacid'=>$_W['uniacid']));

    if(ISCUSTOM == 1 && CUSTOM_VERSION == 'DS'){
        $dsreply = pdo_fetch("select * from " . tablename('haoman_dpm_ds_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
    }
    if(ISCUSTOM == 1 && CUSTOM_VERSION == 'ZNL'){
        $znlreply = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_znl_reply')." WHERE rid='".$rid."' " );
    }
    $num = $reply['share_acid'] < 100 ? 100 : $reply['share_acid'];


    $fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where id =:id and  rid = :rid and from_user=:from_user",array(':id'=>$id,':rid'=>$rid,':from_user'=>$from_user));

    if(empty($fans)){
        message('抱歉，参数错误！!', '', 'error');//调试代码
    }

    $total = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('haoman_dpm_cash') . " WHERE rid = :rid and uniacid = :uniacid AND from_user = :from_user",array(':rid'=>$rid,':uniacid' => $_W['uniacid'],':from_user' => $from_user));
    $pindex = max(1, intval($_GPC['page']));
    $psize = 30;
    $pager = pagination($total, $pindex, $psize);
    $start = ($pindex - 1) * $psize;
    $limit .= " LIMIT {$start},{$psize}";
   $list = pdo_fetchall("select * from " . tablename('haoman_dpm_cash') . " where rid = :rid and from_user =:from_user and uniacid = :uniacid order by createtime desc",array(':rid'=>$rid,':from_user'=>$from_user,'uniacid'=>$uniacid));



    $wttotal = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('haoman_dpm_cash') . " WHERE rid = :rid and uniacid = :uniacid AND from_user = :from_user AND status = 2",array(':rid'=>$rid,':uniacid' => $_W['uniacid'],':from_user' => $from_user));
    $wtpindex = max(1, intval($_GPC['page']));
    $wtpsize = 30;
    $wtpager = pagination($wttotal, $wtpindex, $wtpsize);
    $wtstart = ($wtpindex - 1) * $wtpsize;
    $wtlimit .= " LIMIT {$wtstart},{$wtpsize}";
   $wtlist = pdo_fetchall("select * from " . tablename('haoman_dpm_cash') . " where rid = :rid and from_user =:from_user and uniacid = :uniacid and status=2 order by createtime desc",array(':rid'=>$rid,':from_user'=>$from_user,'uniacid'=>$uniacid));


//
$dstotal = pdo_fetchcolumn("SELECT count(id) FROM " . tablename('haoman_dpm_cash') . " WHERE rid = :rid and uniacid = :uniacid AND from_user = :from_user AND status = 0",array(':rid'=>$rid,':uniacid' => $_W['uniacid'],':from_user' => $from_user));
$dspindex = max(1, intval($_GPC['page']));
$dspsize = 30;
$dspager = pagination($dstotal, $dspindex, $dspsize);
$dsstart = ($dspindex - 1) * $dspsize;
$dslimit .= " LIMIT {$dsstart},{$dspsize}";
$dslist = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_cash') . " WHERE rid = :rid and uniacid = :uniacid AND from_user = :from_user AND status = 0 ORDER BY createtime DESC " . $dslimit, array(':rid'=>$rid,':uniacid' => $_W['uniacid'],':from_user' => $from_user));

include $this->template('txmore');
