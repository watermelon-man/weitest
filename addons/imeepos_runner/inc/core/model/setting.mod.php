<?php
class setting{
	public $table = 'imeepos_runner3_setting';
	
	public function __construct(){
		$this->install();
	}

	function delete($code){
		global $_W;
		pdo_delete($this->table,array('code'=>$code,'uniacid'=>$_W['uniacid']));
	}
	
	function getByCode($code){
		global $_W;
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND code = :code";
		$params = array(':uniacid'=>$_W['uniacid'],':code'=>$code);
		$item = pdo_fetch($sql,$params);
		return $item;
	}
	function getSystem($code){
		global $_W;
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE code = :code limit 1";
		$params = array(':code'=>$code);
		$item = pdo_fetch($sql,$params);
		
		if(!empty($item)){
			return iunserializer($item['value']);
		}else{
			return array();
		}
	}
	/*
     * 结构转数组
     * */
	function cloud_object_array($array) {
		if(is_object($array)) {
			$array = (array)$array;
		} if(is_array($array)) {
			foreach($array as $key=>$value) {
				$array[$key] = $this->cloud_object_array($value);
			}
		}
		return $array;
	}
	function getOauth(){
		global $_W;
		load()->func('db');
		load()->model('setting');
		load()->func('communication');
		$data = array();
		$data['ip'] = gethostbyname($_SERVER['SERVER_ADDR']);
		$data['domain'] = $_SERVER['HTTP_HOST'];
		$setting = setting_load('site');
		$data['id'] =isset($setting['site']['key'])? $setting['site']['key'] : '1';
		$data['module']= 'imeepos_runner';

		$url = 'http://meepo.com.cn/meepo/api/meepo.php';
		$res = ihttp_post($url,$data);
		$res = $this->cloud_object_array($res);
		$content = $res['content'];
		$content = json_decode($content);
		$content = $this->cloud_object_array($content);
		$setting = $content['setting'];
		if(!empty($setting) && $setting['status'] == 1){
			if(pdo_tableexists('imeepos_runner3_setting')){
				$sql = "SELECT * FROM ".tablename('imeepos_runner3_setting')." WHERE code = :code";
				$params = array(':code'=>'auth');
				$auth = pdo_fetch($sql,$params);
				$data = array();
				$data['code'] = 'auth';
				$data['value'] = iserializer($setting);
				if(empty($auth)){
					pdo_insert('imeepos_runner3_setting',$data);
				}else{
					pdo_update('imeepos_runner3_setting',$data,array('id'=>$auth['id']));
				}
			}
			return true;
		}
		return false;
	}
	
	function getValue($code){
		global $_W;
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND code = :code";
		$params = array(':uniacid'=>$_W['uniacid'],':code'=>$code);
		$item = pdo_fetch($sql,$params);
		if(!empty($item)){
			return iunserializer($item['value']);
		}else{
			return array();
		}
	}
	
	function update($code,$value){
		global $_W;
		$item = $this->getByCode($code);
		if(empty($item)){
			pdo_insert($this->table,array('uniacid'=>$_W['uniacid'],'code'=>$code,'value'=>$value));
		}else{
			pdo_update($this->table,array('value'=>$value),array('uniacid'=>$_W['uniacid'],'code'=>$code));
		}
	}
	
	public function install(){
		if(!pdo_tableexists($this->table)){
			$sql = "CREATE TABLE ".tablename($this->table)." (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uniacid` int(11) DEFAULT '0',
			  `code` varchar(32) DEFAULT '',
			  `value` text,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
	}
}