<?php
global $_GPC, $_W;
// $rid = intval($_GPC['rid']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

load()->model('reply');
load()->func('tpl');
$sql = "uniacid = :uniacid and `module` = :module";
$params = array();
$params[':uniacid'] = $_W['uniacid'];
$params[':module'] = 'haoman_dpm';

$rowlist = reply_search($sql, $params);

// message($rid);

if($operation == 'updataad'){

	$id = $_GPC['listid'];

    $dateF = date('Y-m-d', time());
    $datearr = explode('-', $dateF);
    $rPathName = 'images/' . $_W['uniacid'] . '/' . $datearr[0] . '/' . $datearr[1] . '/';
    $pathName = IA_ROOT . '/attachment/' . $rPathName;
    $fileName = uniqid() . '.jpg';
    $oImgUrl = $pathName . $fileName;
    $rImgUrl = $rPathName . $fileName;
    $filename = $oImgUrl;
    $this->get_narrow_img(tomedia($_GPC['bg']),0.1,$filename);
    $updata = array(
        'uniacid' => $_W['uniacid'],
        'bg' => $_GPC['bg'],
        'name' => $_GPC['name'],
        'thumbnail' =>$rImgUrl,
        'createtime' =>time(),
    );


	$temp =  pdo_update('haoman_dpm_bgitems',$updata,array('id'=>$id));

	message("修改成功",$this->createWebUrl('bp_bgup'),"success");


}elseif($operation == 'addad'){

	// message($_GPC['cardname']);
    $dateF = date('Y-m-d', time());
    $datearr = explode('-', $dateF);
    $rPathName = 'images/' . $_W['uniacid'] . '/' . $datearr[0] . '/' . $datearr[1] . '/';
    $pathName = IA_ROOT . '/attachment/' . $rPathName;
    $fileName = uniqid() . '.jpg';
    $oImgUrl = $pathName . $fileName;
    $rImgUrl = $rPathName . $fileName;
    $filename = $oImgUrl;
    $this->get_narrow_img(tomedia($_GPC['bg']),0.1,$filename);
	$updata = array(
		'uniacid' => $_W['uniacid'],
		'bg' => $_GPC['bg'],
		'name' => $_GPC['name'],
		'thumbnail' =>$rImgUrl,
		'createtime' =>time(),
	);


	$temp = pdo_insert('haoman_dpm_bgitems', $updata);

	message("添加成功",$this->createWebUrl('bp_bgup'),"success");

}elseif($operation == 'up'){

    $uid = intval($_GPC['uid']);
    $list = pdo_fetch("select * from " . tablename('haoman_dpm_bgitems') . "  where id=:uid ", array(':uid' => $uid));

    include $this->template('updatabp_bgup');

}elseif($operation == 'del'){

    $uid = intval($_GPC['uid']);
    if(empty($uid)){
        message('获取ID出错，请刷新后重试', '', 'error');
    }
    pdo_delete('haoman_dpm_bgitems', array('id' => $uid));

    message("删除成功",$this->createWebUrl('bp_bgup'),"success");


}elseif($operation == 'delall'){

    foreach ($_GPC['idArr'] as $k=>$id){
        if ($id == 0 ||$id ==1)
            continue;
        $rule = pdo_fetch("select * from " . tablename('haoman_dpm_bgitems') . " where id = :id ", array(':id' => $id));
        if (empty($rule)) {
            message('抱歉，您选择的不存在或是已经被删除！', '', 'error');
        }
        pdo_delete('haoman_dpm_bgitems', array('id' => $id));
    }
    $data = array(
        'errno' => 0,
        'msg' => "批量删除成功",
    );

    echo json_encode($data);


}else{

	$now = time();
	$addcard1 = array(
		"getstarttime" => $now,
		"getendtime" => strtotime(date("Y-m-d H:i", $now + 7 * 24 * 3600)),
	);

	include $this->template('newbp_bgup');

}