<?php
/*替换为你自己的数据库名*/
$dbname = $_W['config']['db']['master']['database'];
/*填入数据库连接信息*/
$host = $_W['config']['db']['master']['host'];
$port = $_W['config']['db']['master']['port'];
$user = $_W['config']['db']['master']['username'];//用户名(api key)
$pwd = $_W['config']['db']['master']['password'];//密码(secret key)
/*以上信息都可以在数据库详情页查找到*/
$mark = 0;
$link=mysqli_connect("{$host}",$user,$pwd,$dbname); 
// 检查连接 
if (!$link) 
{ 
	$mark = 1;
	/*接着调用mysql_connect()连接服务器*/
	$link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
	if(!$link) {
		$mark = 2;
	    die("Connect Server Failed: " . mysql_error());
	}

	// 连接成功后立即调用mysql_select_db()选中需要连接的数据库
	if(!mysql_select_db($dbname,$link)) {
	    die("Select Database Failed: " . mysql_error($link));
	}
} 