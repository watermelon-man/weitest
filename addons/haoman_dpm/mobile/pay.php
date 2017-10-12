<?php
global $_GPC, $_W;
$uniacid = $_W['uniacid'];
$from_user = $_W['fans']['from_user'];
$orderid = intval($_GPC['orderid']);

if(empty($orderid)){
    message("订单号不能为空",'','error');
}
$pay_type = empty($_GPC['pay_type'])?0:$_GPC['pay_type'];


if($pay_type==0){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>0));
}elseif ($pay_type==1){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_ds_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>1));
}elseif ($pay_type==2){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>2));
}elseif ($pay_type==3){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>3));
}elseif ($pay_type==4){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>4));
}elseif ($pay_type==8){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>8));
}elseif ($pay_type==6){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>6));
}elseif ($pay_type==7){
    $order = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE uniacid = :uniacid AND id = :id AND status = :status AND pay_type=:pay_type", array(':uniacid' => $uniacid,':id' => $orderid,':status' => 1,':pay_type'=>7));
}

if($order == false){

    message("不存在该笔订单！".$pay_type,'','error');

}else {

    $params['tid'] = $order['transid'];
    $params['user'] = $_W['fans']['from_user'];


    if($order['pay_type']==0){
        $params['title'] = $order['from_realname'] . "报名费用";
        $params['fee'] = $order['pay_total']/100;
    }elseif ($order['pay_type']==1){
        $params['title'] = "给".$order['nickname']."的打赏小费";
        $params['fee'] = $order['pay_total'];
    }elseif ($order['pay_type']==2){
        $params['title'] = "大屏幕霸屏费用";
        $params['fee'] = $order['pay_total'];
    }elseif ($order['pay_type']==3){
        $params['title'] = "大屏幕打赏费用";
        $params['fee'] = $order['pay_total'];
    }elseif ($order['pay_type']==4){
        $params['title'] = "大屏幕发红包";
        $params['fee'] = $order['pay_addr'];
    }elseif ($order['pay_type']==8){
        $params['title'] = "商城购物";
        $params['fee'] = $order['pay_total'];
    }elseif ($order['pay_type']==6){
        $params['title'] = "大屏幕表白";
        $params['fee'] = $order['pay_total'];
    }elseif ($order['pay_type']==7){
        $params['title'] = "大屏幕送礼";
        $params['fee'] = $order['pay_total'];
    }

    $params['ordersn'] = $order['transid'];


    include $this->template('bm_pay');
}