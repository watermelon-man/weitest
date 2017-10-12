<?php
global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$token = !empty($_GPC['token']) ? $_GPC['token'] : '0';
load()->model('reply');
load()->func('tpl');
$sql = "uniacid = :uniacid and `module` = :module";
$params = array();
$params[':uniacid'] = $_W['uniacid'];
$params[':module'] = 'haoman_dpm';

$rowlist = reply_search($sql, $params);

// message($rid);

if($token==2){

    if($operation == 'addad'){

        $price = $_GPC['price'];
        if(empty($price)||$price<0){
            $price=0;
        }
        $updata = array(
            'rid' => $rid,
            'uniacid' => $_W['uniacid'],
            'sort' => 1,
            'thumb' => $_GPC['thumb'],
            'bp_vodiobg' => $_GPC['bp_vodiobg'],
            'name' => $_GPC['bp_name'],
            'extend_from' => 0,
            'price' => $price,
            'bp_time' => $price,
            'rank_position' => 1,
            'enabled' => $_GPC['enabled'],
            'selected' => 1,
            'condition_text' => $_GPC['condition_text'],
        );

        // message($keywords['name']);

        $temp = pdo_insert('haoman_dpm_bptheme', $updata);

        message("添加霸屏主题成功",$this->createWebUrl('bapinshow',array('rid'=>$rid,'token'=>2)),"success");

    }elseif($operation == 'up'){
        $uid = intval($_GPC['uid']);
        if(empty($uid)){
            message('获取霸屏主题ID出错，请刷新后重试', '', 'error');
        }
        $item = pdo_fetch("select * from " . tablename('haoman_dpm_bptheme') . "  where id=:uid ", array(':uid' => $uid));
        $keywords = reply_single($item['rid']);
        include $this->template('updatabp_theme');
        exit();

    }elseif($operation == 'del'){
        $uid = intval($_GPC['uid']);
        if(empty($uid)){
            message('获取主题ID出错，请刷新后重试', '', 'error');
        }
        pdo_delete('haoman_dpm_bptheme', array('id' => $uid));
        message("删除霸屏主题成功",$this->createWebUrl('bapinshow',array('rid'=>$rid,'token'=>2)),"success");

    }elseif($operation == 'updataad'){

        $id = $_GPC['listid'];


        $keywords = reply_single($_GPC['rulename']);
        $price = $_GPC['price'];
        if(empty($price)||$price<0){
            $price=0;
        }
        $updata = array(
            'rid' => $rid,
            'uniacid' => $_W['uniacid'],
            'sort' => 1,
            'thumb' => $_GPC['thumb'],
            'name' => $_GPC['bp_name'],
            'bp_vodiobg' => $_GPC['bp_vodiobg'],
            'extend_from' => 0,
            'price' => $price,
            'bp_time' => $price,
            'rank_position' => 1,
            'enabled' => $_GPC['enabled'],
            'selected' => 1,
            'condition_text' => $_GPC['condition_text'],
        );



        $temp =  pdo_update('haoman_dpm_bptheme',$updata,array('id'=>$id));

        message("修改霸屏主题成功",$this->createWebUrl('bapinshow',array('rid'=>$rid,'token'=>2)),"success");


    }else{

        include $this->template('newbb_theme');
        exit();
    }


}
elseif($token==3){

    if($operation == 'addad'){

        $updata = array(
            'rid' => $rid,
            'uniacid' => $_W['uniacid'],
            'createtime' => time(),
            'status' => 0,
            'bp_type' => $_GPC['bp_type'],
            'bp_images' => $_GPC['bp_images'],
            'bp_videos' => $_GPC['bp_videos'],
        );

        // message($keywords['name']);

        $temp = pdo_insert('haoman_dpm_bpmoney', $updata);

        message("添加霸屏背景成功",$this->createWebUrl('bapinshow',array('rid'=>$rid,'token'=>3)),"success");

    }elseif($operation == 'up'){
        $uid = intval($_GPC['uid']);
        if(empty($uid)){
            message('获取霸屏背景ID出错，请刷新后重试', '', 'error');
        }
        $item = pdo_fetch("select * from " . tablename('haoman_dpm_bpmoney') . "  where id=:uid ", array(':uid' => $uid));

        $keywords = reply_single($item['rid']);
        include $this->template('updatanew_bpbg');
        exit();

    }elseif($operation == 'del'){
        $uid = intval($_GPC['uid']);
        if(empty($uid)){
            message('获取背景ID出错，请刷新后重试', '', 'error');
        }
        pdo_delete('haoman_dpm_bpmoney', array('id' => $uid));
        message("删除霸屏背景成功",$this->createWebUrl('bapinshow',array('rid'=>$rid,'token'=>3)),"success");

    }elseif($operation == 'updataad'){

        $id = $_GPC['listid'];

        $bp_images = $_GPC['bp_images'];
        if($_GPC['bp_type']==4){
            $bp_images = $_GPC['bp_images2'];
        }
        $updata = array(
            'rid' => $rid,
            'uniacid' => $_W['uniacid'],
            'createtime' => time(),
            'status' => 0,
            'bp_type' => $_GPC['bp_type'],
            'bp_images' => $_GPC['bp_images'],
            'bp_videos' => $_GPC['bp_videos'],
        );



        $temp =  pdo_update('haoman_dpm_bpmoney',$updata,array('id'=>$id));

        message("修改霸屏背景成功",$this->createWebUrl('bapinshow',array('rid'=>$rid,'token'=>3)),"success");


    }else{

        include $this->template('new_bpbg');
        exit();
    }


}


if($operation == 'updataad'){

    $id = $_GPC['listid'];

    if($_GPC['bp_money']<=0){
        message('霸屏金额最小值为0.01元，请留意', '', 'error');
    }
    if($_GPC['bp_time']<=0){
        message('霸屏时间最小值为1秒，请留意', '', 'error');
    }
    // message($_GPC['cardnum']);
    $keywords = reply_single($_GPC['rulename']);
   $img_count = $_GPC['img_count'];
   if($img_count<1||empty($img_count)||$img_count>4){
       $img_count =1;
   }
    $updata = array(
        'rid' => $rid,
        'uniacid' => $_W['uniacid'],
        'bp_money' => $_GPC['bp_money'],
        'bp_time' => intval($_GPC['bp_time']),
        'createtime' => time(),
        'status' => $_GPC['status'],
        'bp_type' => $_GPC['bp_type'],
        'bp_images' => $_GPC['bp_images'],
        'img_count' => $img_count,
    );


    $temp =  pdo_update('haoman_dpm_bpmoney',$updata,array('id'=>$id));

    message("修改霸屏金额成功",$this->createWebUrl('bapinshow',array('rid'=>$rid)),"success");


}elseif($operation == 'addad'){

    // message($_GPC['cardname']);
    if($_GPC['bp_money']<=0){
        message('霸屏金额最小值为0.01元，请留意', '', 'error');
    }
    if($_GPC['bp_time']<=0){
        message('霸屏时间最小值为1秒，请留意', '', 'error');
    }
    $keywords = reply_single($_GPC['rulename']);
    $img_count = $_GPC['img_count'];
    if($img_count<1||empty($img_count)||$img_count>4){
        $img_count =1;
    }
    $updata = array(
        'rid' => $rid,
        'uniacid' => $_W['uniacid'],
        'bp_money' => $_GPC['bp_money'],
        'bp_time' => intval($_GPC['bp_time']),
        'createtime' => time(),
        'status' => $_GPC['status'],
        'bp_type' => $_GPC['bp_type'],
        'bp_images' => $_GPC['bp_images'],
        'img_count' => $img_count,
    );

    // message($keywords['name']);

    $temp = pdo_insert('haoman_dpm_bpmoney', $updata);

    message("添加霸屏金额成功",$this->createWebUrl('bapinshow',array('rid'=>$rid)),"success");

}elseif($operation == 'up'){
    $uid = intval($_GPC['uid']);
    if(empty($uid)){
        message('获取霸屏金额ID出错，请刷新后重试', '', 'error');
    }
    $item = pdo_fetch("select * from " . tablename('haoman_dpm_bpmoney') . "  where id=:uid ", array(':uid' => $uid));
    $keywords = reply_single($item['rid']);
    include $this->template('updatabapin');

}elseif($operation == 'del'){
    $uid = intval($_GPC['uid']);
    if(empty($uid)){
        message('获取奖品ID出错，请刷新后重试', '', 'error');
    }
    pdo_delete('haoman_dpm_bpmoney', array('id' => $uid));
    message("删除霸屏金额成功",$this->createWebUrl('bapinshow',array('rid'=>$rid)),"success");

}else{


    include $this->template('newbapin');

}