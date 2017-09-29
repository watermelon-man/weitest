<?php
/**
 * 微信尬聊墙模块微站定义
 *
 * @author tyzy313481929
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . "/addons/sanshi_discuz/curd.class.php";
class Sanshi_discuzModuleSite extends WeModuleSite {
    
        public function doMobileIndex(){
            global $_W,$_GPC;
             //这个操作被定义用来呈现 管理中心导航菜单
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $sql = 'SELECT COUNT(*) FROM  '.tablename('sanshi_topic').' WHERE `weid`=:weid';
            $params = array(':weid' => $_W['uniacid']);
            $total = pdo_fetchcolumn($sql, $params);
            if ($total > 0) {
                $sql = 'SELECT * FROM '.tablename('sanshi_topic').' WHERE `weid` = :weid ORDER BY `id` DESC LIMIT '.($pindex - 1) * $psize . ' ,' . $psize;
                $list = pdo_fetchall($sql, $params);
                $pager = pagination($total, $pindex, $psize);
            }
            include $this->template('index');
        }
        
        /**
         * 手机页面详情
         * @global type $_W
         * @global type $_GPC
         */
        public function doMobileTopic(){
            global $_W,$_GPC;
            $weid = $_W['uniacid'];
            if($_GPC['id']){
                //查出话题明细
                $row = pdo_fetch("SELECT * FROM ".tablename('sanshi_topic') . " WHERE id =:id", array(':id'=> intval($_GPC['id'])));
                //查出留言明细
                $sql = "select * from ".tablename('sanshi_discuss'). " where topic_id=:topic_id and weid=:weid and checked = 1";
                $discuz_list = pdo_fetchall($sql, array(
                    ':topic_id' => $_GPC['id'],
                    ':weid'     => $weid
                 ));
                //更新阅读次数
               $sql = "update ".tablename('sanshi_topic').  "set topic_read=topic_read+1 where id = '".intval($_GPC['id'])."'";
               pdo_query($sql);
            }
//            var_dump($discuz_list);
            include $this->template('list');
        }
        
        /**
         * 手机保存处理
         * @global type $_W
         * @global type $_GPC
         */
        
        public function doMobileSavediscuss(){
            global $_W,$_GPC;
            if(empty($_GPC['topic_id']) || empty($_GPC['discuss'])){
                echo json_encode(array(
                    'code' => 400,
                    'message' => '信息不完整',
                    'data'   =>array()
                ));
            }else{
                //确定是否需要审核
                $topic  = pdo_getcolumn('sanshi_topic', array('id' =>intval($_GPC['topic_id'])), 'is_check',1);
                $checked = $topic['is_check'] == 1 ? : 2;
                $data = array(
                    'weid'     => $_W['uniacid'],
                    'topic_id' => $_GPC['topic_id'],
                    'openid'   => $_W['openid'],
                    'discuss'  => $_GPC['discuss'],
                    'is_check' => $checked
                );
                pdo_insert('sanshi_discuss', $data);
                $id = pdo_insertid();
                 //更新评论次数
               $sql = "update ".tablename('sanshi_topic').  "set topic_num=topic_num+1 where id = '".intval($_GPC['topic_id'])."'";
               pdo_query($sql);
                echo json_encode(array(
                    'code' => 200,
                    'message' => '保存成功',
                    'data'   =>array()
                ));
            }
            exit();
        }
        public function doWebTopic() {
            global $_W,$_GPC;
            //这个操作被定义用来呈现 管理中心导航菜单
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $sql = 'SELECT COUNT(*) FROM  '.tablename('sanshi_topic').' WHERE `weid`=:weid';
            $params = array(':weid' => $_W['uniacid']);
            $total = pdo_fetchcolumn($sql, $params);
            if ($total > 0) {
                $sql = 'SELECT * FROM '.tablename('sanshi_topic').' WHERE `weid` = :weid ORDER BY `id` DESC LIMIT '.($pindex - 1) * $psize . ' ,' . $psize;
                $list = pdo_fetchall($sql, $params);
                $pager = pagination($total, $pindex, $psize);
            }
            include $this->template('topic');
	}
        /**
         * 网页话题管理
         * @global type $_W
         * @global type $_GPC
         */
	public function doWebDiscuss() {
            global $_W,$_GPC;
            //这个操作被定义用来呈现 管理中心导航菜单
            $topic_id = intval($_GPC['topic_id']);
            $pindex   = max(1, intval($_GPC['page']));
            $psize    = 10;
            $topic = pdo_get('sanshi_topic', array('id' => $topic_id));
            $sql = 'SELECT COUNT(*) FROM  '.tablename('sanshi_discuss').' WHERE `weid`=:weid and `topic_id`=:topic_id';
            $params = array(
                ':weid'     => $_W['uniacid'],
                ':topic_id' => $topic_id
                );
            $total = pdo_fetchcolumn($sql, $params);
            if ($total > 0) {
                $sql = 'SELECT * FROM '.tablename('sanshi_discuss').' WHERE `weid` = :weid and `topic_id`=:topic_id ORDER BY `id` DESC LIMIT '.($pindex - 1) * $psize . ' ,' . $psize;
                $list = pdo_fetchall($sql, $params);
                $pager = pagination($total, $pindex, $psize);
            }
//            var_dump($list);
            include $this->template('discuss');
	}
        /**
         * 保存话题
         */
        public function doWebAddtopic() {
            global $_W,$_GPC;
            if('post' == $_GPC['op']){
                 $data = array(
                    'weid'      => $_W['uniacid'],
                    'topicname' => $_GPC['topicname'],
                    'topicdesc' => $_GPC['topicdesc'],
                    'topicback' => $_GPC['topicback'],
                    'is_check'  => $_GPC['is_check']
                    );
                pdo_insert('sanshi_topic', $data);
                $id = pdo_insertid();
                if($id){
                    message('话题保存成功', $this->createWebUrl('topic', array(
                            'op' =>'topic')), 'success');
                }
            }else{
                 include $this->template('addtopic');
            }
        }
        
        /**
         * 修改话题
         */
        public  function doWebEdittopic(){
            global $_W,$_GPC;
            $id = $_GPC['id'];
            $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'edit';
            if('edit' == $operation){
                if (!empty($id)) {
                    $row = pdo_fetch("SELECT * FROM ".tablename('sanshi_topic') . " WHERE id =:id", array(':id'=> $id));
                }
            }else{
                $data = array(
                    'weid'      => $_W['uniacid'],
                    'topicname' => $_GPC['topicname'],
                    'topicdesc' => $_GPC['topicdesc'],
                    'topicback' => $_GPC['topicback'],
                    'is_check'  => $_GPC['is_check']
                );
            if(empty($_GPC['topicname']) || empty($_GPC['topicdesc'])){
                 message('信息不完整', $this->createWebUrl('topic', array(
                    'op' => 'display'
                    )), 'info');
            }
            if(!empty($id)){
             pdo_update('sanshi_topic', $data, array(
                    'id'      => $id,
                    )
                );
            }
             message('保存成功', $this->createWebUrl('topic', array(
                    'op' => 'display'
                    )), 'success');
            }
            include $this->template('edittopic');
        }
        
        /**
         * 手机端点赞
         * @global type $_W
         * @global type $_GPC
         */
        public function doMobileZan(){
            global $_W,$_GPC;
            $discuss_id = intval($_GPC['discuss_id']);
            $openid = $_W['openid'];
            if($discuss_id && $openid){
                $row = pdo_fetch("SELECT * FROM ".tablename('sanshi_discuss_zan') . " WHERE discuss_id =:discuss_id and openid =:openid", array(
                        ':discuss_id' => $discuss_id,
                        ':openid'     => $openid
                    ));
                if($row){
                     pdo_delete('sanshi_discuss_zan', array('id'=>$row['id']));
                     $message = '已经取消赞';
                }else{
                    pdo_insert('sanshi_discuss_zan', array(
                        'weid'  => $_W['uniacid'],
                        'discuss_id' => $discuss_id,
                        'openid'     => $openid
                    ));
                    $message = "已赞";
                }
                echo json_encode(array(
                    'code' => 200,
                    'message' =>$message,
                    'data' =>[]
                ));
            } else {
                echo json_encode(array(
                    'code'    => 400,
                    'message' => '信息不完整',
                    'data'    => []
                ));
            }
        }

        
        /**
         * 网页端审核
         * @global type $_W
         * @global type $_GPC
         */
        public function doWebCheck(){
            global $_W,$_GPC;
            $disuss_id = intval($_GPC['discuss_id']);
            $checked   = intval($_GPC['checked']);
            if($disuss_id && $checked){
                    $data['checked'] = $checked ==1 ? 2 : 1;
                    $result = pdo_update('sanshi_disccus',$data,array('id'=>$disuss_id));
                    if($result){
                        echo json_encode(array(
                                'code'    => 200,
                                'message' => '审核成功',
                                'data'    => []
                            ));
                    }
            }else{
                 echo json_encode(array(
                    'code'    => 400,
                    'message' => '信息不完整',
                    'data'    => []
                ));
            }
            
        }
        
        
        /**
         * 话题删除
         */
        public function doWebTopicdelete(){
            global $_W, $_GPC;
            $id = intval($_GPC['id']);
            if($id){
                if(pdo_delete('sanshi_topic', array('id' => $id))){
                    message('删除成功', $this->createWebUrl('topic', array(
                        'op' => 'display'
                    )), 'success');
                }
            }else{
                 message('参数不完整', $this->createWebUrl('topic', array(
                'op' => 'display'
                )), 'info');
            }
        }
        
         /**
         * 留言删除
         */
        public function doWebDiscussdelete(){
            global $_W, $_GPC;
            $id = intval($_GPC['id']);
            $topic_id = intval($_GPC['topic_id']);
            if($id && $topic_id){
                if(pdo_delete('sanshi_discuss', array('id' => $id))){
                    message('删除成功', $this->createWebUrl('discuss', array(
                        'op'       => 'display',
                        'topic_id' =>$topic_id
                    )), 'success');
                }
            }else{
                 message('参数不完整', $this->createWebUrl('discuss', array(
                     'op' => 'display',
                     'topic_id' =>$topic_id
                )), 'info');
            }
        }
}