<?php
class tpl {
	public $setting = array();
	
	public $table = 'imeepos_runner3_tpl';
	
	public function __construct(){
		$this->install();
	}
	
	public function update($tpl){
		if(empty($tpl['id'])){
			return '';
		}
		if(empty($tpl['name'])){
			return '';
		}
		if(empty($tpl['params'])){
			$tpl['params'] = array('field'=>'params');
		}
		
		if(empty($tpl['data'])){
			$tpl['data'] = array('field'=>'data');
		}
		
		if(empty($tpl['setting'])){
			$tpl['setting'] = array('field'=>'setting');
		}
		$data = array();
		$data['id'] = $tpl['id'];
		$data['name'] = $tpl['name'];
		$data['params'] = iserializer($tpl['params']);
		$data['data'] = iserializer($tpl['data']);
		$data['setting'] = iserializer($tpl['setting']);
		
		$item = $this->getInfo($tpl['id']);
		if(empty($item)){
			pdo_insert($this->table,$data);
		}else{
			pdo_update($this->table,$data,array('id'=>$tpl['id']));
		}
	}
	
	public function getInfo($id){
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE id = :id";
		$params = array(':id'=>$id);
		$list = pdo_fetch($sql,$params);
		return $list;
	}
	
	public function getTpls(){
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE 1 ";
		$list = pdo_fetchall($sql);
		foreach ($list as &$li){
			$li['data'] = iunserializer($li['data']);
			$li['setting'] = iunserializer($li['setting']);
			$li['params'] = iunserializer($li['params']);
		}
		unset($li);
		return $list;
	}
	
	
	public function install(){
		if(!pdo_tableexists('imeepos_runner3_tpl')){
			$sql = "CREATE TABLE ".tablename('imeepos_runner3_tpl')." (
			  `id` varchar(32) DEFAULT '',
			  `name` varchar(32) DEFAULT '',
			  `params` text,
			  `data` text,
			  `setting` text
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
	}
}