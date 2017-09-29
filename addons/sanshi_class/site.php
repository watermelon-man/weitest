<?php

/**
 * 班级信息管理模块微站定义
 *
 * @author tyzy313481929
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Sanshi_classModuleSite extends WeModuleSite {
    
    
    
    public function doMobileIndex()
    {
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $msg = mc_oauth_userinfo();
        $pic = $msg['avatar'];
        $date = empty($_GPC['createday'])? date('Y-m-d') :$_GPC['createday'];
        $nickname = $msg['nickname'];
//        var_dump($openid);
        $sql = 'SELECT id,classname FROM '.tablename('sanshi_class').' WHERE `weid` = :weid   and `openid`=:openid';
//        $sql = 'SELECT A.id,A.classname,A.owner,B.majorname FROM  '.tablename('sanshi_class').' AS A left join '.tablename('sanshi_major').' AS B on A.majorid = B.id WHERE A.`weid`=:weid and A.`openid`=:openid';
        $classlist = pdo_fetchall($sql, array(
            ':weid'  => $uniacid,
            ':openid' => $openid
        ));
        if(empty($classlist)){
               message('无权限查看');
        }
        include $this->template('mobileclass');
    }
    
    /**
     * mobile课程列表
     * @global type $_W
     * @global type $_GPC
     */
    public function doMobileCheck()
    {
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $_W['openid'];
        $msg = mc_oauth_userinfo();
        $pic = $msg['avatar'];
        $nickname = $msg['nickname'];
        $sql = 'SELECT id,classname FROM '.tablename('sanshi_class').' WHERE `weid` = :weid   and `openid`=:openid order by id desc';
        $classlist = pdo_fetchall($sql, array(
            ':weid'  => $uniacid,
            ':openid' => $openid
        ));
        $classid = empty($_GPC['classid']) ? $classlist[0]['id'] : $_GPC['classid'];//设置默认班级
//        var_dump($classid,$uniacid,$openid);
        $sql = 'SELECT * FROM '.tablename('sanshi_class_number').' WHERE `weid` = :weid   and `openid`=:openid and `classid`=:classid ORDER BY createday desc limit 10';
//        echo $sql;
        $list = pdo_fetchall($sql, array(
            ':weid'  =>  $uniacid,
            ':openid' => $openid,
            ':classid'=> $classid
        ));
//        print_r($list);
       include $this->template('checkclass');
    }
    /**
     * 
     */
    public function doMobileGetclassinfo(){
        global $_W,$_GPC;
        $sql = 'SELECT A.owner,B.majorname FROM  '.tablename('sanshi_class').' AS A left join '.tablename('sanshi_major').' AS B on A.majorid = B.id WHERE A.`weid`=:weid and A.  `id`=:classid';
            $params = array(
                ':weid'      => $_W['uniacid'],
                ':classid'   => $_GPC['classid'],
            );
           $row  = pdo_fetch($sql, $params);
           echo json_encode(array(
               'code'    => 200,
               'message' => '',
               'data'    => $row
           ));
           exit();
    }
    /**
     * 保存业务数据
     * @global type $_W
     * @global type $_GPC
     */
    public function doMobileSaveinfo(){
        global $_W,$_GPC;
//        var_dump($_GPC);
        $sql = 'SELECT * FROM  '.tablename('sanshi_class_number').' WHERE `weid`=:weid and `classid`=:classid and `createday`=:createday';
            $params = array(
                ':weid'      => $_W['uniacid'],
                ':classid'   => $_GPC['classid'],
                ':createday'  => $_GPC['createday']
            );
           $row  = pdo_fetch($sql, $params);
           $data = array(
               'weid'     => $_W['uniacid'],
               'classid'  => $_GPC['classid'],
               'male'     => intval($_GPC['male']),
               'female'   => intval($_GPC['female']),
               'openid'   => $_W['openid'],
               'createday'=> $_GPC['createday'],
           );
           if($row){
               pdo_update('sanshi_class_number', $data, array(
                    'id'      => $row['id'],
                    )
                );
           }else{
                pdo_insert('sanshi_class_number', $data);
                $id = pdo_insertid();
           }
           message('保存成功', $this->createMobileUrl('check'));
    }
    
    /**
     * 后台页面管理。
     * @global type $_W
     * @global type $_GPC
     */
    public function doWebManage() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
         $date = !empty($_GPC['createday'])?$_GPC['createday']:date('Y-m-d');
         $sql = 'SELECT * FROM '.tablename('sanshi_class_number').' AS A left join '. tablename('sanshi_class').' AS B on A.classid = B.id left join '.tablename('sanshi_major').' AS C on B.majorid = C.id WHERE A.`weid` = :weid   and `createday`=:createday';
        $list = pdo_fetchall($sql, array(
            ':weid'    => $_W['uniacid'],
            ':createday'=> $date
        ));
        include $this->template('manage');
    }

    /**
     * 专业管理
     */
    public function doWebMajor() {
        global $_W, $_GPC;
        load()->func('tpl');
        $weid = $_W['uniacid'];
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $id = intval($_GPC['id']);
        if ($operation == 'display') {
            if (!empty($id)) {
                $row = pdo_fetch("SELECT id,majorname FROM ".tablename('sanshi_major') . " WHERE id =:id", array(':id'=> $id));
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $sql = 'SELECT COUNT(*) FROM  '.tablename('sanshi_major').' WHERE `weid`=:weid';
            $params = array(':weid' => $_W['uniacid']);
            $total = pdo_fetchcolumn($sql, $params);
            if ($total > 0) {
                $sql = 'SELECT * FROM '.tablename('sanshi_major').' WHERE `weid` = :weid ORDER BY `id` DESC LIMIT '.($pindex - 1) * $psize . ' ,' . $psize;
                $list = pdo_fetchall($sql, $params);
                $pager = pagination($total, $pindex, $psize);
            }
        }elseif ($operation == 'post') {
            if(empty($_GPC['title'])){
                message('专业不能为空', $this->createWebUrl('major', array(
                    'op' => 'display'
                    )), 'info');
            }
            $data = array(
                'majorname'   => $_GPC['title'],
                'weid'    => $_W['uniacid']
            );
//            var_dump($data);die();
            if (!empty($id)) {
                pdo_update('sanshi_major', $data, array(
                    'id'      => $id,
                    'weid'    => $_W['uniacid']
                    )
                );
                 message('专业修改成功', $this->createWebUrl('major', array(
                        'op' =>'display')), 'success');
            }else{
                $isEx = $this->findListEx('sanshi_major','*',array(
                    ':majorname'=>$_GPC['title'],
                    ':weid'     => $_W['uniacid']
                    ) );
                if(empty($isEx)){
                     pdo_insert('sanshi_major', $data);
                     $id = pdo_insertid();
                }else{
                   message('专业不能重复', $this->createWebUrl('major', array(
                'op' =>'display')), 'info');
                }
                message('专业保存成功', $this->createWebUrl('major', array(
                        'op' =>'display')), 'success');
             }
        }elseif($operation == 'delete'){
                $id = intval($_GPC['id']);
                pdo_query("delete from " . tablename('sanshi_major') . " where id=$id");
                message('删除成功', $this->createWebUrl('major', array(
                    'op' => 'display'
                    )), 'success');
        }
        include $this->template('major');
    }
   
    
    /**
     * 查看列表是否存在
     * @param type $table
     * @param type $fileds
     * @param type $params
     * @return type
     */
    public  function  findListEx($table,$fileds,$params=array()){
          if(!empty($params)){
              $where=" where ";
              $index=0;
              foreach($params as $key =>$value){
                  $where.=substr($key,1)."=".$key." ";
                  if($index<count($params)-1){
                      $where.=" and ";
                  }
                  $index++;
              }
          }
          return pdo_fetchall("select ".$fileds." from ".tablename($table).$where,$params);
      }

    

    /**
     * 班级管理列表
     */
    public function doWebClass() {
        global $_W, $_GPC;
        load()->func('tpl');
        $weid = $_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $sql = 'SELECT COUNT(*) FROM  '.tablename('sanshi_class').' WHERE `weid`=:weid';
        $params = array(':weid' => $weid);
        $total = pdo_fetchcolumn($sql, $params);
        if ($total > 0) {
            $sql = 'SELECT A.*,B.majorname FROM '.tablename('sanshi_class').' as A left join '. tablename('sanshi_major'). ' AS B ON A.majorid = B.id  WHERE A.`weid` = :weid ORDER BY `id` DESC LIMIT '.($pindex - 1) * $psize . ' ,' . $psize;
            $list = pdo_fetchall($sql, $params);
            $pager = pagination($total, $pindex, $psize);
        }
        include $this->template('class');
    }
    
    /**
     * 添加班级信息
     * @global type $_W
     * @global type $_GPC
     */
     public function doWebAddclass() {
        global $_W, $_GPC;
        load()->func('tpl');
        $weid = $_W['uniacid'];
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $id = $_GPC['id'];
        if('display' == $operation){
            if (!empty($id)) {
                $row = pdo_fetch("SELECT * FROM ".tablename('sanshi_class') . " WHERE id =:id", array(':id'=> $id));
            }
        }else{
            $data = array(
                'classname'   => $_GPC['classname'],
                'weid'        => $weid,
                'majorid'     => $_GPC['majorid'],
                'openid'      => $_GPC['openid'],
                'owner'       => $_GPC['owner']
            );
            if(empty($_GPC['classname']) || empty($_GPC['openid']) || empty($_GPC['owner'])){
                 message('信息不完整', $this->createWebUrl('class', array(
                    'op' => 'display'
                    )), 'info');
            }
            if(!empty($id)){
             pdo_update('sanshi_class', $data, array(
                    'id'      => $id,
                    )
                );
            }else{
                pdo_insert('sanshi_class', $data);
                $id = pdo_insertid();
            }
             message('保存成功', $this->createWebUrl('class', array(
                    'op' => 'display'
                    )), 'success');
        }
        $major_list = pdo_fetchall("SELECT * FROM ".tablename('sanshi_major')." WHERE weid =:weid", array(':weid'=> $weid));
        include $this->template('addclass');
    }
    
    
    public function doWebDelclass(){
       global $_W, $_GPC;
       $id = intval($_GPC['id']);
       $result = pdo_delete('sanshi_class', array('id' => $id));
       if (!empty($result)) {
               message('删除成功', $this->createWebUrl('class', array(
            'op' => 'display'
            )), 'success');
        }else{
             message('删除失败', $this->createWebUrl('class', array(
            'op' => 'display'
            )), 'error');
        }
    }
}