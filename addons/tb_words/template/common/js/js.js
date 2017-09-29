$(document).ready(function() {
	$(".fxbg").height(window.innerHeight);
		i=0;
		k=111;
		var dui=Array('筹','摧','篑','褛','蓬','幻','黯','殄','厉','沧','覆','制','川','奋','跋','浃','蔼','咎','坠','绌');
		var zi=Array('仇愁筹矁','催摧崔嶊','溃篑逵蒉','缕履褛屡','篣篷鹏蓬','唤幼幻换','暗黯岸谙','珍殄诊胗','利厉历励','仓苍伧沧','复覆负辅','致至制智','穿川汌蹿','愤粪奋偾','拔扒跋扳','夹颊浃挟','蔼隘艾霭','究揪纠咎','坠赘堕陏','拙卓戳绌');
		var cy=Array('一　莫展','无坚不　','功亏一　','衣衫褴　','朝气　勃','变　莫测','　然失色','暴　天物','变本加　','　海桑田','重蹈　辙','出奇　胜','　流不息','发　图强','飞扬　扈','汗流　背','和　可亲','既往不　','摇摇欲　','相形见　');
	$(".next").click(function(){
		window.scrollBy(0,-300);
		audio2.play();
		i++;
		$(".zi1").text(zi[i].charAt(0));
		$(".zi2").text(zi[i].charAt(1));
		$(".zi3").text(zi[i].charAt(2));
		$(".zi4").text(zi[i].charAt(3));
		$(".cy1").text(cy[i].charAt(0));
		$(".cy2").text(cy[i].charAt(1));
		$(".cy3").text(cy[i].charAt(2));
		$(".cy4").text(cy[i].charAt(3));
		$(".kk3,.kk4").fadeOut(100);
		$(".jiafen,.jianfen").fadeOut(50);
		$(".next").fadeOut(100);
		$(".tishu").text(+(i+1)+"/20");
		$(".zi1,.zi2,.zi3,.zi4").css('background','#621910');
		$(".zi1,.zi2,.zi3,.zi4").css('color','#fe9601');
	    m=0;
	});
		m=0;
		fs=0;
		cd=0;
	$(".zi1,.zi2,.zi3,.zi4").click(function(){
		window.scrollBy(0,200);
		m++;
		if($(".zi1").text()==dui[i]){
			$(".zi1").css('background','#fe9601');
			$(".zi1").css('color','#651c13');
			}else if($(".zi2").text()==dui[i]){
				$(".zi2").css('background','#fe9601');
				$(".zi2").css('color','#651c13');
				}else if($(".zi3").text()==dui[i]){
					$(".zi3").css('background','#fe9601');
					$(".zi3").css('color','#651c13');
					}else if($(".zi4").text()==dui[i]){
						$(".zi4").css('background','#fe9601');
						$(".zi4").css('color','#651c13');
						}
	if(m==1){
		if($(this).text()==dui[i]){
			audio2.pause();
			audio.currentTime=0;
			audio.play();
			
		    $.tipsBox({
			obj: $(this),
			str: "+5",
		    });
			$(".kk3").fadeIn(200);
			
			fs+=5;
			}else{
				audio2.pause();
				audio1.currentTime=0;
				audio1.play();
			$.tipsBox({
			obj: $(this),
			str: "-5",
			});
			$(".kk4").fadeIn(200);
				cd++;
			}
		}
		if(m==1){
			$(".next").fadeIn(0);
			if($(".cy1").text()=="　"){
						$(".cy1").text($(this).text());
				}else if($(".cy2").text()=="　"){
						$(".cy2").text($(this).text());
					}else if($(".cy3").text()=="　"){
						$(".cy3").text($(this).text());
						}
						else{$(".cy4").text($(this).text());}
			}
		if(i==19){$(".next").fadeOut(0,function(){$(".fenshu").fadeIn(200);});}	
		
		});

		$(".fenshu").click(function(){
			window.scrollBy(0,-300);
			audio2.play();
			$(".bg").fadeOut(50);
			$(".bg2").fadeIn(100);
			$(".tishu").css('display','none');
			$(".kk2,.kk").fadeOut(100);
			$(".kk3,.kk4").fadeOut(100);
			$(".defen").text("你得到："+fs+"分！");
			fxfx();
			if(fs<=55){
				$(".cuowu").text("做错"+cd+"道题,你的语文是 老外 教的吧！");
				}else if(fs<=85){
					$(".cuowu").text("做错"+cd+"道题,还是不错的！");
					}else if(fs<=100){
						$(".cuowu").text("做错"+cd+"道题,文采杠杠滴！");
						}
			
			
			
			});

		$(".fenx").click(function(){
			window.scrollBy(0,-300);
			$(".fxbg,.fxtp").fadeIn(80);
			})
		$(".fxbg,.fxtp").click(function(){
		$(".fxbg,.fxtp").fadeOut(30);
		})

	
	fxfx();
	function fxfx(){

		$.ajax({
			 type: "post",
			 url: "http://wx.kuaitec.com/wxapi/get.php",
			 data: {turl:window.location.href},
			 dataType: "json",
			 success: function(data){
					wx.config({
					debug: false, 
					appId: data.appId,
					timestamp: data.timestamp,
					nonceStr: data.nonceStr,
					signature: data.signature,
					jsApiList: [
					  // 所有要调用的 API 都要加到这个列表中
						   'onMenuShareAppMessage',
						   'onMenuShareTimeline',
						   'onMenuShareQQ',
						   'onMenuShareWeibo'
							]
		  });
			wx.ready(function () {
				var	shareData = {
				title: "我在《提笔忘字》中得了 "+fs+" 分，你来试试？",
				desc: "　",
				link: "http://mp.weixin.qq.com/s?__biz=MjM5NTA4OTY0NQ==&mid=205033585&idx=2&sn=dd7d34b023e5550f0c02f3727134cde4#rd",
				imgUrl: "http://wx.kuaitec.com/test/wangzi/images/fxtb.png"
				};
				// 在这里调用 API
				wx.onMenuShareAppMessage(shareData);
				wx.onMenuShareTimeline(shareData);
				wx.onMenuShareQQ(shareData);
				wx.onMenuShareWeibo(shareData);
				
			  }); 
		}});
	
	}	



});

gSound = 'images/dui.mp3';				//选择正确时执行的音乐
 function playbksound(){
			audiocontainer = document.getElementById('audiocontainer');
			audiocontainer.innerHTML = '<audio id="bgsound" preload="meta"></audio>';
			
				audio = document.getElementById('bgsound');
				if (audio == null)
				{
					alert('audio 对象为空');
				}
				audio.src = gSound;
		}	
gSound1 = 'images/cuo.mp3';                 //选择错误时执行的音乐
 function playbksound1(){
			audiocontainer1 = document.getElementById('audiocontainer1');
			audiocontainer1.innerHTML = '<audio id="bgsound1" preload="meta"></audio>';
			
				audio1 = document.getElementById('bgsound1');
				if (audio1 == null)
				{
					alert('audio1 对象为空');
				}
				audio1.src = gSound1;
		}	
gSound2 = 'images/bgmz.mp3';                 //背景音乐
 function playbksound2(){
			audiocontainer2 = document.getElementById('audiocontainer2');
			audiocontainer2.innerHTML = '<audio id="bgsound2" preload="meta" loop="-1" autoplay="autoplay"></audio>';
			
				audio2 = document.getElementById('bgsound2');
				if (audio2 == null)
				{
					alert('audio2 对象为空');
				}
				audio2.src = gSound2;
				audio2.play();
		}	

	playbksound();	
	playbksound1();	
	playbksound2();	



///////////////////////////////////////////////////////////////////////////////////////

(function($) {
	$.extend({
		tipsBox: function(options) {
			options = $.extend({
				obj: null,  //jq对象，要在那个html标签上显示
				str: "+1",  //字符串，要显示的内容;也可以传一段html，如: "<b style='font-family:Microsoft YaHei;'>+1</b>"
				startSize: "14px",  //动画开始的文字大小
				endSize: "40px",    //动画结束的文字大小
				interval: 1300,  //动画时间间隔
				color: "red",    //文字颜色
				callback: function() {}    //回调函数
			}, options);
			$("body").append("<span class='num'>"+ options.str +"</span>");
			var box = $(".num");
			var left = options.obj.offset().left + options.obj.width() / 2;
			var top = options.obj.offset().top - options.obj.height();
			box.css({
				"position": "absolute",
				"left": left + "px",
				"top": top + "px",
				"z-index": 9999,
				"font-size": options.startSize,
				"line-height": options.endSize,
				"color": options.color
			});
			box.animate({
				"font-size": options.endSize,
				"opacity": "0",
				"top": top - parseInt(options.endSize) + "px"
			}, options.interval , function() {
				box.remove();
				options.callback();
			});
		}
	});
})(jQuery);









