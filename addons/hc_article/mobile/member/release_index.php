<?php
	$rembermobile = 'hc_article_rembermobile'.$uniacid;
	if($op=='display'){
		$isreg = 0;
		if(empty($_COOKIE[$ismobile]) || empty($_COOKIE[$isauthcode])){
			
		} else {
			$member = pdo_fetch("select * from ".tablename('hc_article_member')." where ispay = 1 and uniacid = ".$uniacid." and mobile = '".trim($_COOKIE[$ismobile])."' and authcode = '".trim($_COOKIE[$isauthcode])."'");
			if(!empty($member)){
				$isreg = 1;
			}
		}
		if(intval($_COOKIE['remember-username'])==1){
			$username = $_COOKIE[$rembermobile];
		}
		if($isreg == 0){
			include $this->template('member/login');
			exit;		
		}
	}
	
	if($op=='login'){
		$isreg = 0;
		$member = pdo_fetchall("select * from ".tablename('hc_article_member')." where ispay = 1 and uniacid = ".$uniacid);
		foreach($member as $h){
			if($h['mobile']==trim($_GPC['mobile']) && $h['authcode']==$_GPC['authcode']){
				$isreg = 1;
				if(time()-$h['codetime'] > 3600){
					echo 0;
					exit;
				}
				setcookie($ismobile, $_GPC['mobile'], time()+3600*24);
				setcookie($rembermobile, $_GPC['mobile'], time()+3600*24);
				setcookie($isauthcode, $_GPC['authcode'], time()+3600*24);
				if($_GPC['rember']){
					setcookie('remember-username', 1, time()+3600*24);
				} else {
					setcookie('remember-username', 0, time()+3600*24);
				}
				echo 1;
				exit;
			}
		}
		if($isreg == 0){
			echo -1;
			exit;
		}
	}
	
	if($op=='exit'){
		setcookie($ismobile, '', time()+3600*240);
		setcookie($isauthcode, '', time()+3600*240);
		$url = $this->createMobileurl('release_index');
		header("location:$url");
	}
	
	//登录状态才可往下执行
	if(empty($_COOKIE[$ismobile]) || empty($_COOKIE[$isauthcode])){
		include $this->template('member/login');
		exit;
	}
	
	include $this->template('member/loginindex');
?>