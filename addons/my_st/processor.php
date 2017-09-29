<?php

defined('IN_IA') or exit('Access Denied');

class My_stModuleProcessor extends WeModuleProcessor {

    public function respond() {
        $content = $this->message['content'];
    }

}
