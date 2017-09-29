<?php

/**
 * 随机回复模块微站定义
 *
 * @author tyzy313481929
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
require_once "jssdk.php";
class Random_textModuleSite extends WeModuleSite {

    public function doMobileIndex() {
        global $_W, $_GPC;
//       $user_info = mc_oauth_userinfo();
//        $url = "http://" . $_SERVER[HTTP_HOST];
//        load()->classs("weixin.account");
////       var_dump($_W['account']);
//        $platform = new WeiXinAccount($_W['account']);
//        $rs = $platform->getJssdkConfig($url);
//        var_dump($rs);
    $jssdk = new JSSDK();
    $rs = $jssdk->GetSignPackage();
var_dump($rs);
        include $this->template('index');
    }

    public function doMobileGetlbs() {
        global $_GPC, $_W;
//$id = intval($_GPC['id']);
        $lat1 = $_GPC['lat'];
        $lon1 = $_GPC['lon'];
        file_put_contents("/tmp/ceshi", son_encode($data));
//        echo json_encode($data);
    }

}
