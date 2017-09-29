// 提示框
function dialog(className, msg, time, url){
  var div = '<div class="msgbox_wrap" style="display:none;"><span class="msgbox" style="z-index: 10000;"><span class="" id="dialogSpan" ></span><i id="dialogi">警告！</i><span class="dialog_end"> </span></span></div>';
  $("body").append(div);
  $("#dialogSpan").get(0).className = className;
  $("#dialogi").html(msg);
  $(".msgbox_wrap").show();
  setTimeout(function(){
    if(url){
      window.location.href = url;
    }
    $(".msgbox_wrap").remove();
  },time ? time : 2000);

}

// 个位补0
function format(a){
  return a.toString().replace(/^(\d)$/, "0$1");
}

//去除左右空格
function trim(str) {
  return (str + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');
}

// 加载
function loading(bol){
  if(bol){
    var div = "<div class='loading'></div>";
    $("body").append(div);
  }else{
    $(".loading").remove();
  }
}

//模拟JS-confirm提示
function confirm(content, func){
  var div = '<div class="confirm"><div class="confirm-bg"></div><div class="confirm-con"><h3>提示</h3><p>'+content+'</p><div><input class="btn-1" type="button" value="取消"><input type="button" class="btn-2" value="确认"></div></div></div>';
  $("body").append(div);
  
  //取消
  $(".btn-1").bind("click", function(){
    $(".btn-1").attr("disabled","disabled");
    $(".confirm").remove();
  });
  //确定
  $(".btn-2").bind("click", function(){
    $(".btn-2").attr("disabled","disabled");
    $(".confirm").remove();
    if(typeof func == 'function'){
      func();
    }
  });

}

//确认提示框
function showAlert(content){
    var div = '<div class="alert"><div class="alertbg"></div><div class="alertdiv"> <div class="alert-head">温馨提示</div><div class="alert-content">'+content+'</div><div class="alert-confirm">确认</div></div></div>';
    $("body").append(div);

    $(".alert-confirm").bind("click", function(){
        $(".alert").remove();
    })
}

// 返回按钮---把父级的父级(fixed)隐藏
function hideParent(obj){
  $(obj).parent().parent().hide();
}