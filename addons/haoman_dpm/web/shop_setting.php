<?php
global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->model('reply');
load()->func('tpl');

// message($rid);

if($operation == 'addad'){
    $id = intval($_GPC['shopid']);

    $insert_ = array(
        'rid' =>$rid,
        'uniacid' => $_W['uniacid'],
        'shop_status' => $_GPC['shop_status'],
        'shop_bg_color' => $_GPC['shop_bg_color'],
        'shop_color' => $_GPC['shop_color'],
        'shop_title' => $_GPC['shop_title'],
        'shop_font_color' => $_GPC['shop_font_color'],
        'creattime' => time(),
    );


    if(empty($id)){
        pdo_insert('haoman_dpm_shop_setting',$insert_);
        message("提交成功",$this->createWebUrl('shop_setting',array('rid'=>$rid)),"success");
    }else{
        pdo_update('haoman_dpm_shop_setting', $insert_, array('id' => $id));
        message("修改成功",$this->createWebUrl('shop_setting',array('rid'=>$rid)),"success");
    }





}else{

    $shop = pdo_fetch("select * from " . tablename('haoman_dpm_shop_setting') . " where rid = :rid order by `id` asc", array(':rid' => $rid));

    include $this->template('shop_setting');

}