<?php

/**
 * 简单预约模块处理程序
 *
 * @author binbin
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Bin_appointModuleProcessor extends WeModuleProcessor {

    public function respond() {
        $content = $this->message['content'];
        //这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
        $rid = $this->rule;
        $rule = pdo_fetch('SELECT  * FROM ' . tablename($this->modulename . '_rule') . " WHERE rid=:rid AND status=1",array(":rid"=>$rid));
        if (!empty($rule)) {
            return $this->respNews(array(array('title' => $rule['title'], 'description' => $rule['description'], 'picurl' => toimage($rule['thumb']), 'url' => $this->createMobileUrl('index', array('rid' => $rid)))));
        }
    }

}
