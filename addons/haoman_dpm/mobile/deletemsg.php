<?php
global $_GPC, $_W;
$id = intval($_GPC['msgid']);
$rid = intval($_GPC['rid']);
$uid = $_GPC['uid'];
$op = $_GPC['op'];
$from_user = $_W['openid'];
$uniacid = $_W['uniacid'];

    load()->model('account');
    $_W['account'] = account_fetch($_W['acid']);
    $cookieid = '__cookie_haoman_dpm_201606186_' . $rid;
    $cookie = json_decode(base64_decode($_COOKIE[$cookieid]),true);
    if ($_W['account']['level'] != 4) {
        $from_user = $cookie['openid'];
    }

    $admin = pdo_fetch("select id from " . tablename('haoman_dpm_bpadmin') . "  where admin_openid=:admin_openid and status=0 and rid=:rid ", array(':admin_openid' => $from_user,':rid'=>$rid));
    if($op=='new'){
        $fid = $_GPC['fid'];
        if($fid){
            if($admin){
                $rule = pdo_fetchall("select id from " . tablename('haoman_dpm_messages') . " where rid = :rid and from_user =:from_user", array(':rid' => $rid,':from_user'=>$fid));
               if(empty($rule)){
                   $result = array(
                       'msg'=>'删除的信息不存在',
                       'id'=>'',

                   );

                   echo json_encode($result);
                   exit();
               }else{
                   if (pdo_delete('haoman_dpm_messages', array('rid' => $rid,'from_user'=>$fid))) {
                       $result = array(
                           'msg'=>'删除成功！',

                       );

                       echo json_encode($result);
                       exit();
                   }
               }
            }
            exit();
        }
        if($admin){
            $rule = pdo_fetch("select id from " . tablename('haoman_dpm_messages') . " where id = :id ", array(':id' => $id));
            if (empty($rule)) {
                $result = array(
                    'msg'=>'删除的信息不存在',
                    'id'=>'',

                );

                echo json_encode($result);
                exit();
            }
            if (pdo_delete('haoman_dpm_messages', array('id' => $id))) {
                $result = array(
                    'msg'=>'删除成功！',
                    'id'=>$id,

                );

                echo json_encode($result);
                exit();
            }
        }

    }else{
        if($admin||$uid==$from_user){
            $rule = pdo_fetch("select id from " . tablename('haoman_dpm_messages') . " where id = :id ", array(':id' => $id));
            if (empty($rule)) {
                $result = array(
                    'isResultTrue' => 0,
                    'msg'=>1,

                );

                echo json_encode($result);
                exit();
            }
            if (pdo_delete('haoman_dpm_messages', array('id' => $id))) {
                $result = array(
                    'isResultTrue' => 1,
                    'msg'=>'删除成功！',

                );

                echo json_encode($result);
                exit();
            }

        }else{
            $result = array(
                'isResultTrue' => 0,
                'msg'=>3,

            );

            echo json_encode($result);
            exit();
        }
    }




