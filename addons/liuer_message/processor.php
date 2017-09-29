<?php
/**
 * 定制留言模块处理程序
 *
 * @author 模块终结者
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Liuer_messageModuleProcessor extends WeModuleProcessor {
	public function respond() {
        global $_W;
        $content = $this->message['content'];

        //这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
        $rid = pdo_getcolumn('rule_keyword',array('content'=>$content,'uniacid'=>$_W['uniacid'],'module'=>'liuer_message'),'rid');

        if($rid){
            return $this->respNews(array(
                'Title' => $this->module['config']['sharetitle'] ? $this->module['config']['sharetitle'] : '定制留言',
                'Description' => $this->module['config']['sharedesc'] ? $this->module['config']['sharedesc'] : '最懂你的留言',
                'PicUrl' => toimage($this->module['config']['shareimg']) ? toimage($this->module['config']['shareimg']) : '',
                'Url' => $this->createMobileUrl('index'),
            ));
        }
    }
}