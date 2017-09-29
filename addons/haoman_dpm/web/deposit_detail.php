<?php
global $_W, $_GPC;
load()->func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$uniacid=$_W['uniacid'];
$rid = $_GPC['rid'];


if($operation==10){
    $status = $_GPC['status'];
    $id_ = $_GPC['id_'];

    $deposit = pdo_fetch("select * from " . tablename('haoman_dpm_deposit') . " where id=:id and rid=:rid", array(':id' => $id_,':rid'=>$rid));
    if(empty($deposit)){
        $data = array(
            'success' => 100,
            'msg' => '未找到该订单',
        );
        echo json_encode($data);
        exit();
    }else{
        if($deposit['status']==$status){
            $data = array(
                'success' => 100,
                'msg' => '订单状态错误',
            );
            echo json_encode($data);
            exit();
        }else{

            if($status==1){
                pdo_update('haoman_dpm_deposit',array('status'=>$status),array('id'=>$deposit['id']));

            }else{
                pdo_update('haoman_dpm_deposit',array('status'=>$status,'closetime'=>time()),array('id'=>$deposit['id']));

            }

            $data = array(
                'success' => 1,
                'msg' => '订单状态修改成功',
            );
            echo json_encode($data);
            exit();

        }
    }

}


if ($operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $status = $_GPC['status'];
    $sendtype = !isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype'];
    $condition = "rid=:rid and uniacid = :uniacid";
    $paras = array(':rid'=>$rid,':uniacid' => $_W['uniacid']);
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime = time();
    }
    if (!empty($_GPC['time'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime = strtotime($_GPC['time']['end']) + 86399;
        $condition .= " AND createtime >= :starttime AND createtime <= :endtime ";
        $paras[':starttime'] = $starttime;
        $paras[':endtime'] = $endtime;
    }

    if (!empty($_GPC['keyword'])) {
        $condition .= " AND orderid LIKE '%{$_GPC['keyword']}%'";
    }
    if (!empty($_GPC['member'])) {
        $condition .= " AND nickname LIKE '%{$_GPC['member']}%'";
    }
    if ($status != '') {
        $condition .= " AND status = '" . intval($status) . "'";
    }

    $sql = 'SELECT * FROM ' . tablename('haoman_dpm_deposit') .   ' WHERE ' . $condition . ' ORDER BY createtime  DESC, status DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql,$paras);
    foreach ($list as &$v){
        $v['nickname'] = pdo_fetchcolumn("SELECT nickname FROM " . tablename('haoman_dpm_fans') . " WHERE id=:id and uniacid=:uniacid and rid=:rid",array(':rid'=>$rid,':uniacid'=>$uniacid,':id'=>$v['fansid']));

    }
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('haoman_dpm_deposit') ." WHERE $condition", $paras);
    $pager = pagination($total, $pindex, $psize);


} elseif ($operation == 'detail') {


    $id = intval($_GPC['id']);
    $item = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_pay_order') . " WHERE id = :id", array(':id' => $id));
    $ids = $item['transid'];
    if (empty($item)) {
        message("抱歉，订单不存在!", referer(), "error");
    }
    if (checksubmit('finish')) {
        pdo_update('haoman_dpm_pay_order', array('status' => 3, 'message' =>'PC完成','closetime'=>time()), array('id' => $id, 'uniacid' => $_W['uniacid']));
        message('订单操作成功！', referer(), 'success');
    }
    if (checksubmit('cancelsend')) {
        $item = pdo_fetch("SELECT transaction_id,transid FROM " . tablename('haoman_dpm_pay_order') . " WHERE id = :id AND uniacid = :uniacid", array(':id' => $id, ':uniacid' => $_W['uniacid']));
//        if (!empty($item['transaction_id'])) {
//            $this->changeWechatSend($item['transid'], 0, $_GPC['cancelreson']);
//        }
        pdo_update('haoman_dpm_pay_order',
            array(
                'status' => 4,
                 'message' => $_GPC['cancelreson'],
                 'closetime'=>time(),
            ),
            array('id' => $id)
        );
        message('取消订单操作成功！', referer(), 'success');
    }

    $item['user'] = pdo_fetch("SELECT realname,nickname,mobile FROM " . tablename('haoman_dpm_fans') . " WHERE rid=:rid and uniacid=:uniacid and from_user=:from_user",array(':rid'=>$rid,':uniacid'=>$uniacid,':from_user'=>$item['from_user']));



    $goods = pdo_fetchall("SELECT g.*, o.* FROM " . tablename('haoman_dpm_pay_order_goods') . " o left join " . tablename('haoman_dpm_shop_goods') . " g on o.goodsid=g.id " . " WHERE o.orderid='{$ids}'");

    $item['goods'] = $goods;

} elseif ($operation == 'delete') {
    /*订单删除*/
    $orderid = intval($_GPC['id']);
    if (pdo_delete('haoman_dpm_deposit', array('id' => $orderid))) {
        message('订单删除成功', $this->createWebUrl('deposit_detail', array('op' => 'display','rid'=>$rid)), 'success');
    } else {
        message('订单不存在或已被删除', $this->createWebUrl('deposit_detail', array('op' => 'display','rid'=>$rid)), 'error');
    }
}

include $this->template('deposit_detail');