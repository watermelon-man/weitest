<?php
/**
 * 随机回复模块处理程序
 *
 * @author tyzy313481929
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Random_textModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
                return $this->respNews(array(
                'Title' => $this->module['config']['sharetitle'] ? $this->module['config']['sharetitle'] : '定制留言',
                'Description' => $this->module['config']['sharedesc'] ? $this->module['config']['sharedesc'] : '最懂你的留言',
                'PicUrl' => toimage($this->module['config']['shareimg']) ? toimage($this->module['config']['shareimg']) : '',
                'Url' => $this->createMobileUrl('index'),
            ));
	}
}