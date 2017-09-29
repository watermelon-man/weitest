<?php
/**
 * 小逗比聊天机器人模块微站定义
 *
 * @author zwe10
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Liaotian_doubiModuleSite extends WeModuleSite {
    public function doWebIndex(){
        include  $this->template("index");
    }
}