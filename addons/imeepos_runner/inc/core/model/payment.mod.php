<?php
class payment{
    public $table = 'imeepos_runner3_paylog';

    public function getInfo($tid){
        global $_W;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND tid = :tid";
        $par = array(":uniacid"=>$_W['uniacid'],':tid'=>$tid);
        $paylog = pdo_fetch($sql,$par);
        return $paylog;
    }

    public function getSetting($tid){
        global $_W;
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE uniacid = :uniacid AND tid = :tid";
        $par = array(":uniacid"=>$_W['uniacid'],':tid'=>$tid);
        $paylog = pdo_fetch($sql,$par);
        $setting = iunserializer($paylog['setting']);
        return $setting;
    }

    public function payResult($params){

    }
}