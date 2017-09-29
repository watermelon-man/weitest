<?php

/**
 * 成语大全模块处理程序
 *
 * @author tyzy313481929
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Random_chengyuModuleProcessor extends WeModuleProcessor {

    public $appkey = '44f4f9639061a24c06b073c924d69e2e';
    public $url = 'http://v.juhe.cn/chengyu/query';

    public function respond() {
        $content = $this->message['content'];
        //这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
        $content_arr = explode('+', $content);
        $params = array(
            "word" => $content_arr[1], //填写需要查询的汉字，UTF8 urlencode编码
            "key" => $this->appkey, //应用APPKEY(应用详细页查询)
            "dtype" => "", //返回数据的格式,xml或json，默认json
        );
        $paramstring = http_build_query($params);
        $chengyu_content = $this->juhecurl($this->url, $paramstring);
        $result = json_decode($chengyu_content, true);
        if ($result) {
            if ($result['error_code'] == '0') {
                $rs = '释意:';
                $rs .= $result['result']['chengyujs'].PHP_EOL;
                $rs .= '出自:';
                $rs .= $result['result']['from_'];
            } else {
                $rs = $result['reason'];
            }
        } else {
            $rs = "请求失败";
        }
        return $this->respText($rs);
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    public function juhecurl($url, $params = false, $ispost = 0) {
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

}
