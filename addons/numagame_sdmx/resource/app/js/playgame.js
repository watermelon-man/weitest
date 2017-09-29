function playgame_init() {
	updateShare(0);
}
function playgame_submitScore(score) {
	updateShareScore(score);
	Playgame.shareFriend(); 
}
function updateShare(score) { 
	var descContent = window.shareInfo.share_desc || "山顶冒险小游戏,邀您一起来玩吧"; 
	console.log(window.shareInfo)
	if(score > 0)
		shareTitle = "我玩了" + score + "分"+",据说超过60分的人这周运气都不会太差哦！";
	else
		shareTitle =  window.shareInfo.share_title  || "据说这是个女孩子比男孩子更容易得高分的游戏！";
	appid = '';
	Playgame.setShareInfo(shareTitle,descContent);
	document.title = shareTitle;
	shareToFrind();
}
function updateShareScore(score) {
	updateShare(score);
}
function shareToFrind(){
	  wx.ready(function(){
			 wx.onMenuShareTimeline({
			        title: window.shareInfo.share_title,
			        link: window.shareInfo.share_url, 
			        imgUrl: window.shareInfo.share_logo, 
			        success: function () {  },
			        cancel: function () {   }
			 });
			 wx.onMenuShareAppMessage({ 
			        desc: window.shareInfo.share_desc,
			        title: window.shareInfo.share_title,
			        link: window.shareInfo.share_url, 
			        imgUrl: window.shareInfo.share_logo, 
			        type: 'link',
			        dataUrl: '',
			        success: function () {   },
			        cancel: function () {   }
			    });
	  	});
}