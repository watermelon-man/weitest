<?php

/**
 * 中秋吃月饼模块定义
 *
 * @author 
 * @url QQ:263328455
 */

defined('IN_IA') or exit('Access Denied');



class Game_zqModule extends WeModule {

public $tablename = 'game_zq_reply';

public $mname = "game_zq";

public $title = "中秋吃月饼";

public $gplay = "玩法很简单,点击躲过石头和深渊,游戏会根据积分高低进行排名";

	public function fieldsFormDisplay($rid = 0) {

		global $_W;

		if(!empty($rid)){

			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));

			$reply['data'] = unserialize($reply['data']);

			

		}else{

			$reply = array(

			'hd_title'=>$this->title,

			'hd_desc'=>'邀请您的朋友一起来玩'.$this->title.'游戏吧!',

			'hd_img'=>$_W['siteroot']."addons/".$this->mname."/template/mobile/images/avg.jpg",

			'desc'=>"<p><strong>游戏玩法</strong></p>".$this->gplay."<br><p><strong>奖品设置</strong></p>

<p>第1名 iphone6splus一部</p>

<p>第2名 iphone6一部</p><p>第3名 小米5一部</p><p>第4-10名 红米2一部</p>",

				'max_num' => 100,

				'day_num' => 30,

				'share_title'=>$this->title,

				'share_img'=>$_W['siteroot']."addons/".$this->mname."/template/mobile/images/share.jpg",

				'share_desc'=>'邀请您的朋友一起来玩'.$this->title.'吧,一起来拿奖吧',

				'ad_img'=>$_W['siteroot']."addons/".$this->mname."/template/mobile/images/avg01.jpg",

				'share_url'=>'',

				'copyright'=>'XXX版权所有',

					'start_time' => TIMESTAMP,

				'end_time' => TIMESTAMP+3600*24*30,

				'game_time'=>15,

				'data'=>array(

					'mp3'=>$_W['siteroot']."addons/".$this->mname."/template/mobile/js/1.mp3"

				)

			);

			

		}

		

		include $this->template('form');

	}



	public function fieldsFormValidate($rid = 0) {

		return true;

	}



	public function fieldsFormSubmit($rid=0) {

global $_GPC, $_W;

	load()->func('file');

	$id = intval($_GPC['reply_id']);

	if(intval($_GPC['day_num'])>intval($_GPC['max_num'])){message('每天次数必须小于等于总次数！', '', 'error');}

	$data  = array(

		'mp3'=>$_GPC['mp3']

	);

			$insert = array(

			'rid' => $rid,

			'hd_img' => $_GPC['hd_img'],

			'hd_title' => $_GPC['hd_title'],

			'hd_desc' => $_GPC['hd_desc'],

			'max_num' => intval($_GPC['max_num']),

			'day_num' => intval($_GPC['day_num']),

			

			'desc' => htmlspecialchars_decode($_GPC['desc']),

			'share_img' => $_GPC['share_img'],

			'share_title' => $_GPC['share_title'],

			'share_desc' => $_GPC['share_desc'],		

			'share_url' =>$_GPC['share_url'],

			'ad_img' =>$_GPC['ad_img'],

			'copyright' =>$_GPC['copyright'],

			

			

			'start_time'=>strtotime($_GPC['htime']['start']),

			'end_time'=>strtotime($_GPC['htime']['end']),

			'game_time'=>intval($_GPC['game_time']),

			'data'=>serialize($data),

		);

		if (empty($id)) {

			pdo_insert($this->tablename, $insert);

		} else {

			pdo_update($this->tablename, $insert, array('id' => $id));

		}



	}



	public function ruleDeleted($rid=0) {

		global $_W;

$row = pdo_fetchall("SELECT id  FROM ".tablename($this->mod_name."_reply")." WHERE rid = '$rid'");

		$deleteid = array();

		if (!empty($row)) {

			foreach ($row as $k => $v) {

				$deleteid[] = $v['id'];

			}

		}

		pdo_delete($this->mname."_reply", "id IN ('".implode("','", $deleteid)."')");

		pdo_delete($this->mname.'_fans',array('rid'=>$rid));

		pdo_delete($this->mname.'_num',array('rid'=>$rid));

		pdo_delete($this->mname.'_top',array('rid'=>$rid));

		return true;

	}





}