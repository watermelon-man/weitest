!
    function (t, i, e) {
        var n = t.documentElement,
            a = "orientationchange" in i ? "orientationchange" : "resize",
            s = function () {
                var t = n.clientWidth;
                t !== e && (n.style.fontSize = 20 * (t / 320) + "px")
            };
        t.addEventListener !== e && (i.addEventListener(a, s, !1), t.addEventListener("DOMContentLoaded", s, !1))
    }(document, window), window.oncontextmenu = function () {
    return !1
};
var sliderAnimate = function (t) {
    this.data = t, this.isClose = !1, this.firstDelay = t.firstDelay || 0, this.first = !0, this.offsetX = this.data.offsetX || 0, this.offsetY = this.data.offsetY || 0, this.bg = this.data.bg || "#999", this.init()
};
sliderAnimate.prototype = {
    init: function () {
        this.type = null, this.action = [this.animate1, this.animate2, this.animate3, this.animate4];
        var t = this,
            i = this.data.img;
        $(window).bind("resize.sliderAnimate", function () {
            var e = 0,
                n = 0,
                a = $(i.parentNode).width() / $(i.parentNode).height();
            if ($(i).width() / $(i).height() <= a) {
                $(i).css({
                    width: "100%",
                    left: 0
                });
                var s = ($(i).height() - $(i.parentNode).height()) / -2;
                $(i).css({
                    top: s
                }), n = s, e = 0
            } else {
                $(i).css({
                    height: "100%"
                });
                var o = ($(i).width() - $(i.parentNode).width()) / -2;
                $(i).css({
                    left: o
                }), n = 0, e = o
            }
            t.data.offsetX = e, t.data.offsetY = n, null != t.type && t.reset.call(t)
        })
    },
    createOne: function (t, i, e, n, a, s) {
        var o = document.createElement("div");
        $(o).css({
            width: t,
            height: i,
            left: -1 * a,
            top: -1 * s,
            "-webkit-perspective": t + "px",
            perspective: t + "px"
        }), o.className = "oneSliderOut";
        var r = document.createElement("div");
        r.className = "oneSliderInner", $(r).css({
            width: "100%",
            height: "100%",
            position: "relative",
            "z-index": 9,
            "-webkit-transform-style": "preserve-3d",
            "-webkit-transform": "translateZ(" + e / 2 * -1 + "px) rotateY(0deg)"
        }).attr({
            tz: e / 2 * -1
        });
        var d = document.createElement("div");
        $(d).css({
            width: "100%",
            height: "100%",
            background: this.bg,
            "-webkit-transform": "translateZ(" + 0.5 * e + "px)"
        }), d.innerHTML = '<img style="left:' + (a + this.data.offsetX) + "px; top:" + (s + this.data.offsetY) + "px; width:" + n.width + "px; height:" + n.height + 'px; " src="' + n.src + '" />';
        var c = document.createElement("div");
        $(c).css({
            width: e,
            height: "100%",
            background: this.bg,
            left: -1 * e,
            top: 0,
            "-webkit-transform-origin": "right",
            "-webkit-transform": "translateZ(" + 0.5 * e + "px) rotateY(-90deg)"
        });
        var h = document.createElement("div");
        $(h).css({
            width: e,
            height: "100%",
            background: this.bg,
            left: t,
            top: 0,
            "-webkit-transform-origin": "left",
            "-webkit-transform": "translateZ(" + 0.5 * e + "px) rotateY(90deg)"
        });
        var l = document.createElement("div");
        $(l).css({
            width: "100%",
            height: e,
            background: this.bg,
            left: 0,
            top: -1 * e,
            "-webkit-transform-origin": "bottom",
            "-webkit-transform": "translateZ(" + 0.5 * e + "px) rotateX(90deg)"
        });
        var f = document.createElement("div");
        $(f).css({
            width: "100%",
            height: e,
            background: this.bg,
            left: 0,
            top: i,
            "-webkit-transform-origin": "top",
            "-webkit-transform": "translateZ(" + 0.5 * e + "px) rotateX(-90deg)"
        });
        var m = document.createElement("div");
        return $(m).css({
            width: "100%",
            height: "100%",
            background: this.bg,
            left: 0,
            top: 0,
            "-webkit-transform-origin": "top",
            "-webkit-transform": "translateZ(" + e * -0.5 + "px) rotateY(-180deg)"
        }).addClass("oneSliderBack"), m.innerHTML = '<img style="left:' + (a + this.data.offsetX) + "px; top:" + (s + this.data.offsetY) + "px; width:" + n.width + "px; height:" + n.height + 'px; " src="' + n.src + '" />', r.innerHTML = d.outerHTML + c.outerHTML + h.outerHTML + l.outerHTML + f.outerHTML + m.outerHTML, o.innerHTML = r.outerHTML, o.outerHTML
    },
    create: function (t, i, e) {
        e = null == e ? 0 : e;
        var n = t.parentNode.offsetWidth,
            a = t.parentNode.offsetHeight,
            s = document.createElement("div");
        s.className = "sliderImageBox", $(s).css({
            left: 0,
            top: 0,
            width: n,
            height: a
        });
        for (var o = this.cutX * this.cutY, r = Math.ceil(n / this.cutX), d = Math.ceil(a / this.cutY), c = "",
                 h = 0; o > h; h++) {
            var l = h % this.cutX * r * -1,
                f = parseInt(h / this.cutX) * d * -1;
            l = Math.ceil(l), f = Math.ceil(f), c += this.createOne(r, d, this.z, t, l, f, e)
        }
        c += '<div style="clear:both"></div>', s.innerHTML = c, $(".sliderImageBox").remove(), $(s).appendTo(i)
    },
    getRandom: function (t, i) {
        return parseInt(Math.random() * ((i > t ? i - t : t - i) + 1) + (i > t ? t : i))
    },
    animate1: function () {
        this.type = 1, this.cutX = 4, this.cutY = 6, this.z = 20, this.create(this.data.img, this.data.box), $(this.data.img).css({
            visibility: "hidden"
        });
        var t = this.data.box,
            i = ["animate1_s1", "animate1_s2", "animate1_s3", "animate1_s4", "animate1_s5", "animate1_s6", "animate1_s7"],
            e = this,
            n = $(t).find(".oneSliderInner");
        setTimeout(function () {
            for (var t = 0; t < n.length; t++) {
                !
                    function (t) {
                        var a = i[e.getRandom(0, 6)],
                            s = e.getRandom(0, 200);
                        setTimeout(function () {
                            $(n[t]).addClass(a)
                        }, s)
                    }(t)
            }
        }, e.first ? e.firstDelay : 0), setTimeout(function () {
            $(e.data.img).css({
                visibility: "visible"
            })
        }, e.first ? e.firstDelay + 2300 : 2300)
    },
    animate2: function () {
        this.type = 2, this.cutX = 1, this.cutY = 10, this.z = 80, this.create(this.data.img, this.data.box), $(this.data.img).css({
            visibility: "hidden"
        });
        var t = this.data.box,
            i = this,
            e = $(t).find(".oneSliderInner");
        setTimeout(function () {
            for (var t = 0; t < e.length; t++) {
                !
                    function (t) {
                        setTimeout(function () {
                            $(e[t]).addClass("animate2_s1")
                        }, 80 * t)
                    }(t)
            }
        }, i.first ? i.firstDelay : 0), setTimeout(function () {
            $(i.data.img).css({
                visibility: "visible"
            })
        }, i.first ? i.firstDelay + 2000 : 2000)
    },
    animate3: function () {
        this.type = 3, this.cutX = 1, this.cutY = 9, this.z = 20, this.create(this.data.img, this.data.box), $(this.data.img).css({
            visibility: "hidden"
        });
        var t = this.data.box,
            i = this,
            e = $(t).find(".oneSliderInner");
        setTimeout(function () {
            for (var t = 0; t < e.length; t++) {
                !
                    function (t) {
                        setTimeout(function () {
                            $(e[t]).addClass("animate3_s1")
                        }, 60 * t)
                    }(t)
            }
        }, i.first ? i.firstDelay : 0), setTimeout(function () {
            $(i.data.img).css({
                visibility: "visible"
            })
        }, i.first ? i.firstDelay + 1500 : 1500)
    },
    animate4: function () {
        this.type = 4, this.cutX = 3, this.cutY = 5;
        this.cutX * this.cutY;
        this.z = 20, this.create(this.data.img, this.data.box, "4"), $(this.data.img).css({
            visibility: "hidden"
        });
        var t = this.data.box,
            i = this,
            e = $(t).find(".oneSliderInner");
        setTimeout(function () {
            for (var t = 0; t < e.length; t++) {
                $(e[t]).addClass(0 == t || 2 == t || 4 == t || 6 == t || 8 == t || 10 == t || 12 == t || 14 == t ? "animate4_s1" : "animate4_s2")
            }
        }, i.first ? i.firstDelay : 0), setTimeout(function () {
            $(i.data.img).css({
                visibility: "visible"
            })
        }, i.first ? i.firstDelay + 1400 : 1400)
    },
    auto: function () {
        var t = this,
            i = function () {
                0 == t.action.length && (t.action = [t.animate1, t.animate2, t.animate3, t.animate4]);
                var i = t.getRandom(0, t.action.length - 1);
                t.action[i].call(t), t.action.splice(i, 1), t.first = !1
            };
        setTimeout(function () {
            i()
        }, 0), this.timeout = setInterval(function () {
            t.isClose || i()
        }, 6000)
    },
    reset: function () {
        null != this.type && this.create(this.data.img, this.data.box, this.type)
    },
    close: function () {
        this.isClose = !0, clearInterval(this.timeout), $(window).unbind("resize.sliderAnimate")
    }
};
var tools = {
    windowOnload: function (t) {
        var i = window.onload;
        window.onload = "function" == typeof i ?
            function () {
                i(), t()
            } : function () {
                t()
            }
    },
    removeEl: function (t) {
        var i = "string" == typeof t ? document.querySelector(t) : t;
        return i.parentNode.removeChild(i)
    },
    image3d: function (t, i) {
        i = null == i ? !1 : i;
        var e = 0,
            n = 0,
            a = $(t.parentNode).width() / $(t.parentNode).height();
        if ($(t).width() / $(t).height() <= a) {
            $(t).css({
                width: "100%",
                height: "100%",
                left: 0
            });
            var s = ($(t).height() - $(t.parentNode).height()) / -2;
            $(t).css({
                top: s,
                visibility: "visible"
            }), n = s, e = 0
        } else {
            $(t).css({
                height: "100%",
                width: "100%",
                top: 0
            });
            var o = ($(t).width() - $(t.parentNode).width()) / -2;
            $(t).css({
                left: o,
                visibility: "visible"
            }), n = 0, e = o
        }
        if (!i) {
            $(t).css({
                visibility: "hidden"
            });
            var r = $(t).attr("auto"),
                d = $(t).attr("type");
            "ds" == d || "cake" == d ? r = !1 : (this.sliderObj = new sliderAnimate({
                img: $(".richLeftBg")[0],
                box: $(".richLeft")[0],
                offsetX: e,
                offsetY: n,
                firstDelay: 500
            }), this.sliderObj.auto())
        }
    },
    layer: function (t) {
        var i = this;
        document.querySelectorAll(".warning-container")[0] || ($("#yiyu-container").append('<div class="warning-container"><div class="warning-1 active">' + t + "</div></div>"), setTimeout(function () {
            i.removeEl(document.querySelectorAll(".warning-container")[0])
        }, 2030))
    },
    confirm: function (t, i) {
        var e = this;
        if (!document.querySelectorAll(".confirm-plugin")[0]) {
            var n = document.createElement("div");
            n.className = "confirm-plugin", n.innerHTML = '<div class="confirm-box active"><div class="confirm-is-return">' + t + '</div><div class="confirm-85eaeba8e"><div class="confirm-button-1">确定</div><div class="confirm-button-2">取消</div></div></div>', document.getElementById("yiyu-container").appendChild(n), document.querySelectorAll(".confirm-button-1")[0].addEventListener("click", function (t) {
                i(), e.removeEl(n), t.stopPropagation()
            }, !1), document.querySelectorAll(".confirm-button-2")[0].addEventListener("click", function (t) {
                e.removeEl(n), t.stopPropagation()
            }, !1)
        }
    },
    chromeSupport: function () {
        tools.removeEl(document.querySelectorAll(".loading")[0]);
        $("body").append('<div class="chrome-1491013521193"><div><a href="http://rj.baidu.com/soft/detail/14744.html?ald"><img src="../addons/haoman_dpm/new_messages/chrome.svg" alt="chrome"></a><h3><i class="iconfont"></i>当前浏览器不支持浏览，请更换谷歌浏览器</h3></div></div>')
    },
    loading: function () {
        $("body").append('<div class="ajax_loading"><div><a href="#"><img src="../addons/haoman_dpm/new_messages/loading.gif" alt="chrome"></a><h3>加载中....</h3></div></div>')
    }
};