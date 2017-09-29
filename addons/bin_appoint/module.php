<?php
/**
 * 小预约模块定义
 *
 * @author 751602550
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Bin_appointModule extends WeModule {

    public $table_rule = "bin_appoint_rule";
    public $table_type = "bin_appoint_type";
    public $table_record = "bin_appoint_record";
    public $_weid = '';
    
    public function __construct() {
        global $_W, $_GPC;
        $this->_weid = $_W['uniacid'];
    }
    
    public function fieldsFormDisplay($rid = 0) {
        global $_W, $_GPC;
        $cates = pdo_fetchall("SELECT * FROM ".tablename($this->table_type)." WHERE weid=:weid AND status=1",array(":weid"=>$this->_weid));
        $rule = pdo_fetch("SELECT * FROM ".tablename($this->table_rule)." WHERE rid=:rid",array("rid"=>$rid));
        include $this->template("rule");
        //要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
    }

    public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        return '';
    }

    public function fieldsFormSubmit($rid) {
        //规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
        global $_W, $_GPC;
        $starttime=strtotime($_GPC['datelimit']['start']);
        $endtime=strtotime($_GPC['datelimit']['end']);
        if($starttime>TIMESTAMP){
            $timestatus=0;
        }elseif($endtime<TIMESTAMP){
            $timestatus=2;
        }else{
            $timestatus=1;
        }
        $data = array(
            'rid' => $rid,
            'weid' => $_W['weid'],
            'title' => $_GPC['title'],
            'thumb' => $_GPC['thumb'],
            'description' => $_GPC['description'],
            'starttime' => $starttime,
            'endtime' => $endtime,
            'sharetitle' => $_GPC['sharetitle'],
            'sharethumb' => $_GPC['sharethumb'],
            'sharedesc' => $_GPC['sharedesc'],
            'typeid' => $_GPC['cate'],
            'status'=>$_GPC['status'],
            'timestatus'=>$timestatus,
            'activity_img' => $_GPC['activity_img']
        );
        $rule = pdo_fetch('select * from ' . tablename($this->table_rule) . " where rid='{$rid}'");
        if (!empty($rule)) {
            pdo_update($this->table_rule, $data, array('id' => $rule['id']));
        } else {
            pdo_insert($this->table_rule, $data);
        }
    }

    public function ruleDeleted($rid) {
        //删除规则时调用，这里 $rid 为对应的规则编号
        pdo_delete($this->table_rule, array('rid' => $rid));
    }

}
