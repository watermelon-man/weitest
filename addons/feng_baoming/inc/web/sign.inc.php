<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 16/9/29
 * Time: 下午3:16
 */
defined('IN_IA') or exit ('Access Denied');
include MODULE_ROOT."/inc/web/init.php";
$arr = array("index","list","add");
$op = $_GPC['op'];
if($_W['isajax']){
    $action=$_GPC['action'];
     if ($action=="del"){
        $idd = $_GPC['idd'];
        $res = pdo_delete("feng_baoming_user",array("id"=>$idd,"uniacid"=>$uniacid));
        if($res){
            echo exit(json_encode(array("error"=>1,"message"=>"删除成功")));
            die;
        }else{
            echo exit(json_encode(array("error"=>-1,"message"=>"删除失败")));
            die;
        }
    }
}
if(!in_array($op,$arr)){
    $op="index";
}
if($op=="index"){
    //这里是列表
    $list = pdo_fetchall("SELECT * FROM ".tablename("feng_baoming_user")." WHERE uniacid='$uniacid'");
    include $this->template("web/sign/index");
}