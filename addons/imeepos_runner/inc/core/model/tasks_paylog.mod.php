<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class tasks_paylog
{
    public $table = 'imeepos_runner3_tasks_paylog';

    public function __construct()
    {
        $this->install();
    }

    public function getall($params = array()){
        global $_W;
        $params['uniacid'] = $_W['uniacid'];
        $list = pdo_getall($this->table,$params);
        return $list;
    }

    public function delete($id){
        if(empty($id)){
            return '';
        }
        pdo_delete($this->table,array('id'=>$id));
    }

    public function getList($page,$where ="",$params = array()){
        global $_W,$_GPC;
        if(empty($page)){
            $page = 1;
        }
        $psize = 20;
        $total = 0;
        $params['uniacid'] = $_W['uniacid'];
        $p = trim($_GPC['tid']);
        if(!empty($p)){
            $where .= " AND tid = :tid";
            $params[':tid'] = $p;
        }
        unset($p);
        $p = trim($_GPC['tasks_id']);
        if(!empty($p)){
            $where .= " AND tasks_id = :tasks_id";
            $params[':tasks_id'] = $p;
        }
        unset($p);
        $p = trim($_GPC['start_time']);
        if(!empty($p)){
            $where .= " AND start_time >= :start_time";
            $params[':start_time'] = $p;
        }
        unset($p);
        
        $p = trim($_GPC['end_time']);
        if(!empty($p)){
            $where .= " AND end_time <= :end_time";
            $params[':end_time'] = $p;
        }
        unset($p);
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
    public function update($data){
        global $_W;
        $data['uniacid'] = $_W['uniacid'];
        $tid = $data['tid'];
        $item = pdo_get($this->table,array('tid'=>$tid));
        if(empty($data['id']) || empty($item)){
            pdo_insert($this->table,$data);
            $data['id'] = pdo_insertid();
        }else{
            //更新
            pdo_update($this->table,$data,array('uniacid'=>$_W['uniacid'],'id'=>$data['id']));
        }
        return $data;
    }
    public function getByTasksId($tasks_id){
        global $_W;
        $item = pdo_get($this->table,array('tasks_id'=>$tasks_id));
        return $item;
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
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'create_time')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `create_time` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'tid')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `tid` varchar(64) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'tasks_id')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `tasks_id` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'openid')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `openid` varchar(64) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'type')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `type` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'fee')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `fee` decimal(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'status')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `status` tinyint(2) DEFAULT '0'");
        }
    }
}