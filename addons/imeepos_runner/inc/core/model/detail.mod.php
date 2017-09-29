<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class detail
{
    public $table = 'imeepos_runner3_detail';

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
              `taskid` int(11) DEFAULT '0',
              `goodsweight` float(10,2) DEFAULT '0.00',
              `goodscost` float(10,2) DEFAULT '0.00',
              `goodsname` varchar(64) DEFAULT '',
              `sendprovince` varchar(32) DEFAULT '',
              `sendcity` varchar(32) DEFAULT '',
              `sendaddress` varchar(132) DEFAULT '',
              `receiveprovince` varchar(32) DEFAULT '',
              `receivecity` varchar(32) DEFAULT '',
              `receiveaddress` varchar(132) DEFAULT '',
              `pickupdate` int(11) DEFAULT '0',
              `sendlon` varchar(64) DEFAULT '',
              `sendlat` varchar(64) DEFAULT '',
              `receivelon` varchar(64) DEFAULT '',
              `receivelat` varchar(64) DEFAULT '',
              `distance` int(11) DEFAULT '0',
              `dataTimeValue` int(11) DEFAULT '0',
              `time` tinyint(2) DEFAULT '0',
              `base_fee` float(10,2) DEFAULT '0.00',
              `fee` float(10,2) DEFAULT '0.00',
              `total` float(10,2) DEFAULT '0.00',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'images')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `images` varchar(1000) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'message')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `message` varchar(1000) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'senddetail')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `senddetail` varchar(320) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'sendrealname')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `sendrealname` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'sendmobile')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `sendmobile` varchar(64) DEFAULT ''");
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
        if(!pdo_fieldexists($this->table,'base_fee')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `base_fee` float(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'fee')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `fee` float(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'total')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `total` float(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'small_money')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `small_money` float(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'float_distance')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `float_distance` float(10,2) DEFAULT '0.00'");
        }
    }
}