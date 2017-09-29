<?php
defined('IN_IA') or exit('Access Denied');
class hc_articleModuleSite extends WeModuleSite {
	
	public function __mobile($f_name){
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$uid = $_W['member']['uid'];
		$openid = $_W['openid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$shareid = 'hc_article_shareid'.$uniacid;
		if(intval($_GPC['mid'])){
			$day_cookies = 15;
			if(empty($_COOKIE[$shareid]) || (($_GPC['mid']!=$_COOKIE[$shareid]) && !empty($_GPC['mid']))){
				setcookie("$shareid", $_GPC['mid'], time()+3600*24*$day_cookies);
			}
		}
		$rule = pdo_fetch('SELECT * FROM '.tablename('hc_article_rule')." WHERE `uniacid` = :uniacid ",array(':uniacid' => $uniacid));
		include_once  'mobile/'.strtolower(substr($f_name,8)).'.php';
	}
	
	public function __member($f_name){
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$ismobile = 'hc_article_mobile'.$uniacid;
		$isauthcode = 'hc_article_authcode'.$uniacid;
		if(strtolower(substr($f_name,8))!='release_index'){
			if(empty($_COOKIE[$ismobile]) || empty($_COOKIE[$isauthcode])){
				include $this->template('member/login');
				exit;
			}
			$member = pdo_fetch("select * from ".tablename('hc_article_member')." where ispay = 1 and uniacid = ".$uniacid." and mobile = '".trim($_COOKIE[$ismobile])."' and authcode = '".trim($_COOKIE[$isauthcode])."'");
			if(empty($member)){
				include $this->template('member/login');
				exit;
			}
		}
		include_once  'mobile/member/'.strtolower(substr($f_name,8)).'.php';
	}
	
	public function __web($f_name){
		global $_W,$_GPC;
		checklogin();
		$uniacid = $_W['uniacid'];
		load()->func('tpl');
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		include_once  'web/'.strtolower(substr($f_name,5)).'.php';
	}
	
	//首页
	public function doMobileIndex(){
		$this->__mobile(__FUNCTION__);
	}
	
	//文章首页
	public function doMobileArticleIndex(){
		$this->__mobile(__FUNCTION__);
	}
	
	//详情
	public function doMobileDetail(){
		$this->__mobile(__FUNCTION__);
	}
	
	//个人中心
	public function doMobileHome(){
		$this->__mobile(__FUNCTION__);
	}
	
	//个人资料
	public function doMobileMemberInfo(){
		$this->__mobile(__FUNCTION__);
	}
	
	//注册
	public function doMobileRegister(){
		$this->__mobile(__FUNCTION__);
	}
	
	//我的下线
	public function doMobileTeam(){
		$this->__mobile(__FUNCTION__);
	}
	
	//提现
	public function doMobileWithdraw(){
		$this->__mobile(__FUNCTION__);
	}
	
	//更多合作
	public function doMobileRule(){
		$this->__mobile(__FUNCTION__);
	}
	
	//发布文章首页
	public function doMobileRelease_Index(){
		$this->__member(__FUNCTION__);
	}
	
	//发布文章
	public function doMobileRelease_Post(){
		$this->__member(__FUNCTION__);
	}
	
	//文章列表
	public function doMobileRelease_list(){
		$this->__member(__FUNCTION__);
	}
	
	//个人信息
	public function doMobileRelease_Home(){
		$this->__member(__FUNCTION__);
	}
	
//以下为后台管理＝＝＝＝＝＝＝＝＝＝＝＝＝＝
	
	//粉丝管理
	public function doWebMember(){
		$this->__web(__FUNCTION__);
	}
	
	//规则管理
	public function doWebRule(){
		$this->__web(__FUNCTION__);
	}
	
	//幻灯片管理
	public function doWebAdv(){
		$this->__web(__FUNCTION__);
	}
	
	//文章分类
	public function doWebType(){
		$this->__web(__FUNCTION__);
	}
	
	//文章管理
	public function doWebArticle(){
		$this->__web(__FUNCTION__);
	}
	
	//问题管理
	public function doWebQuestion(){
		$this->__web(__FUNCTION__);
	}
	
	//个人海报
	public function doWebMemberPoster(){
		$this->__web(__FUNCTION__);
	}
	
	public function payResult($params) {
		global $_W;
		$uniacid = $_W['uniacid'];
        if($params['from'] == 'return'){
			if(is_numeric($params['tid'])){
				pdo_update('hc_article_member', array('ispay'=>1), array('id'=>$params['tid']));
				$shareid = 'hc_article_shareid'.$_W['uniacid'];
				setcookie("$shareid", 0);
				message('支付成功！', $this->createMobileUrl('index'), 'success');
			} else {
				$uid = $_W['member']['uid'];
				if($uid){
					$mid = explode('/', $params['tid']);
					mc_credit_update($uid, 'credit2', intval($params['fee']), array('0'=>'', '1'=>$member['nickname'].'充值记录'));
					$input = array(
						'uniacid'=>$uniacid,
						'mid'=>$mid[0],
						'credit2'=>intval($params['fee']),
						'flag'=>1,
						'createtime'=>time()
					);
					pdo_insert('hc_article_withdraw', $input);
					$mid = $mid[0];
					$viplevels = pdo_fetchall('SELECT * FROM '.tablename('hc_article_viplevel')." WHERE uniacid = ".$uniacid." order by needmoney desc");
					$myinput = pdo_fetchcolumn("select sum(credit2) from ".tablename('hc_article_withdraw')." where flag = 1 and uniacid = ".$uniacid." and mid = ".$mid);
					$viplevel = array();
					$flag = 0;
					foreach($viplevels as $v){
						$viplevel[$v['id']] = $v['needmoney'];
						if($myinput>=$v['needmoney'] && $flag==0){
							pdo_update('hc_article_member', array('viplevel'=>$v['id']), array('id'=>$mid));
							$flag++;
						}
					}
					$rule = pdo_fetch('SELECT * FROM '.tablename('hc_article_rule')." WHERE `uniacid` = :uniacid ",array(':uniacid' => $uniacid));
					$time = time();
					$shareid = pdo_fetchcolumn('SELECT shareid FROM '.tablename('hc_article_member')." WHERE id = ".$mid);
					if(intval($shareid)){
						for($j=1; $j<=3; $j++){
							if(intval($shareid)){
								$commission = 'commission'.$j;
								$commission = array(
									'uniacid'=>$uniacid,
									'mid'=>$shareid,
									'shareid'=>$mid[0],
									'aid'=>0,
									'credit2'=>intval($params['fee'])*$rule[$commission]/100,
									'flag'=>1,
									'createtime'=>$time
								);
								pdo_insert('hc_article_commission', $commission);
								$shareid = pdo_fetchcolumn('SELECT shareid FROM '.tablename('hc_article_member')." WHERE id = ".$shareid);
							} else {
								break;
							}
						}
					}
					for($i=0; $i<3; $i++){
						$member = pdo_fetch('SELECT shareid, viplevel FROM '.tablename('hc_article_member')." WHERE id = ".$mid);
						$shareid = $member['shareid'];
						if(intval($shareid)){
							//上线shareid
							$highmember = pdo_fetch('SELECT shareid, viplevel FROM '.tablename('hc_article_member')." WHERE id = ".$shareid);
							$highshareid = $highmember['shareid'];
							if($viplevel[$member['viplevel']] >= $viplevel[$highmember['viplevel']]){
								//我的充值金额
								$myinput = pdo_fetchcolumn("select sum(credit2) from ".tablename('hc_article_withdraw')." where flag = 1 and uniacid = ".$uniacid." and mid = ".$mid);
								//上线充值金额
								$highinput = pdo_fetchcolumn("select sum(credit2) from ".tablename('hc_article_withdraw')." where flag = 1 and uniacid = ".$uniacid." and mid = ".$shareid);
								if($myinput > $highinput){
									if(intval($highshareid)){
										pdo_update('hc_article_member', array('shareid'=>$highshareid), array('id'=>$mid));
										pdo_update('hc_article_member', array('shareid'=>$mid), array('id'=>$shareid));
									} else {
										pdo_update('hc_article_member', array('shareid'=>0), array('id'=>$mid));
										pdo_update('hc_article_member', array('shareid'=>$shareid), array('shareid'=>$mid));
										pdo_update('hc_article_member', array('shareid'=>$mid), array('id'=>$shareid));
										break;
									}
									$mid = $shareid;
								}
							} else {
								break;
							}
						}
					}
				}
				message('充值成功', $this->createMobileUrl('withdraw', array('op'=>'input', 'opp'=>'log')));
			}
        }
    }
	
	public function doMobileUserinfo() {
		global $_GPC,$_W;
		$uniacid = $_W['uniacid'];//当前公众号ID
		load()->func('communication');
		//用户不授权返回提示说明
		if ($_GPC['code']=="authdeny"){
		    $url = $_W['siteroot'].'app/'.$this->createMobileUrl('index', array(), true);
			header("location:$url");
			exit('authdeny');
		}
		//高级接口取未关注用户Openid
		if (isset($_GPC['code'])){
		    //第二步：获得到了OpenID
		    $appid = $_W['account']['key'];
		    $secret = $_W['account']['secret'];
			$serverapp = $_W['account']['level'];	
			if ($serverapp!=4) {
				$cfg = $this->module['config'];
			    $appid = $cfg['appid'];
			    $secret = $cfg['secret'];
				if(empty($appid) || empty($secret)){
					return ;
				}
			}
			$state = $_GPC['state'];
			//1为关注用户, 0为未关注用户
			//查询活动时间
			$code = $_GPC['code'];
		    $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
		    $content = ihttp_get($oauth2_code);
		    $token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
				echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				exit;
			}

		    $openid = $token['openid'];
			//再次查询是否为关注用户
			$profile = pdo_fetch("select * from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
			//关注用户直接获取信息	
			if ($profile['follow']==1){
			    $state = 1;
			}else{
				//未关注用户跳转到授权页
				$url = $_W['siteroot'].'app/'.$this->createMobileUrl('userinfo', array(), true);
				$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
				//header("location:$oauth2_code");
			}
			//未关注用户和关注用户取全局access_token值的方式不一样
			
			$access_token = $token['access_token'];
			$oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
			
			//使用全局ACCESS_TOKEN获取OpenID的详细信息			
			$content = ihttp_get($oauth2_url);
			$info = @json_decode($content['content'], true);
			if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {
				echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
				exit;
			}
			if(!empty($_W['member']['uid'])){
				$row = array(
					'uniacid' => $uniacid,
					'nickname'=>$info['nickname'],
					'avatar'=>$info['headimgurl'],
					'realname'=>$info['nickname']
				);
				pdo_update('mc_members', $row, array('uid'=>$_W['member']['uid']));	
			} else {
				$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $uniacid));
				$row = array(
					'uniacid' => $uniacid,
					'nickname'=>$info['nickname'],
					'avatar'=>$info['headimgurl'],
					'realname'=>$info['nickname'],
					'groupid' => $default_groupid,
					'email'=>random(32).'@we7.cc',
					'salt'=>random(8),
					'createtime'=>time()
				);
				pdo_insert('mc_members', $row);
				$user['uid'] = pdo_insertid();
				//$fan = mc_fansinfo($openid);
				//pdo_update('mc_mapping_fans', array('uid'=>$user['uid']), array('fanid'=>$fan['fanid']));
				pdo_update('mc_mapping_fans', array('uid'=>$user['uid']), array('openid'=>$openid, 'uniacid'=>$uniacid));
				_mc_login($user);
			}
			$cookie = 'hc_article_cookie'.$_W['uniacid'];
			setcookie($cookie, 'a', time()+3600*24*15);
			$url = $this->createMobileUrl('register');
			//die('<script>location.href = "'.$url.'";</script>');
			header("location:$url");
			exit;
		}else{
			echo '<h1>网页授权域名设置出错!</h1>';
			exit;
		}
	}
	
	private function CheckCookie() {
		global $_W;
		return;
		$cookie = 'hc_article_cookie'.$_W['uniacid'];
		if(empty($_COOKIE[$cookie])){
			$appid = $_W['account']['key'];
			$secret = $_W['account']['secret'];
			//是否为高级号
			$serverapp = $_W['account']['level'];
			if ($serverapp!=4) {
				$cfg = $this->module['config'];
				$appid = $cfg['appid'];
				$secret = $cfg['secret'];
				if(empty($appid) || empty($secret)){
					return ;
				}
			}
			//借用的
			$url = $_W['siteroot'].'app/'.$this->createMobileUrl('userinfo', array(), true);
			$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
			//exit($oauth2_code);
			header("location:$oauth2_code");
			exit;
		}
	}
}

function hehe($mobile){
	if(preg_match("/1[3458]{1}\d{9}$/",$mobile)){
		//$mphone = substr($mobile,3,5);
		//$lphone = str_replace($mphone,"*****",$mobile);
		return $mobile;
		//return $lphone;
	} else {
		message('电话号码格式不正确');
	}
}

function hoho($phone){
    $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
    if($IsWhat == 1){
        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2', $phone);
    }
    else{
        return  preg_replace('/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2', $phone);
    }
}

function haha($endtime=0){
	//已过的时间秒数
	$usetime = $endtime - time();
	if($usetime<=0){
		return '已过期';
	}
	$hours = $usetime/3600;
	if($hours>24){
		$days = ceil($usetime/86400);
		return '剩余<strong>'.$days.'</strong>天';
	} else {
		$hours = ceil($usetime/3600);
		return '剩余<strong>'.$hours.'</strong>小时';
	}
}

function tpl_ueditor1($id, $value = '', $options = array()) {
	$s = '';
	if (!defined('TPL_INIT_UEDITOR')) {
		$s .= '<script type="text/javascript" src="../web/resource/components/ueditor/ueditor.config.js"></script><script type="text/javascript" src="../web/resource/components/ueditor/ueditor.all.min.js"></script><script type="text/javascript" src="../web/resource/components/ueditor/lang/zh-cn/zh-cn.js"></script>';
	}
	$options['height'] = empty($options['height']) ? 200 : $options['height'];
	$s .= !empty($id) ? "<textarea id=\"{$id}\" name=\"{$id}\" type=\"text/plain\" style=\"height:{$options['height']}px;\">{$value}</textarea>" : '';
	$s .= "
	<script type=\"text/javascript\">
			var ueditoroption = {
				'autoClearinitialContent' : false,
				'toolbars' : [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
					'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion', 'insertvideo',
					'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
					'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
					'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
				'elementPathEnabled' : false,
				'initialFrameHeight': {$options['height']},
				'focus' : false,
				'maximumWords' : 9999999999999
			};
			var opts = {
				type :'image',
				direct : false,
				multi : true,
				tabs : {
					'upload' : 'active',
					'browser' : '',
					'crawler' : ''
				},
				path : '',
				dest_dir : '',
				global : false,
				thumb : false,
				width : 0
			};
			UE.registerUI('myinsertimage',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['../../../../web/resource/js/app/fileUploader'], function(uploader){
							uploader.show(function(imgs){
								if (imgs.length == 0) {
									return;
								} else if (imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src' : imgs[0]['url'],
										'_src' : imgs[0]['attachment'],
										'width' : '100%',
										'alt' : imgs[0].filename
									});
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src' : imgs[i]['url'],
											'_src' : imgs[i]['attachment'],
											'width' : '100%',
											'alt' : imgs[i].filename
										});
									}
									editor.execCommand('insertimage', imglist);
								}
							}, opts);
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules :'background-position: -726px -77px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 19);
			".(!empty($id) ? "
				$(function(){
					var ue = UE.getEditor('{$id}', ueditoroption);
					$('#{$id}').data('editor', ue);
					$('#{$id}').parents('form').submit(function() {
						if (ue.queryCommandState('source')) {
							ue.execCommand('source');
						}
					});
				});" : '')."
	</script>";
	return $s;
}

function tpl_form_field_multi_image1($name, $value = array(), $options = array()) {
	global $_W;
	$options['multiple'] = true;
	$options['direct'] = false;
	$s = '';
	if (!defined('TPL_INIT_MULTI_IMAGE')) {
		$s = '
<script type="text/javascript">
	function uploadMultiImage(elm) {
		var name = $(elm).next().val();
		util.image( "", function(urls){
			$.each(urls, function(idx, url){
				$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+url.url+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+url.attachment+\'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>\');
			});
		}, "", ' . json_encode($options) . ');
	}
	function deleteMultiImage(elm){
		require(["jquery"], function($){
			$(elm).parent().remove();
		});
	}
</script>';
		define('TPL_INIT_MULTI_IMAGE', true);
	}

	$s .= <<<EOF
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="{$name}" />
	</span>
</div>
<div class="input-group multi-img-details">
EOF;
	if (is_array($value) && count($value) > 0) {
		foreach ($value as $row) {
			$s .= '
<div class="multi-item">
	<img src="' . tomedia($row) . '" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
	<input type="hidden" name="' . $name . '[]" value="' . $row . '" >
	<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em>
</div>';
		}
	}
	$s .= '</div>';

	return $s;
}
