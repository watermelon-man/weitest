<?php
global $_GPC, $_W;
$rid = intval($_GPC['id']);
$Ttype = intval($_GPC['Ttype']);
$uniacid = $_W['uniacid'];

//$user_agent = $_SERVER['HTTP_USER_AGENT'];
//if (strpos($user_agent, 'MicroMessenger') === false) {
//
//	header("HTTP/1.1 301 Moved Permanently");
//	header("Location: {$this->createMobileUrl('other',array('type'=>1,'id'=>$rid))}");
//	exit();
//}
//
//$ttp = $this->get_device_type();
//
//if($ttp=='ios'&&$Ttype!=1){
//
//    header("HTTP/1.1 301 Moved Permanently");
//    header("Location: {$this->createMobileUrl('messagesindex',array('Ttype'=>1,'id'=>$rid))}");
//    exit();
//}
//
////网页授权借用开始
//
//load()->model('account');
//$_W['account'] = account_fetch($_W['acid']);
//$cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
//$cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
//if ($_W['account']['level'] != 4) {
//	$from_user = $cookie['openid'];
//	$avatar = $cookie['avatar'];
//	$nickname = $cookie['nickname'];
//}else{
//	$from_user = $_W['fans']['from_user'];
//	$avatar = $_W['fans']['tag']['avatar'];
//	$nickname = $_W['fans']['nickname'];
//}
//
//$code = $_GPC['code'];
//$urltype = '';
//if (empty($from_user) || empty($avatar) || empty($nickname)) {
//	if (!is_array($cookie) || !isset($cookie['avatar']) || !isset($cookie['openid']) || !isset($cookie['nickname'])) {
//		$userinfo = $this->get_UserInfo($rid, $code, $urltype);
//		$nickname = $userinfo['nickname'];
//		$avatar = $userinfo['headimgurl'];
//		$from_user = $userinfo['openid'];
//	} else {
//		$avatar = $cookie['avatar'];
//		$nickname = $cookie['nickname'];
//		$from_user = $cookie['openid'];
//	}
//}
//
////网页授权借用结束

$page_from_user = base64_encode(authcode($from_user, 'ENCODE'));

$from_user='oQAFAwCS19dHrsZhSd4h0uRdEKUM';
$fashb = pdo_fetch( " SELECT bp_logo,top_bg,isfanshb,hb_minmoney,hb_manmoney,counter,hbtype,is_ty,is_messages,big_mobtitle FROM ".tablename('haoman_dpm_hb_setting')." WHERE rid='".$rid."' " );

$reply = pdo_fetch("select rules,isjiabin,mobtitle,ismessage,share_url,share_title,share_desc,share_imgurl,picture,mobpicurl,isqhb,istoupiao,copyright,is_b_share from " . tablename('haoman_dpm_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$xys = pdo_fetch("select isxys from " . tablename('haoman_dpm_xysreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$bp = pdo_fetch("select is_img,isbp,isds,bp_pay,bp_pay2,bp_listword,bp_keyword,ishb,isvo,isbb,is_mf,is_gift from " . tablename('haoman_dpm_bpreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$yyy = pdo_fetch("select isyyy from " . tablename('haoman_dpm_yyyreply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$vote = pdo_fetch("select vote_status from " . tablename('haoman_dpm_newvote_set') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$punishment = pdo_fetch("select is_punishment from " . tablename('haoman_dpm_punishment') . " where rid = :rid and uniacid=:uniacid ", array(':rid'=>$rid,':uniacid'=>$_W['uniacid']));
$shop = pdo_fetch("select shop_status from " . tablename('haoman_dpm_shop_setting') . " where rid = :rid and uniacid=:uniacid ", array(':rid'=>$rid,':uniacid'=>$_W['uniacid']));
$custom = pdo_fetchall("select * from " . tablename('haoman_dpm_custom') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
$photo = pdo_fetch("select is_phone,hb_bgcolor_tm,hd_bgcolor,hd_bgimg from " . tablename('haoman_dpm_photo_setting') . " where rid = :rid order by `id` desc", array(':rid' => $rid));

if(ISCUSTOM == 1 && CUSTOM_VERSION == 'DS'){
    $dsreply = pdo_fetch("select * from " . tablename('haoman_dpm_ds_reply') . " where rid = :rid order by `id` desc", array(':rid' => $rid));
}
if(ISCUSTOM == 1 && CUSTOM_VERSION == 'ZNL'){
    $znlreply = pdo_fetch( " SELECT * FROM ".tablename('haoman_dpm_znl_reply')." WHERE rid='".$rid."' " );
}

if (empty($reply)) {
	message('非法访问，请重新发送消息进入活动页面！');
}

    $bpmoney = pdo_fetchall("select * from " . tablename('haoman_dpm_bpmoney') . " where rid = :rid and uniacid=:uniacid and bp_type=0 order by `bp_time` desc", array(':rid' => $rid, ":uniacid" => $uniacid));
    if($bpmoney){
        $money  =1;
    }
//    $bptheme = pdo_fetchall("select id,`name`,thumb from " . tablename('haoman_dpm_bptheme') . " where rid = :rid and uniacid=:uniacid and enabled=0 order by `id` desc", array(':rid' => $rid, ":uniacid" => $uniacid));

//
////检测是否关注
//if (!empty($reply['share_url'])) {
//	//查询是否为关注用户
//	$fansID = $_W['member']['uid'];
//	$follow = pdo_fetchcolumn("select follow from " . tablename('mc_mapping_fans') . " where uid=:uid and uniacid=:uniacid order by `fanid` desc", array(":uid" => $fansID, ":uniacid" => $uniacid));
//
//	if ($follow == 0) {
//		header("HTTP/1.1 301 Moved Permanently");
//		header("Location: " . $reply['share_url'] . "");
//		exit();
//	}
//
//}
//
    //检测是否为空
    $fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where rid = '" . $rid . "' and from_user='" . $from_user . "'");
    if ($fans == false) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $this->createMobileUrl('information', array('id' => $rid,'from_user'=>$page_from_user)) . "");
        exit();
    }


//$admin = pdo_fetch("select id,createtime,uses_times from " . tablename('haoman_dpm_bpadmin') . "  where admin_openid=:admin_openid and status=0 and rid=:rid", array(':admin_openid' => $from_user,':rid'=>$rid));
//
//if($admin){
//	$nowtime = mktime(0, 0, 0);
//	if ($admin['createtime'] < $nowtime) {
//
//		$admin['uses_times'] = 0;
//
//		$temps = pdo_update('haoman_dpm_bpadmin', array('uses_times'=>$admin['uses_times'],'createtime' => time()), array('id' => $admin['id']));
//
//	}
//}
//
    $bptheme2 = pdo_fetchall("SELECT id,name,extend_from,price,thumb,sort,uniacid,resource,bp_time,rank_position,enabled,selected,condition_text FROM " . tablename('haoman_dpm_bptheme') . " WHERE rid = :rid and uniacid = :uniacid  ORDER BY id DESC ",array(':rid'=>$rid,':uniacid'=>$uniacid));

    $bptheme = $bptheme2;
    if($bptheme2){
        foreach($bptheme2 as $v){
            $res[$v['id']]=$v;
            $arr[]=$v['id'];
        }

        $bptheme2 = json_encode($res);
        $arr = json_encode($arr);
    }
    else{
        $bptheme2='123';
        $arr='123';
    }


//
//if(!empty($fans['realname'])){
//	$nickname = $fans['realname'];
//}
//
if($bp['bp_keyword']){
	$keywords= explode(',',$bp['bp_keyword']);
	$i=1;

	foreach($keywords as $k=>$v){
		$keyword[$i]=$v;
		$i++;
	}

	$keyword = json_encode($keyword);
}else{
	$keyword ="0";
}

if($bp['bp_listword']){
	$bp_listwords = explode(',',$bp['bp_listword']);
	$i=1;

	foreach($bp_listwords as $k=>$v){
		$bp_listword[$i]=$v;
		$i++;
	}
	$bp_listword = json_encode($bp_listword);
}else{
	$bp_listword = "0";
}

//
//
//
////分享信息
//$sharelink = $_W['siteroot'] . 'app/' . $this->createMobileUrl('messagesindex', array('id' => $rid, 'from_user' => $page_from_user));
//$sharetitle = empty($reply['share_title']) ? '一起来聊一聊吧!' : $reply['share_title'];
//$sharedesc = empty($reply['share_desc']) ? '亲，一起来聊一聊吧！！' : str_replace("\r\n", " ", $reply['share_desc']);
//if (!empty($reply['share_imgurl'])) {
//	$shareimg = toimage($reply['share_imgurl']);
//} else {
//	$shareimg = toimage($reply['picture']);
//}
//
//if(empty($reply['mobpicurl'])){
//	$bg = "../addons/haoman_dpm/mobimg/bg.jpg";
//}else{
//	$bg = tomedia($reply['mobpicurl']);
//}
//
//$jssdk = new JSSDK();
//$package = $jssdk->GetSignPackage();

    include $this->template('mob_new_messageindex');
