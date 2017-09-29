<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class buy
{
    public $table = 'imeepos_runner3_buy';

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
    public function getTaskid($id){
        global $_W;
        $item = pdo_get($this->table,array('taskid'=>$id));
        return $item;
    }
    public function install(){
        if(!pdo_tableexists($this->table)){
            $sql = "CREATE TABLE ".tablename($this->table)." (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `uniacid` int(11) DEFAULT '0',
              `openid` varchar(64) DEFAULT '',
              `freight` float(10,2) DEFAULT '0.00',
              `title` varchar(132) DEFAULT '',
              `buyprovince` varchar(32) DEFAULT '',
              `buycity` varchar(32) DEFAULT '',
              `province` varchar(32) DEFAULT '',
              `city` varchar(32) DEFAULT '',
              `address` varchar(132) DEFAULT '',
              `receivelon` varchar(32) DEFAULT '',
              `receivelat` varchar(32) DEFAULT '',
              `expectedtime` int(11) DEFAULT '0',
              `buyaddress` varchar(132) DEFAULT '',
              `sendlon` varchar(32) DEFAULT '',
              `sendlat` varchar(32) DEFAULT '',
              `other` varchar(320) DEFAULT '',
              `distance` int(11) DEFAULT '0',
              `taskid` int(11) DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'limit_time')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `limit_time` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'receiveaddress')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `receiveaddress` varchar(132) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'receivedetail')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `receivedetail` varchar(320) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'receivemobile')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `receivemobile` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'receiverealname')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `receiverealname` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'message')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `message` varchar(640) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'dianfu')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `dianfu` tinyint(2) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'goodscost')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `goodscost` decimal(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'goodstitle')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `goodstitle` varchar(32) DEFAULT ''");
        }
    }
}