var canvas, ctx,sceneW, sceneH,
	world, mouseConstraint;

var footStatus, gameOver, gameStatus=-1, gameStep, gamePause, gameCanTouch=0, fBodyLastA=0,footUpdating=0;

var fHead,fBody,fFoot0,fFoot1,fHand0,fHand1,fTail,fLand0,fLand1,dummyBody,gameRoad,
	fHeadShape,fBodyShape,fFootShape0,fFootShape1,fHandShape0,fHandShape1,fTailShape,fLandShape,
	revoluteLandFootLock0,revoluteLandFootLock1,revoluteLandFootLock,setToAngle=footLockLimit=Math.PI/11,footCanRun=1,callRun;

var peachPosFrontArr=[1],//第1个数据是起步点
	// peachPosMiddleArr=[3,4,5,6,7,8,9,10],//第2个数组是桃的位置
	// peachPosMiddleArr=[3,3,4,4,5,5,6,6],//第2个数组是桃的位置
	peachPosMiddleArr=[3,3,3,3,3,3,3,3],//第2个数组是桃的位置
	peachPosEndArr=[1],//第3个数组是留空的路线
	peachPosMarr,
	peachPosId,
	peachSahpeArr=new Array(),
	roadLineSahpeArr=new Array(),
	roadPosX=0,
	monkeyStepWidth
	;

var monkeyEyeStatus=-1;

//加入手势
addHammer();



// Init canvas
canvas = document.getElementById("myCanvas");
sceneW = canvas.width;
sceneH = canvas.height;
ctx = canvas.getContext("2d");
ctx.lineWidth = 1;


function init(){
	
	footStatus=0;
	gameStatus=0;//0:等待开始,1:进行中,2:over
	gameStep=0;
	gamePause=0;
	islockFoot=0;
	footCanRun=0;
	noScreenMove=1;
	roadPosX=0;

	$('.monkeyGame .stepNum').show();
	$('.monkeyGame .stepNum').text('');


	world = new p2.World();
	world.gravity=[0,-9.779999732971191*1000];
	// world.gravity=[0,-9.779999732971191*200];
	// world.sleeping=true;
	// world.sleepMode=2;

	// world.solver.iterations = 10;//迭代
	// world.solver.tolerance = 0;//公差
	// world.solver.stiffness = 100000000;
	world.defaultContactMaterial.friction=500;//加入摩擦
	world.defaultContactMaterial.relaxation=5;//加入松弛
	// world.defaultContactMaterial.restitution=1;//恢复原状

	makePeachCookie();
	makeGameRoad();
	makeFigure();
	makeFigureJoint();

	fHand0.gravityScale=0;
	fHand1.gravityScale=0;
	fHead.gravityScale=0;
	fBody.gravityScale=0;
	fTail.gravityScale=0;

	world.on("beginContact",ContactTest);
	world.on("endContact",ContactTest2);
	world.on("postStep",function(){
		// console.log(footCanRun)

		fa0=fFoot0.angle-fBody.angle;
		fa1=fFoot1.angle-fBody.angle;

		if(Math.abs(fBody.angle)<0.0008 && gameStatus==1){
			// console.log(fBody.angle);
			// world.gravity[0]=270;
			// world.gravity[0]=320;
			world.gravity[0]=550;
		}
		if(Math.abs(fBody.angle)>0.07 && gameStatus==1){
			world.gravity[0]=0;
		}

		if(fa0>=footLockLimit && fFoot0.lock==0 && footStatus==1){
			fFoot0.lock=1;
			fFootShape0.position[1]=-90
			footBodyJoint0.setLimits(footLockLimit, footLockLimit);
			// console.log(111111111111111+" : "+footStatus+" : "+fa0+" : "+fa1);
		}
		if(fa0<=-footLockLimit && fFoot0.lock==0 && footStatus==0){
			fFoot0.lock=1;
			fFootShape0.position[1]=-90
			footBodyJoint0.setLimits(-footLockLimit, -footLockLimit);
			// console.log(111111111111111+" : "+footStatus+" : "+fa0+" : "+fa1);
		}
		if(fa1>=footLockLimit && fFoot1.lock==0 && footStatus==0){
			fFoot1.lock=1;
			fFootShape1.position[1]=-90
			footBodyJoint1.setLimits(footLockLimit, footLockLimit);
			// console.log(222222222222222+" : "+footStatus+" : "+fa0+" : "+fa1);
		}
		if(fa1<=-footLockLimit && fFoot1.lock==0 && footStatus==1){
			fFoot1.lock=1;
			fFootShape1.position[1]=-90
			footBodyJoint1.setLimits(-footLockLimit, -footLockLimit);
			// console.log(222222222222222+" : "+footStatus+" : "+fa0+" : "+fa1);
			// if(fa0>0){gamePause=1}
		}
	});

	// if($.cookie('showTips')!=1){
		setTimeout(tipsAnmi,0);
		setTimeout('gameCanTouch=1',800);

	// }
	// TweenMax.to($('.siteGame .head img'),0.1,{attr:{src:'images/monkey_head.png'}, repeat:-1, repeatDelay:1});
	// TweenMax.to(fHeadShape.imgObj,1,{attr:{obj:$('.monkeyGame .figure .head1 img')[0]}, repeat:-1, repeatDelay:0.2});
	if(monkeyEyeStatus==-1){
		monkeyEyeStatus=1;
		setEye();
	}
}

function setEye(){
	if(monkeyEyeStatus==1){
		monkeyEyeStatus=0
		fHeadShape.imgObj.obj = $('.monkeyGame .figure .head1 img')[0]
		time=100;
	}else{
		monkeyEyeStatus=1;
		fHeadShape.imgObj.obj = $('.monkeyGame .figure .head img')[0]
		time=300+Math.random()*2000;
	}
	setTimeout(setEye,time);
}

//碰撞检测
function ContactTest2(event){
	if(event.shapeA.cname== undefined || event.shapeB.cname == undefined){
		return false;
	}
	if(event.shapeA.cname.indexOf('fLand')>-1 && event.shapeB.cname.indexOf('figureFoot')>-1){
		if(footStatus==1 && event.shapeA.cname=='fLand0' && event.shapeB.cname=='figureFoot0'){
			footCanRun=0
		}
		if(footStatus==0 && event.shapeA.cname=='fLand1' && event.shapeB.cname=='figureFoot1'){
			footCanRun=0
		}

		// console.log(event.shapeA.cname+" : "+event.shapeB.cname+" : "+fBody.angle)
	}
	//碰到桃子
}
//碰撞检测
function ContactTest(event){
		// console.log(event.shapeA.cname+" : "+event.shapeB.cname+" : "+event.shapeB.body.position[1])
		// console.log(event)
	if(event.shapeA.cname== undefined || event.shapeB.cname == undefined){
		return false;
	}
	if(event.shapeA.cname.indexOf('fLand')>-1 && event.shapeB.cname.indexOf('figureFoot')>-1){
		if(footStatus==1 && event.shapeA.cname=='fLand0' && event.shapeB.cname=='figureFoot0'){
			footCanRun=1
			musicWalk0.play();
		}
		if(footStatus==0 && event.shapeA.cname=='fLand1' && event.shapeB.cname=='figureFoot1'){
			footCanRun=1
			musicWalk1.play();
		}

		// console.log(event.shapeA.cname+" : "+event.shapeB.cname+" : "+fBody.angle)
	}
	//碰到桃子
	if(event.shapeA.cname.indexOf('peach_')>-1 || event.shapeB.cname.indexOf('peach_')>-1){
	// if(event.shapeA.cname.indexOf('peach_')>-1 && (event.shapeB.cname=='figureHead' || event.shapeB.cname=='figureBody' || event.shapeB.cname=='figureHand')){
		givepeach=0;
		if(event.shapeA.cname.indexOf('peach_')>-1 && (event.shapeB.cname=='figureHead' || event.shapeB.cname=='figureBody' || event.shapeB.cname=='figureHand')){
			var id=event.shapeA.cname.split('_')[1];
			givepeach=1;
		}
		if(event.shapeB.cname.indexOf('peach_')>-1 && (event.shapeA.cname=='figureHead' || event.shapeA.cname=='figureBody' || event.shapeA.cname=='figureHand')){
			var id=event.shapeB.cname.split('_')[1];
			givepeach=1;
		}
		// peachSahpeArr[id].body=100;
		if(givepeach==1){
			musicEat.play();
			toX=peachSahpeArr[id].position[0]-200;
			toY=500;
			TweenMax.to(peachSahpeArr[id],0.1,{/*positionX:toX,*/ positionY:toY, ease:Linear.easeNone, onComplete:peachhide})
			// console.log(peachSahpeArr[id]);
		}

	}

	//判断是否结束
	if(gameStatus==1 && ((event.shapeA.cname.indexOf('fLand')>-1 && (event.shapeB.cname=='figureHead' || event.shapeB.cname=='figureBody' || event.shapeB.cname=='figureHand' || event.shapeB.cname=='figureTail')) /*|| Math.abs(fBody.angle)>=Math.PI/5.5*/)){
		// gamePause=1;
		bodyHandJoint0.setLimits(-Math.PI*2, Math.PI*2);
		bodyHandJoint1.setLimits(-Math.PI*2, Math.PI*2);
		bodyTailJoint.setLimits(-Math.PI*2, Math.PI*2);
		footBodyJoint0.setLimits(-footLockLimit, footLockLimit);
		footBodyJoint1.setLimits(-footLockLimit, footLockLimit);
		fa0=fFoot0.angle-fBody.angle;
		fa1=fFoot1.angle-fBody.angle;
		// console.log(fa0 +":"+fa1)
		// alert(1)
		// fHand0.gravityScale=0.5;
		// fHand1.gravityScale=0.5;
		// fHead.gravityScale=1;
		fTail.gravityScale=1;
		fFoot1.gravityScale=1;
		fFoot0.gravityScale=1;
		gameStatus=2;
		gameCanTouch=0;
		gameStartSwitch=1;
		// world.removeConstraint(revoluteLandFootLock0);
		// world.removeConstraint(revoluteLandFootLock1);
		// world.gravity=[0,-2500];
		setTimeout(gameHaveOver,1500);
		// gameHaveOver();
	}

	function peachhide(){
		gameRoad.removeShape(peachSahpeArr[id]);
		obj=$('.monkeyGame .words span[class="none"]:first')
		obj.addClass('hit');
		TweenMax.to(obj,0.1,{scale:1.3, repeat:1, yoyo:true});
		peachPosId = peachPosId>=7 ? 7 : peachPosId+=1; 
		$.cookie('peachPosId',peachPosId);
		// $.cookie('peachPosMarr',peachPosMarr);
	}
}

//整理桃子的cookie数据
function makePeachCookie(){
	// peachPosMarr = $.cookie('peachPosMarr')!=undefined ? $.cookie('peachPosMarr').split(',') : peachPosMiddleArr;
	// peachPosMarr = peachPosMiddleArr;
	// peachPosMarr = [3,4,5,6,7,8,9,10];
	peachPosMarr = [3,3,3,3,3,3,3,3],//第2个数组是桃的位置
	peachPosId = $.cookie('peachPosId')!=undefined && $.cookie('peachPosId')!=-1 ? Number($.cookie('peachPosId')) : -1;
	// peachPosId=-1
	if(peachPosId>7){
		peachPosId=7;
	}
	$('.monkeyGame .words span').removeClass('hit');

	// console.log(peachPosId)
	for(i=0;i<=peachPosId;i++){
		peachPosMarr.shift();
		$($('.monkeyGame .words span')[i]).addClass('hit');
	}

	// peachPosArr=peachPosFrontArr.concat(peachPosMarr).concat(peachPosEndArr);
	peachPosArr=peachPosMarr;
	// console.log(peachPosArr);
}
//定义路面
function makeGameRoad(){
	id=0;
	monkeyStepWidth=70;
	gameRoad = new p2.Body({ mass:0, position:[0,-290]});
	world.addBody(gameRoad);
	// console.log(peachPosArr);

	x=0;
	//console.log(peachPosArr);
	for(i in peachPosArr){
		n=peachPosArr[i];
		x+=monkeyStepWidth*n*3;
		console.log(x+' : '+n)
		peachSahpeArr[i] = new p2.Circle({radius:35});
		peachSahpeArr[i].imgObj={obj:$('.monkeyGame .myP2Word .peach img')[0], fix:[-7,-10]};
		peachSahpeArr[i].cname = 'peach_'+i;
		peachSahpeArr[i].sensor=true;
		gameRoad.addShape(peachSahpeArr[i]);
		peachSahpeArr[i].position=[x,150];
		peachSahpeArr[i].positionX=peachSahpeArr[i].position[0];
		peachSahpeArr[i].positionY=peachSahpeArr[i].position[1];
	}
	for(i=1;i<6;i++){
		roadPosX+=monkeyStepWidth*3;
		x=roadPosX
		road = new p2.Body({ mass:0, position:[x,-297]});
		roadLineSahpeArr[i] = new p2.Box({width:10, height:10});
		roadLineSahpeArr[i].cname = 'roadLine_'+i;
		roadLineSahpeArr[i].sensor=true;
		road.addShape(roadLineSahpeArr[i]);
		world.addBody(road);
	}
}
//创建模型
function makeFigure(){
	//定义尾
	fTailShape = new p2.Circle({ radius:10, sensor:false, collisionResponse:true/*, collisionGroup: 4, collisionMask:4*/});
	fTailShape.imgObj={obj:$('.monkeyGame .figure .tail img')[0], fix:[-35,-55]};
	fTailShape.cname='figureTail';
	fTail = new p2.Body({ mass:1, position:[-50,-248],angularVelocity:0 });
	fTail.addShape(fTailShape);
	world.addBody(fTail);

	//定义手
	fHandShape0 = new p2.Circle({ radius:7, sensor:false, collisionResponse:true/*, collisionGroup: 4, collisionMask:4*/});
	fHandShape0.imgObj={obj:$('.monkeyGame .figure .hand0 img')[0], fix:[-66,-4]};
	fHand0 = new p2.Body({ mass:1, position:[40,-146],angularVelocity:0 });
	fHand0.addShape(fHandShape0);
	// fHand0.addShape(new p2.Box({ width:30, height:3, sensor:false, collisionResponse:true}));
	// fHand0.addShape(new p2.Circle({ radius:1}));

	fHandShape1 = new p2.Circle({ radius:7, sensor:false, collisionResponse:true});
	fHandShape1.imgObj={obj:$('.monkeyGame .figure .hand1 img')[0], fix:[-66,-4]};
	fHandShape1.cname='figureHand';
	fHand1 = new p2.Body({ mass:1, position:[40,-162],angularVelocity:0 });
	fHand1.addShape(fHandShape1);
	// fHand1.addShape(new p2.Box({ width:30, height:3, sensor:false, collisionResponse:true}));
	// fHand1.addShape(new p2.Circle({ radius:1}));

	world.addBody(fHand0);
	world.addBody(fHand1);
	fHand0.angle=0.15
	fHandShape0.position=[50,0];
	fHandShape1.position=[50,0];
	// fHand0.shapes[1].position=[25,0];
	// fHand1.shapes[1].position=[25,0];

	//定义身体
	fBodyShape = new p2.Box({ width: 96, height: 120 });
	fBodyShape.imgObj={obj:$('.monkeyGame .figure .body img')[0], fix:[0,0]};
	fBodyShape.damping=1;
	fBodyShape.cname='figureBody';
	fBody = new p2.Body({ mass:1, position:[0,-180],angularVelocity:0 });

	//定义脚
	fFootShape0 = new p2.Circle({ radius: 9/*, collisionGroup: 4, collisionMask:4*/});
	fFootShape0.material = new p2.Material();
	fFootShape0.imgObj={obj:$('.monkeyGame .figure .foot0 img')[0], fix:[-4,-46]};
	fFootShape0.cname='figureFoot0';
	fFoot0 = new p2.Body({ mass:1, position:[0,-200]});
	fFoot0.addShape(new p2.Box({ width:5, height:40}));
	fFoot0.shapes[0].position=[0,-65];

	fFootShape1 = new p2.Circle({ radius: 9, collisionGroup: 2, collisionMask:2});
	fFootShape1.material = new p2.Material();
	fFootShape1.imgObj={obj:$('.monkeyGame .figure .foot1 img')[0], fix:[-4,-46]};
	fFootShape1.cname='figureFoot1';
	fFoot1 = new p2.Body({ mass:1, position:[0,-200]});
	fFoot1.addShape(new p2.Box({ width:5, height:40, collisionGroup: 2, collisionMask:2}));
	fFoot1.shapes[0].position=[0,-65];

	// fBody.addShape(fFootShape0, [0,-80]);
	// fBody.addShape(fFootShape1, [0,-80]);
	// fBody.addShape(fFootShape0, [-30,-85]);
	// fBody.addShape(fFootShape1, [30,-85]);

	fFoot0.addShape(fFootShape0);
	fFoot1.addShape(fFootShape1);
	world.addBody(fFoot0);
	world.addBody(fFoot1);
	fFootShape0.position=[0,-90];
	fFootShape1.position=[0,-90];

	fBody.addShape(fBodyShape);
	world.addBody(fBody);
	fFoot0.angle=-footLockLimit;
	fFoot1.angle=footLockLimit;
	fFoot0.angle0=-footLockLimit;
	fFoot1.angle0=footLockLimit;
	// fFootShape0.angle=0.6;
	// fFootShape1.angle=-0.6;
	// fFootShape1.collisionResponse=false;

	//定义头部
	fHeadShape = new p2.Box({ width: 200, height: 180 });
	fHeadShape.imgObj={obj:$('.monkeyGame .figure .head img')[0], fix:[-48,-63]};
	fHeadShape.cname='figureHead';
	fHead = new p2.Body({ mass:1, position:[0,-18],angularVelocity:-0 });
	fHead.addShape(fHeadShape);
	// fHead.collisionResponse = false;
	// fBodyShape.sensor=true
	world.addBody(fHead);

	//定义地板
	fLandShape = new p2.Plane();
	fLandShape.material = new p2.Material();
	fLandShape.cname='fLand0';
	fLand0 = new p2.Body({position:[0,-297]});
	fLand0.addShape(fLandShape);
	world.addBody(fLand0);

	fLandShape1 = new p2.Plane({collisionGroup: 2, collisionMask:2});
	fLandShape1.material = new p2.Material();
	fLandShape1.cname='fLand1';
	fLand1 = new p2.Body({position:[0,-297]});
	fLand1.addShape(fLandShape1);
	world.addBody(fLand1);

	//鼠标box
	shape = new p2.Box({ width: 10, height: 10 });
	mouseBody = new p2.Body({position:[0,400]});
	mouseBody.collisionResponse = false;
	mouseBody.addShape(shape);
	world.addBody(mouseBody);

	//锁定脚点
	dummyBody = new p2.Body();
	world.addBody(dummyBody);
}
function makeFigureJoint(){
	footContactMaterial0 = new p2.ContactMaterial(fLandShape.material, fFootShape0.material, {
		relaxation : 1,
		restitution:0,
		surfaceVelocity : 0,
		friction: 500
	});
	// world.addContactMaterial(footContactMaterial0);
	footContactMaterial1 = new p2.ContactMaterial(fLandShape1.material, fFootShape1.material, {
		relaxation : 1,
		restitution:0,
		surfaceVelocity : 0,
		friction: 500
	});
	// world.addContactMaterial(footContactMaterial1);


	//链接头和身
	headBodyJoint = new p2.RevoluteConstraint(fHead, fBody, {
		localPivotA: [0,-95],
		localPivotB: [0,60],
	});
	// headBodyJoint.setLimits(-Math.PI / 8, Math.PI / 8);
	headBodyJoint.setLimits(0, 0);
	world.addConstraint(headBodyJoint);

	//链接身和手
	bodyHandJoint0 = new p2.RevoluteConstraint(fBody, fHand0, {
		localPivotA: [45,30],
		localPivotB: [0,0],
	});
	bodyHandJoint0.setLimits(0, Math.PI / 5);
	// bodyHandJoint0.setLimits(0,0);
	world.addConstraint(bodyHandJoint0);

	//链接身和手
	bodyHandJoint1 = new p2.RevoluteConstraint(fBody, fHand1, {
		localPivotA: [45,20],
		localPivotB: [0,0],
	});
	bodyHandJoint1.setLimits(-Math.PI / 5, 0);
	// bodyHandJoint0.setLimits(0,0);
	world.addConstraint(bodyHandJoint1);

	//链接身和尾
	bodyTailJoint = new p2.RevoluteConstraint(fBody, fTail, {
		localPivotA: [-45,-40],
		localPivotB: [20,0],
	});
	bodyTailJoint.setLimits(-Math.PI / 32, Math.PI / 32);
	world.addConstraint(bodyTailJoint);

	//链接身和左脚
	footBodyJoint0 = new p2.RevoluteConstraint(fBody, fFoot0, {
		localPivotA: [0,-20],
		localPivotB: [0,0],
	});
	footBodyJoint0.setLimits(-footLockLimit, footLockLimit);
	// footBodyJoint0.setLimits(0,0);
	footBodyJoint0.setRelaxation(0.1);
	footBodyJoint0.setStiffness(1e7);
	world.addConstraint(footBodyJoint0);
	//链接身和右脚
	footBodyJoint1 = new p2.RevoluteConstraint(fBody, fFoot1, {
		localPivotA: [0,-20],
		localPivotB: [0,0],
	});
	footBodyJoint1.setLimits(-footLockLimit, footLockLimit);
	// footBodyJoint1.setLimits(0,0);
	footBodyJoint1.setRelaxation(0.1);
	footBodyJoint1.setStiffness(1e7);
	world.addConstraint(footBodyJoint1);
}
//加入手势
function addHammer(){
	var hammer = new Hammer.Manager($('.monkeyGame canvas')[0]);
	obj=fHead;
/*
	hammer.add(new Hammer.Pan({ threshold: 0, pointers: 0 }));
	hammer.add(new Hammer.Tap());
	hammer.on("panstart", function(event){
		var position = getPhysicsCoord(event);
		var hitBodies = world.hitTest(position, [fBody]);
		if(hitBodies.length){
			mouseBody.position = position;
			mouseConstraint = new p2.RevoluteConstraint(mouseBody, fBody, {
				worldPivot: position,
				collideConnected:false
			});
			world.addConstraint(mouseConstraint);
		}
	});
	hammer.on("panmove", function(event){
		var position = getPhysicsCoord(event);
		// mouseBody.position[0] = position[0];
		// mouseBody.position[1] = position[1];
	});
	hammer.on("panend", function(event){
		world.removeConstraint(mouseConstraint);
		mouseConstraint = null;
	});
*/
	document.addEventListener("touchstart", function(evt){
		if(!$(evt.target).attr('canTouch')){
  			evt.preventDefault();
		}
		if(gameStatus==1 || gameStatus==2){
			// evt.preventDefault();
		}
		if(gameCanTouch!=0){
			oneStep();
		}
	}, true);
}
//向前推进
function oneStep(){
	// alert(gameStatus)
	if(gameStatus==1){
		if(footCanRun==1){
			fHead.angularVelocity+=4;
			oneStepAnimate();
		}
	}
	if(gameStatus==0){
		world.gravity=[0,-9.779999732971191*200];
		$('.siteGame .tips').hide();
		tipsAnmiTimeLite.kill();
		TweenMax.killChildTweensOf($('.siteGame .tips'));
		gamePause=0;
		fHead.gravityScale=1;
		fHead.angularVelocity-=0;
		oneStepAnimate();
		gameStatus=1;
	}
}
//走路动作
function oneStepAnimate(){
		footCanRun=0

		gotoX=fBody.position[0]+fBodyShape.width;
		gs=Math.floor(gotoX/monkeyStepWidth/3);

		if(gs!=$('.monkeyGame .stepNum').text()){
			TweenMax.to($('.monkeyGame .stepNum'),0.1,{x:0, scale:1.3, repeat:1, yoyo:true});
		}
		$('.monkeyGame .stepNum').text(gs);

		// gameRoad.positionX=gameRoad.position[0];
		// gameRoad.positionY=gameRoad.position[1];
		// x=gameRoad.position[0]-50;
		// TweenMax.to(gameRoad,0.3,{positionX:x, ease:Linear.easeNone})
		
		TweenMax.killTweensOf($('.siteGame .monkeyGame .road'),false);
		// TweenMax.to($('.siteGame .monkeyGame .road'),0,{force3D:true, backgroundPositionX:'-=6%', ease:Linear.easeNone})
		// TweenMax.to($('.siteGame .monkeyGame .road'),0,{x:0, '-webkit-mask-size':'+=5%'})

		time0=0.8;

		power=530;
		power2= gameStatus == 0 ? 50 : 50;
		power2= 43;
		// power2= 50;
		power3=8;
		angle=footLockLimit;
		footUpdating=1;


		footBodyJoint0.setLimits(-footLockLimit, footLockLimit);
		footBodyJoint1.setLimits(-footLockLimit, footLockLimit);
		fFoot0.lock=0;
		fFoot1.lock=0;
		fa0=fFoot0.angle-fBody.angle;
		fa1=fFoot1.angle-fBody.angle;

		// if(footStatus!=1){
		if(fa1>0){
			footStatus=1;

			fFootShape0.position[1]=-70
			fFootShape1.position[1]=-90
			fFoot0.angularVelocity=power3;
			fFoot1.angularVelocity=-power2;
			fFoot0.gravityScale=0;
			fFoot1.gravityScale=1;

			// TweenMax.to(fFoot0,time0,{angle0:angle, ease:Expo.easeOut, delay:0.0, onUpdate:footUpdate, onUpdateParams:[fFoot0], onComplete:footUpdateOver,onCompleteParams:[footBodyJoint0,angle]})
			// TweenMax.to(fFoot1,time0,{angle0:-angle, ease:Expo.easeOut, delay:0.0, onUpdate:footUpdate, onUpdateParams:[fFoot1], onComplete:footUpdateOver,onCompleteParams:[footBodyJoint1,-angle]})
			// console.log(11)
		}else{
			footStatus=0;

			fFootShape0.position[1]=-90
			fFootShape1.position[1]=-70
			fFoot1.angularVelocity=power3;
			fFoot0.angularVelocity=-power2;
			fFoot1.gravityScale=0;
			fFoot0.gravityScale=1;

			// TweenMax.to(fFoot0,time0,{angle0:-angle, ease:Expo.easeOut, delay:0.0, onUpdate:footUpdate, onUpdateParams:[fFoot1], onComplete:footUpdateOver,onCompleteParams:[footBodyJoint0,-angle]})
			// TweenMax.to(fFoot1,time0,{angle0:angle, ease:Expo.easeOut, delay:0.0, onUpdate:footUpdate, onUpdateParams:[fFoot0], onComplete:footUpdateOver,onCompleteParams:[footBodyJoint1,angle]})
			// console.log(22)
		}

}

function footUpdate(p2Body){
	fBa=Math.abs(fBody.angle)>0.009 ? fBody.angle : 0;
	p2Body.angle=p2Body.angle0+fBa;
}
function footUpdateOver(Joint,angle){
	footUpdating=0;
}

