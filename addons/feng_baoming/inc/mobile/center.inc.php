<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 16/9/27
 * Time: 上午11:26
 */
defined('IN_IA') or exit('Access Denied');
include MODULE_ROOT . '/inc/mobile/init.php';
global $_W,$_GPC;
$arr = array("index","wancheng","ing","huo");//ing,为正在进行,jiaru,为已加入的行程,wancheng为已完成的.
$op=$_GPC['op'];
if(!in_array($op,$arr)){
    $op="index";
}
if($op =="index"){
    if($_W['ispost']){
        $json = file_get_contents("php://input");
        $arr = json_decode($json,true);
        $openid = "olX6Js8LNz9nTz-wEcJNagvuAH4k";
        $template_id = "4F_nplGmTynjkkKCGNGGGYBdoRuPPDKM0PVFUReQdt8";
        $url ="";
        sendtpl($openid,$url,$template_id,$arr);
        die;
    }
    $succtpl = $set['succtpl'];
    $openid = "olX6Js8LNz9nTz-wEcJNagvuAH4k";
    $template_id = $set['succ_tplid'];
    $url = "";
    $content = array();
    foreach ($succtpl as $key=>$vo){
        $content[$vo[0]]=array(
            "value"=>$vo[1],
        );
    }
    p($content);
    sendtpl($openid,$url,$template_id,$content);
    //include $this->template("center/index");
}
else if ($op =="huo"){

    include $this->template("center/huo");
}
