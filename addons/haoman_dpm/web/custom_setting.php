<?php
global $_W  ,$_GPC;

    load()->func('tpl');


    $rid =$_GPC['rid'];
    $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

    if(empty($rid)){
        message('参数错误！', '', 'error');
    }

    if ($operation == 'post') {
        $id = intval($_GPC['id']);


        //修改进入
        if (!empty($id)) {
            $item = pdo_fetch("SELECT * FROM " . tablename('haoman_dpm_custom') . " WHERE id = :id", array(':id' => $id));
            if (empty($item)) {
                message('抱歉，互动不存在或是已经删除！', '', 'error');
            }
        }

        if (checksubmit('submit')) {
            ini_set('max_execution_time', '0');
            if (empty($_GPC['goodsname'])) {
                message('请输入互动名称！');
            }
            if (empty($_GPC['mob_stock'])||empty($_GPC['dpm_stock'])) {
                message('请输入正确的活动地址！');
            }
            $data = array(
                'rid' => intval($rid),
                'uniacid' => intval($_W['uniacid']),
                'title' => $_GPC['goodsname'],
                'thumb'=>$_GPC['thumb'],
                'mob_stock'=>$_GPC['mob_stock'],
                'dpm_stock'=>$_GPC['dpm_stock'],
                'createtime' => time(),
            );

            if (empty($id)) {
                pdo_insert('haoman_dpm_custom', $data);
                $id = pdo_insertid();
            } else {
                unset($data['createtime']);
                pdo_update('haoman_dpm_custom', $data, array('id' => $id));
            }

            message('互动更新成功！', $this->createWebUrl('custom_setting', array('op' => 'display', 'rid' => $rid)), 'success');
        }
    } elseif ($operation == 'display') {
        $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $condition = 'WHERE rid = :rid and `uniacid` = :uniacid';
        $params = array(':rid'=>$rid,':uniacid' => $_W['uniacid']);

        $sql = 'SELECT COUNT(*) FROM ' . tablename('haoman_dpm_custom') . $condition;
        $total = pdo_fetchcolumn($sql, $params);

        if (!empty($total)) {
            $sql = 'SELECT * FROM ' . tablename('haoman_dpm_custom') . $condition . ' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
    } elseif ($operation == 'delete') {
        $id = intval($_GPC['id']);

        $row = pdo_fetch("SELECT id FROM " . tablename('haoman_dpm_custom') . " WHERE id = :id", array(':id' => $id));
        if (empty($row)) {
            message('抱歉，互动规则不存在或是已经被删除！');
        }

        //修改成不直接删除，而设置deleted=1
        pdo_delete('haoman_dpm_custom', array('id' => $id));
        message('删除成功！', referer(), 'success');
    }
    include $this->template('custom_setting');
