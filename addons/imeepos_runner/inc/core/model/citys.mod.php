<?php
class citys{
	public $table = 'imeepos_runner3_citys';

	public function __construct(){
		$this->install();
	}

	public function getList(){
		global $_W;
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid";
		$params = array(':uniacid'=>$_W['uniacid']);
		return pdo_fetchall($sql,$params);
	}

	public function delete($id){
		if(empty($id)){
			return '';
		}
		pdo_delete($this->table,array('id'=>$id));
	}

	public function update($data){
		global $_W;
		$data['uniacid'] = $_W['uniacid'];
		if(empty($data['id'])){
			pdo_insert($this->table,$data);
			$data['id'] = pdo_insertid();
		}else{
			pdo_update($this->table,$data,array('id'=>$data['id']));
		}
		return $data;
	}

	public function install(){
		if(!pdo_tableexists($this->table)){
			$sql = "CREATE TABLE ".tablename($this->table)." (
			    `id` int(11) NOT NULL AUTO_INCREMENT,
				  `uniacid` int(11) DEFAULT NULL,
				  `title` varchar(64) DEFAULT '',
				  `lat` varchar(32) DEFAULT '',
				  `lng` varchar(32) DEFAULT '',
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
	}
}