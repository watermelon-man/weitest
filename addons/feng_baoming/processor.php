<?php
/**
 * huoquxinxi模块处理程序
 *
 * @author feng
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
class Feng_baomingModuleProcessor extends WeModuleProcessor {
	public function respond()
    {
        global $_W;
        $content = $this->message['content'];
    }
}