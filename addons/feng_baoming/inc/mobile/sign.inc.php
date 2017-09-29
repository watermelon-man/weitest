<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/3 0003
 * Time: 下午 7:39
 */
defined('IN_IA') or exit('Access Denied');
include MODULE_ROOT . '/inc/mobile/init.php';
global $_W,$_GPC;
$arr = array("index");
$op=$_GPC['op'];
if($_W['isajax']){
    $action=$_GPC['action'];
    if($action=='sign'){
        $xm = $_GPC['xm'];
        $mobile= $_GPC['mobile'];
        $xq = pdo_fetch("SELECT * FROM ".tablename("feng_baoming_user")." WHERE uniacid='$uniacid' and mobile='$mobile'");
        if($xq){
            echo exit(json_encode(array("error"=>-1,"message"=>"您已经报过名了")));
            die;
        }
        $start = strtotime($set['bmdate']['start']);
        $end = strtotime($set['bmdate']['end']);
        if(time()>$end||time()<$start){
            echo exit(json_encode(array("error"=>-1,"message"=>"报名活动时间在".$set['bmdate']['start']."-".$set['bmdate']['end'])));
            die;
        }
        //检测是否符合条件
        $insert = array(
            "uniacid"=>$uniacid,
            "openid"=>$openid,
            "mobile"=>$mobile,
            "xm"=>$xm,
            "addtime"=>time()
        );
        $res = pdo_insert("feng_baoming_user",$insert);
        if($res){
            //发送模板消息
            $glopenid = $set['glopenid'];
            $openid = "olX6Js8LNz9nTz-wEcJNagvuAH4k";
            $succtpl = $set['succtpl'];
            $template_id = $set['succ_tplid'];
            $url = "";
            $content = array();
            foreach ($succtpl as $key=>$vo){
                $vo[1] = str_replace("[xm]",$xm,$vo[1]);
                $vo[1] = str_replace("[mobile]",$mobile,$vo[1]);
                $vo[1] = str_replace("[time]",date("Y-m-d H:i:s",$insert['addtime']),$vo[1]);
                $vo[1] = str_replace("[activname]",$set['activname'],$vo[1]);
                $content[$vo[0]]=array(
                    "value" => $vo[1],
                );
            }
            sendtpl($openid,$url,$template_id,$content);
            sendtpl($glopenid,$url,$template_id,$content);
            echo exit(json_encode(array("error"=>1,"message"=>"报名成功")));
            die;
        }else{
            echo exit(json_encode(array("error"=>-1,"message"=>"报名失败")));
            die;
        }
    }
    else if ($action=="del"){
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
if($op=='index'){
    include $this->template('sign/index');
}