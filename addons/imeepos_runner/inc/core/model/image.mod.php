<?php
class image{
	public $table = 'imeepos_runner3_image';
	
	public function __construct(){
		global $_W;
		$this->uniacid = $_W['uniacid'];
		$this->install();
	}
	public function fetchall(){
		global $_W;
		$oauth = M('setting')->getSystem('auth');
		if(empty($oauth['code'])){
			return array();
		}
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid";
		$params = array(':uniacid'=>$_W['uniacid']);
		return pdo_fetchall($sql,$params);
	}
	
	public function getRand(){
		global $_W;
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid ORDER BY rand() limit 1";
		$params = array(':uniacid'=>$_W['uniacid']);
		$item = pdo_fetch($sql,$params);
		return tomedia($item['src']);
	}
	
	public function createImage($image){
		load()->func('file');
		if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $image, $result)){
			$type = $result[2];
			$filename = "images/imeepos_runner/".date('Y/m/d')."/".time()."_".random(6).".".$type;
			if(file_write($filename, base64_decode(str_replace($result[1],'',$image)))){
				$return = tomedia($filename);
			}
		}else{
			$return = tomedia($image);
		}
		return $return;
	}
	
	public function install(){
		$oauth = M('setting')->getSystem('auth');
		if(empty($oauth['code'])){
			return array();
		}
		if(!pdo_tableexists($this->table)){
			$sql = "CREATE TABLE ".tablename($this->table)." (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uniacid` int(11) DEFAULT NULL,
			  `src` varchar(300) DEFAULT NULL,
			  `code` varchar(64) DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
	}
}