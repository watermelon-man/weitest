<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 16/9/27
 * Time: 上午11:17
 */
defined('IN_IA') or exit('Access Denied');
include  'inc/function/function.php';
class Feng_baomingModuleSite extends WeModuleSite {
    public $settings;
    public function __construct() {
        global $_W;
        $sql = 'SELECT `settings` FROM ' . tablename('uni_account_modules') . ' WHERE `uniacid` = :uniacid AND `module` = :module';
        $settings = pdo_fetchcolumn($sql, array(':uniacid' => $_W['uniacid'], ':module' => 'feng_baoming'));
        $settings = iunserializer($settings);
        $this -> settings = $settings;
    }
}
