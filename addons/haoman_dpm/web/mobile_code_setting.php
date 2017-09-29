<?php
global $_GPC, $_W;
$rid = intval($_GPC['rid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->model('reply');
load()->func('tpl');

// message($rid);

if($operation=='post'){

    if (checksubmit()) {
            $count = $_GPC['mobile_number'];

            $picid = pdo_fetch("SELECT max(pici) FROM ".tablename('haoman_dpm_pw')." WHERE rid = :rid and uniacid = :uniacid", array(':uniacid' => $_W['uniacid'],':rid'=>$rid));
            $picid = $picid['max(pici)'];

            $pici = !empty($picid) ? ($picid+1) : 1;
            // print_r($picid);exit();

            $data = array('rid'=>$rid,'uniacid' => $_W['uniacid'], 'createtime' => time('Ymd'), 'codenum' => 1,'is_qrcode'=>0, 'pici' => $pici,'status'=>$_GPC['status']);
            pdo_insert('haoman_dpm_pici', $data);

                $randcode = $this->genkeyword(8);
                $pwid = 'pwid'.date('Ymd') . sprintf('%d', time());


                $updata = array(
                    'rid' => $rid,
                    'pici' => $pici,
                    'uniacid' => $_W['uniacid'],
                    'pwid' => $pwid,
                    'title' => $count,
                    'num' => 1,
                    'iscqr' => 0,
                    'createtime' =>time(),
                );

                $temp = pdo_insert('haoman_dpm_pw', $updata);



            message('手机号添加成功', $this->createWebUrl('mobile_code_setting',array('rid'=>$rid,'op'=>'post')), 'success');

    }

    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $sql = 'select * from ' . tablename('haoman_dpm_pici') . 'where  uniacid = :uniacid and rid =:rid LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $prarm = array(':uniacid' => $_W['uniacid'],':rid'=>$rid);
    $list = pdo_fetchall($sql, $prarm);


    $count = pdo_fetchcolumn('select count(*) from ' . tablename('haoman_dpm_pici') . 'where uniacid = :uniacid and rid =:rid', $prarm);
    $pager = pagination($count, $pindex, $psize);
    include $this->template('mobile_code_set');
}elseif ($operation == 'codeshow') {

    $pici = $_GPC['pici'];

    $t = time();
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $sql = 'select * from ' . tablename('haoman_dpm_pw') . 'where uniacid = :uniacid and pici = :pici and rid =:rid LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $prarm = array(':uniacid' => $_W['uniacid'] ,':pici' => $pici,':rid'=>$rid);
    $list = pdo_fetchall($sql, $prarm);
    $count = pdo_fetchcolumn('select count(*) from ' . tablename('haoman_dpm_pw') . 'where uniacid = :uniacid and pici = :pici and rid =:rid', $prarm);
    $pager = pagination($count, $pindex, $psize);

    load()->func('tpl');
    include $this->template('mobile_codeshow');



}elseif ($operation=='picidele'){
    $pici = $_GPC['pici'];
    $res = pdo_fetch('select * from ' . tablename('haoman_dpm_pici') . ' where uniacid = :uniacid and pici = :pici and rid=:rid', array(':uniacid' => $_W['uniacid'] ,':pici' => $pici,':rid'=>$rid));
    if($res){
        pdo_delete('haoman_dpm_pici', array('uniacid' => $_W['uniacid'],'pici' => $pici));
        pdo_delete('haoman_dpm_pw', array('uniacid' => $_W['uniacid'],'pici' => $pici));

        message('删除成功',$this->createWebUrl("mobile_code_setting",array('rid'=>$rid,'op'=>'post')),'success');
    }else{
        message('暂无可删除',$this->createWebUrl("comobile_code_settingde",array('rid'=>$rid,'op'=>'post')),'error');
    }
}elseif ($operation=='deldw'){
    $id = intval($_GPC['id']);
    $pici = intval($_GPC['pici']);
    $rule = pdo_fetch("select * from " . tablename('haoman_dpm_pw') . " where id = :id ", array(':id' => $id));
    $codenum = pdo_fetch("select * from " . tablename('haoman_dpm_pici') . " where pici = :pici ", array(':pici' => $pici));
    if (empty($rule)) {
        message('抱歉，参数错误！');
    }
    pdo_delete('haoman_dpm_pw', array('id' => $id));
    if($rule['pici']!=0){
        if($rule['codenum']==1){
            pdo_delete('haoman_dpm_pici', array('uniacid' => $_W['uniacid'],'pici' => $codenum['pici']));

        }else{
            pdo_update('haoman_dpm_pici', array('codenum' => $codenum['codenum'] - 1), array('pici' => $codenum['pici']));
        }

    }
    message('删除成功！', referer(), 'success');
}elseif ($operation=='import'){
    require_once ROOT_PATH."function.php";

    $filename = $_GPC['csv'];
    if($_W['ispost'])
    {
        $filename = $_FILES['csv']['tmp_name'];
        if(empty($filename))
        {
            //echo '请选择要导入的CSV文件！';
            message('请选择要导入的CSV文件！','','');
            exit;
        }
        $handle = fopen($filename, 'r');



        $result = input_csv($handle); //解析csv

        $len_result = count($result);
        if($len_result==0){
            message('导入的CSV文件没有数据！','','error');
            exit;
        }

        $colname1 = trim(iconv('gb2312','utf-8//IGNORE', $result[0][0]));  //需要增加字段就这边复制一行
//        $colname2 = trim(iconv('gb2312','utf-8//IGNORE', $result[0][1]));
//        $colname3 = trim(iconv('gb2312','utf-8//IGNORE', $result[0][2]));
//        $colname3 = trim(iconv('gb2312','utf-8//IGNORE', $result[0][4]));


        if($colname1=='')//需要增加字段就这边加个判断
        {
            message('请检查导入的数据表是否正确！'.$result[0][2],'','error');
        }

        $picid = pdo_fetch("SELECT max(pici) FROM ".tablename('haoman_dpm_pw')." WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));

        $picid = $picid['max(pici)'];

        $pici = !empty($picid) ? ($picid+1) : 1;

        $codenum = $len_result;
        $tims = time('Ymd');
        fclose($handle); //关闭指针
        $datas = array('rid'=>$rid,'uniacid' => $_W['uniacid'], 'createtime' => time('Ymd'), 'codenum' => count($result)-1, 'pici' => $pici,'status'=>0);
        pdo_insert('haoman_dpm_pici', $datas);

        $results = array_splice($result,1);
        $page_count =array_chunk($results , 5000);//计算循环总页数
        $count = count($page_count);
        if (!empty($results)){
            for($i=0;$i<$count;$i++){
                $insertRows = array();
                foreach($page_count[$i] as $v){
                    $pwid = 'pwid'.date('Ymd') . sprintf('%d', time()).$v[0];
                    $row = array();
                    $row['uniacid'] = $_W['uniacid'];
                    $row['rid']    =$rid;
                    $row['pici']  = $pici;
                    $row['pwid'] = $pwid;
                    $row['title'] = $v[0];
                    $row['num']   = 1;
                    $row['status']    = 0;
                    $row['iscqr']    = 0;
                    $row['createtime']   = time();
                    $sqlString       = '('."'".implode( "','", $row ) . "'".')'; //批量
                    $insertRows[]    = $sqlString;
                }
                $query = $this->addDetail($insertRows); //批量将sql插入数据库。

            }


        }


        //	$query = pdo_query("insert ignore into ".tablename('hm_chb_pw')." (uniacid,orderid,tbname) values $data_values");   //需要增加字段就这边数据库字段增加下
        if($query){

            message('导入成功！',referer(),'success');
            //echo '导入成功！';
        }else{
            message('导入失败！',referer(),'error');
            //echo '导入失败！';
        }
    }

    include $this->template('mobile_import');
}
else{

    $personals = pdo_fetch("select * from " . tablename('haoman_dpm_setting') . " where rid = :rid order by `id` asc", array(':rid' => $rid));
    $personal = unserialize($personals['settings']);
    if(empty($personal['my_bg'])){
        $personal['my_bg'] = '../addons/haoman_dpm/images/my.jpg';
    }
    if(empty($personal['prize_bg'])){
        $personal['prize_bg'] = '../addons/haoman_dpm/images/my.jpg';
    }
    if(empty($personal['money_bg'])){
        $personal['money_bg'] = '../addons/haoman_dpm/images/my.jpg';
    }


}