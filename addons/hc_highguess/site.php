<?php
/**
 * 我画你猜高级版模块定义
 */
defined('IN_IA') or exit('Access Denied');
class Hc_HighGuessModuleSite extends WeModuleSite {
	
	public function doMobileIndex(){
		global $_GPC,$_W;
		$weid = $_W['uniacid'];
		$from_user = $_W['openid'];
		$uid = $_W['member']['uid'];
		$op = $_GPC['op'] ? $_GPC['op'] : 'display';
		$ridcookie = "hc_highguess_rid".$_W['uniacid'];
		$rid = empty($_GPC['rid']) ? $_COOKIE[$ridcookie] : $_GPC['rid'];
		if(empty($rid)){
			message('非法访问！');
		}
		setcookie($ridcookie, $rid, time()+3600*240);
		$reply = pdo_fetch("SELECT * FROM ".tablename('hc_highguess_reply')." WHERE rid = :rid", array(':rid' => $rid));
		if(empty($from_user)){
			message('你的身份已过期，请重新发送关键字进入！', $reply['gzurl'], 'error');
			exit;
		}
		$oauth_openid = "hc_highguess_openid".$_W['uniacid'];
		$urlcookie = "hc_highguess_url".$_W['uniacid'];
		if (empty($_COOKIE[$oauth_openid])) {
			$url = $_W['siteroot'].'.'.$_SERVER['REQUEST_URI'];
			setcookie($urlcookie, $url, time()+3600*240);
			$this->CheckCookie($oauth_openid);
		} else {
			if(!empty($_COOKIE[$urlcookie])){
				$url = $_COOKIE[$urlcookie];
				setcookie($urlcookie, '', time()+3600*240);
				header("location:$url");
			}
		}
		
		if($op=='display'){
			$isregister = pdo_fetch("select * from ".tablename('hc_highguess_member')." where from_user = '".$from_user."' and weid = ".$weid." and rid = ".$rid);
			if(empty($isregister)){
				$this->CheckCookie(time());
			} else {
				$mid = $isregister['id'];
			}
		}
		
		$myinfo = pdo_fetch("select * from ".tablename('hc_highguess_member')." where from_user = '".$from_user."' and weid = ".$weid." and rid = ".$rid);
		
		if($op=='drawword'){
			$wid = intval($_GPC['wid']);
			$mid = $myinfo['id'];
			$follow = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where openid = '".$from_user."' and uniacid = ".$weid);
			if(empty($follow) || $follow['follow']==0){
				message('请先关注该公众号！', $reply['gzurl'], 'error');
			}
			include $this->template('drawword');
			exit;
		}
		
		if($op=='finish'){
			$imgstr = $_GPC['image'];
			$imgdata = substr($imgstr,strpos($imgstr,",") + 1);
			$decodedData = base64_decode($imgdata);
			//file_put_contents(time().'22.png',$decodedData );
			$sname = 'images/hchighguess/'.time().rand(1000,9999).'v.png';
			$fname = '../attachment/'.$sname;
			$indexname = '../attachment/images/hchighguess';
			if(!is_dir($indexname)){
				mkdir($indexname, 0777);
			}
			$fp = fopen($fname,'wb');
			fwrite($fp,$decodedData);
			fclose($fp);
			$myimage = array(
				'weid'=>$weid,
				'rid'=>$rid,
				'mid'=>$myinfo['id'],
				'wid'=>intval($_GPC['qid']),
				'image'=>$sname,
				'createtime'=>time()
			);
			pdo_insert('hc_highguess_images', $myimage);
			echo pdo_InsertId();
			exit;
		}
		if($op=='myimage'){
			$imgid = intval($_GPC['imgid']);
			$mid = intval($_GPC['mid']);
			$member = pdo_fetch("select * from ".tablename('hc_highguess_member')." where id = ".$mid);
			$isregister = pdo_fetch("select * from ".tablename('hc_highguess_member')." where from_user = '".$from_user."' and weid = ".$weid." and rid = ".$rid);
			if(empty($isregister)){
				$this->CheckCookie(time());
			} else {
				$infoid = $isregister['id'];
			}
			$myimage = pdo_fetch("select * from ".tablename('hc_highguess_images')." where id = ".$imgid);
			if(empty($myimage)){
				message('该链接已失效！');
			}
			$selectword = pdo_fetch("select * from ".tablename('hc_highguess_words')." where id = ".$myimage['wid']." and isopen = 1");
			
			if(!empty($selectword)){
				$selectlog = pdo_fetchall("select * from ".tablename('hc_highguess_selectlog')." where weid = ".$weid." and wid = ".$selectword['id']." and imgid = ".$imgid);
			} else {
				echo "<script>
					alert('该词条已被删除！！');
					window.location.href = '".$_W['siteroot'].$this->createMobileUrl('index', array('rid'=>$rid))."'
				</script>";
				exit;
			}
			if($mid != $infoid){
				$other = 1;
				$isselect = pdo_fetch("select * from ".tablename('hc_highguess_selectlog')." where from_user ='".$from_user."' and weid = ".$weid." and wid = ".$selectword['id']." and imgid = ".$imgid);
				if(!empty($selectword['words'])){
					$words = explode("#", $selectword['words']);
					$wordss = array();
					foreach($words as $key=>$w){
						$wordss[$key]['word'] = $w;
						$wordss[$key]['id'] = $selectword['id'];
					}
				}
				if(empty($isselect)){
					include $this->template('selectimage');
					exit;
				}
				$selectlog = pdo_fetchall("select * from ".tablename('hc_highguess_selectlog')." where weid = ".$weid." and wid = ".$selectword['id']." and imgid = ".$imgid);
				$total = pdo_fetchcolumn("select count(id) from ".tablename('hc_highguess_selectlog')." where weid = ".$weid." and wid = ".$selectword['id']." and imgid = ".$imgid." and word = '".$selectword['word']."'");
				$alltotal = pdo_fetchcolumn("select count(id) from ".tablename('hc_highguess_selectlog')." where weid = ".$weid." and wid = ".$selectword['id']." and imgid = ".$imgid);
				$total = empty($total) ? 0 : $total;
				if(empty($alltotal)){
					$unique = 0;
				} else {
					$unique = intval($total / $alltotal * 100);
				}
				include $this->template('myfinished');
				exit;
			}
			if(!empty($selectlog)){
				if(!empty($selectword['words'])){
					$words = explode("#", $selectword['words']);
					$wordss = array();
					foreach($words as $key=>$w){
						$wordss[$key]['word'] = $w;
						$wordss[$key]['id'] = $selectword['id'];
					}
				}
				$total = pdo_fetchcolumn("select count(id) from ".tablename('hc_highguess_selectlog')." where weid = ".$weid." and wid = ".$selectword['id']." and imgid = ".$imgid." and word = '".$selectword['word']."'");
				$alltotal = pdo_fetchcolumn("select count(id) from ".tablename('hc_highguess_selectlog')." where weid = ".$weid." and wid = ".$selectword['id']." and imgid = ".$imgid);
				$total = empty($total) ? 0 : $total;
				if(empty($alltotal)){
					$unique = 0;
				} else {
					$unique = intval($total / $alltotal * 100);
				}
				include $this->template('myfinished');
				exit;
			}
			include $this->template('myimage');
			exit;
		}
		
		if($op=='selectimage'){
			$wid = intval($_GPC['wid']);
			$imgid = $_GPC['imgid'];
			$myimage = pdo_fetch("select image from ".tablename('hc_highguess_images')." where id = ".$imgid);
	
			if($_GPC['opp']=='selected'){
				$selectlog = array(
					'weid'=>$weid,
					'wid'=>$wid,
					'imgid'=>$imgid,
					'from_user'=>$from_user,
					'realname'=>$myinfo['realname'],
					'image'=>$myinfo['avatar'],
					'word'=>$_GPC['word'],
					'createtime'=>time()
				);
				$isselect = pdo_fetch("select * from ".tablename('hc_highguess_selectlog')." where from_user ='".$from_user."' and weid = ".$weid." and wid = ".$wid." and imgid = ".$imgid);
				if(empty($isselect)){
					pdo_insert('hc_highguess_selectlog', $selectlog);
				}
				$url = $this->createMobileUrl('index', array('op'=>'myimage', 'imgid'=>$imgid, 'rid'=>$rid, 'mid'=>$_GPC['mid']));
				header("location:$url");
			}
		}

		$words = pdo_fetchall("select * from ".tablename('hc_highguess_words')." where weid = ".$weid." and isopen = 1");
		include $this->template('index');
	}
	
	public function doWebWords(){
		global $_GPC,$_W;
		$weid = $_W['uniacid'];
		$op = $_GPC['op'] ? $_GPC['op'] : 'display';
		if($op=='display'){
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("select * from ".tablename('hc_highguess_words')." where weid = ".$weid." order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			$total = pdo_fetchcolumn("select count(id) from ".tablename('hc_highguess_words')." where weid = ".$weid);
			$pager = pagination($total, $pindex, $psize);
		}
		if($op=='sort'){
			$op = 'display';
			$sort = array(
				'word'=>$_GPC['word']
			);
			$list = pdo_fetchall("select * from ".tablename('hc_highguess_words')." where weid = ".$weid." and word like '%".$sort['word']."%' order by createtime desc");
		}
		if($op=='post'){
			$id = $_GPC['id'];
			if(intval($id)){
				$item = pdo_fetch("select * from ".tablename('hc_highguess_words')." where id = ".$id);
			}
			if(checksubmit('submit')){
				$newwords = array(
					'weid'=>$_W['uniacid'],
					'word'=>$_GPC['word'],
					'words'=>trim($_GPC['words']),
					'isopen'=>$_GPC['isopen'],
					'createtime'=>time()
				);
				
				if(intval($id)){
					$temp = pdo_update('hc_highguess_words', $newwords, array('id'=>$id));
					if($temp){
						message('提交成功！', $this->createWebUrl('words', array('op'=>'display')), 'success');
					} else {
						message('提交失败！', $this->createWebUrl('words', array('op'=>'post', 'id'=>$id)), 'error');
					}
				} else {
					$temp = pdo_insert('hc_highguess_words', $newwords);
					if($temp){
						message('提交成功！', $this->createWebUrl('words', array('op'=>'display')), 'success');
					} else {
						message('提交失败！', $this->createWebUrl('words', array('op'=>'post', 'id'=>$id)), 'error');
					}
				}
			}
		}
		
		if($op=='delete'){
			$temp = pdo_delete('hc_highguess_words', array('id'=>$_GPC['id']));
			if($temp){
				message('删除成功！', $this->createWebUrl('words', array('op'=>'display')), 'success');
			} else {
				message('删除失败！', $this->createWebUrl('words', array('op'=>'post', 'id'=>$id)), 'error');
			}
		}
		include $this->template('web/words');
	}
	
	private function CheckCookie($cookieValue='cookie_value'){
		global $_W;
		if(empty($_COOKIE[$cookieValue])){
			$serverapp = $_W['account']['level'];	
			//是否为高级号
			if ($serverapp==4) {
				$appid = $_W['account']['key'];
				$secret = $_W['account']['secret'];
			} else {
				//借用的
				$cfg = $this->module['config'];
				$appid = $cfg['appid'];
				$secret = $cfg['secret'];
				if(empty($appid) || empty($secret)){
					return;
				}
			}
			$url = $_W['siteroot'].'app/'.$this->createMobileUrl('userinfo', array('cookieValue'=>$cookieValue), true);
			$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
			header("location:$oauth2_code");
			exit;
		}
	}
	
	public function doMobileUserinfo() {
		global $_GPC,$_W;
		$weid = $_W['uniacid'];
		// cookie的名称
		$cookieValue = $_GPC['cookieValue'];
		load()->func('communication');
		//用户不授权返回提示说明
		if ($_GPC['code']=="authdeny"){
		    $url = $_W['siteroot'].$this->createMobileUrl('index');
			header("location:$url");
			exit('authdeny');
		}
		//高级接口取未关注用户Openid
		if (isset($_GPC['code'])){
			$serverapp = $_W['account']['level'];
			if ($serverapp==4) {
				$appid = $_W['account']['key'];
				$secret = $_W['account']['secret'];
			} else {
				$cfg = $this->module['config'];
			    $appid = $cfg['appid'];
			    $secret = $cfg['secret'];
			}
			//1为关注用户, 0为未关注用户
			$state = $_GPC['state'];
			$code = $_GPC['code'];
		    $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
			$content = ihttp_get($oauth2_code);
		    $token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
				echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				exit;
			}
		    $from_user = $token['openid'];
			//再次查询是否为关注用户
			$profile = pdo_fetch("select * from ".tablename('mc_mapping_fans')." where uniacid = ".$_W['uniacid']." and openid = '".$from_user."'");
			
			//关注用户直接获取信息	
			if ($profile['follow']==1){
			    $state = 1;
			}else{
				//未关注用户跳转到授权页
				$url = $_W['siteroot'].$this->createMobileUrl('userinfo', array('cookieValue'=>$cookieValue), true);
				$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";				
				header("location:$oauth2_code");
			}
			//未关注用户和关注用户取全局access_token值的方式不一样
			if ($state==1){
			    $oauth2_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";
			    $content = ihttp_get($oauth2_url);
			    $token_all = @json_decode($content['content'], true);
			    if(empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {
				    echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';
				    exit;
			    }
				$access_token = $token_all['access_token'];
				$oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
			}else{
			    $access_token = $token['access_token'];
				$oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";
			}
			
			//使用全局ACCESS_TOKEN获取OpenID的详细信息			
			$content = ihttp_get($oauth2_url);
			$info = @json_decode($content['content'], true);
			if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {
				echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';
				exit;
			}

			$ridcookie = "hc_highguess_rid".$_W['uniacid'];
			$rid = intval($_COOKIE[$ridcookie]);
			$myinfo = array(
				'weid'=>$weid,
				'rid'=>$rid,
				'from_user'=>$_W['openid'],
				'realname'=>$info['nickname'],
				'mobile'=>$info['mobile'],
				'avatar'=>$info['headimgurl'],
				'createtime'=>time()
			);
			$isregister = pdo_fetch("select * from ".tablename('hc_highguess_member')." where from_user = '".$_W['openid']."' and weid = ".$weid." and rid = ".$rid);
			if(empty($isregister)){
				pdo_insert('hc_highguess_member', $myinfo);
			}
			
			setcookie($cookieValue, $info['openid'], time()+3600*240);
			$url = $this->createMobileUrl('index');
			header("location:$url");
			exit;
			
		}else{
			echo '<h1>网页授权域名设置出错!</h1>';
			exit;		
		}
	}
}

function char($key=0){
	$charA = array(
		'0'=>'A.',
		'1'=>'B.',
		'2'=>'C.',
		'3'=>'D.',
		'4'=>'E.',
		'5'=>'F.',
		'6'=>'G.',
		'7'=>'H.',
		'8'=>'I.',
		'9'=>'J.'
	);
	return $charA[$key];
}