<?php
global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$rid = intval($_GPC['rid']);
$len = intval($_GPC['last_id']);
$type = intval($_GPC['type_']);

$reply = pdo_fetch("SELECT isckmessage,is_realname FROM " . tablename('haoman_dpm_reply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));



if($reply['isckmessage'] == 0){
    $totaldata = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('haoman_dpm_messages') . " WHERE uniacid = :uniacid AND rid = :rid and status = 1 and is_back !=1 and is_xy !=1", array(':uniacid' => $uniacid,':rid'=>$rid));
    // $limit = $totaldata - $len;
    $list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_messages') . " WHERE rid = :rid and uniacid = :uniacid and status = 1 and id>:id  and is_back !=1 and is_xy !=1 ORDER BY id DESC limit 50",array(':rid'=>$rid,':uniacid'=>$uniacid,':id'=>$len));
}else{
    $totaldata = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('haoman_dpm_messages') . " WHERE uniacid = :uniacid AND rid = :rid and is_back !=1 and is_xy !=1", array(':uniacid' => $uniacid,':rid'=>$rid));
    // $limit = $totaldata - $len;
    $list = pdo_fetchall("SELECT * FROM " . tablename('haoman_dpm_messages') . " WHERE rid = :rid and uniacid = :uniacid and id>:id  and is_back !=1 and is_xy !=1 ORDER BY id DESC limit 50",array(':rid'=>$rid,':uniacid'=>$uniacid,':id'=>$len));
}

foreach ($list as $k=>$vv){

}

    foreach ($list as $k=>&$v){
        if($reply['is_realname']==1) {
            $v['nickname'] = pdo_fetchcolumn("SELECT realname FROM " . tablename('haoman_dpm_fans') . " WHERE rid = :rid and uniacid = :uniacid and from_user =:from_user ORDER BY id DESC", array(':rid' => $rid, ':uniacid' => $uniacid, ':from_user' => $v['from_user']));
            if (empty($v['nickname'])) {
                $v['nickname'] = "匿名用户!";
            }
        }
        if($v['is_bp']!=1){
            $new_list[$k]=$v;
        }
        $v['wordimg'] = tomedia($v['wordimg']);
    }
    unset($v);
$new_list = array_values($new_list);
if($type==1){
    $data = array(
        'ret' => 1,
        'data' => $new_list
    );
}else{
    $data = array(
        'ret' => 1,
        'data' => $list
    );
}



echo json_encode($data);