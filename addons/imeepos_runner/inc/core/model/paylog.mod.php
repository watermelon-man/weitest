<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class paylog extends Imeepos_runnerModuleSite
{
    public $table = 'imeepos_runner3_paylog';

    public function __construct()
    {
        $this->install();
    }
    public function refund(){

    }
    public function refundquery(){
        
    }

    function changeWechatSend($ordersn, $status, $msg = '')
    {
        global $_W;
        $paylog = pdo_fetch("SELECT plid, openid, tag FROM " . tablename('core_paylog') . " WHERE tid = '{$ordersn}' AND status = 1 AND type = 'wechat'");
        if (!empty($paylog['openid'])) {
            $paylog['tag'] = iunserializer($paylog['tag']);
            $acid          = $paylog['tag']['acid'];
            load()->model('account');
            $account = account_fetch($acid);
            $payment = uni_setting($account['uniacid'], 'payment');
            if ($payment['payment']['wechat']['version'] == '2') {
                return true;
            }
            $send           = array(
                'appid' => $account['key'],
                'openid' => $paylog['openid'],
                'transid' => $paylog['tag']['transaction_id'],
                'out_trade_no' => $paylog['plid'],
                'deliver_timestamp' => TIMESTAMP,
                'deliver_status' => $status,
                'deliver_msg' => $msg
            );
            $sign           = $send;
            $sign['appkey'] = $payment['payment']['wechat']['signkey'];
            ksort($sign);
            $string = '';
            foreach ($sign as $key => $v) {
                $key = strtolower($key);
                $string .= "{$key}={$v}&";
            }
            $send['app_signature'] = sha1(rtrim($string, '&'));
            $send['sign_method']   = 'sha1';
            $account               = WeAccount::create($acid);
            $response              = $account->changeOrderStatus($send);
            if (is_error($response)) {
                message($response['message']);
            }
        }
    }

    public function payResult($params = array()){
        global $_W,$_GPC;


        // ini_set("display_errors", "On");
        // error_reporting(E_ALL | E_STRICT);


        load()->func('communication');
        
        $tid = $params['tid'];
        $result = $params['result'];
        $type = $params['type'];
        $from = $params['from'];
        $fee = floatval($params['fee']);

        $paylog = $this->getInfoByOrdersn($tid);

        $setting = iunserializer($paylog['setting']);
        $runner = M('member')->getInfo($_W['openid']);
        $sysms_set = M('setting')->getValue('sms_set');
        //发布分类任务
        if($paylog['type'] == 'post_category_task'){
            //发布任务
            if($result == 'success' || ($result == 'failed' && $type == 'delivery')){
                if($paylog['status'] != 1){
                    $task = $setting;
                    if(!empty($task['id'])){
                        $task['status'] = 1;
                        M('category_task')->update($task);

                        $content = "";
                        $content = "恭喜您，您的任务已成功发布！正在为您安排最佳的跑腿服务人员，请耐心等待~\n";
                        $content .= "订单编号：".$tid."\n";
                        if($type == 'delivery'){
                            $content .= "支付方式：货到付款\n";
                        }
                        if($type == 'credit'){
                            $content .= "支付方式：余额支付\n";
                        }
                        if($type == 'alipay'){
                            $content .= "支付方式：支付宝支付\n";
                        }
                        if($type == 'wechat'){
                            $content .= "支付方式：微信支付\n";
                        }
                        if($type == 'unionpay'){
                            $content .= "支付方式：银联支付\n";
                        }
                        if($type == 'baifubao'){
                            $content .= "支付方式：百度钱包支付\n";
                        }
                        $content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
                        $content .= "咚咚咚，您的订单信息已发送给符合要求的跑腿服务人员，您的确认码是".$task['code']."，请注意保存，不要泄露~点击详情查看订单状态";
                        $url = $_W['siteroot'].'app/'.$this->createMobileUrl('detail',array('id'=>$setting['taskid']));
                        $retrun = mc_notice_consume2($_W['openid'], '任务发布成功提醒', $content, $url,'');
                    }
                }
            }

            if($result == 'failed'){
                if($type == 'delivery'){
                    $paylog = $this->getInfoByOrdersn($tid);
                    $paylog['type'] = $type;
                    $this->update($paylog);
                }
            }
            if($result == 'success'){
                $paylog = $this->getInfoByOrdersn($tid);
                $paylog['type'] = $type;
                $paylog['status'] = 1;
                $this->update($paylog);
            }
            if ($from == 'return') {
                if ($result == 'success') {
                    //进入群发页面
                    message('支付成功！', $this->createMobileUrl('home'), 'success');
                } else {
                    message('支付成功！', $this->createMobileUrl('home'), 'success');
                }
            }
        }
        if($paylog['type'] == 'add_shangjin'){
            if($result == 'success' || ($result == 'failed' && $type == 'delivery')){
                if($paylog['status'] != 1){
                    $task = $setting;
                    if(!empty($task['id'])){
                        M('tasks')->update($task);
                        $data = array();
                        $data['tid'] = $tid;
                        $data['create_time'] = time();
                        $data['tasks_id'] = $task['id'];
                        $data['openid'] = $_W['openid'];
                        $data['fee'] = $fee;
                        $data['type'] = $type;

                        M('tasks_paylog')->update($data);
                        $content = "";
                        $content = "恭喜您成功增加赏金！~\n";
                        $content .= "订单编号：".$tid."\n";
                        if($type == 'delivery'){
                            $content .= "支付方式：货到付款\n";
                        }
                        if($type == 'credit'){
                            $content .= "支付方式：余额支付\n";
                        }
                        if($type == 'alipay'){
                            $content .= "支付方式：支付宝支付\n";
                        }
                        if($type == 'wechat'){
                            $content .= "支付方式：微信支付\n";
                        }
                        if($type == 'unionpay'){
                            $content .= "支付方式：银联支付\n";
                        }
                        if($type == 'baifubao'){
                            $content .= "支付方式：百度钱包支付\n";
                        }
                        $content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
                        $url = $_W['siteroot'].'app/'.$this->createMobileUrl('detail',array('id'=>$task['id']));

                        $retrun = mc_notice_consume2($_W['openid'], '恭喜您成功增加赏金', $content, $url,'');
                        $member = M('member')->getInfo($_W['openid']);
                        $data = array();
                        $data['uniacid'] = $_W['uniacid'];
                        $data['create_time'] = time();
                        $data['status'] = 0;
                        $data['title'] = "【".$member['nickname']."】增加任务赏金";
                        $data['link'] = '';
                        M('message')->update($data);
                    }
                }
            }
            if ($params['from'] == 'return') {
                if ($params['result'] == 'success') {
                    message('支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['id'],'r'=>1)), 'success');
                } else {
                    message('支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['id'],'r'=>1)), 'success');
                }
            }
        }
        //发布任务
        if($paylog['type'] == 'post_task'){
            $task = M('tasks')->getInfo($setting['taskid']);
            if($result == 'success' || ($result == 'failed' && $type == 'delivery')){
                pdo_update('imeepos_runner3_tasks',array('status'=>1),array('id'=>$task['id']));
                //发布任务成功消息提醒
                $data = array();
                $data['tid'] = $tid;
                $data['create_time'] = time();
                $data['tasks_id'] = $task['id'];
                $data['openid'] = $_W['openid'];
                $data['type'] = $type;
                $data['fee'] = $fee;
                M('tasks_paylog')->update($data);
                $paylog['status'] = 1;
                $this->update($paylog);
                $url = $this->createMobileUrl('qunfa',array('id'=>$setting['taskid'],'r'=>1));
                $url = str_replace('./','',$url);
                $url = $_W['siteroot'].'/app/'.$url;
                $content = ihttp_request($url,'',array(),1);

                //发送消息
                if(!empty($sysms_set['sms_open'])){
                    if(!empty($task['receivemobile'])){
                        M('code')->sendFinishCode($task['code'],$task['receivemobile']);
                    }
                }
            }
            
            if ($from == 'return') {
                if ($result == 'success') {
                    //进入群发页面
                    $content = "";
                    $content = "恭喜您，您的任务已成功发布！正在为您安排最佳的跑腿服务人员，请耐心等待~\n";
                    $content .= "订单编号：".$tid."\n";
                    if($type == 'delivery'){
                        $content .= "支付方式：货到付款\n";
                    }
                    if($type == 'credit'){
                        $content .= "支付方式：余额支付\n";
                    }
                    if($type == 'alipay'){
                        $content .= "支付方式：支付宝支付\n";
                    }
                    if($type == 'wechat'){
                        $content .= "支付方式：微信支付\n";
                    }
                    if($type == 'unionpay'){
                        $content .= "支付方式：银联支付\n";
                    }
                    if($type == 'baifubao'){
                        $content .= "支付方式：百度钱包支付\n";
                    }
                    $content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
                    $content .= "咚咚咚，您的订单信息已发送给符合要求的跑腿服务人员，您的确认码是".$task['code']."，请注意保存，不要泄露~点击详情查看订单状态";
                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('detail',array('id'=>$setting['taskid']));
                    $retrun = mc_notice_consume2($_W['openid'], '任务发布成功提醒', $content, $url,'');
                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                } else {
                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                }
            }
        }
        //帮我买
        if($paylog['type'] == 'post_buy'){
            $task = M('tasks')->getInfo($setting['taskid']);
            if($result == 'success' || ($result == 'failed' && $type == 'delivery')){
                if($paylog['status'] != 1){
                    if(pdo_update('imeepos_runner3_tasks', array('status'=>1), array('id' => intval($setting['taskid'])))){
                        //发布任务成功消息提醒
                        $data = array();
                        $data['tid'] = $tid;
                        $data['create_time'] = time();
                        $data['tasks_id'] = $task['id'];
                        $data['openid'] = $_W['openid'];
                        $data['type'] = $type;
                        $data['fee'] = $fee;
                        M('tasks_paylog')->update($data);

                        $url = $this->createMobileUrl('qunfa',array('id'=>$setting['taskid'],'r'=>1));
                        $url = str_replace('./','',$url);
                        $url = $_W['siteroot'].'/app/'.$url;
                        $content = ihttp_request($url,'',array(),1);

                        $paylog = $this->getInfoByOrdersn($tid);
                        $paylog['status'] = 1;
                        $this->update($paylog);

                        if(!empty($sysms_set['sms_open'])){
                            if(!empty($task['receivemobile'])){
                                M('code')->sendFinishCode($task['code'],$task['receivemobile']);
                            }
                        }

                        $member = M('member')->getInfo($_W['openid']);
                        $data = array();
                        $data['uniacid'] = $_W['uniacid'];
                        $data['create_time'] = time();
                        $data['status'] = 0;
                        $data['title'] = "【".$member['nickname']."】完成支付";
                        $data['link'] = '';
                        M('message')->update($data);
                    }
                }
            }
            if ($from == 'return') {
                if ($result == 'success') {
                    $content = "";
                    $content = "恭喜您，您的任务已成功发布！正在为您安排最佳的跑腿服务人员，请耐心等待~\n";
                    $content .= "订单编号：".$tid."\n";
                    if($type == 'delivery'){
                        $content .= "支付方式：货到付款\n";
                    }
                    if($type == 'credit'){
                        $content .= "支付方式：余额支付\n";
                    }
                    if($type == 'alipay'){
                        $content .= "支付方式：支付宝支付\n";
                    }
                    if($type == 'wechat'){
                        $content .= "支付方式：微信支付\n";
                    }
                    if($type == 'unionpay'){
                        $content .= "支付方式：银联支付\n";
                    }
                    if($type == 'baifubao'){
                        $content .= "支付方式：百度钱包支付\n";
                    }
                    $content .= "时间：".date('Y年m月d日 h点i分',time())."\n";
                    $content .= "确认码：".$task['code']."\n";
                    $content .= "点击查看详情~";

                    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('detail',array('id'=>$setting['taskid']));
                    $retrun = mc_notice_consume2($_W['openid'], '任务发布成功提醒', $content, $url,'');
                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                } else {
                    message('恭喜您支付成功！', $this->createMobileUrl('detail',array('id'=>$setting['taskid'],'r'=>1)), 'success');
                }
            }
        }
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

    public function clear(){
        global $_W;
        
    }

    public function getList($page,$where =""){
        global $_W;
        $this->clear();
        
        if(empty($page)){
            $page = 1;
        }
        $psize = 20;
        $total = 0;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid {$where} ORDER BY time DESC limit ".(($page-1)*$psize).",".$psize;
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
    public function getInfoByOrdersn($ordersn){
        if(empty($ordersn)){
            return '';
        }
        global $_W;
        $sql = "SELECT * FROM ".tablename('imeepos_runner3_paylog')." WHERE uniacid = :uniacid AND tid = :tid";
        $par = array(":uniacid"=>$_W['uniacid'],':tid'=>$ordersn);
        $paylog = pdo_fetch($sql,$par);
        return $paylog;
    }
    public function payResultAddShangJin($ordersn){
        global $_W;
        $paylog = $this->getInfoByOrdersn($ordersn);
        $data = unserialize($paylog['setting']);
        if(!empty($data['id'])){
            M('tasks')->update($data);
            return true;
        }
        return false;
    }
    public function autoUpdate($data){
        global $_W;
        $data['tid'] = "U".time().random(6,true);
        $data['uniacid'] = $_W['uniacid'];
        $data['status'] = 0;
        $data['openid'] = $_W['openid'];
        $data['time'] = time();
        $return = $this->update($data);
        return $return;
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
    public function totalfee(){
        global $_W,$_GPC;
        $return = array();
        $return['all'] = array();
        $params = array(':uniacid'=>$_W['uniacid']);
        $where = "";
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
        $sql = "SELECT SUM(fee) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `time` >=:star_time AND `time` <=:end_time {$where}";
        $params[':star_time'] = $start_time;
        $params[':end_time'] = $end_time;
    
        $return['day']['fee'] = pdo_fetchcolumn($sql,$params);
        $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `time` >=:star_time AND `time` <=:end_time {$where}";
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
        $sql = "SELECT SUM(fee) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `time` >=:star_time AND `time` < :end_time {$where}";
    
        $return['week']['fee'] = pdo_fetchcolumn($sql,$params);
        $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND `time` >:star_time AND `time` < :end_time {$where}";
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
        $sql = "SELECT SUM(fee) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND time >=:star_time AND time <=:end_time {$where}";
        $return['month']['fee'] = pdo_fetchcolumn($sql,$params);
        $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND time >=:star_time AND time <=:end_time {$where}";
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
        $oauth = M('setting')->getSystem('auth');
        if(empty($oauth['code'])){
            return array();
        }
        if(!pdo_tableexists($this->table)){
            $sql = "CREATE TABLE ".tablename($this->table)." (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `uniacid` int(11) DEFAULT '0',
              `tid` varchar(64) DEFAULT '',
              `time` int(11) DEFAULT '0',
              `setting` text,
              `status` tinyint(2) DEFAULT '0',
              `openid` varchar(64) DEFAULT '',
              `fee` float(10,2) DEFAULT '0.00',
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'fee')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `fee` float(10,2) DEFAULT '0.00'");
        }
        if(!pdo_fieldexists($this->table,'type')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `type` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'taskid')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `taskid` int(10) DEFAULT '0'");
        }
    }
}