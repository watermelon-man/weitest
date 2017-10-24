$(function(){ 
	// popover
	$("article:nth-of-type(2) textarea").on("click",function () {
		mui("#className").popover('toggle');		
		$("body").on("click",function () {
			var checked = getCheckBoxRes();
			if($(":checkbox[name=checkbox]:checked").size() > 3){
				highLight($(".mui-input-row textarea"));
				isRight = false;
			}else{
				normal($("input[name='school']"));
				isRight = true;
			}
			console.log(checked);
			$(".mui-input-row textarea")[0].innerHTML = checked;
		})
	})
}); 

