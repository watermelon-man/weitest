<?php
/**
 * function.php
 * @authors smilér (2605401514@qq.com)
 * @link: http://www.i9u.cc
 * @date: 2017/3/30 下午3:11
 */

function pr($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function getTips($str,$type=0){
    if(strpos($str,'|') !== false){
        $arr = explode('|',$str);
        echo $arr[$type];
    }else{
        echo $str;
    }
}