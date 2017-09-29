<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/30
 * Time: 13:30
 */
/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */

class tasks
{
    public $table = 'imeepos_runner3_tasks';

    public function __construct()
    {
        $this->install();
        //$this->clear();
        //$this->check();
    }

    public function getall($params=array()){
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

    function check(){
        global $_W;
        $code = 'plugin_setting';
        $item = M('setting')->getByCode($code);
        $plugin = iunserializer($item['value']);
        if(!empty($plugin['delete_time'])){
            $delete_time = floatval($plugin['delete_time']);
        }
        if(empty($delete_time)){
            return '';
        }
        $delete_time = 60*60*$delete_time;
        $time = time()-$delete_time;
        
        load()->model('mc');
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE 1 AND uniacid = :uniacid AND limit_time <= :limit_time AND status = :status";
        $params = array(':uniacid'=>$_W['uniacid'],':limit_time'=>$time,':status'=>1);

        $list = pdo_fetchall($sql,$params);

        if(!empty($list)){
            foreach ($list as $li){
                $fee = 0;
                $fee2 = 0;
                //打钱到跑腿余额
                $paylogs = M('tasks_paylog')->getall(array('tasks_id'=>$li['id']));
                foreach ($paylogs as $paylog){
                    if($paylog['type'] != 'delivery'){
                        //线上付款
                        $log = M('paylog')->getInfoByOrdersn($paylog['tid']);
                        $fee = $fee + floatval($log['fee']);
                    }else{
                        //货到付款
                        $log = M('paylog')->getInfoByOrdersn($paylog['tid']);
                        $fee2 = $fee2 + floatval($log['fee']);
                    }
                }
                if(!empty($fee)){
                    $uid = mc_openid2uid($li['openid']);
                    mc_credit_update($uid, 'credit2',$fee,array($uid, '任务过期退款', 0, 0));
                    /*$id = intval($li['id']);*/
                    /*pdo_delete($this->table,array('id'=>$li['id']));
                    pdo_delete('imeepos_runner3_tasks',array('id'=>$id));
                    pdo_delete('imeepos_runner3_recive',array('taskid'=>$id));
                    pdo_delete('imeepos_runner3_detail',array('taskid'=>$id));
                    pdo_delete('imeepos_runner3_buy',array('taskid'=>$id));
                    pdo_delete('imeepos_runner3_listenlog',array('taskid'=>$id));
                    pdo_delete('imeepos_runner3_tasks_log',array('taskid'=>$id));
                    pdo_delete('imeepos_runner3_tasks_paylog',array('tasks_id'=>$id));*/
                }
            }
        }
    }

    public function clear(){
        global $_W;
        //删除过期未支付订单
        $code = 'plugin_setting';
        $item = M('setting')->getByCode($code);
        $plugin = iunserializer($item['value']);
        if(!empty($plugin['web_delete_time'])){
            $web_delete_time = floatval($plugin['web_delete_time']);
        }
        if(empty($web_delete_time)){
            return '';
        }
        $web_delete_time = 60*60*$web_delete_time;
        $time = time()-$web_delete_time;
        //删除任务
        $sql = "DELETE FROM ".tablename($this->table)." WHERE status = 0 AND create_time < :create_time AND uniacid = :uniacid";
        $params = array(':create_time'=>$time,':uniacid'=>$_W['uniacid']);
        pdo_query($sql,$params);
        //删除支付记录
        if(pdo_tableexists('imeepos_runner3_paylog')){
            $sql = "DELETE FROM ".tablename('imeepos_runner3_paylog')." WHERE status = 0 AND time < :create_time AND uniacid = :uniacid";
            $params = array(':create_time'=>$time,':uniacid'=>$_W['uniacid']);
            pdo_query($sql,$params);
        }
    }

    public function getList($page,$where ="",$params = array()){
        global $_W,$_GPC;
        if(empty($page)){
            $page = 1;
        }
        $psize = 10;
        $params[':uniacid'] = $_W['uniacid'];
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where} ORDER BY create_time DESC limit ".(($page-1)*$psize).",".$psize;
        $result = array();
        $result['list'] = pdo_fetchall($sql,$params);
        $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where} ";
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
    public function addReadNum($id){
        if(empty($id)){
            return '';
        }
        $item = $this->getInfo($id);
        $read_num = $item['read_num'];
        pdo_update($this->table,array('read_num'=>$read_num+1),array('id'=>$id));
    }
    public function addShareNum($id){
        $item = $this->getInfo($id);
        $share_num = $item['share_num'];
        pdo_update($this->table,array('share_num'=>$share_num+1),array('id'=>$id));
    }
    public function addListenNum($id){
        $item = $this->getInfo($id);
        $listen_num = $item['listen_num'];
        pdo_update($this->table,array('listen_num'=>$listen_num+1),array('id'=>$id));
    }

    public function getType(){
        $data = array();
        $data['0'] = '帮我送';
        $data['1'] = '预约送';
        $data['2'] = '帮我买';
        $data['3'] = '帮我买';
        $data['4'] = '帮帮忙';
        $data['5'] = '帮帮忙';
        
        return $data;
    }
    
    public function getInfo($id){
        global $_W;
        $task = pdo_get($this->table,array('id'=>$id));
        if(!empty($task)){
            if($task['type'] == 0 || $task['type'] == 1){
                $sql = "SELECT * FROM ".tablename('imeepos_runner3_detail')." WHERE taskid = :taskid";
                $params = array(':taskid'=>$task['id']);
                $detail = pdo_fetch($sql,$params);
                $detail['total'] = $detail['total'];
            }else{
                $sql = "SELECT * FROM ".tablename('imeepos_runner3_buy')." WHERE taskid = :taskid";
                $params = array(':taskid'=>$task['id']);
                $detail = pdo_fetch($sql,$params);
                $detail['total'] = $detail['freight'];
            }
            $task['detail'] = $detail;
        }else{
            $task = array();
        }
        return $task;
    }
    public function totalstatus(){
        global $_W,$_GPC;
        $return = array();
        $return['all'] = array();
        $params = array(':uniacid'=>$_W['uniacid']);
        $where = "";
        $p = trim($_GPC['start_time']);
        if(!empty($p)){
            $where .= " AND create_time >= :start_time AND status > 0";
            $params[':start_time'] = strtotime($p);
        }
        unset($p);
        $p = trim($_GPC['end_time']);
        if(!empty($p)){
            $where .= " AND create_time <= :end_time";
            $params[':end_time'] = strtotime($p);
        }
        unset($p);
        $sql = "SELECT SUM(status) FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where}";
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
        $sql = "SELECT SUM(total) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `create_time` >=:star_time AND `create_time` <=:end_time {$where}";
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
        $sql = "SELECT SUM(total) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `create_time` >=:star_time AND `create_time` < :end_time {$where}";
    
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
        $sql = "SELECT SUM(total) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND create_time >=:star_time AND create_time <=:end_time {$where}";
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

    public function install(){
        if(!pdo_tableexists($this->table)){
            $sql = "CREATE TABLE ".tablename($this->table)." (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `uniacid` int(11) DEFAULT '0',
              `status` tinyint(2) DEFAULT '1',
              `create_time` int(11) DEFAULT '0',
              `cityid` int(11) DEFAULT '0',
              `media_id` varchar(132) DEFAULT '',
              `openid` varchar(64) DEFAULT '',
              `desc` text,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'total')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `total` float(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'small_money')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `small_money` float(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'limit_time')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `limit_time` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'address')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `address` varchar(320) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'city')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `city` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'desc')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `desc` text");
        }
        if(!pdo_fieldexists($this->table,'type')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `type` tinyint(4) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'update_time')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `update_time` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'code')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `code` varchar(64) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'qrcode')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `qrcode` text");
        }
        if(!pdo_fieldexists($this->table,'read_num')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `read_num` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'share_num')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `share_num` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'listen_num')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `listen_num` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'message')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `message` varchar(320) DEFAULT ''");
        }

        if(!pdo_indexexists($this->table,'INDEX_OPENID')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_OPENID ( `openid` )");
        }
        if(!pdo_indexexists($this->table,'INDEX_UNIACID')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_UNIACID ( `uniacid` )");
        }
		if(!pdo_indexexists($this->table,'INDEX_STATUS')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_STATUS ( `status` )");
        }
    }
}