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
if(!in_array($op,$arr)){
   $op="index";
}
if($op=="index"){
        //这里是列表
    include $this->template("web/card_num/index");
}else if($op=="add"){
        //添加或者导入
    include $this->template("web/card_num/add");
}