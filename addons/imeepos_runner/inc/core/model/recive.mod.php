<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class recive extends Imeepos_runnerModuleSite
{
    public $table = 'imeepos_runner3_recive';

    public function __construct()
    {
        global $_W;
        $this->install();
        load()->model('mc');
        $setting = M('setting')->getValue('v_set');

        //自动完成任务
        $auto_finish_time = floatval($setting['auto_finish_time']);
        $time = time()- $auto_finish_time * 60*60;

        if($auto_finish_time > 0){
            $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND create_time < :create_time AND status = :status";
            $params = array(':uniacid'=>$_W['uniacid'],':create_time'=>$time ,':status'=>0);
            $list = pdo_fetchall($sql,$params);
            foreach ($list as $li){
                $li['status'] = 1;
                $this->update($li);
                //完成任务并结束订单
                $this->finish_auto($li['id']);
            }
        }
        //查看已完成任务
        if(empty($setting['star_length'])){
            $setting['star_length'] = 0;
        }
        $time = time() - $setting['star_length'] * 60*60;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND status = :status AND update_time <= :update_time";
        $params = array(':uniacid'=>$_W['uniacid'],':status'=>1,':update_time'=>$time);
        $recives = pdo_fetchall($sql,$params);
        foreach ($recives as $recive){
            //自动评价
            if(empty($recive['update_time'])){
                $recive['update_time'] = time();
            }else{
                $recive['status'] = 2;
                $this->update($recive);
                $receive_openid = $recive['openid'];
                $receive_uid = mc_openid2uid($receive_openid);
                $task = M('tasks')->getInfo($recive['taskid']);
                $task_openid = $task['openid'];
                $task_uid = mc_openid2uid($task_openid);
                //接单评价发单
                $data = array();
                $data['uniacid'] = $_W['uniacid'];
                $data['taskid'] = $recive['taskid'];
                $data['from_openid'] = $receive_openid;
                $data['to_openid'] = $task_openid;
                $data['star'] = 6;
                $data['type'] = 3;
                $data['content'] = "系统自动好评";
                $data['create_time'] = time();
                M('star')->update($data);
                //奖励发单人
                $max_star = intval($setting['max_star']);
                $max_credit = intval($setting['max_credit']);
                $max_xinyu = intval($setting['max_xinyu']);

                $star = 6;
                $credit = ($star - $max_star) * $max_credit;
                mc_credit_update($task_uid, 'credit1',$credit,array($task_uid, '自动6星好评奖励', 0, 0));
                $xinyu = ($star - $max_star) * $max_xinyu;
                M('member')->addxinyu($task_openid,$xinyu);

                //发单评价接单
                $data = array();
                $data['uniacid'] = $_W['uniacid'];
                $data['taskid'] = $recive['taskid'];
                $data['from_openid'] = $task_openid;
                $data['to_openid'] = $receive_openid;
                $data['star'] = 6;
                $data['type'] = 1;
                $data['content'] = "系统自动好评";
                $data['create_time'] = time();
                M('star')->update($data);
                //奖励发单人
                $min_star = intval($setting['min_star']);
                $min_credit = intval($setting['min_credit']);
                $min_xinyu = intval($setting['min_xinyu']);

                $max_star = intval($setting['max_star']);
                $max_credit = intval($setting['max_credit']);
                $max_xinyu = intval($setting['max_xinyu']);

                $star = 6;
                $credit = ($star - $max_star) * $max_credit;
                mc_credit_update($receive_uid, 'credit1',$credit,array($receive_uid, '自动6星好评奖励', 0, 0));
                $xinyu = ($star - $max_star) * $max_xinyu;
                M('member')->addxinyu($receive_openid,$xinyu);
            }
        }
    }
    public function finish_auto($id){
        global $_W;
        if(empty($id)){
            return '';
        }
        $reciveid = $id;
        $recive = $this->getInfo($id);
        $task = M('tasks')->getInfo($recive['taskid']);
        if(!empty($task)){
            if($task['status'] == 3 || $task['status'] == 2){
                //用户改变订单任务状态
                $fee = 0;
                $fee2 = 0;
                //打钱到跑腿余额
                $paylogs = M('tasks_paylog')->getall(array('tasks_id'=>$task['id']));
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
                //默认平台佣金系数
                $plugin_setting = M('setting')->getValue('plugin_setting');
                $platform_money = intval($plugin_setting['platform_money']);
                if(empty($platform_money) || $platform_money > 100){
                    $platform_money = 100;
                }

                //找到相应任务类型的佣金系数
                if($task['type'] == 0 || $task['type'] == 1){
                    //帮我送
                    $plugin_setting = M('setting')->getValue('divider_set');
                    $platform_money = intval($plugin_setting['platform_money']);
                    if(empty($platform_money) || $platform_money > 100){
                        $platform_money = 100;
                    }
                }

                if($task['type'] == 2 || $task['type'] == 3){
                    //帮我买
                    $plugin_setting = M('setting')->getValue('buy_set');
                    $platform_money = intval($plugin_setting['platform_money']);
                    if(empty($platform_money) || $platform_money > 100){
                        $platform_money = 100;
                    }
                }

                if($task['type'] == 4 || $task['type'] == 5){
                    //帮我买
                    $plugin_setting = M('setting')->getValue('help_set');
                    $platform_money = intval($plugin_setting['platform_money']);
                    if(empty($platform_money) || $platform_money > 100){
                        $platform_money = 100;
                    }
                }
                $fee = $fee * ($platform_money / 100 );
                $fee3 = $fee2 * (1-$platform_money /100);

                $uid = mc_openid2uid($recive['openid']);
                mc_credit_update($uid, 'credit2',$fee,array($uid, '跑腿佣金', 0, 0));
                //插入记录表
                $data = array();
                $data['uniacid'] = $_W['uniacid'];
                $data['openid'] = $_W['openid'];
                $data['create_time'] = time();
                $data['reciveid'] = $recive['id'];

                //赏金到账通知
                $content = "";
                $content = "恭喜您，成功完成订单！\n";
                $content .= "时间：".date('Y年m月d日 h点i分',$data['create_time'])."\n";
                if(!empty($fee)){
                    $content .= "在线支付：".$fee."元\n";
                    $data['fee'] = $fee;
                    M('moneylog')->update($data);
                }
                if(!empty($fee2)){
                    $content .= "货到付款应收：".$fee2."元\n";
                    $data['fee'] = $fee2;
                    M('moneylog')->update($data);
                }
                if(!empty($fee3)){
                    $content .= "平台费用扣除：".$fee3."元\n";
                    $data['fee'] = '-'.$fee3;
                    M('moneylog')->update($data);
                    mc_credit_update($uid, 'credit2',"-".$fee3,array($uid, '平台费用扣除', 0, 0));
                }
                $content .= "咚咚咚，恭喜您，恭喜您，任务完成!您的佣金已到账，请注意查收~，点击继续赚钱";

                $url = $_W['siteroot'].'app/'.$this->createMobileUrl('index');
                $retrun = mc_notice_consume2($recive['openid'], '赏金到账通知', $content, $url,'');
                pdo_update('imeepos_runner3_tasks',array('status'=>4,'update_time'=>time()),array('id'=>$task['id']));
                $this->finish($recive['id']);
                $data = array();
                $data['result'] = 1;
                $data['message'] = "恭喜您，任务完成!";
                if(!empty($fee)){
                    $data['message'] .= '直接到账佣金'.$fee."元已到账余额，";
                }
                if(!empty($fee2)){
                    $data['message'] .= "货到付款应收".$fee2."元，请注意收取！";
                }
                return $data;
            }else if($task['status'] == 4){
                $sql = "SELECT * FROM ".tablename('imeepos_runner3_moneylog')." WHERE reciveid = :reciveid";
                $params = array(':reciveid'=>$reciveid);
                $money = pdo_fetch($sql,$params);
                $return = array();
                $return['result'] =1;
                $return['tid'] = $money['id'];
                $return['message'] = '此订单已结款！';
                return $return;
            }else{
                $return = array();
                $return['result'] = 3;
                $return['message'] = "订单不存在或已删除！请核实！";
                return $return;
            }
        }else{
            $return = array();
            $return['result'] =3;
            $return['message'] = "订单不存在或已删除！请核实！";
            return $return;
        }
        $return = array();
        $return['result'] =3;
        $return['message'] = "系统错误，请联系站点管理员！";
        return $return;
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
    
    public function getRecive($taskid){
        if(empty($taskid)){
            return '';
        }
        global $_W;
        $sql = "SELECT * FROM ".tablename('imeepos_runner3_recive')." WHERE uniacid = :uniacid AND taskid = :taskid";
        $params = array(':uniacid'=>$_W['uniacid'],':taskid'=>$taskid);
        $recive = pdo_fetch($sql,$params);
        return $recive;
    }

    public function getList($page,$where ="",$params){
        global $_W;
        if(empty($page)){
            $page = 1;
        }
        $psize = 20;
        $total = 0;
        $params[':uniacid'] = $_W['uniacid'];
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
    public function finish($id){
        if(empty($id)){
            return array();
        }
        pdo_update($this->table,array('status'=>1,'update_time'=>time()),array('id'=>$id));
    }
    public function install(){
        if(!pdo_tableexists($this->table)){
            $sql = "CREATE TABLE ".tablename($this->table)." (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `uniacid` int(11) DEFAULT '0',
              `openid` varchar(64) DEFAULT '',
              `taskid` int(11) DEFAULT '0',
              `create_time` int(11) DEFAULT '0',
              `fee` float(10,2) DEFAULT '0.00',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'update_time')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `update_time` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'status')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `status` tinyint(2) DEFAULT '0'");
        }

        //这里会出错么？
    
        if(!pdo_indexexists($this->table,'INDEX_OPENID')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_OPENID ( `openid` )");
        }
        if(!pdo_indexexists($this->table,'INDEX_UNIACID')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_UNIACID ( `uniacid` )");
        }
        if(!pdo_indexexists($this->table,'INDEX_TASKID')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD INDEX INDEX_TASKID ( `taskid` )");
        }
    }
    
}