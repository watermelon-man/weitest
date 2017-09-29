<?php

/**
 * 中秋吃月饼模块定义
 *
 * @author 
 * @url QQ:263328455
 */
defined('IN_IA') or exit('Access Denied');

function hidetel($phone) {

    $IsWhat = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i', $phone);

    if ($IsWhat == 1) {

        return preg_replace('/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i', '$1****$2', $phone);
    } else {

        return preg_replace('/(1[3587]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
    }
}

class Game_zqModuleSite extends WeModuleSite {

    public $t_fans = "game_zq_fans";
    public $t_num = "game_zq_num";
    public $t_reply = "game_zq_reply";
    public $t_top = "game_zq_top";
    public $mod_name = "game_zq";

    public function __construct() {

        global $_GPC, $_W;

        load()->model('mc');

        $openid = $_W['openid'];

        $id = intval($_GPC['id']);

        $isreg = pdo_fetchcolumn("select headimgurl from " . tablename($this->t_fans) . "  where  rid=$id and openid='$openid'");



        //是否注册过0表示没有



        if (empty($isreg) && !empty($openid)) {

            $userinfo = mc_oauth_userinfo();

            $data = array(
                'rid' => $id,
                'weid' => $_W['uniacid'],
                'openid' => $_W['openid'],
                'nickname' => $userinfo['nickname'],
                'headimgurl' => $userinfo['headimgurl']
            );

            $isreg1 = pdo_fetchcolumn("select count(*) from " . tablename($this->t_fans) . " where  rid=$id and openid='$openid'");

            if ($isreg1 == 0) {

                pdo_insert($this->t_fans, $data);
            }
        }
    }

    public function doMobileindex() {

        global $_GPC, $_W;

        $openid = $_W['openid'];

        $id = intval($_GPC['id']);

        $yobyurl = $_W['siteroot'] . "addons/" . $this->mod_name . "/template/mobile/";

        $modname = $this->mod_name;

        $row = pdo_fetch("SELECT * FROM " . tablename($this->t_reply) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));

        $data = unserialize($row['data']);

        $z = pdo_fetchcolumn("select max(fen) from " . tablename($this->t_top) . "  where openid='$openid' and rid=$id "); //最多分数



        if (!empty($row['share_url'])) {//不填写表示不关注
            $follow = pdo_fetchcolumn('SELECT follow FROM ' . tablename('mc_mapping_fans') . "  where uniacid=" . $_W['uniacid'] . " and openid='$openid' ");

            if (empty($openid)) {
                header('Location:' . $row['share_url']);
            }

            if ($follow != 1) {
                header('Location:' . $row['share_url']);
            }
        }

        if (empty($id)) {
            message('id参数错误了！', '', 'error');
        }



        $now = time();

        if ($row['end_time'] < $now) {

            die('<script type="text/javascript">alert("活动已结束!");location.href="' . $this->createMobileUrl('rank', array('id' => $id)) . '"

</script>');
        }



        $day_num = $row['day_num']; //每天最多次数

        $max_num = $row['max_num']; //总次数



        $today = date('Y-m-d', time());

        $daynum = pdo_fetchcolumn("select day_num from " . tablename($this->t_num) . "  where   openid='$openid'  and  createtime='$today'  and rid = '$id' "); //查询当天次数

        $playnum = pdo_fetchcolumn("select play_num from " . tablename($this->t_fans) . "  where  openid='$openid' and rid=$id "); //记录所玩次数

        if ($playnum < $max_num) {

            if ($daynum > ($day_num - 1)) {

                die('<script>alert("今天' . $day_num . '次机会已经用完了,分享游戏到朋友圈可以多玩一次哦!");location.href="' . $this->createMobileUrl('rank', array('id' => $id)) . '"</script>');
            }
        } else {

            die('<script>alert("总' . $max_num . '次机会已经用完了,不能再参加本次活动了!");location.href="' . $this->createMobileUrl('rank', array('id' => $id)) . '"</script>');
        }





        include $this->template('index');
    }

    public function doMobileshare() {//分享增加一次机会
        global $_GPC, $_W;

        $id = intval($_GPC['id']);

        $openid = $_GPC['openid'];

        $today = date('Y-m-d', time());

        $is_share = pdo_fetch("select id,is_share,day_num from " . tablename($this->t_num) . "  where  rid=$id and openid='$openid' and createtime='$today' "); //今天是否分享

        if ($is_share['is_share'] == 1) {//分享过
            echo 0;
        } else {

            pdo_query("UPDATE " . tablename($this->t_num) . " SET is_share=1,day_num=day_num-1 WHERE id=" . $is_share['id']); //改分享状态

            echo 1;
        }
    }

    public function doMobilesaveindex() {//注册会员
        global $_GPC, $_W;

        $id = intval($_GPC['id']);

        if (empty($_W['openid'])) {

            echo json_encode(array(
                'msg' => 'openid获取出错'
            ));
        }

        $openid = $_W['openid'];

        $true_name = $_GPC['true_name'];

        $phone = $_GPC['phone'];

        $row = pdo_fetch("select id,phone from " . tablename($this->t_fans) . "  where  rid=$id and openid='$openid'");

        if (empty($row['phone'])) {

            $data = array(
                'names' => $true_name,
                'phone' => $phone,
            );

            pdo_update($this->t_fans, $data, array('id' => $row['id']));

            echo json_encode(array(
                'done' => 'true'
            ));
        } else {



            echo json_encode(array(
                'msg' => '八戒,你已注册过了,不再需要注册信息'
            ));
        }
    }

    public function doMobilerule() {//规则
        global $_GPC, $_W;

        $id = intval($_GPC['id']);

        $modname = $this->mod_name;

        $row = pdo_fetch("SELECT * FROM " . tablename($this->t_reply) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));



        include $this->template('rule');
    }

    public function doMobilerank() {//排名
        global $_GPC, $_W;

        $weid = $_W['uniacid'];

        $id = intval($_GPC['id']);

        $openid = $_W['openid'];

        $modname = $this->mod_name;

        $row = pdo_fetch("SELECT * FROM " . tablename($this->t_reply) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));

        $z = pdo_fetchcolumn("select max(fen) from " . tablename($this->t_top) . "  where openid='$openid' and rid=$id "); //最多分数

        $isreg = pdo_fetchcolumn("select phone from " . tablename($this->t_fans) . "  where  openid='$openid' and rid=$id "); //是否注册

        $tp_sql = "select (select count(*)+1 as rank from  " . tablename($this->t_fans) . " as B where B.max_fen>A.max_fen and rid=$id and  openid!='') as rank from " . tablename($this->t_fans) . " as A where rid=$id  and openid='$openid'  order by max_fen desc limit 1";



        $top = pdo_fetchcolumn($tp_sql); //排名        

        $n = pdo_fetchcolumn("select count(*) from " . tablename($this->t_fans) . "  where  rid=$id and  openid!=''"); //参数人数





        $list = pdo_fetchall("select * from " . tablename($this->t_fans) . "  where  rid=$id  and  openid!='' order   by max_fen desc  limit 100 ");



        include $this->template('rank');
    }

    public function doMobileajaxrec() {//ajax 写入积分记录
        global $_GPC, $_W;

        $id = intval($_GPC['id']);

        $openid = $_W['openid'];



        $score = $_GPC['pt_count'];

        $weid = $_W['uniacid'];



        $row = pdo_fetch("select * from " . tablename($this->t_fans) . "  where  rid=$id and openid='$openid'");

        if (!empty($row)) {

            $data = array(
                'rid' => $id,
                'weid' => $weid,
                'openid' => $openid,
                'fen' => $score,
                'createtime' => time(),
            );

            $today = date('Y-m-d', time());

            $nn = pdo_fetchcolumn("select id  from " . tablename($this->t_num) . "  where   openid='$openid'  and  createtime='$today'   ");

            if ($nn > 0) {

                pdo_query("UPDATE " . tablename($this->t_num) . " SET day_num =day_num+1 WHERE id=" . $nn);
            } else {

                $data1 = array(
                    'weid' => $weid,
                    'rid' => $id,
                    'openid' => $openid,
                    'createtime' => $today,
                    'day_num' => 1,
                );

                pdo_insert($this->t_num, $data1);
            }





            pdo_insert($this->t_top, $data);





            $max_fen = pdo_fetchcolumn("select max(fen) from " . tablename($this->t_top) . "  where openid='$openid' and rid=$id "); //最高分



            pdo_query("UPDATE " . tablename($this->t_fans) . " SET play_num = play_num+1,max_fen=$max_fen  WHERE  openid='$openid' and  rid=$id "); //更新最高成绩和所玩次数
        }
    }

    public function doWebList() {

        global $_GPC, $_W;

        $weid = $_W['uniacid'];

        checklogin();

        $id = intval($_GPC['id']);

        if (checksubmit('delete')) {

            pdo_delete($this->t_fans, " id  IN  ('" . implode("','", $_GPC['delete']) . "')");

            message('删除成功！', $this->createWebUrl('list', array('id' => $id, 'page' => $_GPC['page'])));
        }



        $pindex = max(1, intval($_GPC['page']));

        $psize = 50;

        $condition = '';

        if (!empty($_GPC['keyword'])) {

            $condition .= " AND (names LIKE '%" . $_GPC['keyword'] . "%'   or  phone  LIKE '%" . $_GPC['keyword'] . "%')  ";
        }

        $list = pdo_fetchall("SELECT *  FROM " . tablename($this->t_fans) . " WHERE rid=$id and weid =" . $weid . $condition . "  ORDER BY max_fen desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->t_fans) . " WHERE rid=$id and  weid =" . $weid . $condition);



        $pager = pagination($total, $pindex, $psize);



        include $this->template('list');
    }

    public function doWebDaodata() {//导出CSV数据
        global $_W, $_GPC;

        $weid = $_W['weid'];

        $id = intval($_GPC['id']);

        $list = pdo_fetchall("SELECT * FROM " . tablename($this->t_fans) . " where weid='$weid' and  rid=$id    and  openid!=''  order by max_fen desc limit 100"); //查询

        $export_str = "姓名,手机号,排名,所玩次数,最高纪录\n";

        foreach ($list as $k => $v) {

            $kk = $k + 1;



            if (empty($v['names'])) {



                $name = $v['nickname'];
            } else {

                $name = $v['names'];
            }





            $export_str .= $name . ',' . $v['phone'] . ',' . $kk . ',' . $v['play_num'] . ',' . $v['max_fen'] . "\n";
        }

        header("Content-type:text/csv");

        header("Content-Disposition:attachment;filename=中秋吃月饼.csv");

        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');

        header('Expires:0');

        header('Pragma:public');

        $export_str = mb_convert_encoding($export_str, "gb2312", 'auto');

        echo $export_str;
    }

}
