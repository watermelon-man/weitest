<?php
/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class goods
{
    public $table = 'imeepos_runner3_goods';

    public function __construct()
    {
        $this->install();
    }
    public function getall(){
        global $_W;
        $list = pdo_getall($this->table,array('uniacid'=>$_W['uniacid']));
        return $list;
    }
    public function delete($id){
        if(empty($id)){
            return '';
        }
        pdo_delete($this->table,array('id'=>$id));
    }
	public function getMyGoods($page = 1,$openid = ''){
		global $_W;
		if(empty($page)){
			$page = 1;
		}
		if(empty($openid)){
			$openid = $_W['openid'];
		}
		$psize = 30;
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE openid = :openid AND uniacid = :uniacid limit ".(($page-1)*$psize).",".$psize;
		$params = array(':openid'=>$openid,':uniacid'=>$_W['uniacid']);
		return pdo_fetchall($sql,$params);
	}
	public function getDefault($openid = ''){
		$sql = "SELECT * FROM ".tablename($this->table)." WHERE openid = :openid AND uniacid = :uniacid AND isdefault = :isdefault limit 1";
		$params = array(':openid'=>$openid,':uniacid'=>$_W['uniacid'],':isdefault'=>1);
		return pdo_fetch($sql,$params);
	}
	public function updateAddress($data = array()){
		global $_W;

		$data['openid'] = $_W['openid'];
		$data['uniacid'] = $_W['uniacid'];

		if(empty($data['cityname'])){
			return array();
		}
		if(!empty($data['id'])){
			pdo_update($this->table,$data,array('id'=>$data['id']));
		}else{
			pdo_insert($this->table,$data);
			$data['id'] = pdo_insertid();
		}
		return $data;
	}
    public function getList($page,$where =""){
        global $_W;
        if(empty($page)){
            $page = 1;
        }
        $psize = 20;
        $total = 0;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where} ORDER BY create_time DESC limit ".(($page-1)*$psize).",".$psize;
        $params = array(':uniacid'=>$_W['uniacid']);
        $result = array();
        $result['list'] = pdo_fetchall($sql,$params);
        $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where}";
        $total = pdo_fetchcolumn($sql,$params);

        $result['pager'] = pagination($total, $page, $psize);
        if(empty($result)){
            return array();
        }else{
            return $result;
        }
    }
    public function update($data){
        global $_W;
        $data['uniacid'] = $_W['uniacid'];
        if(empty($data['id'])){
            pdo_insert($this->table,$data);
            $data['id'] = pdo_insertid();
        }else{
            //更新
            pdo_update($this->table,$data,array('uniacid'=>$_W['uniacid'],'id'=>$data['id']));
        }
        return $data;
    }
    public function getInfo($id){
        global $_W;
        $item = pdo_get($this->table,array('id'=>$id));
        return $item;
    }
	public function install(){
        
		if(!pdo_tableexists($this->table)){
			$sql = "CREATE TABLE ".tablename($this->table)." (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `uniacid` int(11) DEFAULT '0',
			  `openid` varchar(64) DEFAULT '',
			  `name` varchar(320) DEFAULT '',
			  `weight` varchar(128) DEFAULT '',
			  `price` decimal(10,2) DEFAULT '0.00',
			  `detail` varchar(320) DEFAULT '',
			  `create_at` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
		if(!pdo_fieldexists($this->table,'class_id')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `class_id` int(11) DEFAULT '0'");
        }

	}
}
