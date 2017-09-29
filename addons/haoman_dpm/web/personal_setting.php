<?php
global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->model('reply');
load()->func('tpl');

// message($rid);

if($operation == 'addad'){
    $id = intval($_GPC['personalid']);

    $insert_ = array(
        'personal_style' => $_GPC['personal_style'],
        'mobtitle' => $_GPC['mobtitle'],
        'show_sex' => $_GPC['show_sex'],
        'show_qd' => $_GPC['show_qd'],
        'is_changge' => $_GPC['is_changge'],
        'my_bg' => $_GPC['my_bg'],
        'prize_bg' => $_GPC['prize_bg'],
        'money_bg' => $_GPC['money_bg'],
    );


    if(empty($id)){
        pdo_insert('haoman_dpm_setting', array('settings' => serialize($insert_), 'rid' =>$rid,'uniacid' => $_W['uniacid']));
        message("提交成功",$this->createWebUrl('personal_setting',array('rid'=>$rid)),"success");
    }else{
        pdo_update('haoman_dpm_setting', array('settings' => serialize($insert_)), array('id' => $id));
        message("修改成功",$this->createWebUrl('personal_setting',array('rid'=>$rid)),"success");
    }





}else{

    $personals = pdo_fetch("select * from " . tablename('haoman_dpm_setting') . " where rid = :rid order by `id` asc", array(':rid' => $rid));
    $personal = unserialize($personals['settings']);
    if(empty($personal['my_bg'])){
        $personal['my_bg'] = '../addons/haoman_dpm/images/my.jpg';
    }
    if(empty($personal['prize_bg'])){
        $personal['prize_bg'] = '../addons/haoman_dpm/images/my.jpg';
    }
    if(empty($personal['money_bg'])){
        $personal['money_bg'] = '../addons/haoman_dpm/images/my.jpg';
    }
    include $this->template('personal_setting');

}