<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 16/8/4
 * Time: 上午10:34
 */
function p($arr){
    echo "<pre>";
    print_r($arr);
}
//模板消息提醒
function sendtpl($openid, $url, $template_id, $content) {
    global $_GPC, $_W;
    load() -> classs('weixin.account');
    load() -> func('communication');
    $obj = new WeiXinAccount();
    $access_token = $obj -> fetch_available_token();
    $data = array(
        'touser' => $openid,
        'template_id' => $template_id,
        'url' => $url,
        'topcolor' => "#FF0000",
        'data' => $content,
    );
    $json = json_encode($data);
    $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
    $ret = ihttp_post($url, $json);
}
//文本消息提醒
function sendtext($text,$openid){
    global $_GPC, $_W;
    load() -> func('tpl');
    load() -> model('mc');
    $acc = WeAccount::create($_W['account']['acid']);
    $send = array("touser" =>$openid, "msgtype" => "text", "text" => array("content" => urlencode($text)));
    $res = $acc -> sendCustomNotice($send);
}
 function getmedia($media_id,$file,$picname){
    $access_token = WeAccount::token();
    $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$media_id;
    if (!file_exists($file)) {
        mkdir($file, 0777, true);
    }
    $targetName = '..'.$file.'/'.$picname;
    $ch = curl_init($url); // 初始化
    $fp = fopen($targetName, 'wb'); // 打开写入
    curl_setopt($ch, CURLOPT_FILE, $fp); // 设置输出文件的位置，值是一个资源类型
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $file.'/'.$picname;
}
//提现
function tx($partner_trade_no,$openid,$amount,$des,$appid,$key,$mchid){
    global $_W,$_GPC;
    load()->func('communication');
    $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
    $pars = array();
    $pars['mch_appid'] = $appid;
    $pars['mchid'] = $mchid;
    $pars['nonce_str']= random(32);
    $pars['partner_trade_no']=$partner_trade_no;
    $pars['openid']=$openid;
    $pars['check_name']= 'NO_CHECK';
    $pars['amount']=$amount;
    $pars['desc']=$des;
    $pars['spbill_create_ip']='127.0.0.1';
    ksort($pars,SORT_STRING);
    $string1 = '';
    foreach ($pars as $k => $v) {
        $string1 .= "{$k}={$v}&";
    }
    $string1 .= "key={$key}";//支付密钥
    $pars['sign'] = strtoupper(md5($string1));
    $xml = array2xml($pars);
    $extras = array();
    $extras['CURLOPT_CAINFO'] = MODULE_ROOT . '/cert/rootca.pem.' . $_W['uniacid'];
    $extras['CURLOPT_SSLCERT'] = MODULE_ROOT . '/cert/apiclient_cert.pem.' . $_W['uniacid'];
    $extras['CURLOPT_SSLKEY'] = MODULE_ROOT . '/cert/apiclient_key.pem.' . $_W['uniacid'];
    $procResult = null;
    $resp = ihttp_request($url, $xml, $extras);
    if (is_error($resp)) {
        $procResult = $resp;
    } else {
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
        $dom = new DOMDocument();
        if ($dom->loadXML($xml)) {
            $xpath = new DOMXPath($dom);
            $code = $xpath->evaluate('string(//xml/return_code)');
            $ret = $xpath->evaluate('string(//xml/result_code)');
            if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
                $procResult = error(1,'success');
            } else {
                $error = $xpath->evaluate('string(//xml/err_code_des)');
                $code = $xpath->evaluate('string(//xml/err_code)');
                $procResult = error(-2, array('code' => $code, 'error' => $error));
            }
        } else {
            $procResult = error(-1, 'error response');
        }
    }
    //echo exit(json_encode(array('error'=>-1,'message'=>$procResult)));
    return $procResult;
}
//把textarea 转换成数组
function textareaarr($string){
    $succ = explode("\n",trim($string));
    //分割成一个数组
    foreach ($succ as $key=>$vo){
        $arrvo =  explode("|",trim($vo));
        foreach($arrvo as $k=>$v){
            $arrvo[$k] =trim($v);
        }
        $arr[] = $arrvo;
        unset($arrvo);
    }
        return $arr;
}
//把数组转化成textarea换行数据
function arrtotextarea($arr){
    foreach($arr as $key=>$vo){
        $arr[$key] = implode("|",$vo);
    }
    $string = implode("\n",$arr);
    return $string;
}