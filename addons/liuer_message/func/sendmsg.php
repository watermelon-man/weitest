<?php
/**
 * sendmsg.php 发送模板消息
 * @authors Smilér (2605401514@qq.com)
 * @link: http://www.iyoumei.net
 * @date: 2017/3/22 13:49
 */

//发送留言成功消息
function sendlyTpl($openid,$templateid,$data){
    $weAcc = WeAccount::create();
    $postdata = array(
        'first' => array(
            'value' => $data['title'],
            'color' => '#ff9900'
        ),
        'keyword1' => array(
            'value' => $data['name'],
            'color' => '#273177'
        ),
        'keyword2' => array(
            'value' => $data['content'],
            'color' => '#273177'
        ),
        'remark' => array(
            'value' => $data['remark'],
            'color' => '#273177'
        ),
    );
    $weAcc->sendTplNotice($openid,$templateid,$postdata,$data['url']);
}

