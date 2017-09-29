<?php
/**
 * 轨迹签到模块微站定义
 *
 * @author nbyiheng.net
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Nbyiheng_001ModuleSite extends WeModuleSite {
	
	
	

	public function doMobileSing() {
		
	global $_W,$_GPC;
			


			
	$uid=$_W['member']['uid'];
	
	if(checksubmit('submit')) 
	{
		
		$title=empty($_GPC['mainc'])?message('请填写事由！','', 'error'):trim($_GPC['mainc']);
		$addrStr=empty($_GPC['addrinfo'])?message('请获取地址！','', 'error'):trim($_GPC['addrinfo']);
		$remark=trim($_GPC['remark']);
		$imageStr=json_encode($_GPC['image']);
		$addrinfo=explode(",",$addrStr);
		
		$setting = pdo_fetch("SELECT * FROM ".tablename('nbyiheng_setting').' WHERE uniacid = :uniacid limit 1', array('uniacid' => $_W['uniacid']));

		//print_r($addrinfo);  
					
		//添加一条签到记录
			$sign_data = array(
				'uniacid' => $_W['uniacid'],
				'timesp' => $_W['timestamp'],
				'client' => $_W['clientip'],
				'os' => $_W['os'],
				'container' => $_W['container'],
				
			
				'title' => $title,
				'remark' => $remark,
				'addr' => $addrinfo[0],
				'lng' =>$addrinfo[1],
				'lat' => $addrinfo[2],
				'imagestr' => $imageStr,
				
			);
			
			$result = pdo_insert('nbyiheng_record', $sign_data);
			
			if (!empty($result)) {
				
				message('签到成功！','', 'success');
			}
			
			
			exit();

	}  

		$list = pdo_fetchall("SELECT id,title, timesp,addr FROM ".tablename('nbyiheng_record').' where uid=:uid and uniacid =:uniacid ORDER BY `id` desc limit 0,5', array('uid' =>$uid,'uniacid' => $_W['uniacid']));

		
		load()->func('tpl'); 
		
		include $this->template('sign_in');
		
		//这个操作被定义用来呈现 功能封面
	}
	
	
	
	
	public function doMobileGetlocation(){ 
		
			global $_W,$_GPC;
			
			$setting = pdo_fetch("SELECT * FROM ".tablename('nbyiheng_setting').' WHERE uniacid = :uniacid limit 1', array('uniacid' => $_W['uniacid']));
			
			$locationjson=str_replace('&quot;','"',$_GPC['location']);
		
			//$locationjson='{"latitude":"0.921082","speed":"0.0","accuracy":"40.0","longitude":"121.60211","errMsg":"getLocation:ok"}';
			
			$tmpLocation =json_decode($locationjson);
				
			$tmpLng=$tmpLocation->longitude;

			$tmpLat=$tmpLocation->latitude;
			
			$q = "http://api.map.baidu.com/geoconv/v1/?coords=$tmpLng,$tmpLat&from=1&to=5&ak=".$setting['baiduak'];
			//转成百度坐标
			$Location=(json_decode(file_get_contents($q)));
			
			$X=$Location->result[0]->x;
			$Y=$Location->result[0]->y;
			//x:0.921082 y:121.60211
			// [x] => 121.61279747844
            // [y] => 0.92418672153
		
			$q="http://api.map.baidu.com/geocoder/v2/?location=$Y,$X&output=json&pois=1&ak=".$setting['baiduak'];
						
			$resultQ =json_decode(file_get_contents($q));
			
			$selectTxt=$resultQ->result->formatted_address;
			$selectLng=$resultQ->result->location->lng;
			$selectLat=$resultQ->result->location->lat;
			$html= "<select id=\"location\" name=\"addrinfo\" class=\"mui-btn mui-btn-block\"><option value=\"$selectTxt,$selectLng,$selectLat\">$selectTxt</option>";
			
			$arrpois=$resultQ->result->pois;
			 
			foreach ($arrpois as $value)
			{
				$arrpoint=$value->point;
				$html .="<option value=\"$value->addr,$arrpoint->x,$arrpoint->y;\">$value->addr</option>";
			}

			$html .="</select>";
		 
			exit($html);  
		
	}
		
	public function doMobileList() {
		
	global $_W,$_GPC;

	$uid=$_W['member']['uid'];
	

	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$params = array(':uniacid'=>$_W['uniacid'], 'uid' => $uid);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('nbyiheng_record') . " WHERE uniacid = :uniacid and uid =:uid", $params);
	
	$list = pdo_fetchall("SELECT id,title, timesp,addr FROM ".tablename('nbyiheng_record').' WHERE uniacid = :uniacid and uid =:uid ORDER BY `id` desc ' . "LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		
		
		
	$pager = pagination($total, $pindex, $psize);	
		
	include $this->template('list');  
	
	}   

	public function doMobileDetail() {
		
	global $_W,$_GPC;

	$id= $_GPC['id'];

	$uid=$_W['member']['uid'];
	//$uid=3;
	if ($_GPC['op']=='del')
	{
		
		$result = pdo_delete('nbyiheng_record', array('id' => $id,'uid'=>$uid));
				if (!empty($result)) {
					message('删除成功',$this->createMobileUrl('sing'), 'success');
			}
			
		exit();			
	}
	
	$params = array(':id'=>$id,':uniacid'=>$_W['uniacid'], 'uid' => $uid);
	$info = pdo_fetch("SELECT * FROM ".tablename('nbyiheng_record')." WHERE id =:id and uid = :uid and uniacid = :uniacid LIMIT 1", $params);
	
	
	$imgarr=json_decode($info['imagestr']);
	foreach($imgarr as $img)
	
	{
		 $info['imgstr'] .='<div class="mui-card-header mui-card-media" style="width:100%;height:40vw;background-image:url(/attachment/'.$img.')"></div>';

	}
	
	include $this->template('detail');  
	
}   
	
	public function doWebSetting() {
			global $_W,$_GPC;
		$setting_data = array(
				'baiduAk' => trim($_GPC['baiduAk']),
				'uniacid' => $_W['uniacid'],
			);
			
$setting = pdo_fetch("SELECT * FROM ".tablename('nbyiheng_setting').' WHERE uniacid = :uniacid limit 1', array('uniacid' => $_W['uniacid']));

			
	if(checksubmit('save')) 
	{
		
			
			$result = pdo_insert('nbyiheng_setting', $setting_data);
			if (!empty($result)) {
				
				message('新增成功','', 'success');
			}
			
		
	}
	if(checksubmit('update')) 
	{
		
			$result = pdo_update('nbyiheng_setting', $setting_data, array('id' => 1));

			if (!empty($result)) {
				
				message('更新成功','', 'success');
			}
			
		
	}
		include $this->template('web/manage');
	}
	public function doWebManage() {
		global $_W,$_GPC;
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$params = array(':uniacid'=>$_W['uniacid']);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('nbyiheng_record') . " WHERE uniacid = :uniacid ", $params);
	$pager = pagination($total, $pindex, $psize);
	
		
		
	$list = pdo_fetchall("SELECT * FROM `ims_nbyiheng_record` LEFT JOIN `ims_mc_mapping_fans` ON `ims_nbyiheng_record`.`uid` = `ims_mc_mapping_fans`.uid AND `ims_nbyiheng_record`.uniacid= ".$_W['uniacid'] ." and `ims_mc_mapping_fans`.`uniacid`= ".$_W['uniacid'] ." ORDER BY `ims_nbyiheng_record`.id  DESC limit " . ($pindex - 1) * $psize . ',' . $psize);
   
	load()->func('tpl'); 
		
	include $this->template('web/list');
	}

}