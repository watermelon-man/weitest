<?php
/**
 * 微报名模块微站定义
 *
 * @author tyzy313481929
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Sanshi_baomingModuleSite extends WeModuleSite {

    
        public function doMobileIndex(){
            global $_W,$_GPC;
            $info = pdo_get('sanshi_baoming_medium', array(
                'uniacid' => $_W['uniacid']
            ));
            $exist = pdo_get('sanshi_baoming_records', array(
                'openid'=> $_W['openid'],
                'uniacid'=>$_W['uniacid']
            ));
            include $this->template('index');
        }
        public function doMobileForm(){
            global $_W,$_GPC;
            $info = pdo_get('sanshi_baoming_medium', array(
                'uniacid' => $_W['uniacid']
            ));
            $list = pdo_getall('sanshi_baoming_major', array(
                'uniacid' => $_W['uniacid']
            ));
            include $this->template('form');
        }
        
        
        public function doMobileedit(){
            // 前台报名修改页面 赋值
            global $_GPC, $_W;
            $openid = $_W['openid'];
            $info = pdo_get('sanshi_baoming_medium', array(
                'uniacid' => $_W['uniacid']
            ));
            $record = pdo_get('sanshi_baoming_records', array(
                'openid'=> $_W['openid'],
                'uniacid'=>$_W['uniacid']
            ));
            $pieces = explode(",", $record['major']); //字符串转换成数组
            $list = pdo_getall('sanshi_baoming_major', array(
                'uniacid' => $_W['uniacid']
            ));
//            var_dump($list);
            include $this->template('edit');
        }
        /**
         * 保存报名信息
         */
        public function doMobileSaveform(){
            global $_GPC, $_W;
            if(empty($_GPC['name'])){
                    message("姓名不能为空",$this->createMobileUrl('form'));
            }
            if(empty($_GPC['major'])){
                    message("专业不能为空",$this->createMobileUrl('form'));
            }
            if($_GPC['major'] <= 4){
                    message("专业不能超过三个",$this->createMobileUrl('form'));
            }
            if(empty($_GPC['mobile'])){
                    message("联系方式不能为空",$this->createMobileUrl('form'));
            }
            if(!preg_match("/^1[34578]{1}\d{9}$/",$_GPC['mobile'])){
                message("手机号格式不对",$this->createMobileUrl('form'));   
            }
//            var_dump($_GPC);die();
            $majors = implode(',',$_GPC['major']);
            $data = array(
                'uniacid' => $_W['uniacid'],
                'openid'  => $_W['openid'],
                'name'    => $_GPC['name'],
                'sex'     => $_GPC['sex'],
                'major'   => $majors,
                'mobile'  => $_GPC['mobile'],
                'qq'      => $_GPC['qq'],
                'school'  => $_GPC['school'],
            );
            if($_GPC['id']){
                pdo_update('sanshi_baoming_records',$data,array(
                    'id' => $_GPC['id']
                ));
            }else{
                pdo_insert('sanshi_baoming_records',$data);
            }
            
             message("报名成功",$this->createMobileUrl('edit'));
        }
	public function doWebBasic() {
           global $_W,$_GPC;
            load()->func('tpl');
            $info = pdo_get('sanshi_baoming_medium', array(
                'uniacid' => $_W['uniacid']
            ));
            include $this->template('detail');
	}
        /**
         * 
         * @global type $_W
         * @global type $_GPC
         */
	public function doWebMajor() {
            global $_W,$_GPC;
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $list = pdo_fetchall('SELECT * FROM '.tablename('sanshi_baoming_major').' ORDER BY id desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sanshi_baoming_major'));
            $pager = pagination($total, $pindex, $psize);
            include $this->template('major');
	}
        
        /**
         * 添加页面
         * @global type $_W
         * @global type $_GPC
         */
        public function doWebAddMajor() {
            global $_W,$_GPC;
            //这个操作被定义用来呈现 管理中心导航菜单
            include $this->template('addmajor');
	}
        /**
         * 保存添加页面
         * @global type $_W
         * @global type $_GPC
         */
        public function doWebSaveaddmajor(){
            global $_W,$_GPC;
            $data = array(
                'uniacid'=> $_W['uniacid'],
                'major' => $_GPC['majorname'],
            );
            if(pdo_insert('sanshi_baoming_major', $data)){
                message('保存专业成功', $this->createWebUrl('major'),'success');
            }else{
                message('保存专业失败', $this->createWebUrl('major'),'error');
            }
        }
        /**
         * 
         * @global type $_GPC
         * @global type $_W
         */
        public function doWebmodify(){
//            echo '专业修改';
            global $_GPC, $_W;
            $id = $_GPC['id'];
            $list = pdo_fetch("select * from ".tablename('sanshi_baoming_major')."where id = $id");
            include $this->template('modify');
        }
        
        public function doWebSavemodify(){
//            echo '执行修改';
            global $_GPC, $_W;
            if(empty($_GPC['major'])){
                    message("专业不能为空",$this->createWebUrl('major'));
            }
            $id = $_GPC['id'];
            $data = array(
                'major' => $_GPC['major'],
            );
            pdo_update('sanshi_baoming_major', $data, array('id' => $id));
            message("修改成功",$this->createWebUrl('major'));
        }
        
        
        public function doWebdelete(){
            global $_GPC, $_W;
            $id = $_GPC['id'];
            if(empty($id)){
                message("删除错误,请重试",$this->createWebUrl('major'));
            }
            $result = pdo_delete('sanshi_baoming_major', array('id' => $id));
            if (!empty($result)) {
                message("删除成功",$this->createWebUrl('major'));
            }else{
                message("删除失败",$this->createWebUrl('major'));
            }
        }
       
	public function doWebEnter() {
		//这个操作被定义用来呈现 管理中心导航菜单
            global $_W,$_GPC;
            //这个操作被定义用来呈现 管理中心导航菜单
            $list = pdo_getall('sanshi_baoming_records',array(
                 'uniacid'=> $_W['uniacid'],
            ));
//            var_dump($list);
            foreach($list as &$val){
                $major_arr = explode(',', $val['major']);
//                var_dump($major_arr);
                foreach($major_arr as $mal){
//                    var_dump($mal);
                    $major_name[] = pdo_getcolumn('sanshi_baoming_major', array('id'=>$mal),'major');
//                    var_dump($major_name);
                }
//                var_dump($major_name);
                $val['major_name'] = implode(',', $major_name);
            }
            include $this->template('records');
	}
        
        public function doWebSavebasic(){
            global $_W,$_GPC;
            $data = array(
                'image'   => $_GPC['image'],
                'simple'  => $_GPC['simple'],
                'detailed'=> $_GPC['detailed'],
                'uniacid' => $_W['uniacid']
            );
//            var_dump($data);die();
            if($_GPC['id']){
                pdo_update('sanshi_baoming_medium', $data, array(
                    'id' => intval($_GPC['id'])
                ));
            }else{
                pdo_insert('sanshi_baoming_medium', $data);
            }
            message('保存成功');
        }

}