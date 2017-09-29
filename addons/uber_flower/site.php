<?php
/**
 * 微步互动 Uber qq1476708765
 */
defined('IN_IA') or exit('Access Denied');
require_once "define.php";
require_once "model.php";

class Uber_FlowerModuleSite extends WeModuleSite
{
    public $table_reply = 'uber_flower_reply';
    public $table_fans = 'uber_flower_fans';
    public $table_share = 'uber_flower_share';
	public $table_friend = 'uber_flower_friend';
	public $table_award = 'uber_flower_award';
	public $table_merchant = 'uber_flower_merchant';

    function __construct()
    {
        global $_W, $_GPC;
		$openid = $this->getOpenid();		
		$rid = intval($_GPC['rid']);
		$currenttime = $this->getMktime();
		if(!empty($openid))$fansone = $this->getUsers($rid,$openid);
		if(!empty($fansone)){
			if ($fansone['createtime'] <= $currenttime) {
				pdo_update($this->table_fans, array('todaynum' => 0), array('id'=>$fansone['id']));
			}
		}
    }

    public function doMobileIndex()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $openid = $this->getOpenid();
        $rid = intval($_GPC['rid']);
        if (empty($rid)) {
            message('抱歉，参数错误！', '', 'error');
        }

        $reply = $this->getReplyData($rid);
        if ($reply == false) {
            message('抱歉，活动不存在！', $this->createMobileUrl('rank', array('rid' => $rid)), 'error');
        } else {
            if ($reply['starttime'] > TIMESTAMP) {
                message('活动未开始，请等待...', $this->createMobileUrl('rank', array('rid' => $rid)), 'error');
            }
            if ($reply['endtime'] < TIMESTAMP) {
                message('抱歉，活动已经结束，下次再来吧！', $this->createMobileUrl('rank', array('rid' => $rid)), 'error');
            }
            if ($reply['status'] == 0) {
                message('活动暂停，请稍后...', $this->createMobileUrl('rank', array('rid' => $rid)), 'error');
            }
        }

        $followurl = $reply['followurl'];
        if (empty($openid)) {
            if (!empty($followurl)) {
                header("location:{$followurl}");exit;
            } else {
				$this->isWeiXin();
            }
        }
		$follow = $this->getFollowed($openid);
        if ($follow == 0) {
            if ($reply['isfollow'] == 1) {
                if (!empty($followurl)) {
                    header("location:{$followurl}");exit;
                } else {
					
                }
            }
        }
        $bgurl = !empty($reply['bgurl']) ? tomedia($reply['bgurl']) : "../addons/uber_flower/template/mobile/".$this->module['config']['style']."/images/front/cover.jpg";

        $gametime = $reply['gametime'];
        $gamelevel = $reply['gamelevel'];
		pdo_update($this->table_reply, array('viewnum' => $reply['viewnum'] + 1), array('id' => $reply['id']));
        $this->addUsers($rid,$openid);
		$fans = $this->getUsers($rid,$openid);
		$sharedata = $this->getShareData($rid);
        extract($sharedata);
        include $this->template('index');
    }
	
	public function doMobileStartGame()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $this->getOpenid();
        $rid = intval($_GPC['rid']);
        if (empty($rid)) {
			$data = array('status'=>0);
            $this->showMsg('抱歉，参数错误！',0,$data);
        }
        if (empty($openid)) {
			$data = array('status'=>0);
            $this->showMsg('会话已过期，请从微信端发送关键字重新进入!',0,$data);
        }

        $reply = $this->getReplyData($rid);
        $daysharetip = '';
        if ($reply['daysharenum'] > 0) {
            $daysharetip = '您还可以通过邀请朋友参加来增加游戏次数！';
        }

        if ($reply == false) {
			$data = array('status'=>0);
            $this->showMsg('抱歉，活动不存在！',0,$data);
        } else {
            if ($reply['starttime'] > TIMESTAMP) {
				$data = array('status'=>0);
                $this->showMsg('活动未开始，请等待...',0,$data);
            }
            if ($reply['endtime'] < TIMESTAMP) {
				$data = array('status'=>0);
                $this->showMsg('抱歉，活动已经结束，下次再来吧！',0,$data);
            }
            if ($reply['status'] == 0) {
				$data = array('status'=>0);
                $this->showMsg('活动暂停，请稍后...',0,$data);
            }
        }

        $playtimes = intval($reply['playtimes']);//总游戏次数
        $everydaytimes = intval($reply['everydaytimes']);//每天游戏次数

        $fans = $this->getUsers($rid,$openid);

        if (empty($fans)) {
			$data = array('status'=>0);
            $this->showMsg('粉丝不存在！',0,$data);
        }

        $usertotalnum = intval($fans['totalnum']);//用户总游戏次数
        $shareawardnum = intval($fans['shareawardnum']);//分享可游戏次数
        $todaynum = intval($fans['todaynum']);//用户今日游戏次数
        $nowtime = $this->getMktime();
        if ($fans['createtime'] <= $nowtime) {
            $todaynum = 0;//今日次数设置为0
        }

        if ($playtimes != 0 && $usertotalnum >= $playtimes && $shareawardnum <= 0) { //总次数有限制  用户已抽奖次数>=总抽奖次数
			$data = array('status'=>0);
            $this->showMsg('抱歉，您游戏次数用完了，请下次再来吧！' . $daysharetip,0,$data);
        }

        if ($everydaytimes != 0 && $todaynum >= $everydaytimes && $shareawardnum <= 0) { //今天次数有限制
			$data = array('status'=>0);
            $this->showMsg('抱歉，您今日游戏次数用完了，请下次再来吧！！' . $daysharetip,0,$data);
        }

        if ($playtimes == 0 || $playtimes > $usertotalnum) {
            if ($everydaytimes == 0 || $everydaytimes > $todaynum) {
				$data = array('status'=>1,'gametime'=>$reply['gametime']);
                $this->showMsg('success',1,$data);
            }
        }

        if ($shareawardnum > 0) {
			$data = array('status'=>1,'gametime'=>$reply['gametime']);
            $this->showMsg('success', 1,$data);
        } else {
			$data = array('status'=>0);
            $this->showMsg('抱歉，您游戏次数用完了，请下次再来吧！！' . $daysharetip,0,$data);
        }
    }

   public function doMobileSaveUserinfo()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $this->getOpenid();
        $rid = intval($_GPC['rid']);
        $realname = trim($_GPC['realname']);
        $mobile = trim($_GPC['mobile']);
		$address = trim($_GPC['address']);
        $qq = trim($_GPC['qq']);
		$mchid = intval($_GPC['mchid']);

        if (empty($rid)) {
            $this->showMsg('抱歉，参数错误！');
        }
        if (empty($openid)) {
            $this->showMsg('会话已过期，请从微信端发送关键字重新进入!');
        }
		if (empty($realname)||empty($mobile)) {
            $this->showMsg('请输入真实姓名或手机号！');
        }

        $reply = $this->getReplyData($rid);
        if ($reply == false) {
            $this->showMsg('抱歉，活动不存在！');
        }

        $fans = $this->getUsers($rid,$openid);

        if (empty($fans)) {
            $this->showMsg('粉丝不存在！');
        }

        $datafans = array(
            'realname' => $realname,
            'mobile' => $mobile,	
			'status' => 1,		
        );
		if($fans['mchid']==0){$datafans['mchid'] = $mchid;}
		else{$datafans['mchid'] = $fans['mchid'];}
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		$uid>0 && $result = mc_update($uid, array('realname'=>$realname,'mobile'=>$mobile,'address'=>$address,'qq'=>$qq));
        pdo_update($this->table_fans, $datafans, array('id' => $fans['id']));
        $this->showMsg('success', 1);
    }
	
	    public function doMobileRank()
    {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $openid = $this->getOpenid();
		
		$page = !empty($_GPC['page'])?$_GPC['page']:0;
		$pagenum = $page*10;
		

        $rid = intval($_GPC['rid']);
        if (empty($rid)) {
            message('抱歉，参数错误！', '', 'error');
        }

       	$reply = $this->getReplyData($rid);
        $condition = ' credit ';
        if ($reply == false) {
            $this->showMsg('抱歉，活动不存在！');
        } else {
            if ($reply['mode'] == 1) {
                $condition = ' totalcredit ';
            }
        }
		$this->addUsers($rid,$openid);		
		$pindex = max(1, intval($_GPC['currentPage']));
		$psize = $reply['showusernum'];
		if($psize==0)$psize = 20;
		$fanslist = pdo_fetchall("SELECT * FROM " . tablename($this->table_fans) . " WHERE status=1 AND rid=:rid AND credit>0 ORDER BY {$condition} ASC,createtime DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize, array(':rid' => $rid));
		//echo "<pre>";print_r($fanslist);exit;
        $rank = array();
        $myrank = array();
        foreach($fanslist as $key => $value) {
			$merchant = pdo_fetchcolumn("SELECT byname FROM " . tablename($this->table_merchant) . " WHERE id=:mchid ORDER BY id DESC LIMIT 1", array(':mchid' => $value['mchid']));
			if(empty($merchant))$merchant='';
			
            $rank[$key] = array('No' => $key + 1, 'Avatar' => tomedia($value['avatar']), 'NickName' => cutstr($value['nickname'],3,true),'Merchant' => cutstr($merchant,6,true), 'Mobile' => $value['mobile'], 'Point' => $reply['mode'] == 0? $value['credit'] : $value['totalcredit']);
            if ($value['openid'] == $openid) {
                $myrank = array('No' => $key + 1, 'Avatar' => tomedia($value['avatar']),'NickName' => cutstr($value['nickname'],3,true), 'Merchant' => cutstr($merchant,6,true), 'Mobile' => $value['mobile'], 'Point' => $reply['mode'] == 0? $value['credit'] : $value['totalcredit']);
            }
        }
		$myrank=$this->getArraySort($rid,$openid,$reply['mode']);
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {

			//分享信息
			$sharedata = $this->getShareData($rid);
			extract($sharedata);
			include $this->template('rank');
		} else if($operation == 'scorelist'){
			
			$result = array(
				'list' => $rank,
				'startpage' => $pindex++,
			);
			echo json_encode($result);
			exit;
		}
    }

    public function doMobileSaveScore()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $this->getOpenid();
        $rid = intval($_GPC['rid']);
        $point = floatval($_GPC['point']);
		
        if (empty($rid)) {
            $this->showMsg('抱歉，参数错误！');
        }
		$_point = $point/100000;
		
        if ($_point <= 0) {
            $this->showMsg('您用力过猛啦！');
        }
		
        if (empty($openid)) {
            $this->showMsg('会话已过期，请从微信端发送关键字重新进入!');
        }

        $reply = $this->getReplyData($rid);

        if ($reply == false) {
            $this->showMsg('抱歉，活动不存在！');
        } else {
            if ($reply['starttime'] > TIMESTAMP) {
				$data['status']=-4;
                $this->showMsg('活动未开始，请等待...',0,$data);
            }
            if ($reply['endtime'] < TIMESTAMP) {
				$data['status']=-3;
                $this->showMsg('抱歉，活动已经结束，下次再来吧！',$data);
            }
            if ($reply['status'] == 0) {
				$data['status']=-5;
                $this->showMsg('活动暂停，请稍后...',$data);
            }
        }

        $playtimes = intval($reply['playtimes']);//总游戏次数
        $everydaytimes = intval($reply['everydaytimes']);//每天游戏次数

        $fans = $this->getUsers($rid,$openid);
		
        if (empty($fans)) {
            $this->showMsg('粉丝不存在！');
        }
		if($reply['isprofile']==1){
			if(!empty($reply['profile']))$profile = iunserializer($reply['profile']);
			if (empty($fans['realname'])||empty($fans['mobile'])||$fans['status']==0) {
				$data['status']=-2;
				$this->showMsg('请输入姓名或手机号！',0,$data);
			}
			if ($profile['address']==1&&empty($fans['address'])) {
				$data['status']=-2;
				$this->showMsg('请输入邮寄地址！',0,$data);
			}
			if ($profile['qq']==1&&empty($fans['qq'])) {
				$data['status']=-2;
				$this->showMsg('请输入QQ号！',0,$data);
			}
		}
        $gamecredit = $fans['credit'];
		if (0 == $gamecredit) {
            $gamecredit = $point;
        }
        if ($point < $gamecredit) {
            $gamecredit = $point;
        }
        $totalgamecredit = $fans['totalcredit'] + $point;

        $usertotalnum = intval($fans['totalnum']);//用户总游戏次数
        $shareawardnum = intval($fans['shareawardnum']);//分享可游戏次数
        $todaynum = intval($fans['todaynum']);//用户今日游戏次数		
		
        $nowtime = $this->getMktime();		
        if ($fans['createtime'] <= $nowtime) {
            $todaynum = 0;//今日次数设置为0
        }

        if ($playtimes != 0 && $usertotalnum >= $playtimes && $shareawardnum <= 0) { //总次数有限制  用户已抽奖次数>=总抽奖次数
			$data['status']=-6;
            $this->showMsg('抱歉，您游戏次数用完了，请下次再来吧！',0,$data);
        }

        if ($everydaytimes != 0 && $todaynum >= $everydaytimes && $shareawardnum <= 0) { //今天次数有限制
			$data['status']=-6;
            $this->showMsg('抱歉，您今日游戏次数用完了，请下次再来吧！',0,$data);
        }

        if ($playtimes == 0 || $playtimes > $usertotalnum) {
            if ($everydaytimes == 0 || $everydaytimes > $todaynum) {
                $datafans = array(
                    'credit' => $gamecredit,
                    'totalcredit' => $totalgamecredit,
                    'totalnum' => $fans['totalnum'] + 1,
                    'todaynum' => $todaynum + 1,
                    'createtime' => TIMESTAMP,
                );
                pdo_update($this->table_fans, $datafans, array('id' => $fans['id']));
                $datarecord = array(
                    'uniacid' => $uniacid,
                    'rid' => $rid,
                    'openid' => $openid,
                    'credit' => $point,
                    'pubdate' => TIMESTAMP
                );
                pdo_insert($this->table_award, $datarecord);
                $this->showMsg('success', 1,array('status'=>1));
            }
        }

        if($shareawardnum > 0) {
            $endsharenum = $shareawardnum - 1;
            pdo_update($this->table_fans, array('shareawardnum' => $endsharenum,'credit' => $gamecredit,
                'totalcredit' => $totalgamecredit), array('id' => $fans['id']));
            $datarecord = array(
                'uniacid' => $uniacid,
                'rid' => $rid,
                'openid' => $openid,
                'credit' => $point,
                'pubdate' => TIMESTAMP
            );
            pdo_insert($this->table_award, $datarecord);
            $this->showMsg('success', 2,array('status'=>1));
        } else {
            $this->showMsg('抱歉，您游戏次数用完了，请下次再来吧！',0,array('status'=>-6));
        }
    }


    public function doMobileShare()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $openid = $this->getOpenid();
        $rid = intval($_GPC['rid']);

        $fans = $this->getUsers($rid,$openid);
        if (!empty($fans)) {
            $reply = $this->getReplyData($rid);
            if (empty($reply)) {
                $this->showMsg('感谢您的分享!', 1);
            }

            if ($reply['daysharenum'] <= 0 || $reply['shareawardnum'] <= 0) {
                $this->showMsg('感谢您的分享!.', 1);
            }

            $daysharenum = $reply['daysharenum'];
            $awardnum = $reply['shareawardnum'];
            $data = array(
                'todaysharenum' => $fans['todaysharenum'] + 1,
                'sharenum' => $fans['sharenum'] + $awardnum,
                'shareawardnum' => $fans['shareawardnum'] + $awardnum,
                'lastsharetime' => TIMESTAMP
            );

            $nowtime = $this->getMktime();
            if ($fans['lastsharetime'] <= $nowtime) {
                $data['todaysharenum'] = 1;
            }

            if ($data['todaysharenum'] > $daysharenum) {
                $this->showMsg('感谢您的分享!!', 1);
            }

            if ($reply['shareawardnum'] > 0) {
                pdo_update($this->table_fans, $data, array('id' => $fans['id']));
                $this->showMsg('感谢您的分享，您获得' . $awardnum . '次游戏机会', 1);
            } else {
                $this->showMsg('感谢您的分享!!!', 1);
            }
        } else {
            $this->showMsg('粉丝不存在！');
        }
    }

    public function showMsg($msg, $success = 0,$data=array()) {
        $result = array(
            'msg' => $msg,
            'success' => $success,
			'data'=>$data,
        );
        echo json_encode($result);
        exit;
    }

    public function doWebManage() {
        global $_GPC, $_W;
        load()->model('reply');
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $sql = "uniacid = :uniacid AND `module` = :module";
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        $params[':module'] = 'uber_flower';

        if (isset($_GPC['keywords'])) {
            $sql .= ' AND `name` LIKE :keywords';
            $params[':keywords'] = "%{$_GPC['keywords']}%";
        }

        $list = reply_search($sql, $params, $pindex, $psize, $total);
        $pager = pagination($total, $pindex, $psize);
		//echo "<pre>";print_r($list);exit;
        if (!empty($list)) {
            foreach ($list as &$item) {
                $condition = "`rid`={$item['id']}";
                $item['keywords'] = reply_keywords_search($condition);
                $reply = pdo_fetch("SELECT * FROM " . tablename($this->table_reply) . " WHERE rid = :rid ", array(':rid' => $item['id']));
                $item['title'] = $reply['title'];
				$item['viewnum'] = $reply['viewnum'];
                $item['starttime'] = date('Y-m-d H:i', $reply['starttime']);
                $endtime = $reply['endtime'];
                $item['endtime'] = date('Y-m-d H:i', $endtime);
                $nowtime = time();
                if ($reply['starttime'] > $nowtime) {
                    $item['show'] = '<span class="label label-warning">未开始</span>';
                } elseif ($endtime < $nowtime) {
                    $item['show'] = '<span class="label label-default">已结束</span>';
                } else {
                    if ($reply['status'] == 1) {
                        $item['show'] = '<span class="label label-success">已开始</span>';
                    } else {
                        $item['show'] = '<span class="label label-default">已暂停</span>';
                    }
                }
                $item['status'] = $reply['status'];
                $item['uniacid'] = $reply['uniacid'];
            }
			unset($item);
        }
        include $this->template('manage');
    }
	
	public function doWebMerchant()
    {
        global $_GPC, $_W;
        checklogin();
        load()->func('tpl');
        $action = 'merchant';
        $title = '商家管理';
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update($this->table_merchant, array('displayorder' => $displayorder), array('id' => $id));
                }
                message('更新排序成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
            }

            $children = array();
            $merchant = pdo_fetchall("SELECT * FROM " . tablename($this->table_merchant) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid DESC, displayorder DESC,id DESC");
            foreach ($merchant as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($merchant[$index]);
                }
            }
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename($this->table_merchant) . " WHERE id = '$id'");
            } else {
                $item = array(
                    'displayorder' => 0,
                );
            }

            if (!empty($item)) {

            }

            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename($this->table_merchant) . " WHERE id = '$parentid' ORDER BY displayorder DESC,id DESC");
                if (empty($parent)) {
                    message('抱歉，上级商家不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入商家名称！');
                }

                $data = array(
                    'uniacid' => $_W['uniacid'],
                    'name' => $_GPC['catename'],
					'byname' => $_GPC['byname'],
					'description' => $_GPC['description'],
					'banner' => $_GPC['banner'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'parentid' => intval($parentid),
					'createtime' => TIMESTAMP,
                );

                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update($this->table_merchant, $data, array('id' => $id));
                } else {
                    pdo_insert($this->table_merchant, $data);
                    $id = pdo_insertid();
                }
                message('更新商家成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
            }
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT id, parentid FROM " . tablename($this->table_merchant) . " WHERE id = '$id'");
            if (empty($item)) {
                message('抱歉，商家不存在或是已经被删除！', $this->createWebUrl('merchant', array('op' => 'display')), 'error');
            }
            pdo_delete($this->table_merchant, array('id' => $id, 'parentid' => $id), 'OR');
            message('商家删除成功！', $this->createWebUrl('merchant', array('op' => 'display')), 'success');
        }
        include $this->template('merchant');
    }
	
	public function  doWebAddprize(){
			global $_W,$_GPC;
			$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
			if($operation == 'display'){
				
			} elseif ($operation == 'addtype') {
				$addt = $_GPC['addt'];
				$key   = $_GPC['kw'];
				if ($addt == 'type') {
					$newtype =1;
					include $this->template('tp_prize');
				}
			}


    }

    public function doWebDelete() {
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $rule = pdo_fetch("SELECT id, module FROM " . tablename('rule') . " WHERE id = :id and uniacid=:uniacid", array(':id' => $rid, ':uniacid' => $_W['uniacid']));
        if (empty($rule)) {
            message('抱歉，要修改的规则不存在或是已经被删除！');
        }
        if (pdo_delete('rule', array('id' => $rid))) {
            pdo_delete('rule_keyword', array('rid' => $rid));
            //删除统计相关数据
            pdo_delete('stat_rule', array('rid' => $rid));
            pdo_delete('stat_keyword', array('rid' => $rid));
			
			pdo_delete($this->table_reply, array('rid' => $rid));
			pdo_delete($this->table_fans, array('rid' => $rid));
			pdo_delete($this->table_award, array('rid' => $rid));
			pdo_delete($this->table_friend, array('rid' => $rid));
			pdo_delete($this->table_share, array('rid' => $rid));

        }
        message('规则操作成功！', $this->createWebUrl('manage', array('op' => 'display')), 'success');
    }

    public function doWebDeleteAll() {
        global $_GPC, $_W;

        foreach ($_GPC['idArr'] as $k => $rid) {
            $rid = intval($rid);
            if ($rid == 0)
                continue;
            $rule = pdo_fetch("SELECT id, module FROM " . tablename('rule') . " WHERE id = :id and uniacid=:uniacid", array(':id' => $rid, ':uniacid' => $_W['uniacid']));
            if (empty($rule)) {
                $this->message('抱歉，要修改的规则不存在或是已经被删除！');
            }
            if (pdo_delete('rule', array('id' => $rid))) {
                pdo_delete('rule_keyword', array('rid' => $rid));
                //删除统计相关数据
                pdo_delete('stat_rule', array('rid' => $rid));
                pdo_delete('stat_keyword', array('rid' => $rid));
                //调用模块中的删除
                $module = WeUtility::createModule($rule['module']);
                if (method_exists($module, 'ruleDeleted')) {
                    $module->ruleDeleted($rid);
                }
            }
        }
        $this->message('规则操作成功！', '', 0);
    }

    public function doWebFanslist() {
        global $_GPC, $_W;
        load()->func('tpl');
        $uniacid = $_W['uniacid'];
        $rid = intval($_GPC['rid']);

        if (empty($rid)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $url = $this->createWebUrl('fanslist', array('op' => 'display', 'rid' => $rid));

        if ($operation == 'display') {

            $reply = $this->getReplyData($rid);
            $condition = ' credit ';
            if ($reply == false) {
                $this->showMsg('抱歉，活动不存在！');
            } else {
                if ($reply['mode'] == 1) {
                    $condition = ' totalcredit ';
                }
            }
            $pindex = max(1, intval($_GPC['page']));
            $psize = 15;
			///////////////////////////////////////
			if (!empty($_GPC['nickname'])) {
				$nickname = trim($_GPC['nickname']);	
            	$where.=" and nickname LIKE '%{$nickname}%'";
			}
			//导出标题以及参数设置
			if(isset($_GPC['exchange'])&&$_GPC['exchange']==0){
				$statustitle = '未兑换';
				$where.=' and exchange=0 ';
			}
			else if($_GPC['exchange']==1){
				 $statustitle = '已兑换';
				$where.=' and exchange=1 ';
			}
			////////////////////////////////////////////////////////////
            if ($_GPC['out_put'] == 'output') {
                $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_fans) . " WHERE rid = :rid {$where} ORDER BY {$condition} ASC,createtime DESC ", array(':rid' => $rid));

                $i = 0;
                foreach ($list as $key => $value) {
                    $arr[$i]['rank'] = $key + 1;
                    $arr[$i]['realname'] = $value['realname'];
                    $arr[$i]['mobile'] = $value['mobile'];
                    $arr[$i]['openid'] = $value['openid'];
                    $arr[$i]['nickname'] = $value['nickname'];
                    $arr[$i]['credit'] = $value['credit'];
                    $arr[$i]['totalcredit'] = $value['totalcredit'];
					$arr[$i]['exchange'] = $value['exchange']? '是':'否';
                    $arr[$i]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
                    $i++;
                }
                $this->exportexcel($arr, array('排名', '姓名', '联系电话','微信ID', '昵称', '单次距离', '累积距离','兑换', '参与时间'), time());
                exit();
            }

            $start = ($pindex - 1) * $psize;
            $limit = "";
            $limit .= " LIMIT {$start},{$psize}";
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_fans) . " WHERE rid = :rid {$where} ORDER BY  status DESC,{$condition} ASC,createtime DESC " . $limit, array(':rid' => $rid));

            $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_fans) . " WHERE rid = :rid {$where} ", array(':rid' => $rid));
            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'post') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT * FROM " . tablename($this->table_fans) . " WHERE id = :id", array(':id' => $id));

            if (checksubmit()) {
                $data = array(
                    'uniacid' => $uniacid,
                    'rid' => $rid,
                    'nickname' => trim($_GPC['nickname']),
                    'realname' => trim($_GPC['realname']),
					'address' => trim($_GPC['address']),
					'qq' => trim($_GPC['qq']),
                    'mobile' => trim($_GPC['mobile']),
					'mchid' => intval($_GPC['mchid']),
                    'credit' => floatval($_GPC['credit']),
                    'totalcredit' => floatval($_GPC['totalcredit']),
                    'status' => intval($_GPC['status']),
					'totalnum' => intval($_GPC['totalnum']),
					'todaynum' => intval($_GPC['todaynum']),
					'exchange' => intval($_GPC['exchange']),
					'pubdate' => TIMESTAMP
                );
                if (!empty($_GPC['avatar'])) {
                    $data['avatar'] = $_GPC['avatar'];
                }

                if (empty($item)) {
                    pdo_insert($this->table_fans, $data);
                } else {
                    unset($data['pubdate']);
                    pdo_update($this->table_fans, $data, array('id' => $id, 'uniacid' => $uniacid));
                }
                message('操作成功！', $url, 'success');
            }
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT id FROM " . tablename($this->table_fans) . " WHERE id = :id AND uniacid=:uniacid", array(':id' => $id, ':uniacid' => $uniacid));
            if (empty($item)) {
                message('抱歉，不存在或是已经被删除！', $url, 'error');
            }
            pdo_delete($this->table_fans, array('id' => $id, 'uniacid' => $uniacid));
            message('删除成功！', $url, 'success');
        }
        include $this->template('fanslist');
    }


    public function doWebAwardlist() {
        global $_GPC, $_W;
        $uniacid = $_W['uniacid'];
        $rid = intval($_GPC['rid']);
        $fansid = intval($_GPC['fansid']);

        if (empty($rid)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $url = $this->createWebUrl('awardlist', array('op' => 'display', 'rid' => $rid, 'fansid' => $fansid));

        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 12;
            $start = ($pindex - 1) * $psize;
            $limit = "";
            $limit .= " LIMIT {$start},{$psize}";
            $list = pdo_fetchall("SELECT * FROM " . tablename($this->table_award) . " WHERE rid = :rid AND fansid=:fansid ORDER BY id DESC " . $limit, array(':rid' => $rid, ':fansid' => $fansid));

            $total = pdo_fetchcolumn("SELECT count(1) FROM " . tablename($this->table_award) . " WHERE rid = :rid AND fansid=:fansid ", array(':rid' => $rid, ':fansid' => $fansid));
            $pager = pagination($total, $pindex, $psize);
        } else if ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $item = pdo_fetch("SELECT id FROM " . tablename($this->table_award) . " WHERE id = :id", array(':id' => $id));
            if (empty($item)) {
                message('抱歉，不存在或是已经被删除！', $url, 'error');
            }
            pdo_delete($this->table_award, array('id' => $id, 'uniacid' => $uniacid));
            message('删除成功！', $url, 'success');
        }

        include $this->template('awardlist');
    }

    public function doWebSetshow() {
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $isstatus = intval($_GPC['status']);

        if (empty($rid)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }
        $temp = pdo_update($this->table_reply, array('status' => $isstatus), array('rid' => $rid));
        message('状态设置成功！', referer(), 'success');
    }
///////////////////////////////////一条神奇的分割线//////////////////////////////////一条神奇的分割线//////////////////////////////一条神奇的分割线/////

	public function getShareData($rid) {
		global $_W,$_GPC;
		$reply = pdo_fetch('SELECT share_title,share_image,share_desc,share_url FROM ' . tablename($this->table_reply) . " WHERE rid='{$rid}' AND uniacid='{$_W[uniacid]}'");
		$share_url = empty($reply['share_url']) ? $_W['siteroot'] . 'app/' . $this->createMobileUrl('index', array('rid' => $rid), true) : $reply['share_url'];
        $share_title = empty($reply['share_title']) ? $reply['title'] : $reply['share_title'];
        $share_desc = empty($reply['share_desc']) ? $reply['title'] : str_replace("\r\n", " ", $reply['share_desc']);
        $share_image = tomedia($reply['share_image']);
		$data['share_url'] = $share_url;
		$data['share_title'] = $share_title;
		$data['share_desc'] = $share_desc;
		$data['share_image'] = $share_image;
		//echo "<pre>";print_r($data);exit;
		return $data;
	}

	public function getFollowed($openid) {
		global $_W;
		if(!$openid)$openid=$this->getOpenid();
		if(!$openid){
			$status=0;	
		}else{
			$follow = pdo_fetch('select follow from '.tablename('mc_mapping_fans').' where openid=:openid LIMIT 1',array(':openid'=>$openid));
			$status=1;
			if($follow['follow'] <> 1){
				$status=0;
			}
		}
		
		return $status;
	} 
	/**
     * 概率计算
     *
     * @param unknown $proArr            
     * @return Ambigous <string, unknown>
     */
    public function getRand($proArr){
        $result = '';
        // 概率数组的总概率精度
        $proSum = array_sum($proArr);
        // 概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum); // 抽取随机数
            if ($randNum <= $proCur) {
                $result = $key; // 得出结果
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }
	public function getWincode($rid=0,$openid='',$iswin=true){
		global $_W,$_GPC;
		//$wincode = date('dHis').sprintf('%02d', rand(0,99));
		$wincode = "000000000";
		if(!$iswin){$redeemcode = pdo_fetchcolumn('SELECT redeemcode FROM ' . tablename($this->table_fans) . " WHERE rid='{$rid}' AND uniacid='{$_W[uniacid]}' AND openid='{$openid}'");
		return $redeemcode;
		}
		$_wincode = pdo_fetchcolumn('SELECT wincode FROM ' . tablename($this->table_award) . " WHERE rid='{$rid}' AND uniacid='{$_W[uniacid]}' AND openid='{$openid}'");
		if(!empty($_wincode))$wincode = $_wincode;
		return (string)$wincode;
		}



    public function getOpenid() {
		global $_GPC,$_W;
		if(!empty($_SESSION['openid']))return $_SESSION['openid'];
		$openid = $_W['openid'];
		if($_W['account']['level']<4){
			return $this->getOauthOpenid();
		}
		return $openid;
	}
	public function getOauthOpenid(){
		global $_W;
		if(!empty($_SESSION['oauth_openid']))return $_SESSION['oauth_openid'];
		load()->model('mc');
		$userinfo = mc_oauth_userinfo();
		$oauth_openid = $userinfo['openid'];
		return $oauth_openid;	
	}
	public function getRedeemcode()
	{
		$redeemcode = date('His').mt_rand(100, 999);
		return $redeemcode;
	}
	public function getReplyData($rid=0){
		global $_W;
		if($rid==0)return;
		$sql = "select * from " . tablename($this->table_reply) . " where rid = :rid and uniacid=:uniacid order by `id` desc";
		$reply = pdo_fetch($sql, array(':rid' => $rid,':uniacid' => $_W['uniacid']));
		return $reply;
	}
	public function addUsers($rid,$openid){
		$fans_exist = $this->fansExist($rid,$openid);
			if(!$fans_exist){
				$this->fansInsert($rid,$openid);
			} else {
				$this->fansUpdate($rid,$openid);
			}
	}
	public function getUsers($rid,$openid) {
		global $_GPC, $_W;
		$return = pdo_fetch("select * from " . tablename($this->table_fans) . " WHERE uniacid= :uniacid AND rid= :rid AND openid= :openid", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':openid' => $openid));
		return $return;
    }		
	public function fansExist($rid,$openid) {
			global $_GPC, $_W;
			if($_W['account']['level']<4){
				$oauthopenid = $this->getOauthOpenid();
				$return = pdo_fetchcolumn("select COUNT(*) from " . tablename($this->table_fans) . " WHERE uniacid= :uniacid AND rid= :rid AND (openid='{$oauthopenid}' OR oauthopenid='{$oauthopenid}')", array(':uniacid' => $_W['uniacid'],':rid' => $rid));
			}else{
				$return = pdo_fetchcolumn("select COUNT(*) from " . tablename($this->table_fans) . " WHERE uniacid= :uniacid AND rid= :rid AND openid= :openid", array(':uniacid' => $_W['uniacid'],':rid' => $rid,':openid' => $openid));
			}
			return $return;
		}	

	public function fansInsert($rid,$openid){
			global $_GPC, $_W;
			load()->model('mc');
			$fans_data = mc_fetch($_W['member']['uid'], array('avatar','nickname','mobile','realname','address','email','qq'));
			if(!$fans_data['nickname']||!$fans_data['avatar']){
				$userinfo = mc_oauth_userinfo();
				$fans_data['nickname'] = $userinfo['nickname'];
				$fans_data['avatar'] = $userinfo['headimgurl'];
			}
			$fans_data['rid'] = $rid;
			$fans_data['uniacid'] = $_W['uniacid'];
			$fans_data['openid'] = $openid;
			$fans_data['uid'] = $_W['member']['uid'];
			$fans_data['redeemcode'] = $this->getRedeemcode();
			$fans_data['ip'] = CLIENT_IP;
			$fans_data['createtime'] = TIMESTAMP;
			if($_W['account']['level']<4){
				$fans_data['oauthopenid'] = $this->getOauthOpenid();
				pdo_insert($this->table_fans, $fans_data);
			}else{
				pdo_insert($this->table_fans, $fans_data);		
			}
		
	}

    public function fansUpdate($rid,$openid){
			global $_GPC, $_W;
			load()->model('mc');
			$fans_data = mc_fetch($_W['member']['uid'], array('avatar','nickname','mobile','realname','address','email','qq'));
			if(!$fans_data['nickname']||!$fans_data['avatar']){
				$userinfo = mc_oauth_userinfo();
				$fans_data['nickname'] = $userinfo['nickname'];
				$fans_data['avatar'] = $userinfo['headimgurl'];
			}
			$fans_data['uid'] = $_W['member']['uid'];
			$fans_data['pubdate'] = TIMESTAMP;
			$fans_data['ip'] = CLIENT_IP;
			if($_W['account']['level']<4){
				$oauthopenid = $this->getOauthOpenid();
				$oauthsql ='';
				if(!empty($fans_data['nickname']))$oauthsql .= ",nickname='{$fans[nickname]}'";
				if(!empty($fans_data['avatar']))$oauthsql .= ",avatar='{$fans[avatar]}'";
				pdo_query("UPDATE " . tablename($this->table_fans) . "SET  pubdate='{$fans[pubdate]}',openid='{$openid}',ip='{$fans[ip]} {$oauthsql}' WHERE uniacid='{$_W[uniacid]}' AND rid= '{$rid}' AND (openid='{$oauthopenid}' OR oauthopenid='{$oauthopenid}')");
			}else{
				pdo_update($this->table_fans, $fans_data,array('rid'=>$rid,'uniacid'=>$_W['uniacid'],'openid'=>$openid));
			}
					
	}
	public function getArraySort($rid,$openid,$mode=0){
		global $_W;
		$myrank=array();
		$credit_arr = pdo_fetchall("select credit as creditsort,openid from " . tablename('uber_flower_fans') . " WHERE status=1 AND uniacid= :uniacid AND rid= :rid AND credit>0 ORDER BY credit ASC,createtime DESC", array(':uniacid' => $_W['uniacid'],':rid' => $rid));	
		if($mode==1)$credit_arr = pdo_fetchall("select totalcredit as creditsort,openid from " . tablename('uber_flower_fans') . " WHERE status=1 AND uniacid= :uniacid AND rid= :rid AND totalcredit>0 ORDER BY totalcredit ASC,createtime DESC", array(':uniacid' => $_W['uniacid'],':rid' => $rid));	
	
		foreach($credit_arr as $key=>&$credit){
			if($credit['openid']==$openid){
			$myrank=array('No'=>$key+1,'Point' => $credit['creditsort']);
			}
		}
		unset($credit);	
		return $myrank;
		
	}
	public function getMktime(){
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$nowtime = mktime(0,0,0,$month,$day,$year);//当天开始时间戳
		return $nowtime;	
	}
	public function is_weixin()
	{
		if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') === false) {
			return false;
		}
		return true;
	}
	public function isWeiXin(){
		if (!UBER_DEBUG&&!$this->is_weixin()) {
		   exit("<!DOCTYPE html>
					<html>
						<head>
							<meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
							<title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
						</head>
						<body>
						<div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
						</body>
					</html>");
		}
		
	}

	protected function exportexcel($data=array(),$title=array(),$filename='report'){
    	header("Content-type:application/octet-stream");
    	header("Accept-Ranges:bytes");
    	header("Content-type:application/vnd.ms-excel");  
    	header("Content-Disposition:attachment;filename=".$filename.".xls");
    	header("Pragma: no-cache");
    	header("Expires: 0");
    	//导出xls 开始
    	if (!empty($title)){
    	    foreach ($title as $k => $v) {
    	        $title[$k]=iconv("UTF-8", "GB2312",$v);
    	    }
    	    $title= implode("\t", $title);
    	    echo "$title\n";
    	}
    	if (!empty($data)){
    	    foreach($data as $key=>$val){
    	        foreach ($val as $ck => $cv) {
    	            $data[$key][$ck]=iconv("UTF-8", "GB2312", $cv);
    	        }
    	        $data[$key]=implode("\t", $data[$key]);
    	        
    	    }
    	    echo implode("\n",$data);
    	}
 	}
	
	public function template($filename, $type = TEMPLATE_INCLUDEPATH) {
		global $_W;
		$name = strtolower($this->modulename);
		if (defined('IN_SYS')) {
			$source = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$filename}.html";
			$compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$name}/{$filename}.tpl.php";
			if (!is_file($source)) {
				$source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
			} 
			if (!is_file($source)) {
				$source = IA_ROOT . "/addons/{$name}/template/{$filename}.html";
			} 
			if (!is_file($source)) {
				$source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
			} 
			if (!is_file($source)) {
				$source = IA_ROOT . "/web/themes/default/{$filename}.html";
			} 
		} else {
			$template = $this->module['config']['style'];
			$file = IA_ROOT . "/addons/{$name}/data/template/shop_" . $_W['uniacid'];
			if (is_file($file)) {
				$template = file_get_contents($file);
				if (!is_dir(IA_ROOT . '/addons/{$name}/template/mobile/' . $template)) {
					$template = "default";
				} 
			} 
			$compile = IA_ROOT . "/data/tpl/app/{$name}/{$template}/mobile/{$filename}.tpl.php";
			$source = IA_ROOT . "/addons/{$name}/template/mobile/{$template}/{$filename}.html";
			if (!is_file($source)) {
				$source = IA_ROOT . "/addons/{$name}/template/mobile/default/{$filename}.html";
			}
			if (!is_file($source)) {
				$source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
			} 
			if (!is_file($source)) {
				$source = IA_ROOT . "/app/themes/default/{$filename}.html";
			} 
		} 
		if (!is_file($source)) {
			exit("Error: template source '{$filename}' is not exist!");
		} 
		if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
			template_compile($source, $compile, true);
		} 
		return $compile;
	} 
	/////////////////////////////////////////////////////////////////
	
}
