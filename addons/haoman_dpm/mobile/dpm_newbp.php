<?php
global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$rid = intval($_GPC['rid']);
$isbp = $_GPC['bp'];
$reply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_reply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$yyyreply = pdo_fetch("SELECT isyyy FROM " . tablename('haoman_dpm_yyyreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$bpreply = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_bpreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$xysreply = pdo_fetch("SELECT isxys,is_pair,is_turntable,xys_backcolor FROM " . tablename('haoman_dpm_xysreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$xyhreply = pdo_fetch("SELECT is_xysjh,is_xyh FROM " . tablename('haoman_dpm_xyhreply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$video = pdo_fetch("SELECT vodio_bg11 FROM " . tablename('haoman_dpm_mp4') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$shouqian = pdo_fetch("SELECT status,pm_status FROM " . tablename('haoman_dpm_shouqianBase') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$fashb = pdo_fetch( " SELECT bp_type,bp_opacity,bb_bgcoclor,bp_opacity FROM ".tablename('haoman_dpm_hb_setting')." WHERE rid='".$rid."' " );

if($isbp!='isbp'){
    //检查登陆状态
    if(!empty($reply['loginpassword'])){
        $cookieid = '__cookie_haoman_dpmweb_201606186_' . $rid;
        $cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
        if($cookie['loginpassword'] != $reply['loginpassword']){
            message('登陆密码错误或已超时，请重新输入',$this->createMobileUrl("login",array('id'=>$rid)),'error');
        }
    }

//检查登陆状态
}
if($bpreply['openscreen']!=0){
    message('未开启大屏幕功能，请先后台开启！', '', 'error');
}

load()->model('reply');
$keywords = reply_single($rid);


if($bpreply['isbd']==1){
//    $t = time();
//    $start = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
//    $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));



    $params = array(':rid' => $rid, ':uniacid' => $_W['uniacid']);
    $where.=' and createtime>=:createtime1 and createtime<=:createtime2 ';

    if($bpreply['bp_maxnum']==0){
        $params[':createtime1'] = time()-12*60*60;
        $params[':createtime2'] = time();
    }else{
        $params[':createtime1'] = $bpreply['bd_starttime'];
        $params[':createtime2'] = $bpreply['bd_endtime'];
    }



    $topfans = pdo_fetchall("SELECT id,avatar,pay_total,nickname,sum(pay_total)as pt FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid and rid =:rid AND status= 2 " . $where . " GROUP BY from_user  ORDER BY pt DESC limit 5", $params);

}

$bp_bgs= pdo_fetchall("select bp_type,bp_images,bp_videos,id from " . tablename('haoman_dpm_bpmoney') . " where rid = :rid and uniacid=:uniacid and bp_type in(3,4) order by `id` desc", array(':rid' => $rid, ":uniacid" => $uniacid));

 $bp_bg ='';
 $bp_video ='';
foreach ($bp_bgs as $v){
    if($v['bp_type']==3){
        $bp_bg[] = tomedia($v['bp_images']);
    }elseif ($v['bp_type']==4){
        $bp_video[] = tomedia($v['bp_videos']);
    }
}
$bp_bg =json_encode($bp_bg);
$bp_video = json_encode($bp_video);

$bgItems= pdo_fetchall("SELECT bg,thumbnail,`name` FROM " . tablename('haoman_dpm_bgitems') . " WHERE  uniacid = :uniacid ORDER BY id DESC ",array(':uniacid'=>$uniacid));

if($bgItems){
    foreach ($bgItems as &$k){
        $k['bg'] =tomedia($k['bg']);
        $k['thumbnail'] =tomedia($k['thumbnail']);
    }
    unset($k);
}else{
    $bgItems = '';
}
$bgItems = json_encode($bgItems);

$dsDatas = pdo_fetchall("SELECT `name`,`id`,`says`,`ds_time`as`time`,`ds_pic`as`img`,`ds_vodiobg`as`src`,`sort`as`iconName` FROM " . tablename('haoman_dpm_guest') . " WHERE rid = :rid and uniacid = :uniacid and turntable =2 ORDER BY id DESC ",array(':rid'=>$rid,':uniacid'=>$uniacid));


if($dsDatas){
    foreach($dsDatas as $v){
        $res[$v['id']]=$v;
        $arr[]=$v['id'];
    }

    $dsData = json_encode($res);
    $arr = json_encode($arr);
}
else{
    $dsData='123';
    $arr='123';
}
$bptheme = pdo_fetchall("SELECT id as bp_ztindex,`name` as bp_ztdes,thumb as bp_zticon,bp_vodiobg as bp_ztvideo,thumb2 as bp_ztmsgicon,pcicon as bp_pcicon FROM " . tablename('haoman_dpm_bptheme') . " WHERE rid = :rid and uniacid = :uniacid  ORDER BY id DESC ",array(':rid'=>$rid,':uniacid'=>$uniacid));

foreach ($bptheme as &$v){
    $v['bp_zticon'] = tomedia($v['bp_zticon']);
    $v['bp_ztvideo'] = tomedia($v['bp_ztvideo']);
    $v['bp_ztmsgicon'] = tomedia($v['bp_ztmsgicon']);
    $v['bp_pcicon'] = tomedia($v['bp_pcicon']);
}
unset($v);

if($bptheme){
    $bptheme = json_encode($bptheme);
}else{
    $bptheme='1';
}


if(empty($bpreply['bp_bg'])){
    $bg = "../addons/haoman_base/dpm/bg2.jpg";
}else{
    $bg = tomedia($bpreply['bp_bg']);
}
if(empty($bpreply['bp_voice'])){
    $bg_voice = "../addons/haoman_base/dpm/bp.mp3";
}else{
    $bg_voice = tomedia($bpreply['bp_voice']);
}
if(empty($bpreply['bp_carousel_bg'])){
    $lbbg = "../addons/haoman_dpm/bapinimg/onwallNoimg.jpg";
}else{
    $lbbg =tomedia($bpreply['bp_carousel_bg']);
}


$music = tomedia($bpreply['bp_music']);
include $this->template('dpm_newbp');
