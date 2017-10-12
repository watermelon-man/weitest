<?php
global $_GPC,$_W;

$rid = intval($_GPC['rid']);
$uid = $_GPC['uid'];
$from_user = $_W['openid'];
$hbid = $_GPC['id'];
//$lt = $_GPC['lt'];
$uniacid = $_W['uniacid'];

load()->model('account');
$_W['account'] = account_fetch($_W['acid']);
$cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
$cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
if ($_W['account']['level'] != 4) {
    $from_user = $cookie['openid'];
}
$fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where rid = '" . $rid . "' and from_user='" . $from_user . "'");
$op = $_GPC['op'];
if($op=='new'){
    $code = 0;
    $logRst='';
    $msg='';
    if($uid!=$from_user||empty($hbid)){
        $msg = '数据异常';
    }

    $hbaward = pdo_fetchall("select * from " . tablename('haoman_dpm_hb_award') . " where rid=:rid and prize=:prize order by createtime desc", array(':rid' => $rid,':prize'=>$hbid));
    $max = pdo_fetch("select max(credit)as credit from " . tablename('haoman_dpm_hb_award') . " where rid=:rid and prize=:prize", array(':rid' => $rid,':prize'=>$hbid));

    foreach($hbaward as &$v){
        $v['avatar'] = tomedia($v['avatar']);
        $v['createtime'] = date("m-d H:i:s", $v['createtime']) ;

        if($max['credit']==$v['credit']){
            $v['isBestUser'] = 1;
        }

    }
unset($v);


    $logRst = $hbaward;
    $data = array(
        'code' => $code,

        'logRst'=>$logRst,

        'max'=>$max,

        'msg' =>$msg,
    );

    echo json_encode($data);
    exit;
}else{
    if($uid!=$from_user){
        return 404;
    }

    $hbaward = pdo_fetchall("select * from " . tablename('haoman_dpm_hb_award') . " where rid=:rid and prize=:prize order by createtime desc", array(':rid' => $rid,':prize'=>$hbid));

    $max = pdo_fetch("select max(credit)as credit from " . tablename('haoman_dpm_hb_award') . " where rid=:rid and prize=:prize", array(':rid' => $rid,':prize'=>$hbid));


    $bb='';
    $aa='';

    foreach($hbaward as $k=>$v){
        $v['avatar'] = tomedia($v['avatar']);
        $v['createtime'] = date("m-d H:i:s", $v['createtime']) ;

        $bb .='<li>';
        $bb .='<div class="avatar" style="background-image: url('.$v['avatar'].')">';
        if($max['credit']==$v['credit']){
            $bb .='<img src="../addons/haoman_dpm/imgs/bestone.png" width="30%" style="position: absolute;margin-top:8%;margin-left:45%;">';

        }
        $bb .='</div>';
        $bb .='<p><span class="nickname">'.$v['nickname'].'</span><span class="money">'.$v['credit'].'元</span></p>';
        $bb .='<p class="time">'.$v['createtime'].'</p>';
        $bb .='</li>';
    }



    $aa .='<div class="mzh_modal_alert" style="display: block;">';
    $aa .='<div class="mzh_modal_alert_dialog" style="background-color:rgba(0,0,0,0);box-shadow:none;">';
    $aa .='<span class="am-icon-close close"  style="color: #999;"></span>';
    $aa .='<div class="mzh_modal_alert_body">';
    $aa .='<div class="hb_logs">';
    $aa .='<ul>';
    $aa .=$bb;
    $aa .='</ul>';
    $aa .='</div>';
    $aa .='</div>';
    $aa .='</div>';
    $aa .='</div>';

    $result = $aa;

    $this->message($result);
}
