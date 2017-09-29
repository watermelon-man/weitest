<?php
/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class shop
{
    public $table = 'imeepos_runner3_shop';

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
	public function getMy(){
        global $_W;
		$item = pdo_fetch($this->table,array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));
        return $item;
	}
	public function update($data = array()){
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
    public function getList($page,$where ="",$params= array()){
        global $_W;
        if(empty($page)){
            $page = 1;
        }
        $psize = 20;
        $params['uniacid'] = $_W['uniacid'];
        $total = 0;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where} ORDER BY create_time DESC limit ".(($page-1)*$psize).",".$psize;
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
			  `lat` varchar(32) DEFAULT '',
			  `lng` varchar(32) DEFAULT '',
			  `poiaddress` varchar(320) DEFAULT '',
			  `poiname` varchar(128) DEFAULT '',
			  `cityname` varchar(128) DEFAULT '',
			  `desc` varchar(320) DEFAULT '',
			  `realname` varchar(32) DEFAULT '',
			  `mobile` varchar(32) DEFAULT '',
			  `create_time` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			pdo_query($sql);
		}
        if(!pdo_fieldexists($this->table,'status')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `status` tinyint(2) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'image')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `image` varchar(320) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'title')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `title` varchar(64) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'logo')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `logo` varchar(320) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'card_image1')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `card_image1` varchar(320) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'card_image2')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `card_image2` varchar(320) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'register_num')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `register_num` varchar(32) DEFAULT '0'");
        }

        if(!pdo_indexexists($this->table,'INDEX_OPENID')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_OPENID ( `openid` )");
        }
        if(!pdo_indexexists($this->table,'INDEX_UNIACID')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_UNIACID ( `uniacid` )");
        }
	}

	
}
