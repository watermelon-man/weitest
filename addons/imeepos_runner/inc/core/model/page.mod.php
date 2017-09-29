<?php
class page {

    public $table = 'imeepos_site_page';

    public function __construct()
    {
        $this->install();
    }

    public function getInfo($id){
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE id = :id";
        $params = array(':id'=>$id);
        $item = pdo_fetch($sql,$params);
        return $item;
    }

    public function getModulePage($modulename){
        global $_W;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND modulename = :modulename ";
        $params = array(':uniacid'=>$_W['uniacid'],':modulename'=>$modulename);
        $list = pdo_fetchall($sql,$params);
        return $list;
    }
    
    public function install(){
        $oauth = M('setting')->getSystem('auth');
        if(empty($oauth['code'])){
            return array();
        }
        if(!pdo_tableexists($this->table)){
            $sql = "CREATE TABLE ".tablename($this->table)." (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `uniacid` int(10) unsigned NOT NULL,
              `modulename` varchar(255) NOT NULL,
              `title` varchar(50) NOT NULL,
              `description` varchar(255) NOT NULL,
              `params` longtext NOT NULL,
              `html` longtext NOT NULL,
              `type` varchar(255) unsigned NOT NULL,
              `status` tinyint(1) unsigned NOT NULL,
              `createtime` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              KEY `uniacid` (`uniacid`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
        }
    }
}