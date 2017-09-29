<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/23
 * Time: 23:55
 */
class modules
{
    public function getModules($name){
        $sql = "SELECT * FROM ".tablename('modules')." WHERE name = :name";
        $params = array(':name'=>$name);
        $item = pdo_fetch($sql,$params);
    }

}