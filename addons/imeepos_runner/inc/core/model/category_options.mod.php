<?php

/**
 * used: 
 * User: imeepos
 * Qq: 1037483576
 */
class category_options
{
    public $table = 'imeepos_runner3_category_options';

    public function __construct()
    {
        $this->install();
    }

    public function getFiles(){
        $fields = array();

        $field = array();
        $field['title'] = '发货人';
        $field['code'] = 'address';
        $fields['sender'] = $field;

        $field = array();
        $field['title'] = '收货人';
        $field['code'] = 'reciver';
        $fields['reciver'] = $field;

        $field = array();
        $field['title'] = '地理位置';
        $field['code'] = 'address';
        $fields['address'] = $field;

        $field = array();
        $field['title'] = '输入框';
        $field['code'] = 'input';
        $fields['input'] = $field;

        $field = array();
        $field['title'] = '文本框';
        $field['code'] = 'textarea';
        $fields['textarea'] = $field;

        $field = array();
        $field['title'] = '费用';
        $field['code'] = 'fee';
        $fields['fee'] = $field;

        $field = array();
        $field['title'] = '留言反馈';
        $field['code'] = 'textarea';
        $fields['message'] = $field;

        $field = array();
        $field['title'] = '图片';
        $field['code'] = 'image';
        $fields['image'] = $field;

        $field = array();
        $field['title'] = '货物信息';
        $field['code'] = 'goods';
        $fields['goods'] = $field;

        $field = array();
        $field['title'] = '开始抢单时间';
        $field['code'] = 'start_time';
        $fields['start_time'] = $field;

        $field = array();
        $field['title'] = '接单时间限制';
        $field['code'] = 'recive_limit_time';
        $fields['recive_limit_time'] = $field;

        $field = array();
        $field['title'] = '完成时间限制';
        $field['code'] = 'finish_limit_time';
        $fields['finish_limit_time'] = $field;


        return $fields;
    }

    public function getall($params = array()){
        global $_W,$_GPC;
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
        $where .= " AND uniacid = :uniacid";
        $p = trim($_GPC['category_id']);
        if(!empty($p)){
            $where .= " AND category_id = :category_id";
            $params[':category_id'] = $p;
        }
        unset($p);
        $sql = "SELECT * FROM ".tablename($this->table)." WHERE 1 {$where} ORDER BY create_time DESC limit ".(($page-1)*$psize).",".$psize;
        $result = array();
        $result['list'] = pdo_fetchall($sql,$params);
        $sql = "SELECT COUNT(*) FROM ".tablename($this->table)." WHERE 1 {$where}";
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
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
            pdo_query($sql);
        }
        if(!pdo_fieldexists($this->table,'create_time')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `create_time` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'category_id')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `category_id` int(11) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'type')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `type` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'title')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `title` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'placeholder')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `placeholder` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'warning')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `warning` varchar(32) DEFAULT ''");
        }
        if(!pdo_fieldexists($this->table,'need')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `need` tinyint(2) DEFAULT '0'");
        }
        if(!pdo_fieldexists($this->table,'displayorder')){
            pdo_query("ALTER TABLE ".tablename($this->table)." ADD COLUMN `displayorder` int(11) DEFAULT '0'");
        }
    }
}