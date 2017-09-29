<?php
use Qiniu\json_decode;
class idauth {
    public $url = 'http://apis.baidu.com/apix/idauth/idauth';
    public $apikey;
    public $params = array('name'=>'','cardno'=>'');
    public $table = 'imeepos_runner3_idauth';

    public function getInfo($openid = ''){
        global $_W;
        $oauth = M('setting')->getSystem('auth');
        if(empty($oauth['code'])){
            return array();
        }
        if(is_numeric($openid)){
            $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND uid = :uid";
            $params = array(':uniacid'=>$_W['uniacid'],':uid'=>$openid);
            return pdo_fetch($sql,$params);
        }
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND openid = :openid";
        $params = array(':uniacid'=>$_W['uniacid'],':openid'=>$openid);
        return pdo_fetch($sql,$params);
    }

    public function getInfoByCardno($cardno = ''){
        global $_W;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND cardno = :cardno";
        $params = array(':uniacid'=>$_W['uniacid'],':cardno'=>$cardno);
        return pdo_fetch($sql,$params);
    }

    public function initUser($data){
        global $_W;
        $openid = !empty($data['openid'])?$data['openid']:$data['uid'];
        $info = $this->getInfo($openid);
        if(empty($info)){
            pdo_insert($this->table,$data);
        }else{
            if(is_numeric($openid)){
                pdo_update($this->table,$data,array('uniacid'=>$_W['uniacid'],'uid'=>$openid));
            }else{
                pdo_update($this->table,$data,array('uniacid'=>$_W['uniacid'],'openid'=>$openid));
            }
        }
    }

    public function __construct()
    {
        $this->install();
    }
    
    public function setKey($key){
    	$this->apikey = $key;
    }

    public function initCheck($params){
        global $_W;
        load()->model('mc');
        $openid = $params['openid'];
        $uid = mc_openid2uid($openid);
        $info = $this->getInfoByCardno($params['cardno']);
        if(!empty($info)){
            //已存在数据
            if($info['code'] == 0){
                //查询成功，姓名和身份证号一致
                return true;
            }else{
                //重新验证
                $data = $this->check($params);
                if($data['code'] == 0){
                    //插入数据
                    $item = $data['data'];
                    $idcard = array();
                    $idcard['cardno'] = $item['cardno'];
                    $idcard['code'] = 0;
                    $idcard['birthday'] = $item['birthday'];
                    $idcard['sex'] = $item['sex'];
                    $idcard['name'] = $item['name'];
                    $idcard['address'] = $item['address'];
                    $idcard['openid'] = $_W['openid'];
                    $idcard['uniacid'] = $_W['uniacid'];
                    $idcard['uid'] = $uid;
                    
                    pdo_update($this->table,$idcard,array('uniacid'=>$_W['uniacid'],'openid'=>$openid));
                }
            }
        }else{
            //没有验证过数据
            $data = $this->check($params);

            if($data['code'] == 0){
                //插入数据
                $item = $data['data'];
                $idcard = array();
                $idcard['cardno'] = $item['cardno'];
                $idcard['code'] = 0;
                $idcard['birthday'] = $item['birthday'];
                $idcard['sex'] = $item['sex'];
                $idcard['name'] = $item['name'];
                $idcard['address'] = $item['address'];
                $idcard['openid'] = $_W['openid'];
                $idcard['uniacid'] = $_W['uniacid'];
                $idcard['uid'] = $uid;

                pdo_update($this->table,$idcard,array('uniacid'=>$_W['uniacid'],'openid'=>$openid));
            }
        }
    }

    public function check($params){
        global $_W;
        $ch = curl_init();
        $url = 'http://apis.baidu.com/apix/idauth/idauth?name='.$params['name'].'&cardno='.$params['cardno'];
        $header = array(
            'apikey:'.$this->apikey,
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        $res = json_decode($res);
        $res = cloud_object_array($res);
        return json_decode($res);
    }

    public function errorCode($code = 0){
        if($code == '0'){
            return '查询成功，姓名和身份证号一致';
        }
        if($code == '101'){
            return '查询成功，身份证号不存在';
        }
        if($code == '102'){
            return '查询成功，姓名和身份证号不一致';
        }
        if($code == '103'){
            return '查询失败，URL参数错误';
        }
        if($code == '104'){
            return '查询失败，系统正在维护中';
        }
        if($code == '105'){
            return '查询失败，系统错误';
        }
        if($code == '106'){
            return '查询失败，请联系客服';
        }
        if($code == '200'){
            return '查询失败，余额不足，请充值';
        }
        if($code == '201'){
            return '系统异常，请联系客服';
        }
    }
	public function install(){
		if(!pdo_tableexists($this->table)){
			$sql = "CREATE TABLE ".tablename($this->table)." (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uniacid` int(11) DEFAULT '0',
			  `cardno` varchar(32) DEFAULT '',
			  `code` int(11) DEFAULT '0',
			  `birthday` varchar(32) DEFAULT '',
			  `sex` varchar(32) DEFAULT '',
			  `name` varchar(32) DEFAULT '',
			  `address` varchar(64) DEFAULT '',
			  `openid` varchar(64) DEFAULT '',
			  `uid` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
	}
}

if(!function_exists('cloud_object_array')){
	/*
	 * 结构转数组
	 * */
	function cloud_object_array($array) {
		if(is_object($array)) {
			$array = (array)$array;
		} if(is_array($array)) {
			foreach($array as $key=>$value) {
				$array[$key] = cloud_object_array($value);
			}
		}
		return $array;
	}
}