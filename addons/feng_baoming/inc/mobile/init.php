<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 16/9/27
 * Time: 上午11:24
 */

defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$settings = pdo_fetchcolumn("SELECT settings FROM".tablename('uni_account_modules')."WHERE uniacid='$uniacid' and module='feng_baoming'");
$set =iunserializer($settings);