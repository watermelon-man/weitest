<?php
//
// 使用说明
//
// 1. 所有表必须含有weid（类型int，长度11）, createtime（类型int，长度10）, updatetime（类型int，长度10）字段
// 2. 确保程序人口（微信端入口，后台所有入口）处会调用default_cache_starttime判断是否有规则生成。
// 3. 配置表必须有cachesecond（类型int，长度10），starttime（类型int，长度10），endtime（类型int，长度10）字段
//

defined('IN_IA') or exit('Access Denied');

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/FileLock.php');

////////////////////////////////////////////////  jysoft cache layer ////////////////////////////////////////////////  begin
if(!function_exists('rand_array')) {
	function rand_array($oldarray, $num)
	{
		if(empty($oldarray) || count($oldarray) <= 0 || $num <= 0) {
			return null;
		}
    
		$randarray = array();
	    if($num > 1) {
			$idrands =  array_rand($oldarray, $num);
			foreach ($idrands as $val) {
				$randarray[] = $oldarray[$val];
			}
		} else if($num == 1) {
			$idrand =  array_rand($oldarray, $num);
			$randarray[] = $oldarray[$idrand];
		}
		return $randarray;
	}
}

if(!function_exists('cache_prefix_jy')) {
	function cache_prefix_jy($key) {
		//return $GLOBALS['_W']['config']['setting']['authkey'] . $key;
		return $key;
	}
}

if(!function_exists('cache_memcache_jy')) {
	function cache_memcache_jy() {
		global $_W;
		static $memcacheobj;
		
		$config = $_W['config']['setting']['memcache'];
		if(empty($config) || empty($config['server']) || empty($config['port'])){
			return null;
		}
			
		if (!extension_loaded('memcache')) {
			unit_result('UnitTest', 'log', 'Class Memcache', 'not found');
			return null;
		}
		if (empty($memcacheobj)) {
			$memcacheobj = new Memcache();
			if($config['pconnect']) {
				$connect = $memcacheobj->pconnect($config['server'], $config['port']);
			} else {
				$connect = $memcacheobj->connect($config['server'], $config['port']);
			}
			if(!$connect) {
				unit_result('UnitTest', 'log', 'Memcache', 'not in work');
				return null;
			}
		}
		return $memcacheobj;
	}
}

if(!function_exists('cache_read_jy')) {
	function cache_read_jy($key) {
		$memcache = cache_memcache_jy();
		if (empty($memcache) || is_error($memcache)) {
			unit_result('UnitTest', 'log', 'cache_read_jy', 'is_error');
			return cache_read(cache_prefix_jy($key));
		}
		$result = $memcache->get(cache_prefix_jy($key));
		//unit_result('UnitTest', 'log', 'cache_read_jy ：result', json_encode($result));
		return $result;
	}
}

if(!function_exists('cache_write_jy')) { 
	function cache_write_jy($key, $value, $ttl = 0) {
		$memcache = cache_memcache_jy();
		if (empty($memcache) || is_error($memcache)) {
			unit_result('UnitTest', 'log', 'cache_write_jy', 'is_error');
			cache_write(cache_prefix_jy($key), $value);
			return true;
		}
		
		if ($memcache->set(cache_prefix_jy($key), $value, MEMCACHE_COMPRESSED, $ttl)) {
			return true;
		} else {
			return false;
		}
	}
}


if(!function_exists('cache_delete_jy')) {  
	function cache_delete_jy($key) {
		$memcache = cache_memcache_jy();
		if (empty($memcache) || is_error($memcache)) {
			unit_result('UnitTest', 'log', 'cache_delete_jy', 'is_error');
			cache_delete(cache_prefix_jy($key));
			return true;
		}
		if ($memcache->delete(cache_prefix_jy($key))) {
			return true;
		} else {
			return false;
		}
	}
}

if(!function_exists('gen_cache_name')) {   
    function gen_cache_name($tablename, $sql, $paramarray, $keyfield=null){
		global $_GPC, $_W;
		//$openid = $_W['fans']['openid'];

        $cachename = $tablename . '_' . $sql;
		if(!empty($paramarray)) {
			$cachename = $cachename . '_' . json_encode($paramarray);
		}
		if(!empty($keyfield)) {
			$cachename = $cachename . '_' . $keyfield;
		}
		return md5($cachename);
	}
}

//用于单元测试
if(!function_exists('num_table_keys')) {   
    function num_table_keys($tablename){
		if(empty($tablename)) {
			return null;
		}
		$tablekeysname = $tablename . '_cachedkeys';
		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		return count($table_keys);
	}
}

// 本公众号本模块 begin
if(!function_exists('num_module_weid_keys')) {   
    function num_module_weid_keys(){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulepath = MODULE_ROOT;
		$modulename = null;
		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		if(empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_keys';

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		return count($table_keys);
	}
}

if(!function_exists('record_module_weid_keys')) {   
    function record_module_weid_keys($mykey){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulepath = MODULE_ROOT;
		$modulename = null;
		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		if(empty($mykey) || empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_keys';

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return;
				}
			}
		    $table_keys[] = $mykey;
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($table_keys));
		} else {
			$values = array();
			$values[] = $mykey;
			cache_write_jy($tablekeysname, json_encode($values));
		}
	}
}

if(!function_exists('clear_module_weid_keys')) {   
    function clear_module_weid_keys(){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulepath = MODULE_ROOT;
		$modulename = null;
		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		if(empty($modulename) || empty($weid)) {
			return null;
		}

		$tablekeysname = $modulename . $weid . '_keys';

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				cache_delete_jy($value);
			}
		}
		cache_delete_jy($tablekeysname); 
	}
}

if(!function_exists('exist_module_weid_keys')) {   
    function exist_module_weid_keys($mykey){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulepath = MODULE_ROOT;
		$modulename = null;
		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		if(empty($modulename) || empty($weid) || empty($mykey)) {
			return false;
		}

		$tablekeysname = $modulename . $weid . '_keys';

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return true;
				}
			}
		    return false;
		} else {
			return false;
		}
	}
}
// 本公众号本模块 end

if(!function_exists('record_table_keys')) {   
    function record_table_keys($tablename, $mykey){
		if(empty($tablename) || empty($mykey)) {
			return null;
		}
		record_module_weid_keys($mykey);

		$tablekeysname = $tablename . '_cachedkeys';

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return;
				}
			}
		    $table_keys[] = $mykey;
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($table_keys));
		} else {
			$values = array();
			$values[] = $mykey;
			cache_write_jy($tablekeysname, json_encode($values));
		}
	}
}

if(!function_exists('exist_table_keys')) {   
    function exist_table_keys($tablename, $mykey){
		if(empty($tablename) || empty($mykey)) {
			return false;
		}

		$tablekeysname = $tablename . '_cachedkeys';

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return true;
				}
			}
		    return false;
		} else {
			return false;
		}
	}
}

if(!function_exists('clear_table_keys')) {   
    function clear_table_keys($tablename){
		if(empty($tablename)) {
			return false;
		}

		$tablekeysname = $tablename . '_cachedkeys';

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				cache_delete_jy($value);
			}
		} 
		cache_delete_jy($tablekeysname); 
	}
}

if(!function_exists('default_cache_expiretime')) {   
    function default_cache_expiretime($config_table_name=null, $where=null){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulepath = MODULE_ROOT;
		$modulename = null;
		$defauttime = time() + 60;

		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		if(empty($weid) || empty($modulename)) {
			return $defauttime;
		}

		$configkeyname = $modulename . $weid . '_expiretime';

		if(empty($config_table_name)) {
			//读缓存
			$expt = cache_read_jy($configkeyname);

			record_log('developer', 'txt', 'default_cache_expiretime cache: ', $expt);
			if(empty($expt) || $expt <= 0) {
				return $defauttime;
			} else {
				return $expt;
			}
		} else {
			//读表，写缓存
			cache_delete_jy($configkeyname);
			$sql = "SELECT * FROM " . tablename($config_table_name) . " WHERE " . $where . " weid = '{$_W['uniacid']}' ORDER BY `starttime` DESC LIMIT 1";
			$value = pdo_fetch_with_cache($config_table_name, $sql);
			
			if ($value && !empty($value['weid'])) {
				//这里使用配置中的值重新计算过期时间
				if($value['cachesecond'] > 0) {
					$defauttime = time() + $value['cachesecond'];
				} else if($value['endtime'] > 0) {
					$defauttime = $value['endtime'] + 86400;
				} else {
					$defauttime = time() + 60;
				}
				cache_write_jy($configkeyname, $defauttime);
				record_table_keys($config_table_name, $configkeyname);

				record_log('developer', 'txt', 'default_cache_expiretime not cache: ', $defauttime);
				record_log('CacheNotHit', 'txt', 'default_cache_expiretime', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			}
			return $defauttime;
		}
	}
}

if(!function_exists('default_cache_starttime')) {   
    function default_cache_starttime($config_table_name, $where=null){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulepath = MODULE_ROOT;
		$modulename = null;
		$defauttime = time() + 60;

		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		if(empty($weid) || empty($modulename)) {
			return $defauttime;
		}

		$configkeyname = $modulename . $weid . '_expiretime';

		$starttimekeyname = $modulename . $weid . '_starttime';
		$starttime = cache_read_jy($starttimekeyname);

		if(!empty($starttime) && $starttime > 0) {
			//读缓存
			record_log('developer', 'txt', 'default_cache_starttime cache: ', $starttime);
			return $starttime;
		} else {
			//读表，写缓存
			cache_delete_jy($starttimekeyname);
			$value = pdo_fetch_with_cache($config_table_name, "SELECT * FROM " . tablename($config_table_name) . " WHERE  " . $where . " weid = '{$_W['uniacid']}' ORDER BY `starttime` DESC LIMIT 1");
			
			if ($value && !empty($value['weid'])) {
				if($value['cachesecond'] > 0) {
					$defauttime = time() + $value['cachesecond'];
				} else if($value['endtime'] > 0) {
					$defauttime = $value['endtime'] + 86400;
				} else {
					$defauttime = time() + 60;
				}

				cache_write_jy($configkeyname, $defauttime);
				record_table_keys($config_table_name, $configkeyname);

				if($value['starttime'] > 0) {
					$defauttime = $value['starttime'];
					cache_write_jy($starttimekeyname, $value['starttime']);
					record_table_keys($config_table_name, $starttimekeyname);
				}

				record_log('developer', 'txt', 'default_cache_starttime not cache: ', $value['starttime']);
				record_log('CacheNotHit', 'txt', 'default_cache_starttime', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			}
			return $defauttime;
		}
	}
}

if(!function_exists('default_cache_endtime')) {   
    function default_cache_endtime($config_table_name, $where=null){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
		$modulepath = MODULE_ROOT;
		$modulename = null;
		$defauttime = time() + 60;

		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		if(empty($weid) || empty($modulename)) {
			return $defauttime;
		}

		$configkeyname = $modulename . $weid . '_expiretime';

		$starttimekeyname = $modulename . $weid . '_endtime';
		$starttime = cache_read_jy($starttimekeyname);

		if(!empty($starttime) && $starttime > 0) {
			//读缓存
			record_log('developer', 'txt', 'default_cache_endtime cache: ', $starttime);
			return $starttime;
		} else {
			//读表，写缓存
			cache_delete_jy($starttimekeyname);
			$value = pdo_fetch_with_cache($config_table_name, "SELECT * FROM " . tablename($config_table_name) . " WHERE " . $where . "  weid = '{$_W['uniacid']}' ORDER BY `starttime` DESC LIMIT 1");
			
			if ($value && !empty($value['weid'])) {
				if($value['cachesecond'] > 0) {
					$defauttime = time() + $value['cachesecond'];
				} else if($value['endtime'] > 0) {
					$defauttime = $value['endtime'] + 86400;
				} else {
					$defauttime = time() + 60;
				}

				cache_write_jy($configkeyname, $defauttime);
				record_table_keys($config_table_name, $configkeyname);

				if($value['endtime'] > 0) {
					$starttime = $value['endtime'];
					cache_write_jy($starttimekeyname, $value['endtime']);
					record_table_keys($config_table_name, $starttimekeyname);
				}

				record_log('developer', 'txt', 'default_cache_endtime not cache: ', $value['endtime']);
				record_log('CacheNotHit', 'txt', 'default_cache_endtime', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			}
			return $starttime;
		}
	}
}

if(!function_exists('pdo_fetchall_with_cache')) {   
    function pdo_fetchall_with_cache($tablename, $sql, $paramarray=null, $keyfield=null, $jointable=null){
		global $_GPC, $_W;
		//$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($sql)) {
			return null;
		}

		$buffertm = default_cache_expiretime();

		$cachename = gen_cache_name($tablename, $sql, $paramarray, $keyfield);

		$activity_config_cache = json_decode(cache_read_jy($cachename), true);

		$expiretime = $activity_config_cache['expire_time'];
		if(!empty($activity_config_cache) && !empty($expiretime) && time() < $expiretime ) {
			unset($activity_config_cache['expire_time']); 
			record_log('developer', 'txt', 'pdo_fetchall_with_cache already cache: ', json_encode($activity_config_cache));
			record_log('CacheHit', 'txt', 'pdo_fetchall_with_cache', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			return $activity_config_cache;
			
		} else {
			cache_delete_jy($cachename);
			if(!empty($paramarray) && !empty($keyfield)) {
				$values = pdo_fetchall($sql, $paramarray, $keyfield);
			} else if(!empty($paramarray)) {
				$values = pdo_fetchall($sql, $paramarray);
			} else {
				$values = pdo_fetchall($sql);
			}

			if ($values) {
				$data['expire_time'] = $buffertm;
				foreach($values as $key => $value) { 
					$data[$key] = $value;
				}
				cache_write_jy($cachename, json_encode($data));
				record_table_keys($tablename, $cachename);
				if(!empty($jointable)) {
					record_table_keys($jointable, $cachename);
				}

				record_log('developer', 'txt', 'pdo_fetchall_with_cache not cache: ', json_encode($data));
				record_log('CacheNotHit', 'txt', 'pdo_fetchall_with_cache', $tablename . '&' . $sql . '&' . json_encode($paramarrays));

				return $values;
			}
		} 
		return null;
	}
}

if(!function_exists('pdo_fetchcolumn_with_cache')) {   
    function pdo_fetchcolumn_with_cache($tablename, $sql, $paramarray=null, $jointable=null){
		global $_GPC, $_W;
		//$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($sql)) {
			return null;
		}
		
		$buffertm = default_cache_expiretime();

		$cachename = gen_cache_name($tablename, $sql, $paramarray);

		$activity_config_cache = json_decode(cache_read_jy($cachename), true);

		$expiretime = $activity_config_cache['expire_time'];
		if(!empty($activity_config_cache) && !empty($expiretime) && time() < $expiretime ) {
			record_log('developer', 'txt', 'pdo_fetchcolumn_with_cache already cache: ', json_encode($activity_config_cache));
			record_log('CacheHit', 'txt', 'pdo_fetchcolumn_with_cache', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			return $activity_config_cache['colum_val'];
			
		} else {
			cache_delete_jy($cachename);
			if(!empty($paramarray)) {
				$values = pdo_fetchcolumn($sql, $paramarray);
			} else {
				$values = pdo_fetchcolumn($sql);
			}

			if ($values) {
				$data['expire_time'] = $buffertm;
				$data['colum_val'] = $values;
				cache_write_jy($cachename, json_encode($data));
				record_table_keys($tablename, $cachename);
				if(!empty($jointable)) {
					record_table_keys($jointable, $cachename);
				}

				record_log('developer', 'txt', 'pdo_fetchcolumn_with_cache not cache: ', json_encode($data));
				record_log('CacheNotHit', 'txt', 'pdo_fetchcolumn_with_cache', $tablename . '&' . $sql . '&' . json_encode($paramarrays));

				return $values;
			}
		} 
		return null;
	}
}

if(!function_exists('pdo_fetch_with_cache')) {   
    function pdo_fetch_with_cache($tablename, $sql, $paramarray=null, $jointable=null){
		global $_GPC, $_W;
		//$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($sql)) {
			return null;
		}

		$buffertm = default_cache_expiretime();

		$cachename = gen_cache_name($tablename, $sql, $paramarray);

		$activity_config_cache = json_decode(cache_read_jy($cachename), true);

		$expiretime = $activity_config_cache['expire_time'];
		if(!empty($activity_config_cache) && !empty($expiretime) && time() < $expiretime ) {
			record_log('developer', 'txt', 'pdo_fetch_with_cache already cache: ', json_encode($activity_config_cache));
			record_log('CacheHit', 'txt', 'pdo_fetch_with_cache', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			return $activity_config_cache;
			
		} else {
			cache_delete_jy($cachename);
			if(!empty($paramarray)) {
				$values = pdo_fetch($sql, $paramarray);
			} else {
				$values = pdo_fetch($sql);
			}

			if ($values) {
				$data['expire_time'] = $buffertm;
				foreach($values as $key => $value) { 
					$data[$key] = $value;
				}
				cache_write_jy($cachename, json_encode($data));
				record_table_keys($tablename, $cachename);
				if(!empty($jointable)) {
					record_table_keys($jointable, $cachename);
				}

				record_log('developer', 'txt', 'pdo_fetch_with_cache not cache: ', json_encode($data));
				record_log('CacheNotHit', 'txt', 'pdo_fetch_with_cache', $tablename . '&' . $sql . '&' . json_encode($paramarrays));

				return $values;
			}
		} 
		return null;
	}
}

if(!function_exists('pdo_update_with_cache')) {   
    function pdo_update_with_cache($tablename, $arraydata, $arraywhere){
		global $_GPC, $_W;
		//$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($arraydata) || empty($arraywhere)) {
			record_log('developer', 'txt', 'pdo_update_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_update_with_cache: arraydata empty!', json_encode($arraydata));
			record_log('developer', 'txt', 'pdo_update_with_cache: arraywhere empty!', json_encode($arraywhere));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		return pdo_update($tablename, $arraydata, $arraywhere);

	}
}

if(!function_exists('pdo_insert_with_cache')) {   
    function pdo_insert_with_cache($tablename, $arraydata){
		global $_GPC, $_W;
		//$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($arraydata)) {
			record_log('developer', 'txt', 'pdo_insert_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_insert_with_cache: arraydata empty!', json_encode($arraydata));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		return pdo_insert($tablename, $arraydata);
		//$insid = pdo_insertid();
		//unit_result('UnitTest', 'log', 'insid inter', $insid);

	}
}

if(!function_exists('pdo_delete_with_cache')) {   
    function pdo_delete_with_cache($tablename, $wheredata){
		global $_GPC, $_W;
		//$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($wheredata)) {
			record_log('developer', 'txt', 'pdo_delete_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_delete_with_cache: wheredata empty!', json_encode($wheredata));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		return pdo_delete($tablename, $wheredata);

	}
}
////////////////////////////////////////////////  jysoft cache layer ////////////////////////////////////////////////  end


////////////////////////////// jysoft dirty cache layer 真对增删改不对其他用户产生影响的表 /////////////////////////  begin
// 注意，如果一个用户修改了表会对其他用户产生影响，不可以使用这种方式，否则会读到脏数据 !

if(!function_exists('gen_cache_name_singleusr')) {   
    function gen_cache_name_singleusr($tablename, $sql, $paramarray, $keyfield=null){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];

        $cachename = $openid . '_' . $tablename . '_' . $sql;
		if(!empty($paramarray)) {
			$cachename = $cachename . '_' . json_encode($paramarray);
		}
		if(!empty($keyfield)) {
			$cachename = $cachename . '_' . $keyfield;
		}
		return md5($cachename);
	}
}

//用于单元测试
if(!function_exists('num_table_keys_dirty')) {   
    function num_table_keys_dirty($tablename){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];

		if(empty($tablename) || empty($openid)) {
			return null;
		}
		$tablekeysname = $openid . '_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		return count($table_keys);
	}
}

if(!function_exists('record_table_keys_dirty')) {   
    function record_table_keys_dirty($tablename, $mykey){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];

		if(empty($tablename) || empty($mykey) || empty($openid)) {
			return null;
		}
		record_module_weid_keys($mykey);

		$tablekeysname = $openid . '_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return;
				}
			}
		    $table_keys[] = $mykey;
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($table_keys));
		} else {
			$values = array();
			$values[] = $mykey;
			cache_write_jy($tablekeysname, json_encode($values));
		}
	}
}

if(!function_exists('exist_table_keys_dirty')) {   
    function exist_table_keys_dirty($tablename, $mykey){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];

		if(empty($tablename) || empty($mykey) || empty($openid)) {
			return false;
		}

		$tablekeysname = $openid . '_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return true;
				}
			}
		    return false;
		} else {
			return false;
		}
	}
}

if(!function_exists('clear_table_keys_dirty')) {   
    function clear_table_keys_dirty($tablename){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];

		if(empty($tablename) || empty($openid)) {
			return false;
		}

		$tablekeysname = $openid . '_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				cache_delete_jy($value);
			}
		} 
		cache_delete_jy($tablekeysname); 
	}
}

//////////////////////////////////

//用于单元测试
if(!function_exists('num_table_keys_dirty_web')) {   
    function num_table_keys_dirty_web($tablename){
		global $_GPC, $_W;

		if(empty($tablename)) {
			return null;
		}
		$tablekeysname = 'web_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		return count($table_keys);
	}
}

if(!function_exists('record_table_keys_dirty_web')) {   
    function record_table_keys_dirty_web($tablename, $mykey){
		global $_GPC, $_W;

		if(empty($tablename) || empty($mykey)) {
			return null;
		}
		record_module_weid_keys($mykey);

		$tablekeysname = 'web_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return;
				}
			}
		    $table_keys[] = $mykey;
			cache_delete_jy($tablekeysname);
			cache_write_jy($tablekeysname, json_encode($table_keys));
		} else {
			$values = array();
			$values[] = $mykey;
			cache_write_jy($tablekeysname, json_encode($values));
		}
	}
}

if(!function_exists('exist_table_keys_dirty_web')) {   
    function exist_table_keys_dirty_web($tablename, $mykey){
		global $_GPC, $_W;

		if(empty($tablename) || empty($mykey)) {
			return false;
		}

		$tablekeysname = 'web_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				if($value == $mykey) {
					return true;
				}
			}
		    return false;
		} else {
			return false;
		}
	}
}

if(!function_exists('clear_table_keys_dirty_web')) {   
    function clear_table_keys_dirty_web($tablename){
		global $_GPC, $_W;

		if(empty($tablename)) {
			return false;
		}

		$tablekeysname = 'web_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;

		$table_keys = json_decode(cache_read_jy($tablekeysname), true);
		if(!empty($table_keys)) {
			foreach($table_keys as $value){
				cache_delete_jy($value);
			}
		} 
		cache_delete_jy($tablekeysname); 
	}
}

//////////////////////////////////

if(!function_exists('pdo_fetchall_with_cache_dirty')) {   
    function pdo_fetchall_with_cache_dirty($tablename, $sql, $paramarray=null, $keyfield=null, $jointable=null){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($sql) || empty($openid)) {
			return null;
		}

		$buffertm = default_cache_expiretime();

		$cachename = gen_cache_name($tablename, $sql, $paramarray, $keyfield);

		$activity_config_cache = json_decode(cache_read_jy($cachename), true);

		$expiretime = $activity_config_cache['expire_time'];
		if(!empty($activity_config_cache) && !empty($expiretime) && time() < $expiretime ) {
			unset($activity_config_cache['expire_time']); 
			record_log('developer', 'txt', 'pdo_fetchall_with_cache_dirty already cache: ', json_encode($activity_config_cache));
			record_log('CacheHit', 'txt', 'pdo_fetchall_with_cache_dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			return $activity_config_cache;
			
		} else {
            //整表缓存未命中，查看单用户缓存
			$cachename_dirty = gen_cache_name_singleusr($tablename, $sql, $paramarray, $keyfield);
			$dirty_cache = json_decode(cache_read_jy($cachename_dirty), true);
			$expiretime_dirty = $dirty_cache['expire_time'];
			if(!empty($dirty_cache) && !empty($expiretime_dirty) && time() < $expiretime_dirty ) {
				unset($dirty_cache['expire_time']); 
				record_log('developer', 'txt', 'pdo_fetchall_with_cache_dirty dirty already cache: ', json_encode($dirty_cache));
				record_log('CacheHit', 'txt', 'pdo_fetchall_with_cache_dirty dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
				return $dirty_cache;
			} else {
				cache_delete_jy($cachename);
				cache_delete_jy($cachename_dirty);
				if(!empty($paramarray) && !empty($keyfield)) {
					$values = pdo_fetchall($sql, $paramarray, $keyfield);
				} else if(!empty($paramarray)) {
					$values = pdo_fetchall($sql, $paramarray);
				} else {
					$values = pdo_fetchall($sql);
				}

				if ($values) {
					$data['expire_time'] = $buffertm;
					foreach($values as $key => $value) { 
						$data[$key] = $value;
					}
					cache_write_jy($cachename, json_encode($data));
					record_table_keys($tablename, $cachename);
					if(!empty($jointable)) {
						record_table_keys($jointable, $cachename);
					}

					//写单用户缓存
					cache_write_jy($cachename_dirty, json_encode($data));
					record_table_keys_dirty($tablename, $cachename_dirty);
					//并记录到web缓存中
					record_table_keys_dirty_web($tablename, $cachename_dirty);
					if(!empty($jointable)) {
						record_table_keys_dirty($jointable, $cachename_dirty);
						//并记录到web缓存中
						record_table_keys_dirty_web($tablename, $cachename_dirty);
					}
					

					record_log('developer', 'txt', 'pdo_fetchall_with_cache_dirty not cache: ', json_encode($data));
					record_log('CacheNotHit', 'txt', 'pdo_fetchall_with_cache_dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));

					return $values;
				}
			}

		} 
		return null;
	}
}

if(!function_exists('pdo_fetchcolumn_with_cache_dirty')) {   
    function pdo_fetchcolumn_with_cache_dirty($tablename, $sql, $paramarray=null, $jointable=null){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($sql) || empty($openid)) {
			return null;
		}

		$buffertm = default_cache_expiretime();

		$cachename = gen_cache_name($tablename, $sql, $paramarray);

		$activity_config_cache = json_decode(cache_read_jy($cachename), true);

		$expiretime = $activity_config_cache['expire_time'];

		if(!empty($activity_config_cache) && !empty($expiretime) && time() < $expiretime ) {
			record_log('developer', 'txt', 'pdo_fetchcolumn_with_cache_dirty already cache: ', json_encode($activity_config_cache));
			record_log('CacheHit', 'txt', 'pdo_fetchcolumn_with_cache_dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			return $activity_config_cache['colum_val'];
			
		} else {
            //整表缓存未命中，查看单用户缓存
			$cachename_dirty = gen_cache_name_singleusr($tablename, $sql, $paramarray);
			$dirty_cache = json_decode(cache_read_jy($cachename_dirty), true);
			$expiretime_dirty = $dirty_cache['expire_time'];

			if(!empty($dirty_cache) && !empty($expiretime_dirty) && time() < $expiretime_dirty ) { 
				record_log('developer', 'txt', 'pdo_fetchcolumn_with_cache_dirty dirty already cache: ', json_encode($dirty_cache));
				record_log('CacheHit', 'txt', 'pdo_fetchcolumn_with_cache_dirty dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
				return $dirty_cache['colum_val'];
			} else {
				cache_delete_jy($cachename);
				cache_delete_jy($cachename_dirty);
				if(!empty($paramarray)) {
					$values = pdo_fetchcolumn($sql, $paramarray);
				} else {
					$values = pdo_fetchcolumn($sql);
				}

				if ($values) {
					$data['expire_time'] = $buffertm;
					$data['colum_val'] = $values;
					cache_write_jy($cachename, json_encode($data));
					record_table_keys($tablename, $cachename);
					if(!empty($jointable)) {
						record_table_keys($jointable, $cachename);
					}
					//写单用户缓存
					cache_write_jy($cachename_dirty, json_encode($data));
					record_table_keys_dirty($tablename, $cachename_dirty);
					//并记录到web缓存中
					record_table_keys_dirty_web($tablename, $cachename_dirty);
					if(!empty($jointable)) {
						record_table_keys_dirty($jointable, $cachename_dirty);
						//并记录到web缓存中
						record_table_keys_dirty_web($tablename, $cachename_dirty);
					}

					record_log('developer', 'txt', 'pdo_fetchcolumn_with_cache_dirty not cache: ', json_encode($data));
					record_log('CacheNotHit', 'txt', 'pdo_fetchcolumn_with_cache_dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));

					return $values;
				}
			}



		} 
		return null;
	}
}

if(!function_exists('pdo_fetch_with_cache_dirty')) {   
    function pdo_fetch_with_cache_dirty($tablename, $sql, $paramarray=null, $jointable=null){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($sql) || empty($openid)) {
			return null;
		}

		$buffertm = default_cache_expiretime();

		$cachename = gen_cache_name($tablename, $sql, $paramarray);

		$activity_config_cache = json_decode(cache_read_jy($cachename), true);

		$expiretime = $activity_config_cache['expire_time'];
		if(!empty($activity_config_cache) && !empty($expiretime) && time() < $expiretime ) {
			record_log('developer', 'txt', 'pdo_fetch_with_cache_dirty already cache: ', json_encode($activity_config_cache));
			record_log('CacheHit', 'txt', 'pdo_fetch_with_cache_dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
			return $activity_config_cache;
			
		} else {
            //整表缓存未命中，查看单用户缓存
			$cachename_dirty = gen_cache_name_singleusr($tablename, $sql, $paramarray);
			$dirty_cache = json_decode(cache_read_jy($cachename_dirty), true);
			$expiretime_dirty = $dirty_cache['expire_time'];
			if(!empty($dirty_cache) && !empty($expiretime_dirty) && time() < $expiretime_dirty ) { 
				record_log('developer', 'txt', 'pdo_fetch_with_cache_dirty dirty already cache: ', json_encode($dirty_cache));
				record_log('CacheHit', 'txt', 'pdo_fetch_with_cache_dirty dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));
				return $dirty_cache;
			} else {
				cache_delete_jy($cachename);
				cache_delete_jy($cachename_dirty);
				if(!empty($paramarray)) {
					$values = pdo_fetch($sql, $paramarray);
				} else {
					$values = pdo_fetch($sql);
				}

				if ($values) {
					$data['expire_time'] = $buffertm;
					foreach($values as $key => $value) { 
						$data[$key] = $value;
					}
					cache_write_jy($cachename, json_encode($data));
					record_table_keys($tablename, $cachename);
					if(!empty($jointable)) {
						record_table_keys($jointable, $cachename);
					}
					//写单用户缓存
					cache_write_jy($cachename_dirty, json_encode($data));
					record_table_keys_dirty($tablename, $cachename_dirty);
					//并记录到web缓存中
					record_table_keys_dirty_web($tablename, $cachename_dirty);
					if(!empty($jointable)) {
						record_table_keys_dirty($jointable, $cachename_dirty);
						//并记录到web缓存中
						record_table_keys_dirty_web($tablename, $cachename_dirty);
					}

					record_log('developer', 'txt', 'pdo_fetch_with_cache_dirty not cache: ', json_encode($data));
					record_log('CacheNotHit', 'txt', 'pdo_fetch_with_cache_dirty', $tablename . '&' . $sql . '&' . json_encode($paramarrays));

					return $values;
				}
			}
		} 
		return null;
	}
}

////////////// 微信端 增删改 单用户缓存 接口
if(!function_exists('pdo_update_with_cache_dirty')) {   
    function pdo_update_with_cache_dirty($tablename, $arraydata, $arraywhere){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($arraydata) || empty($arraywhere) || empty($openid)) {
			record_log('developer', 'txt', 'pdo_update_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_update_with_cache: arraydata empty!', json_encode($arraydata));
			record_log('developer', 'txt', 'pdo_update_with_cache: arraywhere empty!', json_encode($arraywhere));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		//清除单用户缓存
		clear_table_keys_dirty($tablename);
		$tablekeysname = $openid . '_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		cache_delete_jy($tablekeysname);

		return pdo_update($tablename, $arraydata, $arraywhere);

	}
}

if(!function_exists('pdo_insert_with_cache_dirty')) {   
    function pdo_insert_with_cache_dirty($tablename, $arraydata){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($arraydata) || empty($openid)) {
			record_log('developer', 'txt', 'pdo_insert_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_insert_with_cache: arraydata empty!', json_encode($arraydata));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		//清除单用户缓存
		clear_table_keys_dirty($tablename);
		$tablekeysname = $openid . '_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		cache_delete_jy($tablekeysname);

		return pdo_insert($tablename, $arraydata);
		//$insid = pdo_insertid();
		//unit_result('UnitTest', 'log', 'insid inter', $insid);

	}
}

if(!function_exists('pdo_delete_with_cache_dirty')) {   
    function pdo_delete_with_cache_dirty($tablename, $wheredata){
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		if(empty($tablename) || empty($wheredata) || empty($openid)) {
			record_log('developer', 'txt', 'pdo_delete_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_delete_with_cache: wheredata empty!', json_encode($wheredata));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		//清除单用户缓存
		clear_table_keys_dirty($tablename);
		$tablekeysname = $openid . '_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		cache_delete_jy($tablekeysname);

		return pdo_delete($tablename, $wheredata);

	}
}

////////////// 后台web端 增删改 单用户缓存 接口
// web端拿不到含有单用户的openid标识的key，所以新增一组接口
if(!function_exists('pdo_update_with_cache_dirty_web')) {   
    function pdo_update_with_cache_dirty_web($tablename, $arraydata, $arraywhere){
		global $_GPC, $_W;
		if(empty($tablename) || empty($arraydata) || empty($arraywhere)) {
			record_log('developer', 'txt', 'pdo_update_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_update_with_cache: arraydata empty!', json_encode($arraydata));
			record_log('developer', 'txt', 'pdo_update_with_cache: arraywhere empty!', json_encode($arraywhere));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		//清除单用户缓存
		clear_table_keys_dirty_web($tablename);
		$tablekeysname = 'web_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		cache_delete_jy($tablekeysname);

		return pdo_update($tablename, $arraydata, $arraywhere);

	}
}

if(!function_exists('pdo_insert_with_cache_dirty_web')) {   
    function pdo_insert_with_cache_dirty_web($tablename, $arraydata){
		global $_GPC, $_W;
		if(empty($tablename) || empty($arraydata)) {
			record_log('developer', 'txt', 'pdo_insert_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_insert_with_cache: arraydata empty!', json_encode($arraydata));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		//清除单用户缓存
		clear_table_keys_dirty_web($tablename);
		$tablekeysname = 'web_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		cache_delete_jy($tablekeysname);

		return pdo_insert($tablename, $arraydata);
		//$insid = pdo_insertid();
		//unit_result('UnitTest', 'log', 'insid inter', $insid);

	}
}

if(!function_exists('pdo_delete_with_cache_dirty_web')) {   
    function pdo_delete_with_cache_dirty_web($tablename, $wheredata){
		global $_GPC, $_W;
		if(empty($tablename) || empty($wheredata)) {
			record_log('developer', 'txt', 'pdo_delete_with_cache: tablename empty!', $tablename);
			record_log('developer', 'txt', 'pdo_delete_with_cache: wheredata empty!', json_encode($wheredata));
			return null;
		}

		clear_table_keys($tablename);
		$tablekeysname = $tablename . '_cachedkeys';
		cache_delete_jy($tablekeysname);

		//清除单用户缓存
		clear_table_keys_dirty_web($tablename);
		$tablekeysname = 'web_' . $tablename . '_cachedkeys';
		$tablekeysname = md5($tablekeysname) ;
		cache_delete_jy($tablekeysname);

		return pdo_delete($tablename, $wheredata);

	}
}
//////////////
////////////////////////////// jysoft dirty cache layer 真对增删改不对其他用户产生影响的表 /////////////////////////  end



////////////////////////////////////////////////  jysoft 加锁缓存统计 ////////////////////////////////////////////////  start
//缓存中统计一个表中符合where条件的条目
if(!function_exists('table_count_num_withcache')) {   
    function table_count_num_withcache($tablename, $where){
		global $_W;
		$weid = $_W['uniacid'];

		if(empty($tablename) || empty($where)) {
			return 0;
		}

		$key = gen_cache_name($tablename, 'COUNT(weid)', $where);
		$key .= $weid;

		$data = json_decode(cache_read_jy($key), true);
		$expiretime = $data['expire_time'];

		if(!empty($data) && !empty($expiretime) && time() < $expiretime) 
		{
			record_log('developer', 'txt', 'table_count_num_withcache: cache ', $data['count_num']);
			return $data['count_num'];
		} 
		else 
		{
			cache_delete_jy($key);
			$sql = "SELECT COUNT(weid) FROM " . tablename($tablename);
			$sql .= " WHERE " . $where;
			$question_rules_num = pdo_fetchcolumn_with_cache($tablename, $sql);
			record_log('developer', 'txt', 'table_count_num_withcache: not cache ', $question_rules_num);
			inc_table_count($tablename, $where, $question_rules_num);
			record_module_weid_keys($key);
			return $question_rules_num;
		}
	}
}
if(!function_exists('inc_table_count')) {   
    function inc_table_count($tablename, $where, $num){
		global $_W;
		$weid = $_W['uniacid'];

		if(empty($tablename) || empty($where) || $num <= 0) {
			return;
		}

		$key = gen_cache_name($tablename, 'COUNT(weid)', $where);
		$key .= $weid;

		$data = json_decode(cache_read_jy($key), true);

		$write_key = $key;
		$lock = new CacheLock($write_key);
		$lock->lock();

		$expiretime = $data['expire_time'];
		if(!empty($data) && !empty($expiretime) && time() < $expiretime) 
		{
			$num = $data['count_num'] + $num;
		} 
		else 
		{
			$expiretime = default_cache_expiretime();
		}
		$dataencode['expire_time'] = $expiretime;
		$dataencode['count_num'] = $num;
		cache_write_jy($key, json_encode($dataencode));

		$lock->unlock();

	}
}

//缓存中统计一个表中字段求和，注意该字段必须是数值
if(!function_exists('table_sum_col_withcache')) {   
    function table_sum_col_withcache($tablename, $col, $where){
		global $_W;
		$weid = $_W['uniacid'];

		if(empty($tablename) || empty($col) || empty($where)) {
			return 0;
		}

		$key = gen_cache_name($tablename, $col, $where);
		$key .= $weid;

		$data = json_decode(cache_read_jy($key), true);
		$expiretime = $data['expire_time'];

		if(!empty($data) && !empty($expiretime) && time() < $expiretime) 
		{
			record_log('developer', 'txt', 'table_sum_col_withcache cache', $data['sum_col']);
			return $data['sum_col'];
		} 
		else 
		{
			cache_delete_jy($key);
			$sql = "SELECT " . $col . " FROM " . tablename($tablename);
			$sql .= " WHERE " . $where;
			$listhuojiang = pdo_fetchall_with_cache($tablename, $sql);
			$totalscores = 0;
			if (!empty($listhuojiang)) {
				foreach ($listhuojiang as $row) {
					if(!empty($row[$col])){
						$totalscores += intval($row[$col]);
					}
				}
			}
			record_log('developer', 'txt', 'table_sum_col_withcache not cache', $totalscores);
			inc_col_sum($tablename, $col, $where, $totalscores);
			record_module_weid_keys($key);
			return $totalscores;
		}
	}
}

if(!function_exists('inc_col_sum')) {   
    function inc_col_sum($tablename, $col, $where, $money){
		global $_W;
		$weid = $_W['uniacid'];

		if(empty($tablename) || empty($col) || empty($where) || $money <= 0) {
			return;
		}
		$key = gen_cache_name($tablename, $col, $where);
		$key .= $weid;

		$data = json_decode(cache_read_jy($key), true);

		$write_key = $key;
		$lock = new CacheLock($write_key);
		$lock->lock();

		$expiretime = $data['expire_time'];
		if(!empty($data) && !empty($expiretime) && time() < $expiretime) 
		{
			$money = $data['sum_col'] + $money;
		} 
		$dataencode['expire_time'] = default_cache_expiretime();
		$dataencode['sum_col'] = $money;
		cache_write_jy($key, json_encode($dataencode));

		$lock->unlock();
	}
}
////////////////////////////////////////////////  jysoft 加锁统计 ////////////////////////////////////////////////  end

////////////////////////////////////////////////  jysoft 日志 ////////////////////////////////////////////////  start
if(!function_exists('record_log')) {   
    function record_log($type, $ext, $key, $val){
		global $_GPC, $_W;
		if(empty($type)) {
			$type = 'info';
		}
		if(empty($ext)) {
			$ext = 'log';
		}
		if(empty($key)) {
			$key = '未输入标识';
		}
		if(empty($val)) {
			//$val = '未输入标识内容';
		}
		
		$modulepath = MODULE_ROOT;
		$modulename = null;
		
		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		file_put_contents('../addons/'.$modulename.'/log/'. date("Y m d H" ,strtotime('now')) . '_' . $_W['fans']['openid'] . '_' . $type . '.' . $ext,  date("Y-m-d H:i:s" ,strtotime('now')) . '  ' . $key . ' : ' . $_W['fans']['openid'] .' : ' . PHP_EOL . $val . PHP_EOL  , FILE_APPEND);
	}
}

if(!function_exists('unit_result')) {   
    function unit_result($type, $ext, $key, $val){
		global $_GPC, $_W;
		if(empty($type)) {
			$type = 'info';
		}
		if(empty($ext)) {
			$ext = 'log';
		}
		if(empty($key)) {
			$key = '未输入标识';
		}
		if(empty($val)) {
			//$val = '未输入标识内容';
		}
		
		$modulepath = MODULE_ROOT;
		$modulename = null;
		
		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}

		file_put_contents('../addons/'.$modulename.'/'. $type . '.' . $ext,  date("Y-m-d H:i:s" ,strtotime('now')) . '  ' . $key . ' : ' . $_W['fans']['openid'] .' : ' . PHP_EOL . $val . PHP_EOL  , FILE_APPEND);
	}
}

////////////////////////////////////////////////  jysoft 日志 ////////////////////////////////////////////////  end


////////////////////////////////////////////////  jysoft http方法 ////////////////////////////////////////////  start
if(!function_exists('httpGet')) {
	function httpGet($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$temp = curl_exec($ch);
		curl_close($ch);
		return $temp;
	}
}

if(!function_exists('httpPost')) {
	function httpPost($url, $data) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$temp = curl_exec($ch);
		curl_close($ch);
		return $temp;
	}
}
////////////////////////////////////////////////  jysoft http方法 ////////////////////////////////////////////  end


////////////////////////////////////////////////  jysoft 其他 ////////////////////////////////////////////  start
if(!function_exists('get_module_name')) {
	function get_module_name() {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$modulepath = MODULE_ROOT;
		$modulename = null;
		if(!empty($modulepath)) {
			$len = strlen($modulepath);
			$pos = strpos($modulepath, 'addons/');
			$modulename = substr($modulepath, $pos+7, $len);
		}
		if(empty($modulename)) {
			return 'gzh_' . $weid . '_id';
		} else {
			return $modulename;
		}
	}
}
////////////////////////////////////////////////  jysoft 其他 ////////////////////////////////////////////  end