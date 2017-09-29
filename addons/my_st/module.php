<?php

//decode by QQ:270656184 http://www.yunlu99.com/
defined('IN_IA') or exit('Access Denied');

class My_stModule extends WeModule {

    public function fieldsFormDisplay($rid = 0) {
        
    }

    public function fieldsFormValidate($rid = 0) {
        return '';
    }

    public function fieldsFormSubmit($rid) {
        
    }

    public function ruleDeleted($rid) {
        
    }

    public function settingsDisplay($settings) {
        global $_W, $_GPC;
        if (checksubmit()) {
            if (checksubmit()) {
                $cfg = array('template' => $_GPC['template'], 'title' => $_GPC['title'], 'logo' => $_GPC['logo'],);
                $this->saveSettings($cfg);
                message('保存成功', 'refresh');
            }
        } include $this->template('setting');
    }

}
