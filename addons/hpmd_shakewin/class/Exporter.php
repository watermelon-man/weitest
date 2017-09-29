<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/dbutility.php');

class Exporter 
{
    public static function  ExportFansToCsv()
    {
		set_time_limit(180000);
		$tableHeader = array(
				'nickname' => '昵称',
				'headimg' => '头像',
				'createtime' => '获奖时间',
				'name' => '奖品名称',
				'replywords' => '活动标识',
				'code' => '兑换码',
				'awardstatus' => '状态',
		);
		
		$listawards = DBUtility::getAllWinFans();
		$list = array();
		if(!empty($listawards)) {
			foreach ($listawards as $item) {
				//$subdata['id'] = $item['id'];
				$subdata['awardstatus'] = $item['awardstatus'];
				$subdata['createtime'] = $item['createtime'];

				if(!empty($item['openid'])) {
					$faninfo = DBUtility::getWeixUsrInfoByOpenid($item['openid']);
					if(!empty($faninfo)) {
						$subdata['nickname'] = $faninfo['nickname'];
						$subdata['headimg'] = $faninfo['headimg'];
					}
				}
				if(!empty($item['awardid'])) {
					$awardinfo = DBUtility::getAwardsById($item['awardid']);
					if(!empty($awardinfo)) {
						$subdata['name'] = $awardinfo['name'];
						$subdata['ruleid'] = $awardinfo['ruleid'];
						$subdata['code'] = $awardinfo['code'];
					}

				}
				if(!empty($item['ruleid'])) {
					$replywords = DBUtility::getReplyWordById($item['ruleid']);
					if(!empty($replywords)) {
						$subdata['replywords'] = $replywords;
					}
				}
				$list[] = $subdata;
			}
		}
		// 输入到CSV文件
		$html = "\xEF\xBB\xBF";
		// 输出表头
		foreach ($tableHeader as $value) {
			$html .= $value . ',';
		}
		$html .= "\n";
		// 输出内容
		$curopenid = '';
		
		$sendid = 0;
		$curnum = 0;
		$sendnum = 0;
		foreach ($list as $value) {
			if($value['audit'] == '不通过') {
				continue;
			}
			$curnum += 1;
			foreach ($tableHeader as $key => $header) {
				if ($key == 'awardstatus') {
					if($value[$key] == 0) {
						$value[$key] = '未中奖';
					} else if($value[$key] == 1) {
						$value[$key] = '已中奖/未发放';
					} else if($value[$key] == 1) {
						$value[$key] = '已中奖/已发放';
					}
				}
				if ($key == 'createtime') {
					$value[$key] = date('Y-m-d H:i:s',$value[$key]);
				}
				$html .= $value[$key] . ',';
			}
			$html .= "\n";
		}

		// 输出CSV文件
		header("Content-type:text/csv");
		header("Content-Disposition:attachment; filename=摇一摇中大奖中奖数据.csv");
		echo $html;
		exit();
    }
}