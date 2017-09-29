(function (window) {
    var u = navigator.userAgent;
    var v = navigator.appVersion;
    var b = {};
    b.isIe = u.indexOf('Trident') > -1; //IE内核
    b.isOpera = u.indexOf('Presto') > -1; //opera内核
    b.isWebKit = u.indexOf('AppleWebKit') > -1; //苹果、谷歌内核
    b.isFirefox = u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1; //火狐内核
    b.isMobile = !!u.match(/AppleWebKit.*Mobile.*/); //是否为移动终端
    b.isIos = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    b.isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //android终端或uc浏览器
    b.isIPhone = u.indexOf('iPhone') > -1; //是否为iPhone或者QQHD浏览器
    b.isIPad = u.indexOf('iPad') > -1; //是否iPad
    b.isApp = u.indexOf('Safari') == -1; //是否web应该程序，没有头部与底部
    b.isWx = u.toLowerCase().match(/MicroMessenger/i) == "micromessenger";
    b.isQq = u.toLowerCase().match(/QQ/i) == "qq";
    b.isWeiBo = u.toLowerCase().match(/WeiBo/i) == "weibo";
    b.language = (navigator.browserLanguage || navigator.language).toLowerCase();
    b.isApi = typeof (api) != "undefined";//是否在app中打开
    b.isWin = function () {
        try {
            if (isWin == null || isWin == undefined)
                return false;
            if (isWin == "false") {
                return false;
            } else {
                return true;
            }
        } catch (e) {
            return false;
        }
    }; //是否为主窗口
    window.app = b;
})(window);
//关闭frame
var closeFrame = function () {
    try {
        api.closeWin({ name: api.winName });
    } catch (e) {

    }

}
//显示菜单
var showMenu = function () {

}
apiready = function () {
    app.isApi = true;
    if (!app.isWin()) {
        $(".h").show();
        $(".menu").hide();
    } else {
        $(".h").hide();
    }
    //关闭启动页
    try {
        api.removeLaunchView();
    } catch (e) {

    }
    //返回键
    api.addEventListener({
        name: 'keyback'
    }, function (ret, err) {
        api.closeWin({ name: api.winName });
    });

    apiUrlInt();
};
$(function () {
    if (!app.isApi)
        urlInt();
    $("[img]").each(function () {
        $(this).css("background-image", "url(" + $(this).attr("img") + ")");
    });
});
var apiUrlInt = function () {
    //控制url 连接
    $("[url]").unbind("click");

    $("[url]").click(function () {
        var $this = $(this);
        var href = $this.attr("url");
        var name = $this.attr("frame-name");
        var target = $this.attr("data-target");
        if (target == null || target == undefined || target == "")
            target = name;
        if (target == null || target == undefined || target == "")
            target = href;
        api.openWin({
            name: target,
            url: href,
            bounces: false,
            bgColor: 'rgba(255,255,255,255)',
            vScrollBarEnabled: false,
            hScrollBarEnabled: false,
            showProgress: true,
            softInputMode: true,
            animation: {
                type: "push",                //动画类型（详见动画类型常量）
                subType: "from_right",       //动画子类型（详见动画子类型常量）
                duration: 500                //动画过渡时间，默认300毫秒
            }
        });
    });
}
//web 初始化连接
var urlInt = function () {
    if (!app.isApi) {
        $("[url]").click(function () {
            var href = $(this).attr("url");
            location.href = href;
        });
    } else {
        apiUrlInt();
    }
    // scroll();
}
var scroll = function () {
    var IScroll = $.AMUI.iScroll;
    var myScroll = new IScroll('.of-y-s', {
        click: true, vScroll: true, bounce: true, hScrollbar: false, vScrollbar: false
    });
}
var go = function (url) {
    location.href = url;
}
window.alert = function (c, t) {
    var $a = $("#my-alert");
    if (t != undefined && t != null)
        $a.find(".am-modal-hd").text(t);
    $a.find(".am-modal-bd").text(c);
    $a.modal();
};

//handlebars 插件 编译模板
(function ($) {
    var compiled = {};
    $.fn.handlebars = function (template, data) {
        if (template instanceof jQuery) {
            template = $(template).html();
        }
        compiled[template] = Handlebars.compile(template);
        this.html(compiled[template](data));
        $("#")
    };
    $.nowpageIndex = 0;
    $.page = { pageindex: 1, pagesize: 10, totaldata: 10, totalpage: 1 };
    $.fn.runder = function (u, d, t, f) {
        if ($.page.pageindex > $.page.totalpage || $.nowpageIndex == $.page.pageindex) return;
        $.nowpageIndex = $.page.pageindex;
        $.loading.open();
        var $panel = this;
        $.post(u, d, function (c) {
            if (c.s) {
                $.page.totalpage = c.p.totalpage;
                $.page.pageindex = c.p.pageindex < c.p.totalpage ? c.p.pageindex + 1 : c.p.pageindex;
                if (t instanceof jQuery) {
                    t = $(t).html();
                }
                compiled[t] = Handlebars.compile(t);
                var html = compiled[t](c.d);
                $panel.html($panel.html() + html);
                if (f != undefined && f != null) {
                    f(html);
                }
            } else {
                alert("错误");
            }
            $.loading.close();
        }, "JSON");

    }
    $.loading = {
        open: function () {
            try {
                //api.showProgress({ style: "default", animationType: "zoom", title: "小二提示", text: "玩命的加载...", modal: true });
            } catch (e) {
                $("#loading").modal();
            }
        },
        close: function () {
            try {
                //api.hideProgress();
            } catch (e) {
                $("#loading").modal("close");
            } finally {
                $("#loading").modal("close");
            }

        }
    };
})(jQuery);
//格式化日期
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

function getScrollTop(page) {
    if (page == null)
        page = $(document.body);
    if (page instanceof jQuery)
        page = $(page)
    else
        page = $("#" + page);
    var scrollTop = 0, bodyScrollTop = 0, documentScrollTop = 0;
    if (page) {
        bodyScrollTop = page.scrollTop;
    }
    if (page) {
        documentScrollTop = page.scrollTop;
    }
    scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
    return scrollTop;
}

function getScrollHeight(page) {
    if (page == null)
        page = $(document.body);
    if (page instanceof jQuery)
        page = $(page)
    else
        page = $("#" + page);
    var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
    if (page) {
        bodyScrollHeight = page.scrollHeight;
    }
    if (page) {
        documentScrollHeight = page.scrollHeight;
    }
    scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
    return scrollHeight;
}

//浏览器视口的高度
function getWindowHeight(page) {
    if (page == null)
        page = $(document.body);
    if (page instanceof jQuery)
        page = $(page)
    else
        page = $("#" + page);
    var windowHeight = 0;
    if (document.compatMode == "CSS1Compat") {
        windowHeight = page.clientHeight;
    } else {
        windowHeight = page.clientHeight;
    }
    return windowHeight;
}
//
$("ac-list li").click(function () {
    $(this).find("input").focus();
});
//数字格式为货币
Number.prototype.formatMoney = function (places, symbol, thousand, decimal) {
    places = !isNaN(places = Math.abs(places)) ? places : 2;
    symbol = symbol !== undefined ? symbol : "";
    thousand = thousand || ",";
    decimal = decimal || ".";
    var number = this,
        negative = number < 0 ? "-" : "",
        i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
};
//div 滚动加载更多
(function ($) {
    $.fn.onScrollEnd = function (c) {

        $(this).scroll(function () {
            var _this = $(this);
            var top = _this.scrollTop();
            var h = _this[0].scrollHeight;
            var vh = _this.height();
            if (vh + top + 5 >= h) {
                c();
            }
        });
    }
})(jQuery)
//微信功能封装
var weixin = function () {
    var orderNo = "";
    var AppPay = function () {
        var wxPay = api.require('wxPay');
        var start = function () {
            wxPay.config({
                apiKey: 'wxc777455d99f1a570',
                mchId: '1262971101',
                partnerKey: '75871c30c714217aa70a1c743601851b',
                notifyUrl: 'http://g.linjiacn.com/pay/wxnotify'
            }, function (ret, err) {

                if (ret.status) {
                    paying()
                } else {
                    alert(err.code);
                }
            });
            //$.post("/Pay/WxConfig", function (d) {
            //    api.alert({ msg: JSON.stringify(d) });
            //    wxPay.config(function (ret, err) {
            //        api.alert({ msg: ret.status });
            //        if (ret.status) {
            //            paying()
            //        } else {
            //            api.alert({ msg: JSON.stringify(ret) });
            //        }
            //    });
            //});

        }
        var paying = function () {
            $.post("/Pay/WxAppInfo", { orderNo: orderNo }, function (d) {
                api.alert({ msg: JSON.stringify(d) });
                wxPay.pay(d, function (ret, err) {
                    api.alert({ msg: JSON.stringify(ret) + "," + JSON.stringify(err) });
                    if (ret.status) {
                        //alert(ret.result);
                    } else {
                        //alert(err.code+","+err.msg);
                    }
                });
            });
        }
        start();
    }

    var JsPay = function () {
        location.href = "/pay/wxpay/" + orderNo;
    }
    var getConfig = function (callback) {
        $.post("/Pay/WxConfig", function (d) {
            callback(d);
        });
    }
    function getNoncestr() {
        var timestamp = new Date().getTime();
        var Num = "";
        for (var i = 0; i < 6; i++) {
            Num += Math.floor(Math.random() * 10);
        }
        timestamp = timestamp + Num;
        return timestamp;
    }
    this.configStore = function () {
        var weiXin = api.require('weiXin');
        weiXin.config({
            appId: "wxc777455d99f1a570",
            mchId: "1262971101",
            partnerKey: "75871c30c714217aa70a1c743601851b",
            notifyUrl: "http://www.apicloud.com"
        }, function (ret, err) {
            if (ret.status) {
                api.alert({ msg: 'config:' + ret.status });
            } else {
                api.alert({ msg: '注册失败' });
            }
        })
    };


    this.Pay = function (order) {
        orderNo = order;
        if (app.isApi)
            AppPay();
        else
            JsPay();
    }
    //分享页面
    this.SharePage = function (t, des, img, url) {
        getConfig(function (d) {
            var appid = d.apiKey;
            var wx = api.require("wx");
            wx.shareWebpage({
                apiKey: appid,
                scene: "timeline",
                title: t,
                description: des,
                thumb: img,
                contentUrl: url

            }, function (ret, err) {
                api.alert({ msg: JSON.stringify(ret) });
                if (ret.status) {
                    alert('分享成功');
                } else {
                    alert(err.code);
                }
            });
        })

    }
    this.Oauth = function () {
        var wx = api.require('wx');
        wx.isInstalled(function (ret, err) {
            if (ret.installed) {
                alert("当前设备已安装微信客户端");
                wx.auth({
                    apiKey: "wxc777455d99f1a570"
                }, function (ret, err) {
                    api.alert({ msg: JSON.stringify(ret) + JSON.stringify(err) });
                    if (ret.status) {
                        api.alert({ msg: "配置成功:" + ret.code });
                    } else {
                        api.alert({ msg: "配置失败:" + err.code });
                    }
                });
            } else {
                alert('当前设备未安装微信客户端');
            }
        });

    }
    this.IsInstalled = function () {
        var wx = api.require('wx');
        wx.isInstalled(function (ret, err) {
            if (ret.installed) {
                alert("当前设备已安装微信客户端");
            } else {
                alert('当前设备未安装微信客户端');
            }
        });
    }
    this.ShareText = function (txt) {
        getConfig(function (d) {
            var wx = api.require('wx');
            wx.shareText({
                apiKey: d.apiKey,
                scene: 'timeline',
                text: txt
            }, function (ret, err) {
                if (ret.status) {
                    alert('分享成功');
                } else {
                    alert(err.code);
                }
            });
        });

    }

    this.reg = function () {
        var weiXin = api.require('weiXin');
        weiXin.registerApp(function (ret, err) {
            if (ret.status) {
                api.alert({ msg: '' + ret.status });
            } else {
                api.alert({ msg: '注册失败' });
            }
        })
    };
    this.loginWeiXin = function () {
        var weiXin = api.require('weiXin');
        weiXin.auth(function (ret, err) {
            if (ret.status) {
                api.alert({ msg: ret.token });
            } else {
                api.alert({ msg: err.msg });
            }
        });

    };
    this.logoutWeiXin = function () {
        var weiXin = api.require('weiXin');
        weiXin.cancelAuth(function (ret, err) {
            if (ret.status) {
                api.alert({ msg: '退出成功' });
            } else {
                api.alert({ msg: err.msg });
            }
        });

    };
    this.getUsersInfomation = function () {
        var weiXin = api.require('weiXin');
        weiXin.getUserInfo(function (ret, err) {
            if (ret.status) {
                api.alert({ msg: ret.nickname });
            } else {
                api.alert({ msg: err.msg });
            }
        });
    }

    this.refreshUserToken = function () {
        var weiXin = api.require('weiXin');
        weiXin.refreshToken(function (ret, err) {
            if (ret.status) {
                api.alert({ msg: '刷新成功' });
            } else {
                api.alert({ msg: err.msg });
            }
        });
    }
    //支付类接口
    this.getPayToken = function () {
        var weiXin = api.require('weiXin');
        weiXin.getToken({
            appId: 'wxc777455d99f1a570',
            secret: '02bc930fd4ea46fa3ce1b395ca30d0a5'
        }, function (ret, err) {
            if (ret.status) {
                document.getElementById("paytokenis").value = ret.token;
                document.getElementById("exipires").value = ret.expires;

            } else {
                api.alert({ msg: err.msg });
            }
        });
    };
    this.getPayOrder = function () {
        var subject = '电商宝支付';
        var body = '来自电商宝平台支付';
        var orderId = '1234567';
        var amount = '0.01';
        var noncestr = getNoncestr();//防重发字符串，
        var timestamp = new Date().getTime();//订单时间戳
        //特别提醒 ： 这个是个细节难点，我们必须把时间戳精确的秒，而不是毫秒
        //时间戳作用1：生成订单信息时，其中有两个参数：timestamp和app_signature 都需要传递这个防重发字符串
        //时间戳作用2：调用支付订单功能时，其中有两个参数：timestamp和sign 都需要传递这个防重发字符串
        timestamp = parseInt(timestamp / 1000);
        var packageInfo = getPackage(orderId, amount, subject, body);//生成package

        var info = getOrderInfo(noncestr, timestamp, packageInfo);

        var weiXin = api.require('weiXin');
        weiXin.getOrder({
            token: document.getElementById("paytokenis").value,
            orderInfo: info
        }, function (ret, err) {
            if (ret.status) {
                document.getElementById("payorderidis").value = ret.orderId;

                var signatureInfoT = "appid=wxc777455d99f1a570";
                signatureInfoT += "&appkey=v2appkeywxc777455d99f1a570";
                signatureInfoT += "&noncestr=" + noncestr;
                signatureInfoT += "&package=" + 'Sign=WXPay';
                signatureInfoT += "&partnerid=" + '75871c30c714217aa70a1c743601851b';
                signatureInfoT += "&prepayid=" + ret.orderId;
                signatureInfoT += "&timestamp=" + timestamp;

                signatureInfoT = $.sha1(signatureInfoT);
                weiXin.payOrder({
                    orderId: ret.orderId,
                    partnerId: '75871c30c714217aa70a1c743601851b',
                    nonceStr: noncestr,
                    timeStamp: timestamp,
                    package: 'Sign=WXPay',
                    sign: signatureInfoT
                }, function (ret, err) {
                    if (ret.status) {
                        document.getElementById("payResult").innerHTML = ret.result;
                    } else {
                        api.alert({ msg: err.msg });
                    }
                });
            } else {
                api.alert({ msg: err.msg });
            }
        });
    }
    function gotoPayOrder() {
        var noncestr = getNoncestr();//防重发字符串
        var timestamp = new Date().getTime();//订单时间戳
        //特别提醒 ： 这个是个细节难点，我们必须把时间戳精确的秒，而不是毫秒
        //时间戳作用1：生成订单信息时，其中有两个参数：timestamp和app_signature 都需要传递这个防重发字符串
        //时间戳作用2：调用支付订单功能时，其中有两个参数：timestamp和sign 都需要传递这个防重发字符串
        timestamp = parseInt(timestamp / 1000);


        var subject = '电商宝支付';
        var body = '来自电商宝平台支付';
        var orderId = '1234567';
        var amount = '0.01';
        var packageInfo = getPackage(orderId, amount, subject, body);//生成package

        var signatureInfo = "appid=wx79b2627b32e7950f";
        signatureInfo += "&appkey=v2appkeywxc777455d99f1a570";
        signatureInfo += "&noncestr=" + noncestr;
        signatureInfo += "&package=" + packageInfo;
        signatureInfo += "&partnerid=" + '75871c30c714217aa70a1c743601851b';
        signatureInfo += "&prepayid=" + document.getElementById("payorderidis").value;
        signatureInfo += "&timestamp=" + timestamp;

        signatureInfo = $.sha1(signatureInfo);
        var weiXin = api.require('weiXin');
        weiXin.payOrder({
            orderId: document.getElementById("payorderidis").value,
            partnerId: '75871c30c714217aa70a1c743601851b',
            nonceStr: noncestr,
            timeStamp: timestamp,
            package: 'Sign=WXPay',
            sign: signatureInfo
        }, function (ret, err) {
            if (ret.status) {
                document.getElementById("payResult").innerHTML = ret.result;
            } else {
                api.alert({ msg: err.msg });
            }
        });
    };
    //生成订单信息
    function getOrderInfo(noncestr, timestamp, packageInfo) {
        var traceid = "crestxu";//用户唯一biaosh

        var signatureInfo = "appid=wxc777455d99f1a570";
        signatureInfo += "&appkey=v2appkey75871c30c714217aa70a1c743601851b";
        signatureInfo += "&noncestr=" + noncestr;
        signatureInfo += "&package=" + packageInfo;
        signatureInfo += "&timestamp=" + timestamp;
        signatureInfo += "&traceid=" + traceid;
        var signatureInfoSign = $.sha1(signatureInfo);

        var orderInfo = {
            appid: "wxc777455d99f1a570",
            traceid: traceid,
            noncestr: noncestr,
            package: packageInfo,
            timestamp: timestamp + "",
            app_signature: signatureInfoSign,
            sign_method: "sha1"
        };

        //			alert("orderInfo-->"+$api.jsonToStr(orderInfo));
        return orderInfo;
    };
    //生成package,
    //作用1：生成订单信息时,其中有两个参数：package和app_signature 都需要这个package
    //作用2：调用支付订单功能时，其中有两个参数：package和sign 都需要传递这个package
    function getPackage(orderId, amount, subject, body) {
        var packageInfo = "bank_type=WX";
        packageInfo += "&body=" + body;
        packageInfo += "&fee_type=1";
        packageInfo += "&input_charset=UTF-8";
        packageInfo += "&notify_url=http://www.123.cc";
        packageInfo += "&out_trade_no=" + orderId;
        packageInfo += "&partner=75871c30c714217aa70a1c743601851b";
        packageInfo += "&spbill_create_ip=127.0.0.0";
        packageInfo += "&total_fee=2";
        var packageInfoSign = packageInfo + "&key=75871c30c714217aa70a1c743601851b";
        packageInfoSign = $.md5(packageInfoSign).toUpperCase();

        //因为生成sign(即packageInfoSign)之前packageInfo必须是原文,
        //函数返回值：packageInfo+sign，其中package必须是进行URL编码的,所以又重新拼接了packageInfo
        packageInfo = "bank_type=WX";
        packageInfo += "&body=" + encodeURIComponent(body);
        packageInfo += "&fee_type=1";
        packageInfo += "&input_charset=UTF-8";
        packageInfo += "&notify_url=" + encodeURIComponent("http://www.2dian.cc");
        packageInfo += "&out_trade_no=" + orderId;
        packageInfo += "&partner=1224219301";
        packageInfo += "&spbill_create_ip=127.0.0.0";
        packageInfo += "&total_fee=2";

        return packageInfo + "&sign=" + packageInfoSign;
    }
    //v3
    this.payStoreOrder = function () {
        var weiXin = api.require('weiXin');
        weiXin.pay({
            body: "如意金钩棒",
            totalFee: "1",
            tradeNo: "123456789abcdefghijklmnopqrstuvw"
        }, function (ret, err) {
            if (ret.status) {
                api.alert({ msg: '' + ret.status });
            } else {
                api.alert({ msg: JSON.stringify(ret) + JSON.stringify(err) });
            }
        })
    };

    this.wxAppPay = function (d) {
        var weiXin = api.require('weiXin');
        //注册APP
        weiXin.registerApp(function (ret, err) {
            api.alert({ msg: JSON.stringify(ret) + JSON.stringify(err) });
            if (ret.status) {
                //获取token
                weiXin.getToken({
                    appId: 'wxc777455d99f1a570',
                    secret: '02bc930fd4ea46fa3ce1b395ca30d0a5'
                }, function (ret, err) {
                    if (ret.status) {
                        document.getElementById("paytokenis").value = ret.token;
                        document.getElementById("exipires").value = ret.expires;
                        //登录
                        weiXin.auth(function (ret, err) {
                            api.alert({ msg: JSON.stringify(ret) + JSON.stringify(err) });
                            if (ret.status) {
                                //weiXin.getUserInfo(function (ret, err) {
                                //    api.alert({ msg: JSON.stringify(ret) + JSON.stringify(err) });
                                //    if (ret.status) {
                                //        api.alert({ msg: '获取成功' });
                                //    } else {
                                //        api.alert({ msg: err.msg });
                                //    }
                                //});
                            } else {
                                //api.alert({ msg: err.msg });
                            }
                        });
                    } else {
                        api.alert({ msg: err.msg });
                    }
                });
                
            } else {
                api.alert({ msg: '注册失败' });
            }
        })
       
    //    weiXin.payOrder(d.d, function (ret, err) {
    //        if (!d.s && d.t == 0)
    //        {
    //            location.href = d;
    //            return;
    //        }
    //        api.alert({ msg: JSON.stringify(ret) + JSON.stringify(err) });
    //        if (ret.status) {
    //            api.alert({ msg: '支付成功' });
    //        }
    //        else {
    //            api.alert({ msg: err.msg });
    //        }
    //});

    };
}
//支付宝支付封装
var aliPay = function () {
    var AppPay = function () { }
    var WebPay = function () { }
    this.Pay = function () {
        if (app.isApi)
            AppPay();
        else
            WebPay();
    }
}


// 渲染 
function ZipImage(src,callback) {
    // 参数，最大高度 
    var MAX_HEIGHT = 700;
    var image = new Image();
    image.onload = function () {
        var canvas = document.getElementById("canvas");
        if (image.height > MAX_HEIGHT) {
            image.width *= MAX_HEIGHT / image.height;
            image.height = MAX_HEIGHT;
        }
        var ctx = canvas.getContext("2d");
        // canvas清屏 
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        // 重置canvas宽高 
        canvas.width = image.width;
        canvas.height = image.height;
        // 将图像绘制到canvas上 
        ctx.drawImage(image, 0, 0, image.width, image.height);
        var data = canvas.toDataURL("image/png");
        callback(data);
    }; 
    image.src = src;
};