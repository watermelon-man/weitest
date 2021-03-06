<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class moneylog
{
    public $table = 'imeepos_runner3_moneylog';

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

    public function total($type = 1){
    global $_W,$_GPC;
    $oauth = M('setting')->getSystem('auth');
    if(empty($oauth['code'])){
        return array();
    }
    $return = array();
    $return['all'] = array();
    $params = array(':uniacid'=>$_W['uniacid']);
    $where = "";
    $p = trim($_GPC['openid']);
    if(!empty($p)){
        $where .= " AND openid = :openid";
        $params[':openid'] = $p;
    }
    unset($p);
    $p = trim($_GPC['start_time']);
    if(!empty($p)){
        $where .= " AND create_time >= :start_time";
        $params[':start_time'] = strtotime($p);
    }
    unset($p);
    $p = trim($_GPC['end_time']);
    if(!empty($p)){
        $where .= " AND create_time <= :end_time";
        $params[':end_time'] = strtotime($p);
    }
    unset($p);
    $sql = "SELECT SUM(fee) FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where}";
    $return['all']['fee'] = pdo_fetchcolumn($sql,$params);
    $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where}";
    $return['all']['sum'] = pdo_fetchcolumn($sql,$params);
    if(empty($return['all']['fee'])){
        $return['all']['fee'] = 0.00;
    }
    if(empty($return['all']['sum'])){
        $return['all']['sum'] = 0;
    }
    $start_time = strtotime(date('Y-m-d',time()));
    $end_time = time();

    $return['day'] = array();
    $sql = "SELECT SUM(fee) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `create_time` >=:star_time AND `create_time` <=:end_time {$where}";
    $params[':star_time'] = $start_time;
    $params[':end_time'] = $end_time;

    $return['day']['fee'] = pdo_fetchcolumn($sql,$params);
    $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `create_time` >=:star_time AND `create_time` <=:end_time {$where}";
    $return['day']['sum'] = pdo_fetchcolumn($sql,$params);

    if(empty($return['day']['fee'])){
        $return['day']['fee'] = 0.00;
    }
    if(empty($return['day']['sum'])){
        $return['day']['sum'] = 0;
    }
    $start_time = strtotime(date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"))));
    $end_time = strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))));
    $return['week'] = array();
    $params[':star_time'] = $start_time;
    $params[':end_time'] = $end_time;
    $sql = "SELECT SUM(fee) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `create_time` >=:star_time AND `create_time` < :end_time {$where}";

    $return['week']['fee'] = pdo_fetchcolumn($sql,$params);
    $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `create_time` >:star_time AND `create_time` < :end_time {$where}";
    $return['week']['sum'] = pdo_fetchcolumn($sql,$params);
    if(empty($return['week']['fee'])){
        $return['week']['fee'] = 0.00;
    }
    if(empty($return['week']['sum'])){
        $return['week']['sum'] = 0;
    }
    $start_time = strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),1,date("Y"))));
    $end_time = strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y"))));
    $return['month'] = array();
    $params[':star_time'] = $start_time;
    $params[':end_time'] = $end_time;
    $sql = "SELECT SUM(fee) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND create_time >=:star_time AND create_time <=:end_time {$where}";
    $return['month']['fee'] = pdo_fetchcolumn($sql,$params);
    $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND create_time >=:star_time AND create_time <=:end_time {$where}";
    $return['month']['sum'] = pdo_fetchcolumn($sql,$params);
    if(empty($return['month']['fee'])){
        $return['month']['fee'] = 0.00;
    }
    if(empty($return['month']['sum'])){
        $return['month']['sum'] = 0;
    }
    return $return;
}

    public function getList($page,$where =""){
        global $_W,$_GPC;
        if(empty($page)){
            $page = 1;
        }
        $psize = 20;
        $total = 0;
        $params = array(':uniacid'=>$_W['uniacid']);
        $p = trim($_GPC['openid']);
        if(!empty($p)){
            $where .= " AND openid = :openid";
            $params[':openid'] = $p;
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
              `reciveid` int(11) DEFAULT '0',
              `create_time` int(11) DEFAULT '0',
              `fee` float(10,2) DEFAULT '0.00',
              `openid` varchar(64) DEFAULT '',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
    }
}