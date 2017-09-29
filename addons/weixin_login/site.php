<?php
/**
 * 公众号授权系统模块微站定义
 *
 * @author Jialin
 * @url http://bbs.we7.cc/thread-13093-1-1.html
 * @qq 77035993
 */

//error_reporting(E_ALL);
defined('IN_IA') or exit('Access Denied');
define('MODULE_PATH','../addons/'.pathinfo(__DIR__,PATHINFO_BASENAME ).'/');

class Weixin_loginModuleSite extends WeModuleSite {

    protected $_dev = array(
        'add' => 'http://wxlogin.pjialin.com/Dev/add',
        'index' => 'http://wxlogin.pjialin.com/Dev/index',
        'update' => 'http://wxlogin.pjialin.com/Dev/update',
        'remove' => 'http://wxlogin.pjialin.com/Dev/remove',
        'disabled' => 'http://wxlogin.pjialin.com/Dev/disabled',
        'file' => 'http://wxauth.pjialin.com/Dev/file',
    );
    public function __construct()
    {
        global $_W;
        $this->W = $_W;
        $this->appid = $this->W['account']['key'];
    }
/*public function __call($name, $arguments)
	{
//		require 'W.php';
//		W::Start($name,$arguments);
	}*/


    public function doWebIndex(){
        global $_W;
        $data = array(
            'appid' => $this->appid,
        );
        $lists = $this->curl($this->_dev['index'],$data);
        $lists = json_decode($lists,true);
        if($lists['Code']%2==0)
            $_lists = $lists['Data']['list'];
        include $this->template('index');
    }


    public function doWebHelp(){
        global $_W;
        $help_url = 'http://wxlogin.pjialin.com/App/Public/help/help.html';
        $help_conn = file_get_contents($help_url);
        include $this->template('help');
    }

    /**
     * 测试是否配置成功
     */
    public function doWebTest()
    {

        include $this->template('test');
    }
    /**
     * 获取二维码
     */
    protected function getEcode($url){
        $eUrl = 'http://qr.liantu.com/api.php?text=' . urlencode($url);
        return $eUrl;
    }

    /**
     * 检查授权配置是否正确
     */
    public function doMobilecheckAuth(){
        global $_W;

        load()->model('mc');
        mc_oauth_userinfo();
        $openid = $_W['fans']['from_user'];
        $userInfo = $this->getWxUserInfo($openid);
        include $this->template('checkAuth');
    }
    /**
     * 获取图片显示路径
     */
    protected function getImageUrl($url){
        return tomedia($url);

    }
    /*
    * 获取微信公众平台用户信息
    */
    protected function getWxUserInfo($openid)
    {
        global $_W;
        $acc = WeAccount::create($_W['uniacid']);
        $fan = $acc->fansQueryInfo($openid, true);
        $fans = mc_oauth_userinfo();
        $fan = array_merge($fans,$fan);
        return $fan;
    }
    public function doWebAdd(){
        global $_W;
        if($_POST){
            $data = array(
                'appid' => $this->appid,
            );
            $data = array_merge($_POST,$data);
            echo $lists = $this->curl($this->_dev['add'],$data);
            die;
        }
        include $this->template('add');
    }
    public function doWebEdit(){
        global $_W;
        $id = $_GET['id'];
        if($id){
            if($_POST){
                $data = array(
                    'appid' => $this->appid,
                    'id' => $id,
                );
                $data = array_merge($_POST,$data);
                echo $lists = $this->curl($this->_dev['update'],$data);
                die;
            }
            $data = array(
                'appid' => $this->appid,
                'id' => $id,
            );
            $lists = $this->curl($this->_dev['index'],$data);
            $lists = json_decode($lists,true);
            if($lists['Code']%2==0)
                $info = $lists['Data']['list'];
            include $this->template('edit');
        }
    }
    public function doWebDel(){
        global $_W;
        $id = $_GET['id'];
        if($id){
            $data = array(
                'appid' => $this->appid,
                'id' => $id,
            );
            echo $lists = $this->curl($this->_dev['remove'],$data);
            die;
        }
    }
    public function doWebDisabled(){
        global $_W;
        $id = $_GET['id'];
        if($id){
            $data = array(
                'appid' => $this->appid,
                'id' => $id,
            );
            echo $lists = $this->curl($this->_dev['disabled'],$data);
            die;
        }
    }
    protected function curl($url, $data = false,$s_option = array()){
        if(!$data){
            return file_get_contents($url);
        }
        $postdata = http_build_query( $data );
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return trim($result, "\xEF\xBB\xBF");
//    $ch = curl_init();
//    $option = array(
//        CURLOPT_URL => $url,
//        CURLOPT_HEADER => 0,
//        CURLOPT_FOLLOWLOCATION => TRUE,
//        CURLOPT_TIMEOUT => 30,
//        CURLOPT_RETURNTRANSFER => TRUE,
//        CURLOPT_SSL_VERIFYPEER => 0,
//    );
//    if ( $data ) {
//        $option[CURLOPT_POST] = 1;
//        $option[CURLOPT_POSTFIELDS] = http_build_query($data);
//    }
//    foreach($s_option as $k => $v){
//        $option[$k] = $v;
//    };
//    curl_setopt_array($ch, $option);
//    $response = curl_exec($ch);
//    if (curl_errno($ch) > 0) {
//        exit("CURL ERROR:$url " . curl_error($ch));
//    }
//    curl_close($ch);
//    return trim($response, "\xEF\xBB\xBF");
    }


    public function doWebFile(){
        global $_W;
        if($_POST){
            $data = array(
                'appid' => $this->appid,
                'type' => 'add',
                'content' => $_POST['content'],
                'name' => $_POST['name'],
            );
            $res = $this->curl($this->_dev['file'],$data);

            echo $res;
            die;
        }
        $resContent = $this->curl($this->_dev['file'],array('appid'=>$this->appid));
        $resContent = json_decode($resContent,true);
        $resContent = $resContent['Data'];
        include $this->template('file');
    }
}