	//设置TweenMax的3D精度
	// CSSPlugin.defaultTransformPerspective = 900;

	//分享题图
	$("body").prepend('<div style="display:none;"><img src="../addons/hc_monkey/style/images/cover400.png"></div>')
	//测试图层
	$("body").prepend('<div class="testLayer"></div>')
	//ajaxLoading
	$("body").prepend('<div class="ajaxLoading"></div>')
	//分享图层
	$("body").prepend('<div class="shareLayer"><div class="p0"><img src="../addons/hc_monkey/style/images/share_p0.png"></div></div>')
	//背景音乐
	// $("body").prepend('<audio class="musicBox" id="musicBox" preload="metadata" src="mp3/bg.mp3">')
	//分享关闭
	$('.shareLayer').hammer({}).on("tap", function(){
		closeDivOpen($(this));
	})


 	$(function(){
 		if($.cookie('musicOpen')!=0){
 			//musicBox.loop = true;
			//musicBox.play();
			TweenMax.to($('.musicBtn'),0.3,{autoAlpha:1});
		}else{
			//musicBox.pause();
			TweenMax.to($('.musicBtn'),0.3,{autoAlpha:0.5});
		}

		$('.testLayer').click(function(){
 			location.reload();
 		});
		$('.testLayer').hammer({}).on("tap press pan swipe", function(){
			location.reload();
		})
 		//音乐按钮
		// $('.musicBtn').hammer({}).on("tap press pan swipe", function(){
		// 	if(musicBox.paused == true){
		// 		$.cookie('musicOpen',1);
		// 		// musicBox.play();
		// 		TweenMax.to($(this),0.3,{autoAlpha:1})
		// 	}else{
		// 		$.cookie('musicOpen',0);
		// 		// musicBox.pause();
		// 		TweenMax.to($(this),0.3,{autoAlpha:0.5})
		// 	}
		// })


		//所有弹出框的分享按钮
		// $('.msgbox .shareBtn').each(function(){
		// 	$(this).hammer({}).on("tap press", function(){
		// 		buttonAnmi($(this));
		// 		// $(this).parents('.msgbox').fadeOut();
		// 		$('.shareLayer').fadeIn(300);
		// 	})
		// })

		TweenMax.to($('.msgbox'),0,{x:0,autoAlpha:1})

		setFullScreenSzie();
	});
	
	$(window).resize(function(){
		setFullScreenSzie();
	})
	
	//按钮震动
	function buttonAnmi(obj,fn,param){
		TweenMax.to(obj,0.1,{autoAlpha:0.5,repeat:1,yoyo:true});
		if(fn!='undefined' || fn!=undefined || fn!=''){
			if(typeof(fn)=='function'){
				setTimeout(fn,200,param);
			}else{
				setTimeout(fn,200);
			}
		}
	}
	//打开隐藏弹窗
	function openDivOpen(obj){
		TweenMax.fromTo(obj,0,{x:0, display:'block', autoAlpha:0},{x:0, display:'block', autoAlpha:1});
	}
	//关闭弹窗
	function closeDivOpen(obj){

		TweenMax.to(obj,0,{x:0, autoAlpha:0, display:'none'});
	}
	
	//设置tableCellHeight定高
	function setFullScreenSzie(){
		$(".fullScreenSzie").each(function(){
			toW=$(window).innerWidth();
			toH=$(window).height();
	
			$(this).width(toW);
			$(this).height(toH);
		});
	}

	//设置背景缩放
	function setBGSize(ow,oh){
		bl=ow/oh;
		sw=$(window).width();
		sh=$(window).height();
		bl2=sw/sh;
		if(bl>bl2){
			$('.dynamicBG').css("background-size","auto 100%");
			$('.dynamicBgShowAll').css("background-size","100% auto");

		}else{
			$('.dynamicBG').css("background-size","100% auto");
			$('.dynamicBgShowAll').css("background-size","auto 100%");
		}
	}	
	
	//取url参数
	(function($){
		$.getUrlParam = function(name){
		var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if (r!=null) return decodeURI(r[2]); return null;}
	})(jQuery);
	
	function getRandom(){
		return Math.round(Math.random()*999999999);
	}
