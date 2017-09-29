<?php

require('../framework/bootstrap.inc.php');

// pdo_query('show global variables like "%datadir%"');

// pdo_delete('imeepos_runner3_member');

if(strpos($_W['siteroot'],'install')){

}else{
	$_W['siteroot'] = $_W['siteroot'].'install/'
}


// pdo_query("DROP table ".tablename('imeepos_runner3_member'));



// $sql = pdo_query("show global variables like '%data%'");
// print_r($sql);