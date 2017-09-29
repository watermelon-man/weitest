<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 16/9/29
 * Time: 下午3:16
 */
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$uniacid = $_W['uniacid'];
$settings = pdo_fetchcolumn("SELECT settings FROM".tablename('uni_account_modules')."WHERE uniacid='$uniacid' and module='qw_carpool'");
$set =iunserializer($settings);