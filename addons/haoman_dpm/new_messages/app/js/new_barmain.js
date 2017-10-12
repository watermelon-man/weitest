"use strict";
window.onerror = function() {
	return !0
};
var vm = null;
!
function() {
	var isChrome = window.navigator.userAgent.indexOf("Chrome") !== -1;
	if (!isChrome) {
		return tools.chromeSupport()
	}
	var e = {
		isActive: 0,
		tonight: [],
		total: [],
		rid: RID,
		weid: WEID,
		enlargeQrcode: !1,
		enlargeQrcodeSeconds: 16,
		qrcodeImg: QRCODE_IMAGE,
		show: !1,
		textShow: !1,
		bpTextShow: !1,
		redtextShow:!1,
		redpackshow:!1,
		themeshow:!1,
		bp_zhutis:bp_zhutis,
		requestFlag: !0,
		number: -1,
		lasttime: -1,
		bpItems: [],
		max_msg_show: msg_display_num || 10,
		dsShow: !1,
		image_index: 0,
		paScreen: {
			id: "",
			weid: "",
			openid: "",
			content: "",
			nickname: "",
			avatar: "",
			sex: "",
			status: "",
			image: "",
			type: "1",
			isshow: "",
			createtime: "",
			rid: "",
			topenid: "",
			toName: "",
			theme:"",
			ms: 60,
			tocontent: "",
			extend_params: "",
			forwho: "",
			// send_nums: "",
			bp_time: 10,
			hb_detail: "",
			fee: "",
			send_nums: "",
			paytid: "",
			zt_name:"重金霸屏",
			color:"red"
		},
		themes: {
			id: 0,
			weid: 0,
			openid: "",
			messageid: 0,
			num: "",
			content: "",
			nickname: "",
			avatar: "",
			sex: 1,
			status: 0,
			image: "",
			type: "1",
			isshow: 1,
			createtime: 123,
			rid: 10,
			topenid: "",
			toName: "",
			ms: 60,
			tocontent: "emptyVideo",
			extend_params: "",
			forwho: "",
			// send_nums: "",
			theme:"",
			bp_time: 20,
			hb_detail: "",
			fee: "",
			send_nums: "",
			paytid: "",
			video:""
		},
		daItems: {
			id: 0,
			weid: 0,
			openid: "",
			messageid: 0,
			num: "",
			content: "",
			nickname: "",
			avatar: "",
			sex: 1,
			status: 0,
			image: "",
			type: "ds",
			isshow: 1,
			createtime: 1488352419,
			rid: 10,
			topenid: "",
			toName: "",
			ms: 60,
			tocontent: "emptyVideo",
			extend_params: "",
			forwho: "",
			// send_nums: "",
			theme:"",
			bp_time: 20,
			hb_detail: "",
			fee: "",
			send_nums: "",
			paytid: ""
		},
		redpack_detail:{
			id: "",
			weid: "",
			openid: "",
			content: "",
			nickname: "",
			avatar: "",
			sex: "",
			status: "",
			image: "",
			type: "bp",
			isshow: "",
			createtime: "",
			rid: "",
			topenid: "",
			toName: "",
			ms: 60,
			tocontent: "",
			extend_params: "",
			forwho: "",
			theme:"",
			// send_nums: "",
			bp_time: 10,
			hb_detail: "",
			fee: "",
			send_nums: "",
			paytid: ""
		},
		giftshow:!1,
		newgift: {
			id: "",
			weid: "",
			openid: "",
			content: "",
			nickname: "",
			avatar: "",
			sex: "",
			status: "",
			image: "",
			type: "bp",
			isshow: "",
			createtime: "",
			rid: "",
			topenid: "",
			toName: "",
			theme:"",
			ms: 60,
			tocontent: "",
			extend_params: "",
			forwho: "",
			// send_nums: "",
			bp_time: 10,
			hb_detail: "",
			fee: "",
			send_nums: "",
		},
		randVideo: {
			emptyVideo: "",
		},
		bp_bg_type:SYS_BP_BG_TYPE,
		randbgImg:SYS_BP_IMGBG,
		randbgVideo:SYS_BP_BG,
		emotionDataIndexs:'',
		emotions: '',
		flag: !0,
		id: -1,
		len: 0,
		deleteShow: !1,
		sexPath: "",
		items: [],
		penel: {
			gear: !1,
			shareLink: window.location.origin,
			index: 0,
			defaultIndex: 0,
			imgPath: "-1",
			bgCheckbox: !1,
			checkAll: !1,
			systemCheckbox: {
				clearCache: !1,
				recoveryDefault: !1,
				saveRefresh: !0,
				moveLeft: !1,
				qrTop: !1,
				chatChange: !1,
				paRight: !1
			},
			limitNumber: 7,
			sexIndex: 0,
			sexCheckbox: !1,
			sexPath: -1,
			qrcodeText: "扫一扫二维码上墙",
			menu: !1,
			toggle: 0,
			speed: 2.55,
			setItems: [{
				name: "背景设置",
				icon: ""
			}, {
				name: "其他设置",
				icon: ""
			}, {
				name: "系统设置",
				icon: ""
			}],
			bgItems: _BP_BGItems,
			sexItems: '',
		},
		confession: {
			show: !1,
			imgClass: "",
			random: 0,
			items: {
				content: "",
				nickname: "",
				avatar: "",
				image: "",
				type: "",
				bp_time: 0,
				forwho: "",
				extend_params: ''
			},
			video: ''
		}
	};
	vm = new Vue({
		el: "#yiyu-container",
		data: function() {
			return e
		},
		created: function() {

			var e = this;
			jQuery.get("../app/index.php?i=" + e.weid + "&c=entry&do=get_times&m=haoman_dpm", {
				rid: e.rid
			}, function(t, n) {

				"success" == n && (e.lasttime = t.lasttime)
			}, "json")
		},
		methods: {
			autoToggle: function() {
				var e = this,
					t = function() {
						e.isActive++, e.isActive > 1 && (e.isActive = 0), setTimeout(t, 10000)
					};
				t()
			},
			ranking: function() {
				var e = this,
					t = function() {
						setTimeout(t,dou_seconds<3000?6000:dou_seconds), jQuery.get("../app/index.php?i=" + e.weid + "&c=entry&do=get_bangdan&m=haoman_dpm", {
							rid: e.rid,
							type: 1
						}, function(t, n) {

							0 == t.errno && "success" == n && (e.total = t.message.sort(function(e, t) {
								return parseFloat(t.money) - parseFloat(e.money)
							}).filter(function(e) {
								return "0.00" != e.money ? e : void 0
							}))
						}, "json"), jQuery.get("../app/index.php?i=" + e.weid + "&c=entry&do=get_bangdan&m=haoman_dpm", {
							rid: e.rid,
							type: 0
						}, function(t, n) {

							0 == t.errno && "success" == n && (e.tonight = t.message.sort(function(e, t) {
								return parseFloat(t.money) - parseFloat(e.money)
							}).filter(function(e) {
								return "0.00" != e.money ? e : void 0
							}))
						}, "json"), $(".bg_video").length < 1 && dou_seconds>0 && document.getElementById("yiyu-container").classList.toggle("jello")
					};
				t()
			},
			requestComment: function() {

				var e = this,
					t = function() {

						setTimeout(t, 4444), 0 != e.flag && -1 != e.lasttime && $.get("../app/index.php?i=" + e.weid + "&c=entry&do=get_moremessages&m=haoman_dpm", {
							lasttime: e.lasttime,
							show_max_id: e.id,
							rid: e.rid
						}, function(t, n) {

							t.delmsg.length > 0 && e.deleteForever(t.delmsg);
							e.flag = !0, 1 == t.hadNew && "success" == n ? $.ajax({
								method: "GET",
								url: "../app/index.php?i=" + e.weid + "&c=entry&do=get_moremessages2&m=haoman_dpm",
								dataType: "json",
								data: {
									lasttime: e.lasttime,
									show_max_id: e.id,
									rid: e.rid
								},
								success: function(t) {

									var n = t.list,
										s = n.length - 1;

									e.lasttime = t.lasttime;
									if (n.length < 1) {
										return
									}
									if(danmu2.isOpen==true&&danmu2.isLoop=='true'&&danmu2.msgArray.length>danmu2.max){

													danmu2.msgArray = danmu2.msgArray.slice(0,danmu2.max);
													danmu2.loop();

									}

									if (e.id != n[s].id) {
										if (-1 == e.id) {

											e.id = n[0].id, e.items = n;

											if(danmu2.isOpen==true&&danmu2.isLoop=='true'){
                                                danmu2.msgArray = n;
                                                danmu2.loop();
											}
											for (var j = 0; j < n.length; j++) {
												var temp_time = parseInt(n[j].createtime);

												// if(temp_time>open_time){
												if(n[j].is_bpshow>0){

													if("1" == n[j].type  || "2" == n[j].type || "3" == n[j].type  || "6" == n[j].type || "4" == n[j].type || "7" == n[j].type||"5" == n[j].type){
														  if(n[j].type=='3'){
															   if(hb_texiaoshow==1){
																	e.bpItems.push(n[j]);
															   }
														  }else{
																e.bpItems.push(n[j]);
														  }
													}else if("dm" == n[j].type){
															danmu.add([n[j]]);
													}
												}
												if (n[j].wordimg != "" && n[j].type != '7') {
													e.change_slide_imgs(n[j].id, n[j].wordimg)
												}
											}
											return void(e.len = s + 1)
										}
										if (e.id != n[0].id) {

											for (var i = 0; i < n.length; i++) {
												e.items.push(n[i]);
                                                // console.log(e.items);
												if("1" == n[i].type || "2" == n[i].type || "3" == n[i].type || "4" == n[i].type || "5" == n[i].type|| "6" == n[i].type|| "7" == n[i].type){
													  if(n[i].type=='3'){
														if(hb_texiaoshow==1){
															e.bpItems.push(n[i]);
														}
													  }else{
														e.bpItems.push(n[i]);
													  }
												}else if("dm" == n[i].type){
														danmu.add([n[i]]);
												}
												if (n[i].wordimg != "" && n[i].type != '7') {
													e.change_slide_imgs(n[i].id, n[i].wordimg)
												}
												if(danmu2.isOpen==true){
													danmu2.add(n[i]);
												}
											}
											e.id = n[0].id, e.len = s + 1
										}
									}
								},
								complete: function() {
									e.flag = !0
								}
							}) : ""
						}, "json")
					};
				t()
			},
			change_slide_imgs: function(id, img) {
				image.add(id, img)
			},
			loop: function() {
				var e = this,
					t = 0,
					n = function() {
						e.penel.speed < 0.7 && (e.penel.speed = 0.7), setTimeout(n, 1000 * e.penel.speed);
						var s = document.getElementById("comment-box"),
							i = document.getElementById("comment-loop");
						$("#comment-loop li").length>2 && i.offsetHeight < s.offsetHeight || $("#comment-loop li").length>2 && (t = i.firstElementChild.offsetHeight + 10, i.style.transition = "transform .5s linear", i.style.transform = "translate3d(0," + -t + "px,0)", setTimeout(function() {
							e.items.shift(), e.items.length < (e.max_msg_show + 1) && e.items.push(e.items[0]), i.style.transition = "none", i.style.transform = "translate3d(0,0,0)"
						}, 550))
					};
				n()
			},
			compare: function(obj) {
				return function(a, b) {
					var value1 = a[obj];
					var value2 = b[obj];
					return value1 - value2
				}
			},
			findarrElem: function(val) {
				var t = this;
				for (var i = 0; i < e.items.length; i++) {
					if (e.items[i].id == val) {
						return i
					}
				}
				return -1
			},
			newest: function(e) {
				return new Date / 1000 - e <= 120 ? {
					infinite: !0,
					active: !0
				} : null
			},
			linkBtn: function(e) {
				1 == e ? (this.penel.shareLink = window.location.href, tools.layer("获取分享链接成功!")) : 2 == e && (document.getElementById("copy-link").select(), tools.layer(1 == document.execCommand("Copy") ? "复制链接成功,赶紧分享给小伙伴们吧 O(∩_∩)O~~" : "复制链接失败 /(ㄒoㄒ)/~~"))
			},
			saveBtn: function(e) {
				var t = this;
				switch (e) {
				case 0:
					var n = document.getElementsByTagName("body")[0];
					if (-1 != t.penel.imgPath && 1 == t.penel.bgCheckbox) {
						var s = t.penel.imgPath;
						return window.localStorage.setItem("bgPath", s), n.style.backgroundImage = "url(" + s + ")", void tools.confirm("保存成功是否返回大屏幕 ?", function() {
							t.penel.gear = !1
						})
					}
					var i = t.penel.bgItems[t.penel.defaultIndex].bg;
					window.localStorage.setItem("bgPath", i), n.style.backgroundImage = "url(" + i + ")", tools.confirm("保存成功是否返回大屏幕 ?", function() {
						t.penel.gear = !1
					});
					break;
				case 1:
					1 == t.penel.sexCheckbox && -1 != t.penel.sexPath ? (t.sexPath = t.penel.sexPath, window.localStorage.setItem("sex_logo", t.sexPath)) : -1 == t.penel.sexPath && (t.sexPath = t.penel.sexItems[t.penel.sexIndex].sex, window.localStorage.setItem("sex_logo", t.sexPath)), "扫一扫二维码上墙" != t.penel.qrcodeText && window.localStorage.setItem("qrcode_text", t.penel.qrcodeText), tools.confirm("保存成功是否返回大屏幕 ?", function() {
						t.penel.gear = !1
					});
					break;
				case 2:
					//1 == t.penel.systemCheckbox.clearCache && window.sessionStorage.setItem("clear_cache", 1), 1 == t.penel.systemCheckbox.moveLeft && window.localStorage.setItem("move_left", 1), 1 == t.penel.systemCheckbox.chatChange && window.localStorage.setItem("chat_change", 1), 1 == t.penel.systemCheckbox.paRight && window.localStorage.setItem("pa_right", 1), 1 == t.penel.systemCheckbox.recoveryDefault && window.localStorage.clear(), 1 == t.penel.systemCheckbox.saveRefresh && window.location.reload(), tools.layer("保存设置成功，刷新就能看到效果啦!");
					if(1 == t.penel.systemCheckbox.clearCache){
						window.sessionStorage.setItem("clear_cache", 1);
					}
					if(1 == t.penel.systemCheckbox.moveLeft){
						window.localStorage.setItem("move_left", 1);
					}else{
						t.penel.systemCheckbox.moveLeft = !1;
						window.localStorage.removeItem("move_left");
					}

					if(1 == t.penel.systemCheckbox.chatChange){
						window.localStorage.setItem("chat_change", 1);
					}else{
						t.penel.systemCheckbox.chatChange = !1;
						window.localStorage.removeItem("chat_change");
					}

					if(1 == t.penel.systemCheckbox.paRight){
						window.localStorage.setItem("pa_right", 1);
					}else{
						t.penel.systemCheckbox.paRight = !1;
						window.localStorage.removeItem("pa_right");
					}
					if(1 == t.penel.systemCheckbox.qrTop){
						window.localStorage.setItem("qr_top", 1);
					}else{
						t.penel.systemCheckbox.qrTop = !1;
						window.localStorage.removeItem("qr_top");
					}
					if(1 == t.penel.systemCheckbox.recoveryDefault){
						window.localStorage.clear();
					}
					if(1 == t.penel.systemCheckbox.saveRefresh) {
						window.location.reload();
						tools.layer("保存设置成功，刷新就能看到效果啦!");
					}
					break;
				case 4:
					if (0 == t.requestFlag) {
						return void tools.layer("当前已有用户霸屏或打赏, 请稍后在试")
					}
					var a = document.getElementById("nickname-1").value,
						o = document.getElementById("content-1").value,
						r = document.getElementById("seconds-1").value;
					if (a = a.length < 1 ? "霸屏" : a, o = o.length < 1 ? "体验霸屏" : o, r = 1 == isNaN(r) || "" == r ? 30 : r, 0 == t.penel.toggle) {
						return t.penel.gear = !1, void setTimeout(function() {
							t.bpItems.push({
								id: "",
								weid: "",
								openid: "",
								messageid: "",
								num: "",
								content: o,
								nickname: a,
								avatar: "",
								sex: "",
								status: "",
								image: "",
								type: "bp",
								isshow: "",
								createtime: "",
								rid: "",
								topenid: "",
								toName: "",
								ms: r,
								tocontent: "",
								paytid: ""
							})
						}, 1000)
					}
					if (1 == t.penel.toggle) {
						var c = Object.keys(t.randVideo);
						c.shift(), t.penel.gear = !1, setTimeout(function() {
							t.bpItems.push({
								id: 0,
								weid: 0,
								openid: "",
								messageid: 0,
								num: "",
								content: o,
								nickname: a,
								avatar: "",
								sex: 1,
								status: 0,
								image: "",
								type: "ds",
								isshow: 1,
								createtime: 1488352419,
								rid: 10,
								topenid: "",
								toName: "",
								ms: r,
								tocontent: c[Math.ceil(Math.random() * c.length - 1)],
								paytid: 20170301151343383000
							})
						}, 1000)
					}
					break;
				default:
					tools.layer("保存失败,请联系管理员!")
				}
			},
			upload: function(e, t, n, s) {
				var i = this,
					a = document.getElementById(e),
					o = document.getElementById(t),
					r = a.files[0];
				if ("image/jpeg" == r.type || "image/png" == r.type || "video/mp4" == r.type) {
					if ("image/jpeg" == r.type || "image/png" == r.type) {
						if ($(".bg_video").length > 0) {
							tools.layer("目前是视频背景模式,请上传视频MP4文件!");
							return
						}
						if (r.size <= s) {
							var c = new FileReader;
							c.readAsDataURL(r), c.onload = function(t) {
								var s = document.createElement("img");
								s.src = t.target.result, s.onload = function() {
									var t = s.naturalWidth,
										a = s.naturalHeight,
										r = "image/jpeg";
									if ("upload-sex" == e) {
										if (100 != t) {
											return void tools.layer("请上传100 * 50的图片")
										}
										if (50 != a) {
											return void tools.layer("请上传100 * 50的图片")
										}
										r = "image/png"
									}
									var c = document.createElement("canvas"),
										m = c.getContext("2d");
									c.width = t, c.height = a, m.drawImage(s, 0, 0, t, a, 0, 0, t, a);
									var l = c.toDataURL(r);
									o.src = l, i.penel[n] = l
								}
							}
						} else {
							tools.layer("只支持2MB以内的jpg,png格式图片,请重新上传!")
						}
					} else {
						if ($(".bg_video").length <= 0) {
							tools.layer("目前是图片背景模式、只支持2MB以内的jpg,png格式图片,请重新上传!!");
							return
						}
						var url = URL.createObjectURL(r);
						if ($(".bg_video").length > 0) {
							return document.getElementById("bg_video").src = url, i.bgvideo(), void tools.confirm("背景视频设置成功是否返回大屏幕 ?", function() {
								i.penel.gear = !1
							});
							tools.layer("视频背景设置成功！")
						}
					}
				} else {
					tools.layer("只支持jpg,png格式图片文件或者MP4格式本地视频文件,请重新上传!")
				}
			},
			testData: function() {
				var e = document.getElementsByTagName("body")[0];
				/*if ($(".bg_video").length == 0 && window.localStorage.getItem("bgPath") ? e.style.backgroundImage = "url(" + window.localStorage.getItem("bgPath") + ")" : backgroundImg && (e.style.backgroundImage = "url(" + backgroundImg + ")"), window.localStorage.getItem("chat_change") && document.getElementById("comment-box").classList.add("active"), window.localStorage.getItem("move_left") && document.querySelectorAll(".main")[0].classList.add("active"), window.localStorage.getItem("sex_logo") && (this.sexPath = window.localStorage.getItem("sex_logo")), window.localStorage.getItem("pa_right") && document.querySelectorAll(".full-screen")[0].classList.add("active"), window.localStorage.getItem("qrcode_text") && (this.penel.qrcodeText = window.localStorage.getItem("qrcode_text")), window.sessionStorage.getItem("clear_cache")) {
					window.sessionStorage.removeItem("clear_cache");
					var t = window.location.href;
					window.location.href = -1 != t.indexOf("&uniqid=") ? t.slice(0, t.indexOf("&uniqid=") + 1) + "uniqid=" + Math.random() : t + "&uniqid=" + Math.random()
				}*/
				if ($(".bg_video").length == 0 && window.localStorage.getItem("bgPath")){
					e.style.backgroundImage = "url(" + window.localStorage.getItem("bgPath") + ")"; 
				}else{
					if(backgroundImg&&$(".bg_video").length == 0){
						e.style.backgroundImage = "url(" + backgroundImg + ")";
					}
				}
				if(window.localStorage.getItem("chat_change")){
					this.penel.systemCheckbox.chatChange = !0;
					document.getElementById("comment-box").classList.add("active");
				} 
				if(window.localStorage.getItem("move_left")){
					this.penel.systemCheckbox.moveLeft = !0;
					document.querySelectorAll(".main")[0].classList.add("active");
				}
				if(window.localStorage.getItem("qr_top")){
						this.penel.systemCheckbox.qrTop = !0;
						var qr_box = $(".qrcode-box").html();//获取滑动框
						$(".right .qrcode-box").remove(),$('.right').prepend("<div class='qrcode-box qrtop'>"+qr_box+"</div>"),$('.right #sb-slider,.right #tuhao-lists').addClass("changebottom");
						//document.querySelectorAll(".full-screen")[0].classList.add("active");
				}
				if(window.localStorage.getItem("sex_logo")){
						this.sexPath = window.localStorage.getItem("sex_logo");
				}
				if(window.localStorage.getItem("pa_right")){
						this.penel.systemCheckbox.paRight = !0;
						document.querySelectorAll(".full-screen")[0].classList.add("active");
				}
				if(window.localStorage.getItem("qrcode_text")){
					   this.penel.qrcodeText = window.localStorage.getItem("qrcode_text");
				}
				if(window.sessionStorage.getItem("clear_cache")){
					window.sessionStorage.removeItem("clear_cache");
					var t = window.location.href;
					window.location.href = -1 != t.indexOf("&uniqid=") ? t.slice(0, t.indexOf("&uniqid=") + 1) + "uniqid=" + Math.random() : t + "&uniqid=" + Math.random()
				}
			},
			menuShow: function() {
				var e = this,
					t = document.querySelectorAll(".right-menu")[0],
					n = window.innerWidth;
				document.addEventListener("mousedown", function(s) {
					var i = s || window.event,
						a = i.button,
						o = i.clientX,
						r = i.clientY,
						c = 1 * new Date;
					o >= n - 150 && (o -= 150), 2 == a ? (t.style.top = r + "px", t.style.left = o + "px", e.penel.menu = !0) : e.penel.menu = !1;
					var m = function(t) {
							var n = t || window.event,
								s = n.clientX,
								i = (n.clientY, 1 * new Date);
							o > s + 50 && 150 >= i - c && 0 == e.penel.gear ? e.penel.gear = !0 : s - 50 > o && 150 >= i - c && 1 == e.penel.gear && (e.penel.gear = !1), document.removeEventListener("mouseup", m, !1)
						};
					document.addEventListener("mouseup", m, !1)
				}, !1)
			},
			menuBtn: function(e) {
				switch (e) {
				case 1:
					if (0 == this.penel.gear) {
						return void tools.layer("当前已处于大屏幕")
					}
					this.penel.gear = !1, this.penel.menu = !1;
					break;
				case 2:
					$(".menus").hide();
					if (1 == this.penel.gear) {
						return void tools.layer("当前已处于控制面板")
					}
					this.penel.gear = !0, this.penel.menu = !1;
					break;
				case 3:
					window.location.reload();
					break;
				case 4:
					var t = document.documentElement;
					if ($(t).find(".bg_video").length > 0) {
						$(t).find(".bg_video").css("z-index", "1");
						this.bgvideo()
					}
					t.webkitRequestFullscreen ? (t.webkitRequestFullscreen(), this.penel.menu = !1) : tools.layer("当前浏览器不支持,请手动按F11");
					break;
				case 5:
					var t = document.documentElement;
					if ($(t).find(".bg_video").length > 0) {
						this.bgvideo();
						$(t).find(".bg_video").css("z-index", "-1")
					}
					document.webkitCancelFullScreen ? (document.webkitCancelFullScreen(), this.penel.menu = !1) : tools.layer("当前浏览器不支持,请手动按F11");
					break;
				case 6:
					window.open(this.qrcodeImg), this.penel.menu = !1;
					break;
				default:
					this.penel.menu = !1
				}
			},
			show_menus: function() {
				var t = this;
				t.penel.gear = 0;
				$(".menus").slideDown()
			},
			close_menus: function() {
				$(".menus").slideUp()
			},
			show_penel: function() {
				this.penel.gear = 1;
				$(".menus").slideUp()
			},
			close_penel: function() {
				this.penel.gear = 0
			},
			deleteTrash: function(e) {
				var t = this,
                    n = this.items[e].createtime;
				tools.confirm("确定要临时删除吗?", function() {
					t.items[e].createtime && (n == t.items[e].createtime ? (t.items.splice(e, 1), tools.layer("删除成功！")) : tools.layer("删除失败,请重试！"))
				})
			},
			
			zhuti2video: function(index){
				var t = this;
				if(t.bp_zhutis.length==0){
					return '';
				}else{
					var bp_themes = t.bp_zhutis;
					for(var j=0;j<bp_themes.length;j++){
						if(bp_themes[j].bp_ztindex==index){
							return bp_themes[j].bp_ztvideo;
						}
					}
					return '';
				}
			},
			getcolor: function(){
                return '#'+('00000'+(Math.random()*0x1000000<<0).toString(16)).substr(-6);
                // var e = .78,
                //     t = ["rgba(141,50,160," + e + ")", "rgba(225,99,15," + e + ")", "rgba(242,73,73," + e + ")", "rgba(18,155,240," + e + ")", "rgba(90,162,12," + e + ")", "rgba(20,185,148," + e + ")"],
                //     n = Math.floor(6 * Math.random());
                // return t[n]
			},
			deleteForever: function(delmsg) {
				var t = this;
				if (delmsg.length > 0) {
					for (var di = 0; di < delmsg.length; di++) {
						var arrkey = t.findarrElem(delmsg[di]);
						if (arrkey != -1) {
							t.items.splice(arrkey, 1)
						}
						if($('#imageBox div[tid="'+delmsg[di]+'"]').length>0){
							image.replace_one(delmsg[di]);
						}
					}
				}
			},
			bgvideo: function() {
				if ($(".bg_video").length > 0) {

					$(".bg_video").css({
						width: $("body").width() + 2,
						height: "auto",
						opacity: 1
					})
				}
			},
			drag: function(e) {
				var t = e || window.event,
					n = e.currentTarget,
					s = t.clientX - n.offsetLeft,
					i = t.clientY - n.offsetTop,
					a = function(e) {
						var t = e || window.event;
						t.clientX - s, t.clientY - i
					};
				document.addEventListener("mousemove", a, !1), document.addEventListener("mouseup", function() {
					document.removeEventListener("mousemove", a, !1)
				}, !1)
			}
		},
		watch: {
			bpItems: function() {
				var e = this;

				if (1 == e.requestFlag && e.bpItems.length > 0) {

					switch (e.bpItems[0].type) {
					case "1":
						e.requestFlag = !1;
						e.paScreen = e.bpItems[0];
                        e.paScreen.color = e.getcolor();
						e.themes =  e.bpItems[0];
						if(e.paScreen.theme==""){
							e.show = !0;
						}else{
							e.themes.video = e.zhuti2video(e.themes.theme);
							e.themeshow = !0;
						}
						e.bpItems.shift();
                        break;
                        case "4":
						e.requestFlag = !1;
						e.paScreen = e.bpItems[0];
						e.themes =  e.bpItems[0];
						if(e.paScreen.theme==""){
							e.show = !0;
						}else{
							e.themes.video = e.zhuti2video(e.themes.theme);
							e.themeshow = !0;

						}
						e.bpItems.shift();
                        break;
                        case "5":
						e.requestFlag = !1;
						e.paScreen = e.bpItems[0];
						e.themes =  e.bpItems[0];
						if(e.paScreen.theme==""){
							e.show = !0;
						}else{
							e.themes.video = e.zhuti2video(e.themes.theme);
							e.themeshow = !0;

						}
						e.bpItems.shift();
                        break;
					case "2":

						e.requestFlag = !1, e.daItems = e.bpItems[0], e.dsShow = !0, e.bpItems.shift();
						break;
					case "6":
						e.requestFlag = !1, e.confession.items = e.bpItems[0], e.confession.show = !0, e.bpItems.shift();
						break;
					case "3":
						e.requestFlag = !1, e.redpack_detail = e.bpItems[0], e.redpackshow = !0, e.bpItems.shift();
						break;
					case "7":
						e.requestFlag = !1, e.newgift = e.bpItems[0], e.giftshow = !0, e.bpItems.shift();
						break;
					default:
						e.bpItems.shift()
					}
				}
			},
			show: function(e) {
				var t = this,
					n = null;
				if (1 == e) {

						var s = t.paScreen.bptime;
						var W_width = $("body").width() + 2;
						if($("#bp_music").length > 0){
                            document.getElementById('media').pause();
                            $("#bp_music")[0].play()
						}
						setTimeout(function () {
                            document.getElementById('media').play();
                            $("#bp_music")[0].pause()
                        },5000)
						if(t.bp_bg_type==3){
						  if(t.randbgVideo.length>0){
							var rand_bg_video = Math.floor((Math.random()*t.randbgVideo.length));
							document.querySelectorAll(".bp-bg-box")[0].innerHTML = '<video id="bp_bg_video_1" autoplay="autoplay" loop="loop" src="' + t.randbgVideo[rand_bg_video] + '" style="width:'+W_width+'px;height:auto"></video>';
						  }else{
							$(".bp-bg-box").css('background-color','#000');
						  }					
						}else if(t.bp_bg_type==2){
						  if(t.randbgImg.length>0){
							var rand_bg_img = Math.floor((Math.random()*t.randbgImg.length));
							$(".bp-bg-box").css({"background-image":"url("+ t.randbgImg[rand_bg_img] +")","background-size":"100% 100%"});
						  }else{
							$(".bp-bg-box").css('background-color','#000');
						  }
						}else{
							$(".bp-bg-box").css('background-color','#000');
						}

						"" != t.paScreen.wordimg && (document.querySelectorAll(".richLeft")[0].innerHTML = ("" == t.paScreen.extend_params ? '<img type="rich" auto="2" class="richLeftBg" style="width: 100%; height: '+(bp_image_xuanku!=1 ?'100%':'auto')+'; left: 0;top:0; visibility: visible;"  onload="'+(bp_image_xuanku!=1 ?'':'tools.image3d(this)')+'" src="' + t.paScreen.wordimg + '">' : ""));
						"" != t.paScreen.extend_params && (document.querySelectorAll(".bpvideo-box")[0].innerHTML = '<video autoplay="autoplay" loop="loop" src="' + t.paScreen.wordimg + '" poster="' + t.paScreen.wordimg + '"></video>');
						var i = function() {

								n = setTimeout(i, 1000), t.paScreen.bptime--, t.paScreen.bptime <= 0 && (t.show = !1, t.requestFlag = !0, t.bpTextShow = !1, t.paScreen.bptime = s, setTimeout(function() {
									t.bpItems.length > 0 && t.$set(t.bpItems[0], "token", Math.random()),$(".bpvideo-box video").length > 0 && $(".bpvideo-box video")[0].pause();
								}, 888), $(".bp-bg-box").html(''),clearTimeout(n))
							};
						setTimeout(i, 1000), setTimeout(function() {
							t.bpTextShow = !0
						}, 999)
					
				}
			},
			giftshow: function(e){
				var t = this,
					n = null;
                if($("#bp_music").length > 0){
                    document.getElementById('media').pause();
                    $("#bp_music")[0].play()
                }
                setTimeout(function () {
                    document.getElementById('media').play();
                    $("#bp_music")[0].pause()
                },5000)
				if (1 == e) {
					var s = this.newgift.bptime,
						i = function() {
							n = setTimeout(i, 1000), t.newgift.bptime--, t.newgift.bptime <= 0 && (t.giftshow = !1, t.requestFlag = !0, clearTimeout(n),setTimeout(function() {
			
								t.bpItems.length > 0 && t.$set(t.bpItems[0], "token", Math.random())
								
							}, 888))
						};
					setTimeout(i, 1000), setTimeout(function() {
						t.giftshow = !0
						
					}, 999)
				}
			},
			redpackshow: function(e) {
				var t = this,
					n = null;
                if($("#bp_music").length > 0){
                    document.getElementById('media').pause();
                    $("#bp_music")[0].play()
                }
                setTimeout(function () {
                    document.getElementById('media').play();
                    $("#bp_music")[0].pause()
                },5000)
				if (1 == e) {
					var s = this.redpack_detail.bptime,
						i = function() {
							n = setTimeout(i, 1000), t.redpack_detail.bptime--, t.redpack_detail.bptime <= 0 && (t.redpackshow = !1, t.requestFlag = !0,t.redtextShow = !1, clearTimeout(n), setTimeout(function() {
			
								t.bpItems.length > 0 && t.$set(t.bpItems[0], "token", Math.random())
								
							}, 888))
						};
					setTimeout(i, 1000), setTimeout(function() {
						t.redtextShow = !0
						
					}, 999)
				}
			},
			themeshow: function(e){
				var t = this,
					theme_n = null;
					theme_n = setTimeout(function(){
							t.themeshow = !1;
							clearTimeout(theme_n);
							t.show = !0;
					}, 5000);
			},
			dsShow: function(e) {
				var t = this,
					n = null;
				if (1 == e) {
                    // console.log(this.daItems.bp_time);
					var s = this.daItems.bp_time,
						i = function() {

							n = setTimeout(i, 1000), t.daItems.bp_time--, t.daItems.bp_time <= 0 && (t.dsShow = !1, t.requestFlag = !0, t.textShow = !1, t.daItems.bp_time = s, clearTimeout(n), setTimeout(function() {
								t.bpItems.length > 0 && t.$set(t.bpItems[0], "token", Math.random())
							}, 888))
						};
					setTimeout(i, 1000), setTimeout(function() {
						t.textShow = !0
					}, 999)
				}
			},
			"confession.show": function(e) {
				var t = this,
					n = null;
				if (1 == e) {
                    if($("#bp_music").length > 0){
                        document.getElementById('media').pause();
                        $("#bp_music")[0].play()
                    }
                    setTimeout(function () {
                        document.getElementById('media').play();
                        $("#bp_music")[0].pause()
                    },5000)
					var s = this.confession.items.bptime,
						i = function() {
							n = setTimeout(i, 1000), t.confession.items.bptime--, t.confession.items.bptime <= 0 && (t.confession.show = !1, t.requestFlag = !0,t.confession.items.bp_time = s, clearTimeout(n), setTimeout(function() {
								t.bpItems.length > 0 && t.$set(t.bpItems[0], "token", Math.random());
								
							}, 888))
						};
					setTimeout(i, 1000), setTimeout(function() {
						t.confession.show = !0
					}, 999)
					var u = null,
						l = ["animated bounce", "animated flash", "animated pulse", "animated rubberBand", "animated shake", "animated headShake", "animated swing", "animated tada", "animated wobble", "animated jello", "animated bounceIn", "animated bounceInDown", "animated bounceInLeft", "animated bounceInRight", "animated bounceInUp", "animated bounceOut", "animated bounceOutDown", "animated bounceOutLeft", "animated bounceOutRight", "animated bounceOutUp", "animated fadeIn", "animated fadeInDown", "animated fadeInDownBig", "animated fadeInLeft", "animated fadeInLeftBig", "animated fadeInRight", "animated fadeInRightBig", "animated fadeInUp", "animated fadeInUpBig", "animated fadeOut", "animated fadeOutDown", "animated fadeOutDownBig", "animated fadeOutLeft", "animated fadeOutLeftBig", "animated fadeOutRight", "animated fadeOutRightBig", "animated fadeOutUp", "animated fadeOutUpBig", "animated flipInX", "animated flipInY", "animated flipOutX", "animated flipOutY", "animated lightSpeedIn", "animated lightSpeedOut", "animated rotateIn", "animated rotateInDownLeft", "animated rotateInDownRight", "animated rotateInUpLeft", "animated rotateInUpRight", "animated rotateOut", "animated rotateOutDownLeft", "animated rotateOutDownRight", "animated rotateOutUpLeft", "animated rotateOutUpRight", "animated hinge", "animated rollIn", "animated rollOut", "animated zoomIn", "animated zoomInDown", "animated zoomInLeft", "animated zoomInRight", "animated zoomInUp", "animated zoomOut", "animated zoomOutDown", "animated zoomOutLeft", "animated zoomOutRight", "animated zoomOutUp", "animated slideInDown", "animated slideInLeft", "animated slideInRight", "animated slideInUp", "animated slideOutDown", "animated slideOutLeft", "animated slideOutRight", "animated slideOutUp"];
					!function e() {
						var css3 = l[Math.floor(Math.random() * l.length)];
						if (!t.confession.show) return void clearTimeout(u);
						t.confession.imgClass = css3, u = setTimeout(e, 2e3)
					}()
				}
			},
			"penel.checkAll": function(e) {
				if (1 == e) {
					for (var t in this.penel.systemCheckbox) {
						this.penel.systemCheckbox[t] = !0
					}
				} else {
					for (var n in this.penel.systemCheckbox) {
						this.penel.systemCheckbox[n] = !1
					}
				}
			},
			"penel.systemCheckbox.chatChange": function(e) {
				var t = document.getElementById("comment-box");
				return 1 == e ? t.classList.add("active") : t.classList.remove("active")
			},
			"penel.systemCheckbox.moveLeft": function(e) {
				var t = document.querySelectorAll(".main")[0];
				return 1 == e ? t.classList.add("active") : t.classList.remove("active")
			},
			"penel.systemCheckbox.paRight": function(e) {
				
				var t = document.querySelectorAll(".full-screen")[0];
				return 1 == e ? t.classList.add("active") : t.classList.remove("active")
			},
			"penel.systemCheckbox.qrTop": function(e) {
				var qr_box = $(".qrcode-box").html();//获取滑动框
				return 1 == e ? ($(".right .qrcode-box").remove(),$('.right').prepend("<div class='qrcode-box qrtop'>"+qr_box+"</div>"),$('.right #sb-slider,.right #tuhao-lists').addClass("changebottom")):($(".right .qrcode-box").remove(),$('.right').append("<div class='qrcode-box'>"+qr_box+"</div>"),$('.right #sb-slider,.right #tuhao-lists').removeClass("changebottom"));
			},
			enlargeQrcode: function(e) {
				var t = this,
					n = null,
					s = t.enlargeQrcodeSeconds;
				if (1 == e) {
					var i = function() {
							return t.enlargeQrcodeSeconds--, t.enlargeQrcodeSeconds <= 0 ? (t.enlargeQrcode = !1, t.enlargeQrcodeSeconds = s, void clearTimeout(n)) : void(n = setTimeout(i, 1000))
						};
					i()
				}
			}
		},
		filters: {
			formatDate: function(e) {
				var t = 1000 * parseInt(e),
					n = new Date(t),
					s = n.getFullYear() + "-",
					i = (n.getMonth() + 1 < 10 ? "0" + (n.getMonth() + 1) : n.getMonth() + 1) + "-",
					a = n.getDate() < 10 ? "0" + n.getDate() + " " : n.getDate() + " ",
					o = n.getHours() < 10 ? "0" + n.getHours() + ":" : n.getHours() + ":",
					r = n.getMinutes() < 10 ? "0" + n.getMinutes() + ":" : n.getMinutes() + ":",
					c = n.getSeconds() < 10 ? "0" + n.getSeconds() : n.getSeconds();
				return s + i + a + o + r + c
			},
			content2emo: function(v) {
				var emotions = e.emotions;
				var emotionDataIndexs = e.emotionDataIndexs;
				var pattern2 = /\[[\u4e00-\u9fa5]+\]/g;
				return v.replace(pattern2, function(match) {
					return '<img style="clear: both;width:50px;" src="' + emotions[emotionDataIndexs[match]][1] + '" class="smallface_img" />'
				})
			},
			zhuti2name: function(index){
				var t = e;
				if(t.bp_zhutis.length==0){
					return '重金霸屏';
				}else{
					var bp_themes = t.bp_zhutis;
					for(var j=0;j<bp_themes.length;j++){
						if(bp_themes[j].bp_ztindex==index){
							return bp_themes[j].bp_ztdes;
						}
					}
					return '重金霸屏';
				}
			},
		},
		computed: {
			computedBgItems: function() {
				return this.penel.bgItems.slice(0, this.penel.limitNumber)
			}
		},
		components: {
			"my-title": {
				props: ["title"],
				template: '<div class="sy-left" v-html="title"></div>'
			}
		},
		mounted: function() {
			var e = this;
			this.bgvideo();
			this.ranking();
			this.autoToggle();
			this.requestComment();
			this.loop();
			this.testData();
			this.menuShow();
			$(".songliBottom img").css({
				'width':$(window).width()/1.5,
				'height':$(window).width()/3,
			});
			var t = null;
			t = setInterval(function() {
				clearInterval(t), tools.removeEl(document.querySelectorAll(".loading")[0])
			}, 2000)
		}
	}), jQuery(function() {
		var e = {
			bgCanvas: function() {
				var e = document.getElementById("yiyu-container"),
					t = document.createElement("canvas");
				t.id = "canv", e.appendChild(t);
				var n = 99,
					s = window.innerWidth,
					i = window.innerHeight,
					a = 0,
					o = 0,
					r = 150,
					c = function(e) {
						return e * Math.PI / 180
					},
					m = function() {
						return Math.sin(Math.floor(360 * Math.random()) * Math.PI / 180)
					},
					l = {
						obj: {
							x: a,
							y: o,
							z: r
						},
						dest: {
							x: 0,
							y: 0,
							z: 1
						},
						dist: {
							x: 0,
							y: 0,
							z: 200
						},
						ang: {
							cplane: 0,
							splane: 0,
							ctheta: 0,
							stheta: 0
						},
						zoom: 1,
						disp: {
							x: s / 2,
							y: i / 2,
							z: 0
						},
						upd: function() {
							l.dist.x = l.dest.x - l.obj.x, l.dist.y = l.dest.y - l.obj.y, l.dist.z = l.dest.z - l.obj.z, l.ang.cplane = -l.dist.z / Math.sqrt(l.dist.x * l.dist.x + l.dist.z * l.dist.z), l.ang.splane = l.dist.x / Math.sqrt(l.dist.x * l.dist.x + l.dist.z * l.dist.z), l.ang.ctheta = Math.sqrt(l.dist.x * l.dist.x + l.dist.z * l.dist.z) / Math.sqrt(l.dist.x * l.dist.x + l.dist.y * l.dist.y + l.dist.z * l.dist.z), l.ang.stheta = -l.dist.y / Math.sqrt(l.dist.x * l.dist.x + l.dist.y * l.dist.y + l.dist.z * l.dist.z)
						}
					},
					d = {
						parts: {
							sz: function(e, t) {
								return {
									x: e.x * t.x,
									y: e.y * t.y,
									z: e.z * t.z
								}
							},
							rot: {
								x: function(e, t) {
									return {
										x: e.x,
										y: e.y * Math.cos(c(t.x)) - e.z * Math.sin(c(t.x)),
										z: e.y * Math.sin(c(t.x)) + e.z * Math.cos(c(t.x))
									}
								},
								y: function(e, t) {
									return {
										x: e.x * Math.cos(c(t.y)) + e.z * Math.sin(c(t.y)),
										y: e.y,
										z: -e.x * Math.sin(c(t.y)) + e.z * Math.cos(c(t.y))
									}
								},
								z: function(e, t) {
									return {
										x: e.x * Math.cos(c(t.z)) - e.y * Math.sin(c(t.z)),
										y: e.x * Math.sin(c(t.z)) + e.y * Math.cos(c(t.z)),
										z: e.z
									}
								}
							},
							pos: function(e, t) {
								return {
									x: e.x + t.x,
									y: e.y + t.y,
									z: e.z + t.z
								}
							}
						},
						pov: {
							plane: function(e) {
								return {
									x: e.x * l.ang.cplane + e.z * l.ang.splane,
									y: e.y,
									z: e.x * -l.ang.splane + e.z * l.ang.cplane
								}
							},
							theta: function(e) {
								return {
									x: e.x,
									y: e.y * l.ang.ctheta - e.z * l.ang.stheta,
									z: e.y * l.ang.stheta + e.z * l.ang.ctheta
								}
							},
							set: function(e) {
								return {
									x: e.x - l.obj.x,
									y: e.y - l.obj.y,
									z: e.z - l.obj.z
								}
							}
						},
						persp: function(e) {
							return {
								x: e.x * l.dist.z / e.z * l.zoom,
								y: e.y * l.dist.z / e.z * l.zoom,
								z: e.z * l.zoom,
								p: l.dist.z / e.z
							}
						},
						disp: function(e, t) {
							return {
								x: e.x + t.x,
								y: -e.y + t.y,
								z: e.z + t.z,
								p: e.p
							}
						},
						steps: function(e, t, n, s, i) {
							var a = d.parts.sz(e, t);
							return a = d.parts.rot.x(a, n), a = d.parts.rot.y(a, n), a = d.parts.rot.z(a, n), a = d.parts.pos(a, s), a = d.pov.plane(a), a = d.pov.theta(a), a = d.pov.set(a), a = d.persp(a), a = d.disp(a, i)
						}
					};
				!
				function() {
					var e = function(e) {
							this.transIn = {}, this.transOut = {}, this.transIn.vtx = e.vtx, this.transIn.sz = e.sz, this.transIn.rot = e.rot, this.transIn.pos = e.pos
						};
					e.prototype.vupd = function() {
						this.transOut = d.steps(this.transIn.vtx, this.transIn.sz, this.transIn.rot, this.transIn.pos, l.disp)
					};
					var t = function() {
							this.vel = 0.04, this.lim = 360, this.diff = 200, this.initPos = 100, this.toX = a, this.toY = o, this.go()
						};
					t.prototype.go = function() {
						this.canvas = document.getElementById("canv"), this.canvas.width = window.innerWidth, this.canvas.height = window.innerHeight, this.$ = canv.getContext("2d"), this.$.globalCompositeOperation = "source-over", this.varr = [], this.dist = [], this.calc = [];
						for (var e = 0, t = n; t > e; e++) {
							this.add()
						}
						this.rotObj = {
							x: 0,
							y: 0,
							z: 0
						}, this.objSz = {
							x: s / 5,
							y: i / 5,
							z: s / 5
						}
					}, t.prototype.add = function() {
						this.varr.push(new e({
							vtx: {
								x: m(),
								y: m(),
								z: m()
							},
							sz: {
								x: 0,
								y: 0,
								z: 0
							},
							rot: {
								x: 20,
								y: -20,
								z: 0
							},
							pos: {
								x: this.diff * Math.sin(360 * Math.random() * Math.PI / 180),
								y: this.diff * Math.sin(360 * Math.random() * Math.PI / 180),
								z: this.diff * Math.sin(360 * Math.random() * Math.PI / 180)
							}
						})), this.calc.push({
							x: 360 * Math.random(),
							y: 360 * Math.random(),
							z: 360 * Math.random()
						})
					}, t.prototype.upd = function() {
						l.obj.x += 0.05 * (this.toX - l.obj.x), l.obj.y += 0.05 * (this.toY - l.obj.y)
					}, t.prototype.draw = function() {
						this.$.clearRect(0, 0, this.canvas.width, this.canvas.height), l.upd(), this.rotObj.x += 0.1, this.rotObj.y += 0.1, this.rotObj.z += 0.1;
						for (var e = 0; e < this.varr.length; e++) {
							for (var t in this.calc[e]) {
								this.calc[e].hasOwnProperty(t) && (this.calc[e][t] += this.vel, this.calc[e][t] > this.lim && (this.calc[e][t] = 0))
							}
							if (this.varr[e].transIn.pos = {
								x: this.diff * Math.cos(this.calc[e].x * Math.PI / 180),
								y: this.diff * Math.sin(this.calc[e].y * Math.PI / 180),
								z: this.diff * Math.sin(this.calc[e].z * Math.PI / 180)
							}, this.varr[e].transIn.rot = this.rotObj, this.varr[e].transIn.sz = this.objSz, this.varr[e].vupd(), !(this.varr[e].transOut.p < 0)) {
								var n = this.$.createRadialGradient(this.varr[e].transOut.x, this.varr[e].transOut.y, this.varr[e].transOut.p, this.varr[e].transOut.x, this.varr[e].transOut.y, 2 * this.varr[e].transOut.p);
								this.$.globalCompositeOperation = "lighter", n.addColorStop(0, "hsla(255, 255%, 255%, 1)"), n.addColorStop(0.5, "hsla(" + (e + 2) + ",85%, 40%,1)"), n.addColorStop(1, "hsla(" + e + ",85%, 40%,.5)"), this.$.fillStyle = n, this.$.beginPath(), this.$.arc(this.varr[e].transOut.x, this.varr[e].transOut.y, 2.2 * this.varr[e].transOut.p, 0, 2 * Math.PI, !1), this.$.fill(), this.$.closePath()
							}
						}
					}, t.prototype.anim = function() {
						window.requestAnimationFrame = function() {
							return window.requestAnimationFrame ||
							function(e) {
								window.setTimeout(e, 1000 / 60)
							}
						}();
						var e = function() {
								this.upd(), this.draw(), window.requestAnimationFrame(e)
							}.bind(this);
						window.requestAnimationFrame(e)
					}, t.prototype.run = function() {
						this.anim()
					};
					var r = new t;
					open_canvas==1 && r.run()
				}()
			}
		};
		for (var t in e) {
			"function" == typeof e[t] && e[t]()
		}
	});
	//console.clear()
}();