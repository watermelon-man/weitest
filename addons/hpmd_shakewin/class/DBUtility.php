<?php

require_once(IA_ROOT . '/addons/'.SHAKE_WIN_DIR.'/class/CacheLayer.php');

class DBUtility 
{
    public static $table_shakewin_awards = "shakewin_awards";
    public static $table_shakewin_cached = "shakewin_cached";
    public static $table_shakewin_fans = "shakewin_fans";
    public static $table_shakewin_rules = "shakewin_rules";
    public static $table_shakewin_wxinfo = "shakewin_wxinfo";

	//shakewin_rules
    public static function getRuleById($id)
    {
		return pdo_fetch_with_cache(DBUtility::$table_shakewin_rules, "select * from " . tablename(DBUtility::$table_shakewin_rules) . " where id=:id limit 1", array(":id" => $id));
    }
    public static function getReplyWordById($id)
    {
		$sql = 'SELECT replywords FROM ' . tablename(DBUtility::$table_shakewin_rules) . ' WHERE `id`=:id';
		$params = array();
		$params[':id'] = $id;
		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_rules, $sql, $params);
    }
    public static function getRuleByReplyWord($replywords)
    {
		return pdo_fetch_with_cache(DBUtility::$table_shakewin_rules, "select * from " . tablename(DBUtility::$table_shakewin_rules) . " where replywords='" . $replywords . "' limit 1");
    }
    public static function getRuleIdByReplyWord($replywords)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$sql = 'SELECT id FROM ' . tablename(DBUtility::$table_shakewin_rules);
		$sql .= ' WHERE `weid` = :weid and `replywords` = :replywords LIMIT 1';
		$params = array(':weid' => $weid, ':replywords' => $replywords);
		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_rules, $sql, $params);
    }
    public static function getRuleIdByReplyWordExceptId($replywords, $exceptid)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$sql = 'SELECT id FROM ' . tablename(DBUtility::$table_shakewin_rules);
		$sql .= ' WHERE id != :id   and `weid` = :weid and `replywords` = :replywords LIMIT 1';
		$params = array(':id' => $exceptid, ':weid' => $weid, ':replywords' => $replywords);
		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_rules, $sql, $params);
    }
    public static function getRuleListWithPage($subsql, $pindex, $psize, $params)
    {
		global $_GPC, $_W;

		return pdo_fetchall_with_cache('shakewin_rules', "SELECT * FROM " . tablename(DBUtility::$table_shakewin_rules) . " WHERE  weid = '{$_W['uniacid']}'  ".$subsql." ORDER BY online desc, createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
    }
    public static function getTotalRuleNum($subsql)
    {
		global $_GPC, $_W;

		return pdo_fetchcolumn_with_cache('shakewin_rules', 'SELECT COUNT(id) FROM ' . tablename(DBUtility::$table_shakewin_rules) . " WHERE  weid = '{$_W['uniacid']}' " . $subsql);
    }
    public static function getNotOnlineRuleIds()
    {
		global $_GPC, $_W;

		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_rules, "SELECT id FROM " . tablename(DBUtility::$table_shakewin_rules) . " WHERE online != 1 and weid = '{$_W['uniacid']}'  ");
    }
    public static function getOnlineBigscreenRuleListWithPage($subsql, $pindex, $psize, $params)
    {
		global $_GPC, $_W;

		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_rules, "SELECT * FROM " . tablename(DBUtility::$table_shakewin_rules) . " WHERE online = 1 and screentime = 1 and weid = '{$_W['uniacid']}'  ".$subsql." ORDER BY createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
    }
    public static function getTotalOnlineBigscreenRuleNum($subsql)
    {
		global $_GPC, $_W;

		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_rules, 'SELECT COUNT(id) FROM ' . tablename(DBUtility::$table_shakewin_rules) . " WHERE online = 1 and screentime = 1 and weid = '{$_W['uniacid']}'  " . $subsql);
    }
    public static function getAllBigScreenRuleIdList()
    {
		global $_GPC, $_W;

		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_rules, "SELECT id FROM " . tablename(DBUtility::$table_shakewin_rules) . " WHERE online = 1 and screentime = 1 and weid = '{$_W['uniacid']}' ORDER BY createtime DESC ");
    }

	//shakewin_wxinfo
    public static function getWeixUsrInfo()
    {
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		$weid = $_W['uniacid'];

		$sql = 'SELECT nickname,headimg FROM ' . tablename(DBUtility::$table_shakewin_wxinfo);
		$sql .= ' WHERE `weid` = :weid and `openid` = :openid LIMIT 1';
		$params = array(':weid' => $weid, ':openid' => $openid);
		return pdo_fetch_with_cache(DBUtility::$table_shakewin_wxinfo, $sql, $params);

    }
    public static function getWeixUsrInfoByOpenid($openid)
    {
		$sql = 'SELECT * FROM ' . tablename(DBUtility::$table_shakewin_wxinfo) . ' WHERE `openid`=:openid';
		$params = array();
		$params[':openid'] = $openid;
		return pdo_fetch_with_cache(DBUtility::$table_shakewin_wxinfo, $sql, $params);

    }
    public static function getWeixOpenid()
    {
		global $_GPC, $_W;
		$openid = $_W['fans']['openid'];
		$weid = $_W['uniacid'];

		$sql = 'SELECT openid FROM ' . tablename(DBUtility::$table_shakewin_wxinfo);
		$sql .= ' WHERE `weid` = :weid and `openid` = :openid LIMIT 1';
		$params = array(':weid' => $weid, ':openid' => $openid);
		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_wxinfo, $sql, $params);

    }

	//shakewin_awards
    public static function getAwardsIdByName($name)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$sql = 'SELECT id FROM ' . tablename(DBUtility::$table_shakewin_awards);
		$sql .= ' WHERE `weid` = :weid and `name` = :name LIMIT 1';
		$params = array(':weid' => $weid, ':name' => $name);
		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_awards, $sql, $params);
    }
    public static function getAwardsUniIdByName($id, $name)
    {
		global $_GPC, $_W;
		$weid = $_W['uniacid'];

		$sql = 'SELECT id FROM ' . tablename(DBUtility::$table_shakewin_awards);
		$sql .= ' WHERE `id` != :id and `weid` = :weid and `name` = :name LIMIT 1';
		$params = array(':id' => $id, ':weid' => $weid, ':name' => $name);
		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_awards, $sql, $params);
    }
    public static function getAwardsByRuleId($ruleid)
    {
		global $_GPC, $_W;

		$sql = 'SELECT * FROM ' . tablename(DBUtility::$table_shakewin_awards) . ' WHERE `ruleid`=:ruleid';
		$params = array();
		$params[':ruleid'] = $ruleid;
		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_awards, $sql, $params);

    }
    public static function getOnlineAwardsByRuleId($ruleid)
    {
		global $_GPC, $_W;

		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_awards, "SELECT * FROM " . tablename(DBUtility::$table_shakewin_awards) . " WHERE  weid = '{$_W['uniacid']}' and  ruleid = " . $ruleid . " and online=1 ORDER BY probebility   "); //优先抽小奖加一个desc
    }
    public static function getAwardsById($id)
    {
		return pdo_fetch_with_cache(DBUtility::$table_shakewin_awards, "select * from " . tablename(DBUtility::$table_shakewin_awards) . " where id=:id limit 1", array(":id" => $id));
    }
    public static function getAwardsListWithPage($subsql, $pindex, $psize, $params)
    {
		global $_GPC, $_W;

		$sqlall = "SELECT a.*, b.replywords, b.createtime FROM " . tablename(DBUtility::$table_shakewin_awards);
		$sqlall .= " a LEFT JOIN ". tablename(DBUtility::$table_shakewin_rules) ." b on a.ruleid = b.id WHERE a.weid = '{$_W['uniacid']}' " .$subsql;
		$sqlall .= " order by b.createtime desc, a.createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_awards, $sqlall, $params, null, DBUtility::$table_shakewin_rules);
    }
    public static function getTotalAwardsNum($subsql)
    {
		global $_GPC, $_W;

		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_awards, 'SELECT COUNT(id) FROM ' . tablename(DBUtility::$table_shakewin_awards) . " WHERE  weid = '{$_W['uniacid']}'  " . $subsql);
    }
    public static function getGiftSumNumByRuleid($ruleid)
    {
		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_awards, 'SELECT SUM(totalnum) FROM  ' . tablename(DBUtility::$table_shakewin_awards) . " WHERE  ruleid=" . $ruleid);
	}

	//shakewin_fans
    public static function getWinFansById($id)
    {
		$sql = 'SELECT * FROM ' . tablename(DBUtility::$table_shakewin_fans) . ' WHERE `id`=:id';
		$params = array();
		$params[':id'] = $id;
		return pdo_fetch_with_cache(DBUtility::$table_shakewin_fans, $sql, $params);
    }
    public static function getWinFansWithPage($subsql, $pindex, $psize, $params)
    {
		global $_GPC, $_W;

		$sqlall = "SELECT * FROM " . tablename(DBUtility::$table_shakewin_fans);
		$sqlall .= "  WHERE weid = '{$_W['uniacid']}'  " . $subsql;
		$sqlall .= " order by ruleid desc, awardstatus, createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_fans, $sqlall, $params);
    }
    public static function getTotalWinFansNum($subsql)
    {
		global $_GPC, $_W;

		return pdo_fetchcolumn_with_cache(DBUtility::$table_shakewin_fans, "SELECT count(id) FROM " . tablename(DBUtility::$table_shakewin_fans) . " WHERE weid = '{$_W['uniacid']}'  " . $subsql);
    }
    public static function getAllWinFans()
    {
		global $_GPC, $_W;

		$sqlall = "SELECT * FROM " . tablename(DBUtility::$table_shakewin_fans);
		$sqlall .= "  WHERE weid = '{$_W['uniacid']}'  ";
		$sqlall .= " order by ruleid desc, awardstatus, createtime desc ";
		return pdo_fetchall_with_cache(DBUtility::$table_shakewin_fans, $sqlall, $params);
    }

	//common
    public static function  insert($table, $data = array())
    {
        return pdo_insert_with_cache($table, $data);

    }

    public static function  update($table, $data = array(), $params = array())
    {
        return pdo_update_with_cache($table, $data, $params);

    }

    public static function  updateById($table, $data = array(), $id)
    {
        return pdo_update_with_cache($table, $data, array('id' => $id));

    }

    public static function  deleteByid($table, $id)
    {
        return pdo_delete_with_cache($table, array('id' => $id));
    }

    public static function  delete($table, $params = array())
    {
        return pdo_delete_with_cache($table, $params);
    }
	
    public static function  clearActivityCache()
    {
        clear_module_weid_keys();
    }
}