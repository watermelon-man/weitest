<?php
	
	if($op=='more'){
		if(empty($rule['morehz'])){
			echo '此处请填写东方云内部机制';
			exit;
		} else {
			$url = $rule['morehz'];
			header("location:$url");
		}
	}
	
	include $this->template('home');
?>