<?php
global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$uid = $_GPC['uid'];
$uniacid = $_W['uniacid'];


$fans = pdo_fetch("select * from " . tablename('haoman_dpm_fans') . " where rid = '" . $rid . "' and from_user='" . $uid . "'");

if(empty($fans['avatar'])||$fans['avatar']=='/0'){
    $avatar = '../addons/haoman_dpm/images/item8.jpg';
}else{
    $avatar = $fans['avatar'];
}

$content = $_GPC['content'];

$image = $_GPC['image'];

$content = $this->emoji_encode($content);

$insert = array(
    'uniacid' => $uniacid,
    'avatar' => $avatar,
    'nickname' => $fans['nickname'],
    'from_user' => $fans['from_user'],
    'word' => $content,
    'wordimg' => $image,
    'rid' => $rid,
    'status' => 1,
    'is_back' => $fans['is_back'],
    'is_xy' =>0,
    'is_bp' =>0,
    'type' =>0,
    'gift' =>0,
    'createtime' => time(),
);
$temp = pdo_insert('haoman_dpm_messages',$insert);
pdo_update('haoman_dpm_fans', array('last_onlinetime' => time()), array('id' => $fans['id']));

$result = array(
    'code' => 8,
    'data' => "信息已上墙，请关注大屏幕！",
    'msg' => "信息已上墙，请关注大屏幕！",

);

$this->message($result);