var timerSiteLoading;
SiteLoadingNum=0;


function preLoad(preLoadImgArr){
	preLoad2(preLoadImgArr);
}
function preLoad2(preLoadImgArr){
	var imgLoadAll = 0;
	var imgList = new Array();
	imgList=preLoadImgArr;
	
	$("img").each(function(){
		src=$(this).attr("src");
		imgLoadAll++;
		imgList.push($(this).attr("src"));
	})
	imgList = getNoRepeat(imgList);

	var timeout = 3000;
	var count = 0;
	var fire = false;
	var fireFn = function(){
		
		fire = true;
		clearTimeout(timerSiteLoading);
		$(".siteLoading .txt span").text(per+'%');
		$(".main").css("opacity",1);
		siteLoaded();
		TweenMax.to($(".siteLoading"),0.3,{x:0,autoAlpha:0,display:'none',onComplete:remove})

		function remove(){
			$(".siteLoading").remove();
		}
	}

	for(var i = 0, len = imgList.length; i < len; i++){
		var img = new Image();
		var loadFn = function(e){
			count++;
			per=Math.floor((count/len)*100);
			$(".siteLoading .line").css('width',per+"%");
			$(".siteLoading .txt span").text(per+'%');

			bl=100/6;
			n=Math.floor(per/bl);

			if (count >= len && !fire) {
				fireFn();
			};
		}
		img.onload = loadFn;
		img.onerror = loadFn;
		img.src = imgList[i];
	}
}

//去除数组中重复值  
function getNoRepeat(s) {  
	return s.sort().join(",,").replace(/(,|^)([^,]+)(,,\2)+(,|$)/g,"$1$2$4").replace(/,,+/g,",").replace(/,$/,"").split(",");  
}

//Loading图层
sendSiteLoading();
function sendSiteLoading(){
	html='<div class="siteLoading">'
	html+='	<div class="content" style="width:'+window.screen.availWidth+'px;height:'+window.screen.availHeight+'px">'
	html+='		<div class="pic"><img src="../addons/hc_monkey/style/images/muzhi.gif"></div>'
	// html+='		<div class="line1"><div class="line2"></div></div>'
	html+='		<div class="txt">正在加载中...<span>0%</span></div>'
	html+='		<div class="footer">技术支持：拇指部落</div>'
	html+='		</div>'
	html+='</div>'
	document.write(html);
}