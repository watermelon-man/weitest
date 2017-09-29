<?php
/**
 * 微步互动 Uber
 */
defined('IN_IA') or exit('Access Denied');
include "model.php";

class Uber_FlowerModuleProcessor extends WeModuleProcessor {

    public function respond() {
        global $_W;
        $rid = $this->rule;
        $sql = "SELECT * FROM " . tablename('uber_flower_reply') . " WHERE `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(':rid' => $rid));
        $from_user = $this->message['from'];

        if ($row == false) {
            return $this->respText("活动已取消...");
        }

        if ($row['status'] == 0) {
            return $this->respText("活动暂停，请稍后...");
        }

        if ($row['starttime'] > time()) {
            return $this->respText("活动未开始，请等待...");
        }

        $endtime = $row['endtime'] + 68399;
        if ( $endtime < time()) {
            return $this->respNews(array(
                        'Title' => $row['share_title'],
                        'Description' => $row['share_desc'],
                        'PicUrl' => toImgUrl($row['share_image']),
                        'Url' => $this->createMobileUrl('index', array('rid' => $rid)),
            ));
        } else {
            return $this->respNews(array(
                        'Title' => $row['title'],
                        'Description' => $row['share_desc'],
                        'PicUrl' => toImgUrl($row['share_image']),
                        'Url' => $this->createMobileUrl('index', array('rid' => $rid)),
            ));
        }
    }

}
