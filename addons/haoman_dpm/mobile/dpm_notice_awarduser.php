<?php
global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$rid = intval($_GPC['rid']);
$awarduser = $_GPC['awarduser'];
$awardid = intval($_GPC['award_id']);
if(count($awarduser)<1){

}
$reply = pdo_fetch("SELECT is_award FROM " . tablename('haoman_dpm_reply') . " WHERE uniacid=:uniacid AND rid = :rid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid));
$mbid = pdo_fetch("select l_templateid from " . tablename('haoman_dpm_notifications') . "  where rid=:rid", array(':rid'=>$rid));

if($reply['is_award']==1) {
          exit();
 }
foreach ($awarduser as $i) {

    $fans = pdo_fetch("SELECT from_user,nickname,realname FROM " . tablename('haoman_dpm_fans') . " WHERE uniacid=:uniacid AND rid = :rid and id = :id LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid, ':id' => $i['id']));

    pdo_update('haoman_dpm_fans', array('zhongjiang' => 9), array('id' => $fans['id']));


    $prize = pdo_fetch("SELECT awardname,prizetype,id FROM " . tablename('haoman_dpm_award') . " WHERE uniacid=:uniacid AND rid = :rid  and from_user = :from_user and titleid = :titleid LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid, ':titleid' => $awardid,':from_user'=>$fans['from_user']));

    $name =empty($fans['realname'])?$fans['nickname']:$fans['realname'];
    $actions = "恭喜您".$name."，参加大屏幕抽奖，抽中了：" . $prize['awardname'] . "，请留意领奖时间！";
    $id = 0;
    //卡券推送
    if($prize['prizetype']==1){

        $url = $_W['siteroot'] .'app/'.$this->createMobileUrl('get_the_award', array('rid' => $rid,'id'=>$prize['id']));

//        $url = 'https://www.baidu.com';
        $actions = "恭喜您".$name."，参加大屏幕抽奖，抽中了：" . $prize['awardname'] . "，<a href='$url'>请点击领奖！</a>";
        $id= $prize['id'];
    }
//    $this->sendText($fans['from_user'], $actions);
    if($reply['is_award']==2){

        $this->prize_k_send_template($fans['from_user'],$mbid['l_templateid'],$id,$prize['awardname'],$rid);

    }elseif ($reply['is_award']==0){
          $this->sendText($fans['from_user'], $actions);
    }


}

    $data = array(
        'ret' => 1,
        'msg' => '成功',
        'awarduser' => $awarduser,
    );
    echo json_encode($data);
    exit();