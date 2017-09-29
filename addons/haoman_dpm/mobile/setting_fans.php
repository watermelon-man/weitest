<?php
global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$rid = intval($_GPC['rid']);



$realname = trim($_GPC['realname']);
$mobile = trim($_GPC['mobile']);
$address = trim($_GPC['address']);
$sex = $_GPC['sex'];
$fansid = $_GPC['fansid'];

if(empty($fansid)){
    $data = array(
        'success' => 100,
        'msg' => '参数错误！',
    );
}else{
    $fans = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_fans') . " WHERE id =:id and  uniacid=:uniacid AND rid = :rid  LIMIT 1", array(':uniacid' => $uniacid, ':rid' => $rid,':id'=>$fansid));

    if(empty($fans)){
        $data = array(
            'success' => 100,
            'msg' => '粉丝数据错误！',
        );
    }else{
      $stem=  pdo_update('haoman_dpm_fans', array('realname'=>$realname,'mobile'=>$mobile,'address' =>$address,'sex'=>$sex), array('id' =>$fansid));
       if($stem){
           $data = array(
               'success' => 1,
               'msg' => '修改成功！',

           );
       }else{
           $data = array(
               'success' => 100,
               'msg' => '修改失败！',

           );
       }
    }

}
echo json_encode($data);