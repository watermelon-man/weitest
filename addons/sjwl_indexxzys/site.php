<?php
/**
 * 星座运势模块微站定义
 *
 * @author 水金网络
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Sjwl_indexxzysModuleSite extends WeModuleSite {
	public $_style = '../addons/sjwl_indexxzys/template/mobile/kf';
	public function doMobileindex()
	{
	include $this->template('is_ticket');
	}
	
	public function doMobileorder()
	{
		global $_W, $_GPC;
		$op = $_GPC['op'];
		require_once '../addons/sjwl_indexxzys/curl.func.php';
		$list = pdo_fetch("select * from " . tablename('sjwl_indexxzys_order') );
		if(empty($list)){
			$appkey = 'b4dede29237cb8c3';//你的appkey
		}else{
			$appkey = $list['idkey'];//你的appkey
		}
		
		$astroid = $op;
		$date= date('Y-m-d',time());
		$url = "http://api.jisuapi.com/astro/fortune?appkey=$appkey&astroid=$astroid&date=$date";
		$result = curlOpen($url);
		$jsonarr = json_decode($result, true);
		//exit(var_dump($jsonarr));
		if($jsonarr['status'] != 0)
		{
			echo $jsonarr['msg'];
			exit();
		}

		$result = $jsonarr['result'];
		 $result['astroid'].' '.$result['astroname'].'<br>';

		$today = $result['today'];
		$tomorrow = $result['tomorrow'];
		$week = $result['week'];
		$month = $result['month'];
		$year = $result['year'];
		 '今日运势：<br>';
		 $today['date'].' '.$today['presummary'].' '.$today['star'].' '.$today['color'].' '.$today['number'].' '.$today['summary'].' '.$today['money'].' '.$today['career'].' '.$today['love'].' '.$today['health'].'<br>';
		 '明日运势：<br>';
		 $tomorrow['date'].' '.$tomorrow['presummary'].' '.$tomorrow['star'].' '.$tomorrow['color'].' '.$tomorrow['number'].' '.$tomorrow['summary'].' '.$tomorrow['money'].' '.$tomorrow['career'].' '.$tomorrow['love'].' '.$tomorrow['health'].'<br>';
		 '本周运势：<br>';
		 $week['date'].' '.$week['job'].' '.$week['money'].' '.$week['career'].' '.$week['love'].' '.$week['health'].'<br>';
		 '本月运势：<br>';
		 $month['date'].' '.$month['summary'].' '.$month['money'].' '.$month['career'].' '.$month['love'].' '.$month['health'].'<br>';
		 '本年运势：<br>';
		 $year['date'].' '.$year['summary'].' '.$year['money'].' '.$year['career'].' '.$year['love'].'<br>';
		
		include $this->template('order');
	}
	
	public function doWeborder()
	{
		global $_W, $_GPC;
		$op = $_GPC['op'];
		$id = $_GPC['id'];
		
		$list = pdo_fetch("select * from " . tablename('sjwl_indexxzys_order') );
		if(!empty($list)){
			$id = $list['id'];
		}
		
		if (checksubmit()) {
			$data = array();
			$data['idkey'] = $_GPC['idkey'];
			
		
		
			if(empty($id)){
				pdo_insert('sjwl_indexxzys_order', $data);
				message('添加成功',$this->createWebUrl('order'),'success');
			}else{
				pdo_update('sjwl_indexxzys_order',$data,array('id'=>$id));
				message('编辑成功',$this->createWebUrl('order'),'success');
			}
		}
		
		
		
		if($op=='delete'){
			if(empty($_GPC['id']))
			{
				message('指定的KEY不正确，请检查！', $this -> createWebUrl('order'),'error');
			}else
			{
				pdo_delete('sjwl_indexxzys_order',array('id'=>$_GPC['id']));
				message('此key已删除！',$this -> createWebUrl('order'),'success');
			}
		}
		include $this->template('order');
	}
	
}