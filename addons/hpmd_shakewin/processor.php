<?php
/**
 * shakewin模块处理程序
 *
 * @author yh
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

define("SHAKE_WIN_DIR", "hpmd_shakewin");
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CacheLayer.php');

class Hpmd_shakewinModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_GPC, $_W;
		$openid = $this->message['from'];
		$content = $this->message['content'];

		$item = pdo_fetch_with_cache('shakewin_rules', "select * from " . tablename('shakewin_rules') . " where replywords='" . $content . "' limit 1");
		if ($item != false && !empty($item['title'])) {
		    //file_put_contents('./addons/hpmd_shakewin/yuyin.txt', 'Title: ' . $fans['title'] . ' Description: ' . $fans['description'] . PHP_EOL , FILE_APPEND);
			return $this->respNews(array(
				'Title' => $item['title'] . ' - 主题`' . $content . '`',
				'Description' => $item['description'],
				'PicUrl' => $_W['siteroot'] . 'addons/hpmd_shakewin/icon.jpg',
				'Url' => $this->createMobileUrl('Index', array('replywords' => $content)),
			));
		}
		
	}
}