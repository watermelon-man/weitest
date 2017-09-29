<?php
/**
 * 小预约模块微站定义
 *
 * @author 751602550
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Bin_appointModuleSite extends WeModuleSite {

    public $table_rule = "bin_appoint_rule";
    public $table_type = "bin_appoint_type";
    public $table_record = "bin_appoint_record";
    public $_weid = '';

    public function __construct() {
        global $_W, $_GPC;
        $this->_weid = $_W['uniacid'];
        pdo_query("UPDATE  " . tablename($this->table_rule) . " SET timestatus=2 WHERE weid=:weid and endtime<" . TIMESTAMP, array(":weid" => $this->_weid));
        pdo_query("UPDATE  " . tablename($this->table_rule) . " SET timestatus=0 WHERE weid=:weid and starttime>" . TIMESTAMP, array(":weid" => $this->_weid));
        pdo_query("UPDATE  " . tablename($this->table_rule) . " SET timestatus=1 WHERE weid=:weid and endtime>" . TIMESTAMP . " and starttime<" . TIMESTAMP, array(":weid" => $this->_weid));
    }

    public function doMobileIndex() {
        global $_W, $_GPC;
        $rid = $_GPC['rid'];
        if ($rid) {
            $list = pdo_fetch("SELECT rule.*,type.content as content,type.typecontent as typecontent FROM " . tablename($this->table_rule) . " rule INNER JOIN " . tablename($this->table_type) . " type ON type.id=rule.typeid WHERE rule.rid=:rid AND rule.status=1 AND rule.weid=:weid ", array(":rid" => $rid, ":weid" => $this->_weid));
        } else {
            $list = pdo_fetch("SELECT rule.*,type.content as content,type.typecontent as typecontent FROM " . tablename($this->table_rule) . " rule INNER JOIN " . tablename($this->table_type) . " type ON type.id=rule.typeid WHERE  rule.status=1 AND rule.weid=:weid ORDER BY rule.sort DESC", array(":weid" => $this->_weid));
        }
        include $this->template('index');
        //这个操作被定义用来呈现 功能封面
    }

    public function doMobileRecord() {
        global $_W, $_GPC;
        if (checksubmit('submit')) {
            $rule = pdo_fetch("SELECT starttime,endtime FROM " . tablename($this->table_rule) . " WHERE id=:ruleid", array(':ruleid' => intval($_GPC['ruleid'])));
            if ($rule['starttime'] > TIMESTAMP) {
                pdo_update($this->table_rule, array("timestatus" => 0), array("id" => $_GPC['ruleid']));
                message("活动未开始");
            } elseif ($rule['endtime'] < TIMESTAMP) {
                pdo_update($this->table_rule, array("timestatus" => 2), array("id" => $_GPC['ruleid']));
                message("活动己结束");
            }
            pdo_update($this->table_rule, array("timestatus" => 1), array("id" => $_GPC['ruleid']));
            $type = pdo_fetch("SELECT * FROM " . tablename($this->table_type) . " WHERE id=:typeid", array(":typeid" => $_GPC['typeid']));
            if ($type['typecontent']) {
                $content = explode(',', $type['typecontent']);
                foreach ($content as $k => $v) {
                    $string.=$v . ":" . $_GPC['data'][$k] . ",";
                }
            }
            $data = array(
                'weid' => $this->_weid,
                'ruleid' => intval($_GPC['ruleid']),
                'rid' => intval($_GPC['rid']),
                'realname' => trim($_GPC['realname']),
                'mobile' => intval($_GPC['mobile']),
                'content' => trim($string),
                'othercontent' => trim($_GPC['othercontent']),
                'openid' => $_GPC['fromuser'],
                'createtime' => TIMESTAMP
            );
            $status = pdo_insert($this->table_record, $data);
            if ($status) {
                message("登记成功", referer(), 'success');
            } else {
                message("输入有误 请重新输入", referer(), 'error');
            }
        }
    }

    public function doWebChange() {
        global $_W, $_GPC;
        if (intval($_GPC['id'])) {
            $status = pdo_update($this->table_record, array('status' => 1), array('id' => intval($_GPC['id'])));
            message("处理成功", referer(), 'success');
        }
    }

    public function doWebRule() {
        //这个操作被定义用来呈现 规则列表
    }

    public function doWebCenter() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W, $_GPC;
        $op = $_GPC['op'];
        if (empty($op) || $op == "display") {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_type) . " where weid=:weid", array(':weid' => $this->_weid));
            $list = pdo_fetchall('SELECT *,(SELECT COUNT(*) FROM ' . tablename($this->table_rule) . ' WHERE typeid=type.id) as count from ' . tablename($this->table_type) . " type WHERE weid=:weid LIMIT " . ($pindex - 1) * $psize . ",{$psize}", array(':weid' => $this->_weid));
            $pager = pagination($total, $pindex, $psize);
        }
        if ($op == 'addtype') {
            $typeid = intval($_GPC['id']);
            if ($typeid) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_type) . " WHERE id=:id", array(":id" => $typeid));
            }
            if (checksubmit('submit')) {
                $parm = array(
                    'weid' => $this->_weid,
                    'typename' => trim($_GPC['typename']),
                    'content' => trim($_GPC['content']),
                    'typecontent' => trim($_GPC['typecontent']),
                    'sort' => intval($_GPC['sort']),
                    'status' => intval($_GPC['status'])
                );
                if ($typeid) {
                    $status = pdo_update($this->table_type, $parm, array("id" => $typeid));
                    if ($status) {
                        message("修改成功", $this->createWebUrl('Center'), 'success');
                    } else {
                        message("修改失败", $this->createWebUrl('Center'), 'error');
                    }
                } else {
                    $status = pdo_insert($this->table_type, $parm);
                    if ($status) {
                        message("添加成功", $this->createWebUrl('Center', array('op' => 'addtype')), 'success');
                    } else {
                        message("添加失败", $this->createWebUrl('Center', array('op' => 'addtype')), 'error');
                    }
                }
            }
        }
        include $this->template('center');
    }

    public function doWebActivity() {
        global $_W, $_GPC;
        $condition = '';
        if ($_GPC['op'] == 'typerecord') {
            $condition.=" AND typeid=" . intval($_GPC['typeid']);
            $title = trim($_GPC['title']);
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_rule) . " where weid=:weid " . $condition, array(':weid' => $this->_weid));
        $list = pdo_fetchall("SELECT * ,(SELECT COUNT(*) FROM " . tablename($this->table_record) . " WHERE ruleid=rule.id) count FROM " . tablename($this->table_rule) . "  rule WHERE weid=:weid " . $condition, array(":weid" => $this->_weid));
        $type = pdo_fetchall("SELECT * FROM " . tablename($this->table_type) . " WHERE weid=:weid", array(":weid" => $this->_weid));
        $pager = pagination($total, $pindex, $psize);
        include $this->template("activity");
    }

    public function doWebDelete() {
        global $_W, $_GPC;
        $op = $_GPC['op'];
        if ($op == 'rule' && $_GPC['id']) {
            $status = pdo_delete($this->table_rule, array('id' => $_GPC['id']));
        }
        if ($op == 'type' && $_GPC['id']) {
            $status = pdo_delete($this->table_type, array('id' => $_GPC['id']));
        }
        if ($op == 'record' && $_GPC['id']) {
            $status = pdo_delete($this->table_record, array('id' => $_GPC['id']));
        }
        if (status) {
            message("删除成功", referer(), 'success');
        } else {
            message('删除失败', referer(), 'error');
        }
    }

    public function doWebRecord() {
        global $_W, $_GPC;
        $op = trim($_GPC['op']);
        if ($op == 'recordnum') {
            $condition = " AND ruleid=" . $_GPC['ruleid'];
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename($this->table_record) . " where weid=:weid " . $condition, array(':weid' => $this->_weid));
        $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_record) . " WHERE weid=:weid " . $condition, array(":weid" => $this->_weid));
        $pager = pagination($total, $pindex, $psize);
        include $this->template("activity");
    }

    public function doMobileWeSiteIndex() {
        //这个操作被定义用来呈现 微站首页导航图标
    }

    public function doMobileWeSitePerson() {
        //这个操作被定义用来呈现 微站个人中心导航
    }

    public function doMobileWeSiteQuick() {
        //这个操作被定义用来呈现 微站快捷功能导航
    }

}
