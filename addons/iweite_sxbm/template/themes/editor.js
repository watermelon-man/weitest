// window.onload = function() {
// 	document.getElementById("textstyle").onclick = function() {
// 		document.getElementById("styleSelector").select();
// 	}
// };
(function() {
	var k = document.createElement("canvas");
	k.width = k.height = 1;
	0 !== k.toDataURL("image/jpeg").indexOf("data:image/jpeg") && (k = document.createElement("script"), k.type = "text/javascript", k.src = "/microblog-v3/2015subject/0211_chunjie/cvsjpeg.js", document.body.insertBefore(k, document.body.firstChild))
})();
(function() {


	function k(b) {
		return document.getElementById(b)
	}
	function g(b, c) {
		return (c || document).querySelector(b)
	}
	function u(b) {
		return b.toString().replace(/(^\s+|\s+$)/g, "")
	}
	function B(b, c) {
		var d = b.className.replace(new RegExp("\\b" + c + "\\b", "g"), "");
		b.className = d.replace(/ +/g, " ").replace(/(^ +| +$)/g, "") + " " + c
	}
	function C(b, c) {
		b.className = b.className.replace(new RegExp("\\b" + c + "\\b", "g"), " ").replace(/ +/g, " ").replace(/(^ +| +$)/g, "")
	}
	function h(b) {
		b = (z + b).replace(/-+/g, "-").replace(/(^-|-$)/g, "").split("-");
		for (var c = 1; c < b.length; c++) b[c] = b[c].substr(0, 1).toLocaleUpperCase() + b[c].substr(1).toLowerCase();
		return b.join("")
	}
	function A() {
		function b() {
			"number" == typeof window.orientation && 0 !== window.orientation || "function" != typeof A || location.reload()
		}
		window.onorientationchange = b;
		"number" == typeof window.orientation && 0 !== window.orientation ? (alert("请旋转您的手机为竖立状态，在横屏界面下无法操作！"), b()) : (A = null, p.init())
	}
	function v() {
		var b = this,
			c = document.body.clientWidth,
			d = c / 640;
		this.ele = document.createElement("div");
		this.ele.className = "canvasEdit";
		this.ele.innerHTML = '<div class="canvas"></div><div class="cvsBtn"><a class="btn_0 cvsEditBtn" href="javascript:void(0)">编辑</a><a class="btn_0" style="display:none;" href="javascript:void(0)">重播</a><a class="btn_0 cvsDelBtn" href="javascript:void(0)">删除</a></div>';
		var e = document.createElement("canvas");
		g(".canvas", this.ele).appendChild(e);
		e.width = 610 * d;
		e.height = 610 * d;
		this.paper = new D(e);
		this.paper.fontWidth = c;
		this.btnBox = g(".cvsBtn", this.ele);
		c = (this.btnBox || document).querySelectorAll("a");
		this.btn_edit = c[0];
		this.btn_replay = c[1];
		this.btn_del = c[2];
		this.btn_edit.onmousedown = function() {
			p.select(b.index)
		};
		this.btn_replay.onclick = function() {
			b.paper.replay()
		};
		this.btn_del.onclick = function() {
			b.del()
		};
		this.paper.onchange = function() {
			p.changeBtn()
		}
	}
	function D(b) {
		if (b.nodeType) this.canvas = b;
		else if ("string" == typeof b) this.canvas = g(b);
		else return;
		this.init();
		this.penmanship = [];
		this.repeatQueue = []
	}
	function E(b, c, d, e) {
		if (!d.bgImg.complete) return "";
		e = e || 610;
		b = b || "png";
		c = c || .7;
		var f = document.createElement("canvas");
		f.width = f.height = e;
		var m, g, h, k, l = d.canvas.width,
			r = d.canvas.height;
		if (1.8 < l / e) for (m = document.createElement("canvas"), g = document.createElement("canvas"), h = m.getContext("2d"), k = g.getContext("2d"), m.width = l, m.height = r, h.drawImage(d.canvas, 0, 0, l, r); 1.8 < l / e;) l = Math.round(.6 * l), r = Math.round(.6 * r), g.width = l, g.height = r, k.drawImage(m, 0, 0, l, r), m.width = l, m.height = r, h.drawImage(g, 0, 0, l, r);
		else m = d.canvas;
		g = f.getContext("2d");
		g.drawImage(d.bgImg, 0, 0, e, e);
		g.drawImage(d.bgCanvas, 0, 0, e, e);
		g.drawImage(m, 0, 0, e, e);
		return f.toDataURL("image/" + b, c)
	}
	var F = function(b, c, d, e) {
			var f = "",
				m = "";
			null != d && (f = new Date((new Date).getTime() + 36E5 * d), f = "; expires=" + f.toGMTString());
			null != e && (m = ";domain=" + e);
			document.cookie = b + "=" + escape(c) + f + m
		},
		H = navigator.userAgent.match(/ OS (\d+).*? Mac OS/) || !1,
		I = !! navigator.userAgent.match(/android/i);
	navigator.userAgent.indexOf("NetType/WIFI");
	var n = -1 !== navigator.userAgent.indexOf("Messenger"); - 1 !== navigator.userAgent.indexOf("QQ") && navigator.userAgent.indexOf("QQBrowser");
	var J = window.LANG || {};

	var n = document.body.clientWidth,
		t = n / 640;
	document.documentElement.style.fontSize = 32 * t + "px";
	document.body.style.width = n + "px";
	g("#editorPage").style.minHeight = document.documentElement.clientHeight + "px";
	n = ""; - 1 !== navigator.userAgent.indexOf("WebKit") ? n = "webkit" : -1 !== navigator.userAgent.indexOf("Firefox") ? n = "moz" : -1 !== navigator.userAgent.indexOf("MSIE") && (n = "ms");
	var y = {
		moz: "animationend",
		webkit: "webkitAnimationEnd",
		ms: "MSAnimationEnd"
	}[n] || "animationend",
		G = {
			moz: "transitionend",
			webkit: "webkitTransitionEnd",
			ms: "MSTransitionEnd"
		}[n] || "transitionend",
		z = {
			moz: "",
			webkit: "-webkit-",
			ms: ""
		}[n] || "",
		p = {
			type: "paper",
			classType: 1,
			wordList: [],
			word: null,
			imgUrl: "",
			imgId: "",
			color: "rgb(0,0,0)",
			penSize: 8,
			index: 0,
			ztext: [
					"知足常乐","宁静致远","家和万事兴","忙时井然，闲时自然",
					"人生如戏，全靠演技","随心而生，随心而行","心想事成","上善若水",
					"天道酬勤","海纳百川","舍得","不写字画画也可以","吃么么香",
					"我爱我家","身体健康","幸福美满","主要看气质","世界那么大我想去看看"
			],
			textIndex: 0,
			firstRand: true, // 是否是第一次打开
			init: function() {
				var b = this;

				b.show_text();

				this.listBox = g(".canvasList");
				// for (var d = this.zfTxt, e = "", f = 0; f < d.length; f++) e += '<dd data-value="' + f + '"><span>' + d[f].t + "</span></dd>";
				k("clear_btn").onclick = function() {
					b.word.paper.clearPaper()
				};
				this.btnBox = k("btnBox");
				this.undo_btn = k("undo_btn");
				this.repeat_btn = k("repeat_btn");
				this.pubBox = k("pubBox");
				this.completeBtn = k("completeBtn");
				this.usual_btn = k("usual_btn");
				this.confirm_btn = k("confirm_btn");
				this.exchange_btn = k("exchange");
				this.tipsCover = k("tipsCover");

				this.undo_btn.onmousedown = function() {
					b.word.paper.undo();
					b.word.paper.repeatActi() && b.showRepeatBtn()
				};
				this.repeat_btn.onmousedown = function() {
					b.word.paper.repeat()
				};
				this.usual_btn.onmousedown = function() {
					// b.show_text();
					document.querySelector(".tipsCover").style.display = "block";
				}
				this.tipsCover.onmousedown = function() {
					document.querySelector(".tipsCover").style.display = "none";	
				}
				var w = 0,
					x = k("palette"),
					l = k("penSize");
				this.tagPSList = document.querySelectorAll("#penSize li");
				this.penColor = k("penColor");
				this.completeBtn.onclick = function() {
					b.complete()
				};
				this.confirm_btn.onmousedown = function() {
					b.uploadJSON();
				}
				this.exchange_btn.onmousedown = function() {
					b._seqText();
				}
				this.addWord()
			},
			_randText: function() {
				texts = this.ztext;
				if (this.firstRand) {
					k("textWord").innerText = this.ztext[0];
					this.firstRand = false;
				} else {
					k("textWord").innerText = texts[ parseInt(Math.random()*9999)%texts.length ];
				}
			},
			_seqText: function() {
				var index = this.textIndex%this.ztext.length;
			 	this.textIndex ++;
				k("textWord").innerText = this.ztext[ index ];
			},
			show_text: function() {
				var classname = k("textCont").className;
				if (classname.indexOf("fadeInDown") == -1) {
				 	k("textCont").className += " fadeInDown";
				 	this._randText();
				} else {
					k("textCont").className = classname.replace(" fadeInDown", "");
				}
			},
			firstAddWord: !0,
			addWord: function() {
				var b = new v;
				this.firstAddWord && I && (new v, new v, b = new v, this.firstAddWord = !1);
				this.listBox.appendChild(b.ele);
				b.index = this.wordList.length;
				this.wordList.push(b);
				this.select(b.index);
			},
			select: function(b) {
				this.wordList[b] && (this.word && this.word.toStatic(), this.word = this.wordList[b], this.word.ele.style.marginTop = 0, this.word.ele.style.marginBottom = 0, this.changeBtn(), this.word.toEdit(), b == this.wordList.length - 1 ? (this.word.ele.parentNode.appendChild(this.btnBox), this.pubBox.style.display = "none") : (this.word.ele.parentNode.insertBefore(this.btnBox, this.wordList[b + 1].ele), this.pubBox.style.display = "block"), this.btnBox.style.visibility = "visible", this.index = this.word.index, this.setPenSize(this.penSize), b = this.word.ele.offsetTop, 0 == this.index && (b = 0), this.scrollYTo(b, 300))
			},
			remove: function(b) {
				if (this.wordList[b]) {
					var c = [];
					this.word == this.wordList[b] && (this.word = null);
					for (var d = 0; d < this.wordList.length; d++) d != b && (this.wordList[d].index = c.length, c.push(this.wordList[d]));
					this.wordList = c;
					this.composition()
				}
			},
			complete: function() {
				if (5 > this.word.paper.moveSum) {
					alert("您还没有写字呢～");
					return;
				};
				if( this.wordList.length >= 10) {
					alert("10个字正好");
					return;
				};
				this.btnBox.style.visibility = "hidden";
				document.body.appendChild(this.btnBox);
				this.pubBox.style.display = "block";
				this.word.toStatic();
				this.composition();
				
				this.addWord();
			},
			composition: function() {
				for (var b = 24.5901 * t, c = 81.967 * t, d, e = 0; e < this.wordList.length; e++) 1 < this.wordList.length ? (0 != e && this.wordList[e].paper.paddingTop > b ? (d = this.wordList[e].paper.paddingTop - b, this.wordList[e].ele.style.marginTop = "-" + (d > c ? c : d) + "px") : this.wordList[e].ele.style.marginTop = 0, e < this.wordList.length - 1 && this.wordList[e].paper.paddingBottom > b ? (d = this.wordList[e].paper.paddingBottom - b, this.wordList[e].ele.style.marginBottom = "-" + (d > c ? c : d) + "px") : this.wordList[e].ele.style.marginBottom = 0) : (this.wordList[e].ele.style.marginTop = 0, this.wordList[e].ele.style.marginBottom = 0)
			},
			changeBtn: function() {
				var b = this;
				this.word && this.word.paper.undoActi() ? C(this.undo_btn, "disabled") : B(this.undo_btn, "disabled");
				this.word && this.word.paper.repeatActi() ? (C(this.repeat_btn, "disabled"), this.showRepeatBtn()) : (B(this.repeat_btn, "disabled"), clearTimeout(b._repeat_timer), b._repeat_timer = setTimeout(function() {
					b.hideRepeatBtn()
				}, 3E3))
			},
			_repeat_timer: null,
			showRepeatBtn: function() {
				var b = this;
				clearTimeout(this._repeat_timer);
				this._repeat_timer = setTimeout(function() {
					b.hideRepeatBtn()
				}, 5E3)
			},
			hideRepeatBtn: function() {
				clearTimeout(this._repeat_timer);
				this.repeat_btn.style.marginLeft = "0"
			},
			setPenSize: function(b) {
				for (var c = 0; c < this.tagPSList.length; c++) this.tagPSList[c].getAttribute("value") == b ? (this.tagPSList[c].className = "selected", this.word.paper.penSize = parseInt(b), this.penSize = b, this.penColor.style.width = this.penColor.style.height = .2 + parseInt(b) / 8 * .8 + "em") : this.tagPSList[c].className = ""
			},
			scrollYTo: function(b, c) {
				function d() {
					g++;
					var c, e = g;
					c = (b - f) * (e /= m) * e + f;
					// c = c - 80;
					window.scrollTo(window.pageXOffset, c);
					g < m && setTimeout(d, Math.round(1E3 / 36))
				}
				var e = document.documentElement.scrollHeight - document.documentElement.clientHeight,
					f = window.pageYOffset;
				b > e && (b = e);
				var m = Math.ceil(36 * (c || 300) / 1E3),
					g = 0;
				d()
			},
			uploadJSON: function() {
				var b, m;
				
				if (1 > this.word.paper.moveSum) {
					alert("您还没有写字呢～");
					return;
				};

				b = '{"username":"'+ encodeURIComponent(username) +'", "headimgurl":"'+ encodeURIComponent(headimg) +'", "openid":"'+ openid +'"';
				if ("paper" == this.type) {
					b += ',"list":[';
					for (var c = [], d = 0; d < this.wordList.length; d++) {
						m = this.wordList[d].paper.getPenmanship();
						if (m) {
							c.push(m);
						}
					}
					b += c.join(",") + "]"
				}
				b += "}";
				
				
				
				var ajax = new XMLHttpRequest;
				ajax.onreadystatechange = function() {
					if (ajax.readyState == 4 && ajax.status == 200) {
						var ret = ajax.responseText;
						console.log(ret);
						ret = JSON.parse(ret);
						
						if ( ret["sta"] == 200 ) {
							window.location.href =show+"&id="+ret["id"]+"&follow="+follow;
						} else {
							alert("出现异常错误，请稍候再试");
						}
					};
				};
				ajax.ontimeout = function() {
					alert("网络环境不佳，请稍候再试！");
				}
				
				
				ajax.open("POST", upload, true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send("txt="+b+"&openid="+openid);
			},
			getThumbnail: function() {
				return 0 < this.wordList.length ? this.wordList[0].paper.getImgData("jpeg", 305, .7) : ""
			}
		};
	v.prototype = {
		toStatic: function() {
			1 < p.wordList.length && 5 > this.paper.moveSum ? (this.del(), p.remove(this.index)) : (this.ele.className = "canvasShow", this.paper.setDisabled(!0), this.paper.surveyPadding(), p.word = null)
		},
		toEdit: function() {
			"play" == this.paper.status && (this.paper._stop(), this.paper.recovery());
			this.ele.className = "canvasEdit";
			this.paper.setDisabled(!1)
		},
		del: function() {
			1 >= p.wordList.length ? (p.select(0), p.word.paper.clearPaper()) : (p.remove(this.index), this.btn_edit.onmousedown = this.btn_del.onclick = this.paper.onchange = null, "play" == this.paper.status && (this.paper._stop(), this.paper.recovery()), this.ele.parentNode.removeChild(this.ele))
		}
	};
	D.prototype = {
		lineWidth: 1,
		color: "rgb(0,0,0)",
		penSize: 8,
		fontWidth: 320,
		// bg: "images/paper.jpg",
		moveSum: 0,
		stroke: null,
		status: "edit",
		init: function() {
			var b = this;
			if (this.canvas.getContext) {
				this.bgCanvas = document.createElement("canvas");
				this.bgCanvas.width = this.canvas.width;
				this.bgCanvas.height = this.canvas.height;
				this.canvas.style.position = "absolute";
				this.canvas.style.left = 0;
				this.canvas.style.top = 0;
				this.canvas.parentNode.appendChild(this.bgCanvas);
				this.ctx = this.canvas.getContext("2d");
				this.bgCtx = this.bgCanvas.getContext("2d");
				this.ctx.strokeStyle = this.color;
				this.ctx.fillStyle = this.color;
				this.addEvent(this.canvas, "selectstart", function() {
					return !1
				});
				this.bgImg = new Image;
				// this.bgImg.src = window.paperImgSrc || this.bg;
				// this.createPat("glodPat", window.glodImgSrc || "/microblog-v3/h5/chunjie/images/glod.png", 150);
				// this.createPat("blackPat", window.blackImgSrc || "/microblog-v3/h5/chunjie/images/black.png", 150);
				var c = function(d) {
						if ("edit" === b.status) {
							var e, f;
							if ("touchstart" == d.type) {
								if (2 <= d.touches.length) return;
								e = d.touches[0].pageX;
								f = d.touches[0].pageY;
								b.removeEvent(b.canvas, "mousedown", c)
							} else e = d.pageX, f = d.pageY;
							b.canvasPos = b.canvas.getBoundingClientRect();
							b.canvasPos = {
								left: b.canvasPos.left + (window.scrollX || window.pageXOffset),
								top: b.canvasPos.top + (window.scrollY || window.pageYOffset)
							};
							e -= b.canvasPos.left;
							f -= b.canvasPos.top;
							b.stroke = {
								t: new Date,
								d: [{
									x: e,
									y: f,
									t: 0
								}],
								c: b.color,
								p: b.penSize
							};
							b.moveBegin(e, f, d.type);
							d.preventDefault()
						}
					};
				this.addEvent(this.canvas, "touchstart", c);
				this.addEvent(this.canvas, "mousedown", c)
			}
		},
		moveBegin: function(b, c, d) {
			var e = this;
			window.getSelection() ? window.getSelection().removeAllRanges() : document.selection.empty();
			this.ctx.save();
			this.ctx.moveTo(b, c);
			this.preDot = null;
			this.moveQueue = [];
			this.firstMove = 0;
			this.lineWidth = this.penSize / 2 * (this.fontWidth / 320);
			this.__moveEvent && (this.removeEvent(document, "mousemove", this.__moveEvent), this.removeEvent(document, "touchmove", this.__moveEvent), this.removeEvent(document, "mouseup", this.__endEvent), this.removeEvent(document, "touchend", this.__endEvent));
			this.__moveEvent = function(b) {
				if ("edit" === e.status) {
					var c, d;
					if ("touchmove" == b.type) {
						if (2 <= b.touches.length) return;
						c = b.touches[0].pageX;
						d = b.touches[0].pageY
					} else c = b.pageX, d = b.pageY;
					c -= e.canvasPos.left;
					d -= e.canvasPos.top;
					e.stroke.d.push({
						x: c,
						y: d,
						t: new Date - e.stroke.t
					});
					e.moving(c, d);
					b.preventDefault()
				}
			};
			this.__endEvent = function(b) {
				if ("edit" === e.status && (e.moveEndFn(), e.penmanship.length ? e.stroke.t -= e.penmanshipTime : (e.penmanshipTime = e.stroke.t, e.stroke.t = e.stroke.t.getTime()), e.penmanship.push(e.stroke), e._clearQueue = null, e.repeatQueue = [], e.stroke = null, "function" == typeof e.onchange)) e.onchange()
			};
			"touchstart" == d ? (this.addEvent(document, "touchmove", this.__moveEvent), this.addEvent(document, "touchend", this.__endEvent)) : (this.addEvent(document, "mousemove", this.__moveEvent), this.addEvent(document, "mouseup", this.__endEvent));
			this.clearPaint();
			this.moving(b, c)
		},
		moving: function(b, c) {
			var d;
			d = 0;
			if (this.moveQueue.length && (d = this.moveQueue[this.moveQueue.length - 1], d = Math.sqrt((d.x - b) * (d.x - b) + (d.y - c) * (d.y - c)), 0 == d)) return;
			this.moveSum++;
			H && !this.firstMove && 2 == this.moveQueue.length && 4 * d < this.moveQueue[1].c && (this.moveQueue[0].x -= 2 / 3 * (this.moveQueue[0].x - this.moveQueue[1].x), this.moveQueue[0].y -= 2 / 3 * (this.moveQueue[0].y - this.moveQueue[1].y), this.moveQueue[1].c /= 2 / 3 * this.moveQueue[1].c);
			d = {
				x: b,
				y: c,
				c: d
			};
			this.moveQueue.push(d);
			3 <= this.moveQueue.length && (d = this.moveQueue.shift(), this.actionPaint(d))
		},
		actionPaint: function(b, c) {
			var d = b.x,
				e = b.y,
				f = b.c;
			if (!this.preDot || 0 !== f) {
				this.nextDot = this.moveQueue.length ? this.moveQueue[0] : null;
				if (f) {
					this.ctx.moveTo(this.preDot.x, this.preDot.y);
					var g = 0;
					!this.firstMove && this.nextDot && f > 3 * this.nextDot.c && (f /= 4, g = 1);
					this.firstMove = 1;
					bs = this.fontWidth / 320 * this.penSize;
					c || (c = f < .003125 * this.fontWidth ? 1.625 * bs : f < .00625 * this.fontWidth ? 1.375 * bs : f < .009375 * this.fontWidth ? 1.25 * bs : f < .015625 * this.fontWidth ? 1.125 * bs : f < .021875 * this.fontWidth ? bs : f < .028125 * this.fontWidth ? .875 * bs : f < .034375 * this.fontWidth ? .75 * bs : f < .046875 * this.fontWidth ? .625 * bs : f < .0625 * this.fontWidth ? .5 * bs : .375 * bs);
					this.toLW = c;
					if (g) for (g = 1; 3 >= g; g++) this.paintDot(d + g / 3 * (this.preDot.x - d), e + g / 3 * (this.preDot.y - e), f)
				}
				this.paintDot(d, e, f);
				this.preDot = b
			}
		},
		moveEndFn: function() {
			this.removeEvent(document, "mousemove", this.__moveEvent);
			this.removeEvent(document, "touchmove", this.__moveEvent);
			this.removeEvent(document, "mouseup", this.__endEvent);
			this.removeEvent(document, "touchend", this.__endEvent);
			--this.ctx.lineWidth;
			for (var b; this.moveQueue.length;) b = this.moveQueue.shift(), this.actionPaint(b, this.fontWidth / 320 * this.penSize / 8);
			this.showToCanvas(!0)
		},
		setDisabled: function(b) {
			b && "edit" == this.status ? this.status = "disabled" : b || "disabled" != this.status || (this.status = "edit")
		},
		undoActi: function() {
			return this._clearQueue && this._clearQueue.length || this.penmanship.length
		},
		undo: function() {
			if ("edit" === this.status) if (this._clearQueue && this._clearQueue.length) {
				if (this.penmanship = this._clearQueue, this._clearQueue = null, this.recovery(), "function" == typeof this.onchange) this.onchange()
			} else if (this.penmanship.length) {
				var b = this.penmanship.pop();
				this.repeatQueue.push([b]);
				0 == this.penmanship.length && (this._moveSum = this.moveSum, this.moveSum = 0);
				this.recovery();
				if ("function" == typeof this.onchange) this.onchange()
			}
		},
		repeatActi: function() {
			return !!this.repeatQueue.length
		},
		repeat: function() {
			if ("edit" === this.status && this.repeatQueue.length && (0 == this.penmanship.length && (this.moveSum = this._moveSum), this.penmanship = this.penmanship.concat(this.repeatQueue.pop()), this.recovery(), "function" == typeof this.onchange)) this.onchange()
		},
		_clearQueue: null,
		clearPaper: function() {
			if ("edit" === this.status && (this.preDot = null, this.moveSum = 0, this.penmanship.length && (this._clearQueue = this.penmanship), this.penmanship = [], this.ctx.beginPath(), this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height), this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height), this.ctx.closePath(), "function" == typeof this.onchange)) this.onchange()
		},
		replay: function() {
			0 != this.penmanship.length && ("play" == this.status ? (this._stop(), this.recovery()) : (this.oldStatus = this.status, this.status = "play", this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height), this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height), this.ctx.beginPath(), this.playPos = 0, this._play()))
		},
		showToCanvas: function(b) {
			if (!this.isRecovery && b) {
				this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height);
				b = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
				console.log(b);
				// for (var c = 0; c < b.data.length; c += 4) 0 != b.data[c + 3] && (b.data[c] = 100, b.data[c + 1] = 30, b.data[c + 2] = 7, b.data[c + 3] = Math.round(.75 * b.data[c + 3]));
				// this.bgCtx.putImageData(b, -5 * t, 5 * t) // 不设置第二层convas
			}
		},
		_stop: function() {
			this.status = this.oldStatus;
			clearTimeout(this._playTimer)
		},
		_play: function() {
			function b() {
				c.color = d;
				c.penSize = e;
				c.playPos >= c.penmanship.length ? c.status = c.oldStatus : (clearTimeout(c._playTimer), c._playTimer = setTimeout(function() {
					c._play()
				}, 300))
			}
			var c = this,
				d = this.color,
				e = this.penSize,
				f = this.penmanship[this.playPos],
				g = 0;
			if (f && "play" == this.status) {
				this.color = f.c;
				this.penSize = f.p;
				this.moveBegin(f.d[0].x, f.d[0].y);
				1 == f.d.length && (c.moveEndFn(), b());
				var h = 1,
					k, g = 0;
				k = f.d[h];
				(function() {
					k && (c.moving(k.x, k.y), h >= f.d.length - 1 && (c.moveEndFn(), b()), h++, k = f.d[h]) && (clearTimeout(c._playTimer), c._playTimer = setTimeout(arguments.callee, k.t - g), g = k.t)
				})();
				this.playPos++
			}
		},
		recovery: function() {
			function b() {
				c.color = d;
				c.penSize = e;
				c.playPos >= c.penmanship.length && (c.status = "edit")
			}
			var c = this,
				d = this.color,
				e = this.penSize;
			this.isRecovery = !0;
			this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
			this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height);
			this.ctx.beginPath();
			this.playPos = 0;
			for (var f = this.penmanship[this.playPos]; f;) {
				this.color = f.c;
				this.penSize = f.p;
				this.moveBegin(f.d[0].x, f.d[0].y);
				1 == f.d.length && (this.moveEndFn(), b());
				for (var g = 1, h = 1; g < f.d.length; g++) {
					var k = f.d[h];
					h++;
					this.moving(k.x, k.y);
					h >= f.d.length && (this.moveEndFn(), b())
				}
				this.playPos++;
				f = this.penmanship[this.playPos]
			}
			this.isRecovery = !1;
			this.showToCanvas(!0)
		},
		paintDot: function(b, c, d) {
			var e = 8,
				f = this.lineWidth,
				g = this.color;
			this.glodPat && "glod" == this.color && (g = this.glodPat);
			this.blackPat && "black" == this.color && (g = this.blackPat);
			this.ctx.fillStyle = g;
			this.ctx.strokeStyle = g;
			if (this.preDot) {
				e = Math.floor(Math.abs(d) / (this.lineWidth / 3));
				if (1 < e) for (f = this.lineWidth, d = 0; d < e; d++) f -= (f - this.toLW) / (8 < e ? e : 8);
				else Math.abs(this.lineWidth - this.toLW) > this.fontWidth / 320 * this.penSize * .025 && (f = this.lineWidth - (this.lineWidth - this.toLW) / 8);
				var h = this.lineWidth * Math.sin(Math.atan((c - this.preDot.y) / (b - this.preDot.x))),
					k = this.lineWidth * Math.cos(Math.atan((c - this.preDot.y) / (b - this.preDot.x))),
					n = f * Math.sin(Math.atan((c - this.preDot.y) / (b - this.preDot.x))),
					l = f * Math.cos(Math.atan((c - this.preDot.y) / (b - this.preDot.x))),
					e = this.preDot.x + h;
				d = this.preDot.y - k;
				var h = this.preDot.x - h,
					k = this.preDot.y + k,
					p = b + n,
					q = c - l,
					n = b - n,
					l = c + l;
				this.ctx.beginPath();
				this.ctx.moveTo(e, d);
				this.ctx.lineTo(h, k);
				this.ctx.lineTo(n, l);
				this.ctx.lineTo(p, q);
				this.ctx.fill();
				this.ctx.closePath();
				this.ctx.fillStyle = g;
				this.ctx.lineWidth = this.lineWidth = f
			}
			this.ctx.beginPath();
			this.ctx.lineWidth = this.lineWidth = f;
			this.ctx.arc(b, c, this.lineWidth, 0, 2 * Math.PI);
			this.ctx.fill();
			this.ctx.closePath()
		},
		getPenmanship: function() {
			for (var b = "", c, d = 0; d < this.penmanship.length; d++) {
				0 != d && (b += ",");
				for (var b = b + ('{"t":' + this.penmanship[d].t + ","), b = b + ('"c":"' + this.penmanship[d].c + '",'), b = b + ('"p":"' + this.penmanship[d].p + '",'), b = b + '"d":[', e = 0; e < this.penmanship[d].d.length; e++) c = this.penmanship[d].d[e], 0 != e && (b += ","), b += Math.round(100 * c.x) / 100 + "," + Math.round(100 * c.y) / 100 + "," + (c.t || 0);
				b += "]}"
			}
			if (b) {
				return b = '{"w":' + this.fontWidth + ',"p":[' + b + "]}"
			} else {
				return false;
			}
		},
		setPenmanship: function(b) {
			try {
				var c;
				"string" == typeof b ? c = eval("(" + b + ")") : "object" == typeof b && (c = b);
				var d = c.p,
					e = this.fontWidth / c.w,
					f;
				for (b = 0; b < d.length; b++) {
					if (0 != d[b].d.length % 3) {
						console.log("数据格式错误!");
						return
					}
					c = [];
					for (var g = 0; g < d[b].d.length; g += 3) f = {
						x: Math.round(d[b].d[g] * e * 10) / 10,
						y: Math.round(d[b].d[g + 1] * e * 10) / 10,
						t: d[b].d[g + 2]
					}, c.push(f);
					d[b].d = c
				}
				this.penmanship = d;
				if ("function" == typeof this.onchange) this.onchange()
			} catch (h) {
				console.log("数据错误"), console.log(h)
			}
		},
		paddingTop: 0,
		paddingBottom: 0,
		surveyPadding: function() {
			this.paddingBottom = this.paddingTop = 0;
			for (var b = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height), c = 0; c < this.canvas.height; c++) for (var d = 0; d < this.canvas.width; d++) if (0 < b.data[4 * (c * this.canvas.height + d) + 3]) {
				this.paddingTop = c - 1;
				c = this.canvas.height;
				break
			}
			for (c = this.canvas.height - 1; 0 <= c; c--) for (d = 0; d < this.canvas.width; d++) if (0 < b.data[4 * (c * this.canvas.height + d) + 3]) {
				this.paddingBottom = this.canvas.height - c - 1;
				c = -1;
				break
			}
		},
		clearPaint: function() {
			this.preDot = null
		},
		addEvent: function(b, c, d) {
			b.attachEvent ? b.attachEvent("on" + c, d) : b.addEventListener(c, d, !1)
		},
		removeEvent: function(b, c, d) {
			b.detachEvent ? b.detachEvent("on" + c, d) : b.removeEventListener(c, d, !1)
		},
		getImgData: function(b, c, d) {
			return E(b, d, this, c || 610)
		},
		getSmallImgData: function(b, c) {
			return E(b, c, this, 80)
		}
	};
	A()
})();