<?php
/**
 * 邀请帖
 *
 * 作者:Man.Dan QQ:82089092
 *
 * qq : 15595755
 */
defined('IN_IA') or exit('Access Denied');

class sanshi_invitativeModuleProcessor extends WeModuleProcessor
{

    public $name = 'sanshi_invitativeModuleProcessor';

    public function isNeedInitContext()
    {
        return 0;
    }

    public function respond()
    {
        global $_W;
        $rid = $this->rule;

        if ($rid) {
            $reply = pdo_fetch("SELECT * FROM " . tablename('sanshi_invitative_reply') . " WHERE rid = :rid", array(':rid' => $rid));
            if ($reply) {
                $sql = 'SELECT * FROM ' . tablename('sanshi_invitative_activity') . ' WHERE status=1 AND `weid`=:weid AND `id`=:id';
                $activity = pdo_fetch($sql, array(':weid' => $_W['uniacid'], ':id' => $reply['activityid']));
                $news = array();
                $news[] = array(
                    'title' => $activity['title'],
                    'description' => strip_tags($activity['description']),
                    'picurl' => $_W['attachurl'] . $activity['thumb'],
                    'url' => $this->createMobileUrl('index', array('id' => $activity['id']))
                );
                return $this->respNews($news);
            }
        }
        return null;
    }

    public function isNeedSaveContext()
    {
        return false;
    }
}
