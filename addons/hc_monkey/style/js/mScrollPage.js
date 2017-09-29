(function(window, document, exportName, undefined) {
	var mScrollPage = function(){};

	mScrollPage.prototype = {
		scroller:{},
		photos:[],
		photoSize:{},
		direction:'vertical',
		init:function(){
			this.scroller.attr('thisId',0);
			this.scroller.attr('running',0);
			this.scroller.attr('direction',this.direction);
			scroller=this.scroller;
			direction=this.direction;


			//如果有指定图片
			if(this.photos.length>0){
				makePhotoList(this.photos,this.scroller);
			}
			that=this;
			hammer = new Hammer(this.scroller[0],{direction: Hammer.DIRECTION_ALL});
			hammer.on("panend", swipeingOnAction);
			hammer.on("pan", swipeingBindAction);

			//如果有全屏背景的情况下，处理背景尺寸
			if(this.photoSize.width!=undefined || this.photoSize.height!=undefined){
				photoSize={width:this.photoSize.width,height:this.photoSize.height};
				setBGSize(this.photoSize,this.scroller);
				$(window).resize(function(){setBGSize(this.photoSize,this.scroller);})
			}
		},
		goto:function(toId){
			TweenMax.to(scroller, 0, {x:0,scrollTo:{x:0}});
			scroller.attr("thisId",0);
			photoScrollBegin(toId);
			// console.log(toId);
		},
		next:function(){
			photoScrollBegin(1);
		},
		pre:function(){
			photoScrollBegin(-1);
		},
		onBegin:function(fn){
			onBegin = fn;
		},
		onComplete:function(fn){
			onComplete = fn;
		},
	};

	function makePhotoList(photos,scroller){
		scroller.html('');
		scroller.append('<ul class="wrap"></ul>');
		for(i in photos){
			p=photos[i];
			scroller.find('ul.wrap').append('<li style="background:url('+p+') no-repeat center center;"></li>');
		}
	}


	//设置背景缩放
	function setBGSize(photoSize,scroller){
		toW=$(window).innerWidth();
		toH=$(window).height();
		scroller.find('>.wrap>li').css({'width':toW,'height':toH});

		bl=photoSize.width/photoSize.height;
		sw=$(window).width();
		sh=$(window).height();
		bl2=sw/sh;
		if(bl>bl2){
			scroller.find('li').css("background-size","auto 100%");
		}else{
			scroller.find('li').css("background-size","100% auto");
		}
	}

	function swipeingBindAction(ev){
		scroller = $(ev.target).parents("ul").parent();
		y=ev.deltaY;
		thisN=Number(scroller.attr("thisId"));
		toT=thisN*scroller.height()-y;
		TweenMax.to(scroller, 0, {scrollTo:{y:toT}});
	}

	function swipeingOnAction(ev){
		direction=scroller.attr('direction');
		if(direction=='vertical'){
			dh=0//$(".siteMain").width()*0.1;//拉动距离
			y=ev.deltaY;
			if( y <= -dh){
				photoScrollBegin(1);
			}
			if( y >= dh){
				photoScrollBegin(-1);
			}
		}
		if(direction=='horizontal'){
			dw=0//$(".siteMain").width()*0.1;//拉动距离
			x=ev.deltaX;
			if( x <= -dw){
				photoScrollBegin(1);
			}
			if( x >= dw){
				photoScrollBegin(-1);
			}
		}	}

	function photoScrollBegin(dir){
		running=scroller.attr("running")
		nextId = Number(scroller.attr("thisId"));
		photosLength = scroller.find("li").length;

		if(running==0){
			scroller.attr("running",1);
			if(dir==undefined){
				nextId++;
			}else{
				nextId+=dir;
			}

			//限定在图片数量范围内
			if(nextId<0 || nextId>photosLength-1){
				scroller.attr("running",0);
				return false;
			}

			if(direction=='vertical'){
				toT=scroller.height()*nextId;
				TweenMax.to(scroller, 0.3, {x:0,scrollTo:{y:toT}, onComplete:photoScrollEnd, onCompleteParams:[nextId]});
			}
			if(direction=='horizontal'){
				toL=scroller.width()*nextId;
				TweenMax.to(scroller, 0.3, {x:0,scrollTo:{x:toL}, onComplete:photoScrollEnd, onCompleteParams:[nextId]});
			}

			if(typeof(onBegin) != 'undefined'){
				onBegin(nextId,scroller);
			}
		}
	}
	
	function photoScrollEnd(nextId){
		scroller.attr("thisId",nextId);
		scroller.attr("running",0);
		if(typeof(onComplete) != 'undefined'){
			onComplete(nextId);
		}
	}
	window[exportName] = mScrollPage;
})(window, document, 'mScrollPage');
