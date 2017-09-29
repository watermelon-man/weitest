<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CommonValStr.php');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/dbutility.php');

class ShakeAuthorize 
{
	public static $APPID = '';
	public static $APPSECRET = '';

    public static function GetFansInfo($replywords, $APPID)
	{
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		$weid = $_W['uniacid'];

		if(empty($replywords)) {
			return;
		}

		record_log('k', 'txt', 'GetFansInfo key ', $APPID);
		$urlcallback = urlencode($_W['siteroot'].'app/index.php?i=' . $weid . '&c=entry&do=GetUsrInfo&m=shakewin&userid=' . $_W['fans']['openid'] . '&replywords=' . $replywords);
		echo "<script type='text/javascript'>window.location.href=\"https://open.weixin.qq.com/connect/oauth2/authorize?appid=". $APPID. "&redirect_uri=" . $urlcallback . "&response_type=code&scope=snsapi_userinfo&state=12321#wechat_redirect\";</script>";
	}
    public static function GetUsrInfo($APPID, $APPSECRET)
	{
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		$weid = $_W['uniacid'];
		$code = $_GPC['code'];
		$fakeopenid = $_GPC['userid'];
		$replywords = $_GPC['replywords'];
		record_log('care', 'txt', 'code: ', $code);
		record_log('care', 'txt', 'replywords: ', $replywords);

		record_log('k', 'txt', 'GetUsrInfo key ', $APPID);
		record_log('k', 'txt', 'GetUsrInfo secret ', $APPSECRET);
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. $APPID .  '&secret=' . $APPSECRET . '&code=' . $code . '&grant_type=authorization_code';
		$ret = httpGet($url);
		$decoderet = json_decode($ret);
		$newopenid = $decoderet->openid;
		$access_token = $decoderet->access_token;

		$url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token='. $access_token .'&openid='. $newopenid .'&lang=zh_CN';
		$ret2 = httpGet($url2);
		record_log('care', 'txt', 'ret2: ', $ret2);
		$decoderet2 = json_decode($ret2);
		$nickname = $decoderet2->nickname;
		$sex = $decoderet2->sex;
		$headimgurl = $decoderet2->headimgurl;
        
		record_log('care', 'txt', 'nickname: ', $nickname);
		record_log('care', 'txt', 'sex: ', $sex);
		record_log('care', 'txt', 'headimgurl: ', $headimgurl);
		if(!empty($nickname) && !empty($headimgurl)) {
			$insert = array(
				'openid' => $openid,
				'weid' => $weid,
				'createtime' => time(),
				'nickname' => $nickname,
				'sex' => $sex,
				'headimg' => $headimgurl,
			);
			DBUtility::insert(DBUtility::$table_shakewin_wxinfo, $insert);

			return array('replywords' => $replywords, 'returnval' => CommonValStr::$GET_USRINFO_SUCCESS_VAL);
		} else {
			return array('replywords' => $replywords, 'returnval' => CommonValStr::$GET_USRINFO_FAIL_VAL);
		}
	}
 
}