<?php

class category_setting
{
    public $table = 'imeepos_runner3_category_setting';

    public function __construct()
    {
        $this->install();
    }

    public function getSetting($category_id,$code){
        $item = $this->getInfo($category_id,$code);
        if(!empty($item)){
            return iunserializer($item['setting']);
        }else{
            return array();
        }
    }
    /**
    删除某设置
     */
    public function delete($category_id,$code){
        global $_W;
        if(empty($code)){
            return '';
        }
        pdo_delete($this->table,array('uniacid'=>$_W['uniacid'],'code'=>$code,'category_id'=>$category_id));
    }

    public function update($data){
        global $_W;
        $item = $this->getInfo($data['category_id'],$data['code']);
        $data['uniacid'] = $_W['uniacid'];
        if(empty($item)){
            pdo_insert($this->table,$data);
        }else{
            pdo_update($this->table,$data,array('uniacid'=>$_W['uniacid'],'category_id'=>$data['category_id'],'code'=>$data['codename']));
        }
    }
    public function getInfo($category_id,$codename){
        global $_W;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND code = :codename AND category_id = :category_id";
        $params = array(':uniacid'=>$_W['uniacid'],':codename'=>$codename,'category_id'=>$category_id);
        $item = pdo_fetch($sql,$params);
        return $item;
    }
    public function install(){
        if(!pdo_tableexists($this->table)){
            $sql = "CREATE TABLE ".tablename($this->table)." (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `code` varchar(32) DEFAULT '',
              `setting` text,
              `uniacid` int(11) DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'category_id')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `category_id` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'create_time')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `create_time` int(11) DEFAULT '0'");
        }
    }
}