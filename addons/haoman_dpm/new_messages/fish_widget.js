
(function($) {
        $.fn.transitionEnd = function(callback) {
            var events = ['webkitTransitionEnd', 'transitionend', 'oTransitionEnd', 'MSTransitionEnd', 'msTransitionEnd'],
                i, dom = this;

            function fireCallBack(e) {
                /*jshint validthis:true */
                if (e.target !== this) return;
                callback.call(this, e);
                for (i = 0; i < events.length; i++) {
                    dom.off(events[i], fireCallBack);
                }
            }
            if (callback) {
                for (i = 0; i < events.length; i++) {
                    dom.on(events[i], fireCallBack);
                }
            }
            return this;
        };

        $.fn.popup = function () {
            $(this).addClass('weui-popup__container--visible');
            var $_this = $(this);
            $(this).find('.weui-popup__overlay').unbind('click').click(function(){
                $_this.removeClass('weui-popup__container--visible');
            });;
        };


        var $toast_loading = null;
        $.showLoading = function(text) {
            if($toast_loading)$toast_loading.remove();
            var html = '<div id="loadingToast" style="display:none;">';
            html += '<div class="weui-mask_transparent"></div>';
            html += '<div class="weui-toast">';
            html += '<i class="weui-loading weui-icon_toast"></i>';
            html += '<p class="weui-toast__content">'+ (text || "数据加载中") +'</p>';
            html += '</div>';
            html += '</div>';
            $toast_loading = $(html);
            $toast_loading.appendTo('body').fadeIn(100);
        }

        $.hideLoading = function() {
            if($toast_loading){
                $toast_loading.fadeOut(100,function(){
                    $toast_loading.remove();
                });
            }
        }

        $.actions = function(params) {
            params = $.extend({}, defaults, params);
            show(params);
        }

        $.closeActions = function() {
            hide();
        }

        var show = function(params) {

            var mask = $("<div class='weui-mask weui-actions_mask'></div>").appendTo(document.body);

            var actions = params.actions || [];

            var actionsHtml = actions.map(function(d, i) {
                return '<div class="weui-actionsheet__cell ' + (d.className || "") + '">' + d.text + '</div>';
            }).join("");

            var titleHtml = "";

            if (params.title) {
                titleHtml = '<div class="weui-actionsheet__title">' + params.title + '</div>';
            }

            var tpl = '<div class="weui-actionsheet " id="weui-actionsheet">'+
                titleHtml +
                '<div class="weui-actionsheet__menu">'+
                actionsHtml +
                '</div>'+
                '<div class="weui-actionsheet__action">'+
                '<div class="weui-actionsheet__cell weui-actionsheet_cancel">取消</div>'+
                '</div>'+
                '</div>';
            var dialog = $(tpl).appendTo(document.body);

            dialog.find(".weui-actionsheet__menu .weui-actionsheet__cell, .weui-actionsheet__action .weui-actionsheet__cell").each(function(i, e) {
                $(e).click(function() {
                    $.closeActions();
                    params.onClose && params.onClose();
                    if(actions[i] && actions[i].onClick) {
                        actions[i].onClick();
                    }
                })
            });

        mask.show();
        dialog.show();
        mask.addClass("weui-mask--visible");
        dialog.addClass("weui-actionsheet_toggle");
    };

    var hide = function() {
        $(".weui-mask").removeClass("weui-mask--visible").transitionEnd(function() {
            $(this).remove();
        });
        $(".weui-actionsheet").removeClass("weui-actionsheet_toggle").transitionEnd(function() {
            $(this).remove();
        });
    }

    var defaults = $.actions.prototype.defaults = {
        title: undefined,
        onClose: undefined
    }
})($);
