/*!
 * jQuery Transit - CSS3 transitions and transformations
 * (c) 2011-2012 Rico Sta. Cruz <rico@ricostacruz.com>
 * MIT Licensed.
 *
 * http://ricostacruz.com/jquery.transit
 * http://github.com/rstacruz/jquery.transit
 */
(function(k){k.transit={version:"0.9.9",propertyMap:{marginLeft:"margin",marginRight:"margin",marginBottom:"margin",marginTop:"margin",paddingLeft:"padding",paddingRight:"padding",paddingBottom:"padding",paddingTop:"padding"},enabled:true,useTransitionEnd:false};var d=document.createElement("div");var q={};function b(v){if(v in d.style){return v}var u=["Moz","Webkit","O","ms"];var r=v.charAt(0).toUpperCase()+v.substr(1);if(v in d.style){return v}for(var t=0;t<u.length;++t){var s=u[t]+r;if(s in d.style){return s}}}function e(){d.style[q.transform]="";d.style[q.transform]="rotateY(90deg)";return d.style[q.transform]!==""}var a=navigator.userAgent.toLowerCase().indexOf("chrome")>-1;q.transition=b("transition");q.transitionDelay=b("transitionDelay");q.transform=b("transform");q.transformOrigin=b("transformOrigin");q.transform3d=e();var i={transition:"transitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd",WebkitTransition:"webkitTransitionEnd",msTransition:"MSTransitionEnd"};var f=q.transitionEnd=i[q.transition]||null;for(var p in q){if(q.hasOwnProperty(p)&&typeof k.support[p]==="undefined"){k.support[p]=q[p]}}d=null;k.cssEase={_default:"ease","in":"ease-in",out:"ease-out","in-out":"ease-in-out",snap:"cubic-bezier(0,1,.5,1)",easeOutCubic:"cubic-bezier(.215,.61,.355,1)",easeInOutCubic:"cubic-bezier(.645,.045,.355,1)",easeInCirc:"cubic-bezier(.6,.04,.98,.335)",easeOutCirc:"cubic-bezier(.075,.82,.165,1)",easeInOutCirc:"cubic-bezier(.785,.135,.15,.86)",easeInExpo:"cubic-bezier(.95,.05,.795,.035)",easeOutExpo:"cubic-bezier(.19,1,.22,1)",easeInOutExpo:"cubic-bezier(1,0,0,1)",easeInQuad:"cubic-bezier(.55,.085,.68,.53)",easeOutQuad:"cubic-bezier(.25,.46,.45,.94)",easeInOutQuad:"cubic-bezier(.455,.03,.515,.955)",easeInQuart:"cubic-bezier(.895,.03,.685,.22)",easeOutQuart:"cubic-bezier(.165,.84,.44,1)",easeInOutQuart:"cubic-bezier(.77,0,.175,1)",easeInQuint:"cubic-bezier(.755,.05,.855,.06)",easeOutQuint:"cubic-bezier(.23,1,.32,1)",easeInOutQuint:"cubic-bezier(.86,0,.07,1)",easeInSine:"cubic-bezier(.47,0,.745,.715)",easeOutSine:"cubic-bezier(.39,.575,.565,1)",easeInOutSine:"cubic-bezier(.445,.05,.55,.95)",easeInBack:"cubic-bezier(.6,-.28,.735,.045)",easeOutBack:"cubic-bezier(.175, .885,.32,1.275)",easeInOutBack:"cubic-bezier(.68,-.55,.265,1.55)"};k.cssHooks["transit:transform"]={get:function(r){return k(r).data("transform")||new j()},set:function(s,r){var t=r;if(!(t instanceof j)){t=new j(t)}if(q.transform==="WebkitTransform"&&!a){s.style[q.transform]=t.toString(true)}else{s.style[q.transform]=t.toString()}k(s).data("transform",t)}};k.cssHooks.transform={set:k.cssHooks["transit:transform"].set};if(k.fn.jquery<"1.8"){k.cssHooks.transformOrigin={get:function(r){return r.style[q.transformOrigin]},set:function(r,s){r.style[q.transformOrigin]=s}};k.cssHooks.transition={get:function(r){return r.style[q.transition]},set:function(r,s){r.style[q.transition]=s}}}n("scale");n("translate");n("rotate");n("rotateX");n("rotateY");n("rotate3d");n("perspective");n("skewX");n("skewY");n("x",true);n("y",true);function j(r){if(typeof r==="string"){this.parse(r)}return this}j.prototype={setFromString:function(t,s){var r=(typeof s==="string")?s.split(","):(s.constructor===Array)?s:[s];r.unshift(t);j.prototype.set.apply(this,r)},set:function(s){var r=Array.prototype.slice.apply(arguments,[1]);if(this.setter[s]){this.setter[s].apply(this,r)}else{this[s]=r.join(",")}},get:function(r){if(this.getter[r]){return this.getter[r].apply(this)}else{return this[r]||0}},setter:{rotate:function(r){this.rotate=o(r,"deg")},rotateX:function(r){this.rotateX=o(r,"deg")},rotateY:function(r){this.rotateY=o(r,"deg")},scale:function(r,s){if(s===undefined){s=r}this.scale=r+","+s},skewX:function(r){this.skewX=o(r,"deg")},skewY:function(r){this.skewY=o(r,"deg")},perspective:function(r){this.perspective=o(r,"px")},x:function(r){this.set("translate",r,null)},y:function(r){this.set("translate",null,r)},translate:function(r,s){if(this._translateX===undefined){this._translateX=0}if(this._translateY===undefined){this._translateY=0}if(r!==null&&r!==undefined){this._translateX=o(r,"px")}if(s!==null&&s!==undefined){this._translateY=o(s,"px")}this.translate=this._translateX+","+this._translateY}},getter:{x:function(){return this._translateX||0},y:function(){return this._translateY||0},scale:function(){var r=(this.scale||"1,1").split(",");if(r[0]){r[0]=parseFloat(r[0])}if(r[1]){r[1]=parseFloat(r[1])}return(r[0]===r[1])?r[0]:r},rotate3d:function(){var t=(this.rotate3d||"0,0,0,0deg").split(",");for(var r=0;r<=3;++r){if(t[r]){t[r]=parseFloat(t[r])}}if(t[3]){t[3]=o(t[3],"deg")}return t}},parse:function(s){var r=this;s.replace(/([a-zA-Z0-9]+)\((.*?)\)/g,function(t,v,u){r.setFromString(v,u)})},toString:function(t){var s=[];for(var r in this){if(this.hasOwnProperty(r)){if((!q.transform3d)&&((r==="rotateX")||(r==="rotateY")||(r==="perspective")||(r==="transformOrigin"))){continue}if(r[0]!=="_"){if(t&&(r==="scale")){s.push(r+"3d("+this[r]+",1)")}else{if(t&&(r==="translate")){s.push(r+"3d("+this[r]+",0)")}else{s.push(r+"("+this[r]+")")}}}}}return s.join(" ")}};function m(s,r,t){if(r===true){s.queue(t)}else{if(r){s.queue(r,t)}else{t()}}}function h(s){var r=[];k.each(s,function(t){t=k.camelCase(t);t=k.transit.propertyMap[t]||k.cssProps[t]||t;t=c(t);if(k.inArray(t,r)===-1){r.push(t)}});return r}function g(s,v,x,r){var t=h(s);if(k.cssEase[x]){x=k.cssEase[x]}var w=""+l(v)+" "+x;if(parseInt(r,10)>0){w+=" "+l(r)}var u=[];k.each(t,function(z,y){u.push(y+" "+w)});return u.join(", ")}k.fn.transition=k.fn.transit=function(z,s,y,C){var D=this;var u=0;var w=true;if(typeof s==="function"){C=s;s=undefined}if(typeof y==="function"){C=y;y=undefined}if(typeof z.easing!=="undefined"){y=z.easing;delete z.easing}if(typeof z.duration!=="undefined"){s=z.duration;delete z.duration}if(typeof z.complete!=="undefined"){C=z.complete;delete z.complete}if(typeof z.queue!=="undefined"){w=z.queue;delete z.queue}if(typeof z.delay!=="undefined"){u=z.delay;delete z.delay}if(typeof s==="undefined"){s=k.fx.speeds._default}if(typeof y==="undefined"){y=k.cssEase._default}s=l(s);var E=g(z,s,y,u);var B=k.transit.enabled&&q.transition;var t=B?(parseInt(s,10)+parseInt(u,10)):0;if(t===0){var A=function(F){D.css(z);if(C){C.apply(D)}if(F){F()}};m(D,w,A);return D}var x={};var r=function(H){var G=false;var F=function(){if(G){D.unbind(f,F)}if(t>0){D.each(function(){this.style[q.transition]=(x[this]||null)})}if(typeof C==="function"){C.apply(D)}if(typeof H==="function"){H()}};if((t>0)&&(f)&&(k.transit.useTransitionEnd)){G=true;D.bind(f,F)}else{window.setTimeout(F,t)}D.each(function(){if(t>0){this.style[q.transition]=E}k(this).css(z)})};var v=function(F){this.offsetWidth;r(F)};m(D,w,v);return this};function n(s,r){if(!r){k.cssNumber[s]=true}k.transit.propertyMap[s]=q.transform;k.cssHooks[s]={get:function(v){var u=k(v).css("transit:transform");return u.get(s)},set:function(v,w){var u=k(v).css("transit:transform");u.setFromString(s,w);k(v).css({"transit:transform":u})}}}function c(r){return r.replace(/([A-Z])/g,function(s){return"-"+s.toLowerCase()})}function o(s,r){if((typeof s==="string")&&(!s.match(/^[\-0-9\.]+$/))){return s}else{return""+s+r}}function l(s){var r=s;if(k.fx.speeds[r]){r=k.fx.speeds[r]}return o(r,"ms")}k.transit.getTransitionValue=g})(jQuery);
/* Modernizr 2.8.3 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-borderradius-cssanimations-csstransitions-canvas-draganddrop-localstorage-testprop-testallprops-hasevent-domprefixes
 */
;window.Modernizr=function(a,b,c){function x(a){i.cssText=a}function y(a,b){return x(prefixes.join(a+";")+(b||""))}function z(a,b){return typeof a===b}function A(a,b){return!!~(""+a).indexOf(b)}function B(a,b){for(var d in a){var e=a[d];if(!A(e,"-")&&i[e]!==c)return b=="pfx"?e:!0}return!1}function C(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:z(f,"function")?f.bind(d||b):f}return!1}function D(a,b,c){var d=a.charAt(0).toUpperCase()+a.slice(1),e=(a+" "+m.join(d+" ")+d).split(" ");return z(b,"string")||z(b,"undefined")?B(e,b):(e=(a+" "+n.join(d+" ")+d).split(" "),C(e,b,c))}var d="2.8.3",e={},f=b.documentElement,g="modernizr",h=b.createElement(g),i=h.style,j,k={}.toString,l="Webkit Moz O ms",m=l.split(" "),n=l.toLowerCase().split(" "),o={},p={},q={},r=[],s=r.slice,t,u=function(){function d(d,e){e=e||b.createElement(a[d]||"div"),d="on"+d;var f=d in e;return f||(e.setAttribute||(e=b.createElement("div")),e.setAttribute&&e.removeAttribute&&(e.setAttribute(d,""),f=z(e[d],"function"),z(e[d],"undefined")||(e[d]=c),e.removeAttribute(d))),e=null,f}var a={select:"input",change:"input",submit:"form",reset:"form",error:"img",load:"img",abort:"img"};return d}(),v={}.hasOwnProperty,w;!z(v,"undefined")&&!z(v.call,"undefined")?w=function(a,b){return v.call(a,b)}:w=function(a,b){return b in a&&z(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=s.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(s.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(s.call(arguments)))};return e}),o.canvas=function(){var a=b.createElement("canvas");return!!a.getContext&&!!a.getContext("2d")},o.draganddrop=function(){var a=b.createElement("div");return"draggable"in a||"ondragstart"in a&&"ondrop"in a},o.borderradius=function(){return D("borderRadius")},o.cssanimations=function(){return D("animationName")},o.csstransitions=function(){return D("transition")},o.localstorage=function(){try{return localStorage.setItem(g,g),localStorage.removeItem(g),!0}catch(a){return!1}};for(var E in o)w(o,E)&&(t=E.toLowerCase(),e[t]=o[E](),r.push((e[t]?"":"no-")+t));return e.addTest=function(a,b){if(typeof a=="object")for(var d in a)w(a,d)&&e.addTest(d,a[d]);else{a=a.toLowerCase();if(e[a]!==c)return e;b=typeof b=="function"?b():b,typeof enableClasses!="undefined"&&enableClasses&&(f.className+=" "+(b?"":"no-")+a),e[a]=b}return e},x(""),h=j=null,e._version=d,e._domPrefixes=n,e._cssomPrefixes=m,e.hasEvent=u,e.testProp=function(a){return B([a])},e.testAllProps=D,e}(this,this.document);
;(function(g){
	var danmu = g.danmu = {
			// 未播放消息列表暂存
		    _msgList: [],

		    // 暂时未找到通道的弹幕列表
		    _prePlayList:[],
		    _prePlayMsgs:[],//暂时未找到通道的弹幕消息列表
		    _danmLastId: 0,
		    // 已经或者在待播放队列
		    _msgPlayedList:[],
		    
		    // 已经播放过的
		    _msgPlayed:[],
		    _msgPlayedNum : 0,// 已播数量
		    
		    // 是否有新消息
		    _getNewMsg:false,
		    
		    // 最大的移动速度 px/s, 超过此值就不能看清了 2004/6
		    _Vmax: 180,

		    // 最小的移动速度px/s: 低于此值体现不出移动的特点来 2304/26
		    _Vmin: 20,
		    // 可见时间值，此值受限于弹幕速度 s
		    // 任何长度的弹幕在可见区域内的总时间是n秒，可以调整此值来改变平均速度
		    // 默认值为 (WW/Vmax + WW/Vmin)/2
		    _Tvisible: null,

		    // 弹幕间距 px
		    _Dmargin: 60,

		    // 定时器的间隔 ms
		    _playCheckTime: 500,
		    
		    // 消息总数
		    _totalMsg:0,
		    
		    // 轮询时间间隔
		    _pullCheckTime: 5000,
		    _pullTimer: null,
		    _pullAjaxObj: null,
		    _conf: null
	}
	
	danmu.init = function(){
		//$(".js_danmuBox").css({ top:'164px' });
		this._WW  = $(window).width();//屏幕宽度
	    this._WH  = $(window).height();//屏幕高度
	    this._Tvisible = parseFloat((this._WW/this._Vmax + this._WW/this._Vmin)/2).toFixed(2)*1;//最大速度+最小速度除以2等于平均
	    this._lineNum = danmu.max_height();
	    this._playState = 'played';
	    danmu.playCheckOnce();
	}
	
	danmu.bindEvent = function(){
		
		danmu.init();
		$(window).on('resize', function(){
		    danmu.init();
		});
	}
	
	// 停止轮询
	  danmu.pullStop = function(){
	    clearTimeout(this._pullTimer);
	    this._pullTimer = null;
	    if (this._pullAjaxObj) this._pullAjaxObj.abort();
	    this._pullAjaxObj = null;
	    return this;
	  };
	/**
	 * 添加消息
	 */
	danmu.add = function(list){
	    if(!this._msgPlayed) this._msgPlayed = [];
	    if (!list || !list.length) return false;
	    var i=0; mL = list.length;
	    this._danmLastId = list[list.length -1].id;
	    danmu._msgList = danmu._msgList.concat(list);
	};
	
	/**
	 * 播放
	 */
	danmu.playCheckOnce = function(){
		var d, ret, bp_time,tmpList = [];
	    // 将preList填满//13
	    var diffNum = this._lineNum - this._prePlayList.length;
	    if(diffNum > 0) {
	      var msgs = this.getMessage(diffNum);//13
	      if (msgs.length) {
			  for(var i = 0; i < msgs.length; i++) {
			      d = this.renderOne(msgs[i]);
			      if(d) this._prePlayList.push(d);
			    }
	      }
	    }
	    
	    // 循环preList
	    for(var i = 0; i < this._prePlayList.length; i++) {
	      d   = this._prePlayList[i];
	      ret = this.setLine(d);
	      if(true !== ret) {
	        // 放入待播放列表
	        tmpList.push(d);
	      } else {
	    	//var ll = $(".js_danmuItem:visible").length;
	    	//$(".js_tips").html('有动画了 弹道数量'+ ll + '弹道高度' + $(".js_danmuBox").offset().top);
	        // 放入动画 contains canvas render
	    	danmu._msgPlayedNum ++;
	        this.animateOne(d);
	      }
	    }
	    // 重置preList
	    this._prePlayList = tmpList;
	    if(this._playState == 'played') {
	      this._playerTimer = setTimeout(function(){
	        danmu.playCheckOnce();
	      }, this._playCheckTime);
	    }
	};
	
	danmu.pauseStop = function(){
		if(this._playerTimer) {
	      clearTimeout(this._playerTimer);
	      this._playerTimer = null;
	    }
	    $('.js_danmuContent').remove();
	    this.pullStop();
	    this._playState = 'stoped';
	}
	
	/**
	 * 停止弹幕
	 */
	 danmu.stop = function(){
		
	    if(this._playerTimer) {
	      clearTimeout(this._playerTimer);
	      this._playerTimer = null;
	    }
	    
	    $('.js_danmuContent').remove();
	    
	    this._prePlayList = [];
	    this.pullStop();

	    this._playState = 'stoped';
    };
	
	/**
	 * 为指定弹幕设置一个弹道
	 */
	  danmu.setLine = function(d){
	    // 加上间距
	    var Sn = this.data(d, 'width');
	    var Tn = this.getDuration(d);
	    var Vn = (Sn+this._WW) / Tn;
	    
	    // 通道中的尾部元素
	    var lineLastD, ret = false;
	    var Sn_1, Tn_1, Vn_1, Tn_1_past, tn, tn_1;
	    var now = (new Date).getTime();

	    // 遍历通道
	    var totalLineNum = this._lineNum;
		
	    for(var i = 0; i < totalLineNum; i++) {
		  lineLastD = this.getLastD(i);
	      // 通道为空，直接可用
	      if(!lineLastD) {
	        ret = this.appendD(d, i);
	        break;
	      }else{
		   if(i!=totalLineNum){
				continue;
		   }else{
				 var r = Math.random() * (totalLineNum);
				 var re = Math.round(r);
                 var random_num = Math.max(Math.min(re, totalLineNum),0)
				 ret = this.appendD(d, random_num);
				 break;
		   }
			
		  }
	      Sn_1      = this.data(lineLastD, 'width');
	      Tn_1      = this.getDuration(lineLastD);
	      Vn_1      = (Sn_1+this._WW) / Tn_1;
	      Tn_1_past = (now - this.data(lineLastD, 'start_t'))/ 1000;
	      
	      if(Tn_1_past*Vn_1 < Sn_1) {
	        continue;
	      }

	      tn   = this._WW / Vn;
	      tn_1 = (this._WW - Vn_1*Tn_1_past + Sn_1) / Vn_1;
	      
	      if(tn_1 < tn) {
		      ret = this.appendD(d, i);
		      break;
		  }
		 
	    }
	    return ret;
	  };
	  
	/**
	 * 本条弹幕所需时间
	 */
	danmu.getDuration = function(d) {
		var bp_time = d.attr("data-bp_time");
	    var dw = this.data(d, 'width'),
	      s = dw + this._WW,
	      v,
	      duration;
	    v = s/this._Tvisible;//可视化速度
	    if(v < this._Vmin) {//此条弹幕比最小还小 就等于最小
	      duration = s/this._Vmin;
	    } else if (v > this._Vmax) {//比最大还大就等于最大
	      duration = s/this._Vmax;
	    } else {//处于最大与最小之间就等于平均
	      duration = bp_time;
	    }
	    return parseFloat(duration).toFixed(2)*1;
	};
	/**
	 * 将元素放入弹道
	 */
	  danmu.appendD = function(d, i){
	    var ret;
        d.appendTo('#js_dRow'+i);
        //d.show();
        ret = true;
	    return ret;
	  };  

	
	/**
	 * 渲染
	 */
	danmu.renderOne = function(msg){
		var d;
		var random_color = this.color();
		//if(msg.type == 'image') return d;
		var msgDom = '<span class="js_danmuContent" style="position:absolute; top:0px; left:0px;">\
			      <dt><img width="28" height="28" src="'+msg.avatar+'" style="border-color:'+random_color+'"></dt>';
		msgDom += '<dd style="margin-left:20px;color:'+random_color+';border-color:'+random_color+'">'+msg.content+'</dd>';
		msgDom += '</span>';
		d = $(msgDom).appendTo('body');
		var newWidth = d.width() + danmu._Dmargin;
		danmu.data(d, 'width', newWidth);
		if(Modernizr.csstransitions) {
	        d.css('x', this._WW);

			d.attr('data-bp_time',msg.bp_time);
	    } else {
	        d.css('left', this._WW);
	    }
		return d;
	}
	danmu.color = function(){
		 var hex = Math.floor(Math.random() * 16777216).toString(16); //生成ffffff以内16进制数
		 while (hex.length < 6) { //while循环判断hex位数，少于6位前面加0凑够6位
		  hex = '0' + hex;
		 }
		 return '#' + hex; //返回‘#'开头16进制颜色
	}
	/**
	 * 获取当前弹道的最后一个弹
	 */
	danmu.getLastD = function(i){
	      var d;
	      d = $('#js_dRow'+i+' .js_danmuContent:last');
		 // alert(d.length);
	      if(d.length==0) d = null;
	    return d;
	};
	
	/**
	 * 存储相关变量
	 */
	danmu.data = function(d, prop, val){
	    if('undefined' == typeof val) {
	      if('undefined' != typeof d['_'+prop]) return d['_'+prop];
	      if('function' == typeof d.data) return d.data(prop);
	      return;
	    }

	    if('function' == typeof d.data) {
	      d.data(prop, val);
	    } else {
	      d['_'+prop] = val;
	    }
	  };
	
	/**
	 * 动画
	 */
	danmu.animateOne = function(d)
	{
		var now =  (new Date).getTime();
	    this.data(d, 'start_t', now);
	    var dw = this.data(d, 'width');
	    if(Modernizr.csstransitions) {
	      d.transition({
	        x: -dw,
	        duration: this.getDuration(d)*1000,//d.attr("data-bp_time"),
	        easing: 'linear',
	        complete: function(){
	          //$(".js_tips").html('');
	          $(this).remove();
	        }
	      });
	    } else {
	      d.animate({
	        left: -dw
	      }, this.getDuration(d)*1000, 'linear', function(){
	        $(this).remove();
	      });
	    }
	    return d;
	}
	
	// 可暂停弹幕
	  danmu.animateOneInterv = function(d)
	  {
		    var now =  (new Date).getTime();
		    this.data(d, 'start_t', now);
		    // canvas invoke
		    if(this.stage) return this.animateOneCanvas(d);

		    var dw = this.data(d, 'width');
		    var timeLeng = danmu.getDuration(d) * 1000;
		    var mostL = danmu._WW + dw + dw;
		    var vSpeed = danmu.getSpeed(d);
		    var vSpeed = vSpeed / 200;
		    var timeCount = 0;
		    var leftX = 0;
		    //d.css('left', danmu._WW+'px');
		    var moveInter = setInterval(function(){
		    	timeCount += 0.05;
		    	leftX += vSpeed;
		    	//console.log('timecount, timeLeng', timeCount, timeLeng);
		    	danmu.data(d, 'hasmoveD', leftX);
		    	
		    	if (leftX > mostL) {
		    		d.remove();
		    		//danmu.deleteByElem(moveInter, danmu._intervalArr);
		    		clearInterval(moveInter);
		    		return ;
		    	}
		    	d.css('left', -leftX+'px');
		     }, 5);
	  }
	  
	// 获取弹幕运动速度
	  danmu.getSpeed = function(d)
	  {
		  var dw = this.data(d, 'width'),
	      s = dw + this._WW,
	      v,
	      duration;

	    v = s/this._Tvisible;
	    
	    if(v < this._Vmin) {
	      return this._Vmin;
	    } else if (v > this._Vmax) {
	      return this._Vmax;
	    } else {
	      return v;
	    }
	  }
	
	danmu.max_height = function(){
		var can_row = 0;
		var wh = $(window).height();
		var can_height = [0,64,128,192,256,320,384,448,512,576,640,704,768,832,896,960,1024,1088,1152,1216,1280];
		for(var i=0;i<can_height.length;i++){
			if(wh<can_height[i]){
				can_row = i;
				break;
			}
		}
		if(can_row==0){
			can_row = 20;
		}
		return can_row;
	}
	/**
	 * 指定消息数量 
	 */
	danmu.getMessage = function(maxNum){
	    maxNum = maxNum || 1;
	    return this._msgList.splice(0, maxNum);
	  };
})(window);