<?php
class tpldata {
	public $table = 'imeepos_runner3_tpl_data';
	
	
	public function __construct(){
		$this->install();
	}
	
	public function update($tpl){
		global $_W;
		
		if(empty($tpl['name'])){
			return '';
		}
		if(empty($tpl['title'])){
			return '';
		}
		if(empty($tpl['uniacid'])){
			$tpl['uniacid'] = $_W['uniacid'];
		}
		if(empty($tpl['pageinfo'])){
			$tpl['pageinfo'] = array();
			$params = array(
					'title'=>'',
					'left_icon'=>'',
					'left_link'=>'',
					'left_title'=>'',
					'right_icon'=>'',
					'right_link'=>'',
					
					'right_title'=>'',
					'desc'=>'',
					'img'=>'',
					'kw'=>'',
					'footer'=>'',
					'floatico'=>'',
					
					'floatstyle'=>'right',
					'floatwidth'=>'40px',
					'floattop'=>'100px',
					'floatimg'=>'',
					'floatlink'=>''
			);
			$tpl['pageinfo']['id'] = 'M0000000000000';
			$tpl['pageinfo']['temp'] = 'topbar';
			$tpl['pageinfo']['params'] = $params;
		}
		$tpl['pageinfo'] = iserializer($tpl['pageinfo']);
		
		if(empty($tpl['pagetype'])){
			$tpl['pagetype'] = 'example';
		}
		
		$info = $this->getInfo($tpl['name']);
		if(empty($info)){
			pdo_insert($this->table,$tpl);
		}else{
			pdo_update($this->table,$tpl,array('uniacid'=>$_W['uniacid'],'name'=>$tpl['name']));
		}
	}
	
	public function getall(){
		$sql = "SELECT * FROm ".tablename($this->table)." WHERE 1";
		$params = array();
		$item = pdo_fetchall($sql,$params);
		return $item;
	}
	
	public function getInfo($name){
		global $_W;
		$sql = "SELECT * FROm ".tablename($this->table)." WHERE uniacid = :uniacid AND name = :name";
		$params = array(':uniacid'=>$_W['uniacid'],':name'=>$name);
		$item = pdo_fetch($sql,$params);
		if(!empty($item)){
			$item['pageinfo'] = iunserializer($item['pageinfo']);
		}
		
		return $item;
	}
	
	public function install(){
		if(!pdo_tableexists($this->table)){
			$sql = "CREATE TABLE ".tablename($this->table)." (
			  `name` varchar(32) DEFAULT '',
			  `title` varchar(32) DEFAULT '',
			  `uniacid` int(11) DEFAULT '0',
			  `html_content` text,
			  `data` text,
			  `create_time` int(11) DEFAULT '0'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
		
		if(!pdo_fieldexists($this->table,'pageinfo')){
			$sql = "ALTER TABLE ".tablename($this->table)." ADD COLUMN `pageinfo` text NULL ";
			pdo_query($sql);
		}
		
		if(!pdo_fieldexists($this->table,'pagetype')){
			$sql = "ALTER TABLE ".tablename($this->table)." ADD COLUMN `pagetype` varchar(32) DEFAULT ''";
			pdo_query($sql);
		}
	}
}