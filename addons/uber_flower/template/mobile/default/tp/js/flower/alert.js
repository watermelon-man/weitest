var myAlert = document.createElement("div");
    myAlert.id = "myAlert";
    myAlert.style.display = "none";
    myAlert.innerHTML='<div style="position:fixed;top:50%;left:50%;z-index:100000;width:280px;max-height:180px;color:#666;margin-top:-90px;margin-left:-140px;border-radius:3px;font:14px/30px \'Microsoft Yahei,Arail\';padding:30px 0;text-align:center;background:#fff;">\
	<a onclick="document.getElementById(\'myAlert\').style.display=\'none\'"  class="close" style="position:absolute;top:0;right:0;z-index:7;padding:8px 5px 17px 10px;width:30px;height:20px;border-radius:0 5px 0 50px;background:#fa0;color:#fff;text-align:right;font-size:9pt;line-height:20px;">关闭</a>\
	<p style="margin:20px 10px 20px;max-height:60px;line-height:22px;overflow:hidden;" id="alerttext"></p>\
	<a onclick="document.getElementById(\'myAlert\').style.display=\'none\'" style="display:block;width:90px;height:30px;color:#fff;margin:0 auto;border-radius:3px;font:16px/30px \'Microsoft Yahei,Arail\';background:#32cd74;">确 定</a>\
</div>\
<div style="position:fixed;left:0;top:0;z-index:99999;width:100%;height:100%;background:rgba(0,0,0,.5);"></div>';
document.body.appendChild(myAlert);
function alert(str){
	document.getElementById('myAlert').style.display='block';
	document.getElementById('alerttext').innerHTML=str;
}