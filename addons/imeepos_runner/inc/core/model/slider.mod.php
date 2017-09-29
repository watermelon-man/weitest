<?php
class slider {
    public $table = 'imeepos_site_slider';

    public function __construct(){
        $this->install();
    }

    public function getSlider($modulename){
        global $_W;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND modulename = :modulename";
        $params = array(':uniacid'=>$_W['uniacid'],':modulename'=>$modulename);

        return pdo_fetchall($sql,$params);
    }

    public function install(){
        if(!pdo_tableexists($this->table)){
            $sql = "CREATE TABLE ".$this->table." (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `uniacid` int(10) unsigned NOT NULL,
              `title` varchar(255) NOT NULL,
              `url` varchar(255) NOT NULL,
              `thumb` varchar(255) NOT NULL,
              `displayorder` tinyint(4) NOT NULL,
              `modulename` varchar(255) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `uniacid` (`uniacid`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
    }
}