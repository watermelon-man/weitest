<?php
class multi{
    public $table = 'imeepos_site_multi';

    public function __construct()
    {
        $this->install();
    }

    public function getMulti($modulename){
        global $_W;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND modulename = :modulename";
        $params = array(':uniacid'=>$_W['uniacid'],':modulename'=>$modulename);
        $item = pdo_fetch($sql,$params);
    }

    public function install(){
        if(!pdo_tableexists($this->table)){
           $sql = "CREATE TABLE ".$this->table." (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `uniacid` int(10) unsigned NOT NULL,
              `title` varchar(30) NOT NULL,
              `styleid` int(10) unsigned NOT NULL,
              `site_info` text NOT NULL,
              `quickmenu` varchar(2000) NOT NULL,
              `status` tinyint(3) unsigned NOT NULL,
              `modulename` varchar(255) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `uniacid` (`uniacid`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
    }
}