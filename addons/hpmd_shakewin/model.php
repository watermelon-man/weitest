<?php

defined('IN_IA') or exit('Access Denied');
require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CacheLayer.php');

//为减少db压力，数据只写缓存。
if(!function_exists('record_shaking_fans')) {   
    function record_shaking_fans($actid, $nickname, $headimg){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($nickname) || empty($headimg) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_shaking' . $actid;
		$subdata['openid'] = $openid;
		//$subdata['openid'] = $nickname; //测试：模拟大量用户摇动手机场景
		$subdata['nickname'] = $nickname;
		$subdata['headimg'] = $headimg;
		$subdata['timestamp'] = time();

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			$result = array();
			foreach($table_keys as $value){
				$key_decode = json_decode($value, true);
				if($key_decode['openid'] != $openid) {
					$result[] = $value;
				}
			}
		    $result[] = json_encode($subdata);
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($result));

		} else {
			$values = array();
			$values[] = json_encode($subdata);
			cache_write_jy($tablekeysname, json_encode($values));
		}
		record_module_weid_keys($tablekeysname);
	}
}
if(!function_exists('get_shaking_fans')) {   
    function get_shaking_fans($actid, $timestamp){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_shaking' . $actid;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			$result = array();
			foreach($table_keys as $value){
				$key_decode = json_decode($value, true);
				if($key_decode['timestamp'] > $timestamp) {
					$result[] = $key_decode;
				}
			}
			return $result;
		} else {
			return null;
		}
	}
}

//大屏幕页面端timer轮询，比上次增长出的作为新的。
if(!function_exists('record_award_fans')) {   
    function record_award_fans($actid, $nickname, $headimg, $name){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($nickname) || empty($headimg) || empty($name) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_award' . $actid;
		$subdata['openid'] = $openid;
		$subdata['nickname'] = $nickname;
		$subdata['headimg'] = $headimg;
		$subdata['name'] = $name;
		$subdata['timestamp'] = time();

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			/*
			$result = array();
			foreach($table_keys as $value){
				$key_decode = json_decode($value, true);
				if($key_decode['openid'] != $openid) {
					$result[] = $value;
				}
			}
		    $result[] = json_encode($subdata);
			*/
			$table_keys[] = json_encode($subdata);
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($table_keys));

		} else {
			$values = array();
			$values[] = json_encode($subdata);
			cache_write_jy($tablekeysname, json_encode($values));
		}
		record_module_weid_keys($tablekeysname);
	}
}
if(!function_exists('get_award_fans')) {   
    function get_award_fans($actid, $timestamp){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$openid = $_W['fans']['openid'];
		$modulepath = MODULE_ROOT;
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_award' . $actid;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			$result = array();
			foreach($table_keys as $value){
				$key_decode = json_decode($value, true);
				if($key_decode['timestamp'] > $timestamp) {
					$result[] = $key_decode;
				}
			}
			return $result;
		} else {
			//尝试从db中获取数据
			$sqlall = "SELECT openid,awardid,createtime FROM " . tablename('shakewin_fans');
			$sqlall .= "  WHERE weid = '{$_W['uniacid']}' AND ruleid=" . $actid;
			$listawards = pdo_fetchall_with_cache('shakewin_fans', $sqlall, $params);
			
			$result = array();
			$list = array();
			if(!empty($listawards)) {
				foreach ($listawards as $item) {
					if(!empty($item['openid'])) {
						$subdata['openid'] = $item['openid'];
						$sql = 'SELECT nickname,headimg  FROM ' . tablename('shakewin_wxinfo') . ' WHERE `openid`=:openid';
						$params = array();
						$params[':openid'] = $item['openid'];
						$faninfo = pdo_fetch_with_cache('shakewin_wxinfo', $sql, $params);
						if(!empty($faninfo)) {
							$subdata['nickname'] = $faninfo['nickname'];
							$subdata['headimg'] = $faninfo['headimg'];
						}
					}
					if(!empty($item['awardid'])) {
						$sql = 'SELECT name FROM ' . tablename('shakewin_awards') . ' WHERE `id`=:id';
						$params = array();
						$params[':id'] = $item['awardid'];
						$awardinfo = pdo_fetchcolumn_with_cache('shakewin_awards', $sql, $params);
						if(!empty($awardinfo)) {
							$subdata['name'] = $awardinfo;
						}

					}
					$subdata['timestamp'] = $item['createtime'];

					if($subdata['timestamp'] > $timestamp) {
						$result[] = $subdata;
					}
					$list[] = json_encode($subdata);
				}
			}
			cache_write_jy($tablekeysname, json_encode($list));
			record_log('ShakeAward', 'txt', 'get_award_fans db', 'get_award_fans db');
			return $result;
		}
	}
}

//大屏幕开始结束，状态读取
if(!function_exists('bigscreen_begin')) {   
    function bigscreen_begin($actid){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulename = get_module_name();
		record_log('BigScreen', 'txt', 'modulename', $modulename);

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_be' . $actid;
		$subdata['begin'] = 1;
		$subdata['timestamp'] = time();
		$encodedata = json_encode($subdata);
		cache_write_jy($tablekeysname, $encodedata);
		record_module_weid_keys($tablekeysname);

		//直接写入数据库，但是要确保在stop时删除这条记录
		$insert = array(
			'cachekey' => $tablekeysname,
			'cacheval' => $encodedata,
		);
		$result = pdo_insert_with_cache('shakewin_cached', $insert);

	}
}
if(!function_exists('bigscreen_stop')) {   
    function bigscreen_stop($actid){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_be' . $actid;
		cache_delete_jy($tablekeysname);
		pdo_delete_with_cache("shakewin_cached", array("cachekey" => $tablekeysname));

		//活动停止 是否自动设置为下线 online==0
	}
}
if(!function_exists('is_bigscreen_begin')) {   
    function is_bigscreen_begin($actid){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulename = get_module_name();

		if(empty($actid) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_be' . $actid;
		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys) && $table_keys['timestamp'] > 0) {
			if($table_keys['begin'] == 1) {
				return true;
			} else {
				return false;
			}
		} else {
			$sql = 'SELECT cacheval FROM ' . tablename('shakewin_cached');
			$sql .= ' WHERE `cachekey` = :cachekey LIMIT 1';
			$params = array(':cachekey' => $tablekeysname);
			$cacheval = pdo_fetchcolumn_with_cache('shakewin_cached', $sql, $params);
			if(!empty($cacheval)) {
				$table_keys_db = json_decode($cacheval, true);
				if(!empty($table_keys_db) && $table_keys_db['timestamp'] > 0) {
					if($table_keys_db['begin'] == 1) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

	}
}