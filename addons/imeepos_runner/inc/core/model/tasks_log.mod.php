<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class tasks_log
{
    public $table = 'imeepos_runner3_tasks_log';

    public function __construct()
    {
        global $_W;
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

    public function getList($page,$where ="",$params=array(),$order=" create_time DESC "){
        global $_W;
        if(empty($page)){
            $page = 1;
        }
        $psize = 20;
        $total = 0;
        $params[':uniacid'] = $_W['uniacid'];
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where} ORDER BY {$order} limit ".(($page-1)*$psize).",".$psize;
        $list = pdo_fetchall($sql,$params);
        $lists = array();
        foreach ($list as $li){
            $li['create_time'] = date('Y-m-d H:i:s',$li['create_time']);
            $lists[] = $li;
        }
        unset($li);
        
        $result = array();
        $result['list'] = $lists;
        
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
              `taskid` int(11) DEFAULT '0',
              `openid` varchar(64) DEFAULT '',
              `content` varchar(1000) DEFAULT '',
              `create_time` int(11) DEFAULT '0',
              `lat` varchar(32) DEFAULT '',
              `lng` varchar(32) DEFAULT '',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'status')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `status` tinyint(11) DEFAULT '0'");
        }
    }
}