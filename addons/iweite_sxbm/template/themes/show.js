// 循环播放
var loop_replay = {
	flag: 0,
	nodes: null,
	playable: true,
	init: function(callback) {
		this.nodes = hall(".cvsBtn a");
		this.show();
		this.callback = callback ? callback : null;
	},
	show: function() {
		if (this.flag == this.nodes.length) {
			this.flag = 0;
			this.playable = false;
			this.onend();
		} else {
			this.nodes[this.flag].click();
			document.querySelectorAll(".canvasShow")[this.flag].querySelector("canvas").style.display = "block"
		}
		this.flag ++;
	},
	onend: function() {
		var that = this;
		h(".username").className += " fadeInDown";
		setTimeout(function(){
			h(".headimg").className += " gaizhang";
			setTimeout(function() {
				that.callback && that.callback();
			}, 800);
		}, 1000);
	}
}

// 开始播放
function paintstart(callback) {
	// y.init();
	y.preload(sxdata);
	setTimeout(function() {
		loop_replay.init(callback);
	}, 1000);
	// 播放音乐
	var audio = document.createElement("audio");
	audio.autoplay = "autoplay";
	audio.loop = "loop";
	audio.src = "http://ws.stream.qqmusic.qq.com/579939.m4a?fromtag=46";
	document.body.appendChild(audio);
}
function _id(b) {
	return document.getElementById(b);
}
function h(b, c) {
	return (c || document).querySelector(b)
}
function hall(b, c) {
	return (c || document).querySelectorAll(b);
}
function t(b, c) {
	var d = b.className.replace(new RegExp("\\b" + c + "\\b", "g"), "");
	b.className = d.replace(/ +/g, " ").replace(/(^ +| +$)/g, "") + " " + c
}
function u(b, c) {
	b.className = b.className.replace(new RegExp("\\b" + c + "\\b", "g"), " ").replace(/ +/g, " ").replace(/(^ +| +$)/g, "")
}
function z(b, c, d) {
	b && b.attachEvent ? b.attachEvent("on" + c, d) : b && b.addEventListener(c, d, !1)
}
function k(b) {
	b = (A + b).replace(/-+/g, "-").replace(/(^-|-$)/g, "").split("-");
	for (var c = 1; c < b.length; c++) b[c] = b[c].substr(0, 1).toLocaleUpperCase() + b[c].substr(1).toLowerCase();
	return b.join("")
}
function q() {
	var b = this,
		c = document.body.clientWidth,
		d = c / 640;
	this.ele = document.createElement("div");
	this.ele.className = "canvasShow";
	this.ele.innerHTML = '<div class="canvas"></div><div class="cvsBtn" style="display:block;padding-top:3.5rem;"><a class="btn_02" href="javascript:void(0)">播放</a></div>';
	var e = document.createElement("canvas");
	h(".canvas", this.ele).appendChild(e);
	e.width = 610 * d;
	e.height = 610 * d;
	this.paper = new w(e);
	this.paper.fontWidth = c;
	this.btnBox = h(".cvsBtn", this.ele);
	this.btn_replay = (this.btnBox || document).querySelectorAll("a")[0];
	this.btn_replay.onclick = function() {
		b.paper.replay()
	}
}
function w(b) {
	if (b.nodeType) this.canvas = b;
	else if ("string" == typeof b) this.canvas = h(b);
	else return;
	this.init();
	this.penmanship = [];
	this.repeatQueue = []
}
	
var l = document.body.clientWidth
var p = l / 640;
document.documentElement.style.fontSize = 32 * p + "px";
document.body.style.width = l + "px";
// window.__loadData = function(b) {
// 	console.log(b);
// 	y.preload(b);
// };
var m = new Image,
	n = new Image,
	y = {
		wordList: [],
		word: null,
		imgMoveSum: 0,
		imgUrl: "",
		color: "",
		index: 0,
		id: "",
		init: function() {
			var b;
			b = getQueryStr("id");
			this.id = b;
			b = "../data/"+b.substr(0, 8)+"/"+b;
			var c = document.createElement("script");
			c.src = b;
			
			// var ajax = new XMLHttpRequest;
			// ajax.onreadystatechange = function() {
			// 	if (ajax.status == 404) {
			// 		alert("not find");
			// 	};
			// }
			// ajax.open("GET", b, true);
			// ajax.send();

			document.body.insertBefore(c, document.body.firstChild)
		},
		preload: function(b) {
			b = JSON.parse(b);
			// 判断字数
			var lens = b.list.length;
			if (lens <= 5) {
				_id("col1").className += " col";
				_id("col2").style.display = "none";
				switch(lens) {
					case 1:
						_id("col1").className += " oneword";
						break;
					case 2:
						_id("col1").className += " twoword";
						break;
					case 5:
						_id("col1").className += " fiveword";
						break;
				}
			} else {
				_id("col1").className += " cols_first";
				_id("col2").className += " cols_second";
			}

			b.type = b.type || (b.list && b.list.length ? "paper" : "text");
			var c = this,
				d = function() {
					this._l = 0;
					n._l || m._l || c.load(b)
				};
			if ("paper" == b.type) {
				for (var e = 0; e < b.list.length; e++) for (var f = 0; f < b.list[e].p.length; f++)"black" == b.list[e].p[f].c && (n._l = 1), "glod" == b.list[e].p[f].c && (m._l = 1);
				n._l || m._l ? (n._l && (n.onload = d, n.src = window.blackImgSrc || "black.png"), m._l && (m.onload = d, m.src = window.glodImgSrc || "glod.png")) : this.load(b)
			} else this.load(b)
		},
		load: function(b) {

			// 设置昵称头像
			_id("username").innerHTML = "<span style='font-weight: 600;font-size: 16px;'>"+b.username+"</span>的墨宝";
			_id("headimg").src = b.headimgurl;
			
			window.scrollTo(0, 0);
			
			var d = b.img || "",
				e = decodeURIComponent(b.t || "").replace(/</g, "&lt;"),
				f = decodeURIComponent(b.username || "").replace(/</g, "&lt;");
			var g = parseInt(b.classType) || 1;
			
			var c = '<div class="scenery">\
						<div class="canvasCont" data-animationIn="fadeIn 1s 3.2s"><div class="canvasList"></div></div>\
					</div';

			// console.log( c );
			var previewPages = hall(".previewPage");
			for(var i=0,lens=previewPages.length; i<lens; i++) {
				previewPages[i].innerHTML = c;
			}
			
			// h(".previewPage").innerHTML = c;

			if ("paper" == b.type) {
				for ((new q, new q, new q), d = 24.5901 * p, g = 81.967 * p, c = 0; c < b.list.length; c++) {
					if (c >= 5) {
						this.listBox = h("#col2 .canvasList");
					} else {
						this.listBox = h("#col1 .canvasList");
					}
					e = new q;
					this.listBox.appendChild(e.ele); 
					e.index = this.wordList.length; 
					this.wordList.push(e); 
					e.paper.setPenmanship(b.list[c]); 
					e.paper.recovery();
				}
			}
		}
	};
q.prototype = {
	toStatic: function() {
		1 < editor.wordList.length && 5 > this.paper.moveSum ? (this.del(1), editor.remove(this.index)) : (this.btnBox.style.display = "block", this.paper.setDisabled(!0), this.word = null)
	}
};
w.prototype = {
	lineWidth: 1,
	color: "rgba(0,0,0, 1)",
	penSize: 8,
	fontWidth: 320,
	moveSum: 0,
	stroke: null,
	status: "show",
	init: function() {
		this.canvas.getContext && (this.bgCanvas = document.createElement("canvas"), this.bgCanvas.width = this.canvas.width, this.bgCanvas.height = this.canvas.height, this.canvas.style.position = "absolute", this.canvas.style.left = 0, this.canvas.style.top = 0, this.canvas.parentNode.appendChild(this.bgCanvas), this.ctx = this.canvas.getContext("2d"), this.bgCtx = this.bgCanvas.getContext("2d"), m.src && this.createPat("glodPat", m, 150), n.src && this.createPat("blackPat", n, 150))
	},
	moveBegin: function(b, c, d) {
		window.getSelection() ? window.getSelection().removeAllRanges() : document.selection.empty();
		this.ctx.save();
		this.ctx.moveTo(b, c);
		this.preDot = null;
		this.moveQueue = [];
		this.firstMove = 0;
		this.lineWidth = this.penSize / 2 * (this.fontWidth / 320);
		this.clearPaint();
		this.moving(b, c)
	},
	moving: function(b, c) {
		var d;
		d = 0;
		if (this.moveQueue.length && (d = this.moveQueue[this.moveQueue.length - 1], d = Math.sqrt((d.x - b) * (d.x - b) + (d.y - c) * (d.y - c)), 0 == d)) return;
		this.moveSum++;
		!this.firstMove && 2 == this.moveQueue.length && 4 * d < this.moveQueue[1].c && (this.moveQueue[0].x -= 2 / 3 * (this.moveQueue[0].x - this.moveQueue[1].x), this.moveQueue[0].y -= 2 / 3 * (this.moveQueue[0].y - this.moveQueue[1].y), this.moveQueue[1].c /= 2 / 3 * this.moveQueue[1].c);
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
		--this.ctx.lineWidth;
		for (var b; this.moveQueue.length;) b = this.moveQueue.shift(), this.actionPaint(b, this.fontWidth / 320 * this.penSize / 8);
		this.showToCanvas(!0)
	},
	_clearQueue: null,
	clearPaper: function() {
		if ("show" === this.status && (this.preDot = null, this.moveSum = 0, this.penmanship.length && (this._clearQueue = this.penmanship), this.penmanship = [], this.ctx.beginPath(), this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height), this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height), this.ctx.closePath(), "function" == typeof this.onchange)) this.onchange()
	},
	replay: function() {
		0 != this.penmanship.length && (
			"play" == this.status ? (this._stop(), this.recovery()) : (this.hideViewImg(), this.oldStatus = this.status,
		 	this.status = "play",
		 	this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height),
		 	this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height),
		 	this.ctx.beginPath(),
		 	this.playPos = 0,
		 	this._play())
		)
	},
	showToCanvas: function(b) {
		if (!this.isRecovery && b) {
			this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height);
			b = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
			// for (var c = 0; c < b.data.length; c += 4) 0 != b.data[c + 3] && (b.data[c] = 159, b.data[c + 1] = 17, b.data[c + 2] = 13, b.data[c + 3] = Math.round(.75 * b.data[c + 3]));
			// this.bgCtx.putImageData(b, -5 * p, 5 * p)
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
			c.playPos >= c.penmanship.length ? c.status = c.oldStatus : (
				clearTimeout(c._playTimer), 
				c._playTimer = setTimeout(function() {
					c._play()
				}, 300)
			)
		}
		var c = this,
			d = this.color,
			e = this.penSize,
			f = this.penmanship[this.playPos],
			g = 0;

		// 执行下一次播放
		if (!this.penmanship[this.playPos+1]) {
			setTimeout(function(){
				if (loop_replay.playable) {
					loop_replay.show();
				};
			}, 1000);
		};

		if (f && "play" == this.status) {
			this.color = f.c;
			this.penSize = addPenSize ? addPenSize : f.p;
			this.moveBegin(f.d[0].x, f.d[0].y);
			1 == f.d.length && (c.moveEndFn(), b());
			var k = 1,
				h, g = 0;
			h = f.d[k];
			(function() {
				h && (
					c.moving(h.x, h.y), 
					k >= f.d.length - 1 && (
						c.moveEndFn(), 
						b()), 
						k++, 
						h = f.d[k]) && (
							clearTimeout(c._playTimer), 
							c._playTimer = setTimeout(
								arguments.callee, h.t - g
							), g = h.t+10)
			})();
			this.playPos++
		}
	},
	recovery: function() {
		var b = this.color,
			c = this.penSize;
		this.isRecovery = !0;
		this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
		this.bgCtx.clearRect(0, 0, this.canvas.width, this.canvas.height);
		this.ctx.beginPath();
		this.playPos = 0;
		for (var d = this.penmanship[this.playPos]; d;) {
			this.color = d.c;
			this.penSize = d.p;
			this.moveBegin(d.d[0].x, d.d[0].y);
			1 == d.d.length && (this.moveEndFn(), this.color = b, this.penSize = c);
			for (var e = 1, f = 1; e < d.d.length; e++) {
				var g = d.d[f];
				f++;
				this.moving(g.x, g.y);
				f >= d.d.length && (this.moveEndFn(), this.color = b, this.penSize = c)
			}
			this.playPos++;
			d = this.penmanship[this.playPos]
		}
		this.isRecovery = !1;
		this.showToCanvas(!0);
		this.surveyPadding()
	},
	hideViewImg: function() {
		this.viewImg && (this.viewImg.style.display = "none")
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
				l = f * Math.sin(Math.atan((c - this.preDot.y) / (b - this.preDot.x))),
				m = f * Math.cos(Math.atan((c - this.preDot.y) / (b - this.preDot.x))),
				e = this.preDot.x + h;
			d = this.preDot.y - k;
			var h = this.preDot.x - h,
				k = this.preDot.y + k,
				n = b + l,
				p = c - m,
				l = b - l,
				m = c + m;
			this.ctx.beginPath();
			this.ctx.moveTo(e, d);
			this.ctx.lineTo(h, k);
			this.ctx.lineTo(l, m);
			this.ctx.lineTo(n, p);
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
	setPenmanship: function(b) {
		try {
			var c;
			"string" == typeof b ? c = eval("(" + b + ")") : "object" == typeof b && (c = b);
			var d = c.p,
				e = this.fontWidth / c.w,
				f;
			for (b = 0; b < d.length; b++) {
				d[b].p = d[b].p || 8;
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
	createPat: function(b, c, d) {
		d = Math.round(d * p);
		var e = document.createElement("canvas");
		e.width = e.height = d;
		e.getContext("2d").drawImage(c, 0, 0, d, d);
		this[b] = this.ctx.createPattern(e, "repeat")
	}
};

function getQueryStr(str) {   
    var reg = new RegExp("(^|&)" + str + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return (r[2]);
    return "";   
}