$(function(){ 
	// popover
	$("article textarea").on("click",function () {
		mui("#className").popover('toggle');		
		$("body").on("click",function () {
			var checked = getCheckBoxRes();
//			if($(":checkbox[name=major]:checked").size() > 3){
                        if($(":checkbox:checked").size() > 3){
				highLight($(".mui-input-row textarea"));
				isRight = false;
			}else{
				normal($(".mui-input-row textarea"));
				isRight = true;
			}
			$(".mui-input-row textarea")[0].innerHTML = checked;
		})
	})
	// 提交
	$("body>button").on("click",function () {
            if(inspect()){
//                var checked = $(".mui-input-row textarea")[0].innerHTML;
//                alert(checked);
//                $.ajax({
//                    type: "POST",
//                    url: "{php echo $this->createMobileUrl('forms')}",
//                    data: {
//                        "major": checked
//                    },
//                    success: function (data) {
//                        if(JSON.parse(data).code == 200){
//                            alert("传输数据成功");
//                        }
//                    },
//                    error: function () {
//                          console.log("提交数据失败！");
//                    }
//                });
	  	document.getElementById("information").submit();
	  };
	})

}); 

var isRight = true;
// 正则
function inspect() {
	// 姓名
	var name = $("input[name='name']").val();
	var myreg1=/^[\u4E00-\u9FA5A-Za-z]+$/; 
	if (!myreg1.test(name)) { 
		highLight($("input[name='name']"));
		isRight = false;
	}else{
		normal($("input[name='name']"));
	}
	// 手机号
	var phone = $("input[name='mobile']").val();
	var myreg2=/^[1][3,4,5,7,8][0-9]{9}$/; 
	if (!myreg2.test(phone)) { 
		highLight($("input[name='mobile']"));
		isRight = false;
	}else{
		normal($("input[name='mobile']"));
	}
	// 课程
//	if ($(":checkbox[name=major]:checked").size() == 0) {
        if ($(":checkbox:checked").size() == 0) {
		highLight($(".mui-input-row textarea"));
		isRight = false;
	}else{
		normal($(".mui-input-row textarea"));
	}
	
	// 学校
	if ($("input[name='school']").val()!="") {
		var school = $("input[name='school']").val();
		var myreg5=/[\u4e00-\u9fa5]/; 
		if (!myreg5.test(school)) { 
			highLight($("input[name='school']"));
			isRight = false;
		}else{
			normal($("input[name='school']"));
		}
	}
	return isRight;
}

// 验证错误提示
function highLight(dom) {
	dom.css("border","1px solid red");
}
function normal(dom) {
	dom.css("border","1px solid rgba(0, 0, 0, .2)");
}

// 获取多选框数据
function getCheckBoxRes(){
//    var rdsObj   = document.getElementsByName('major');
    var rdsObj   = document.getElementsByClassName("major");
    var checkVal = new Array();
    var k        = 0;
    for(i = 0; i < rdsObj.length; i++){
        if(rdsObj[i].checked){
            checkVal[k] = $(rdsObj[i]).prev()[0].innerHTML;
            k++;
        }
    }
    return checkVal;
}
