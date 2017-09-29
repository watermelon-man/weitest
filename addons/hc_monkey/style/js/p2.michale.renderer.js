	function drawCanvasShape(p2Shape,imgObj){
		ctx.beginPath();

		// if(gamePause!=1){
			var x = p2Shape.body.position[0]-(fFoot0.position[0]+fFoot1.position[0])/2,
				y = p2Shape.body.position[1];
		// }else{
			// var x = p2Shape.body.position[0],
				// y = p2Shape.body.position[1];
		// }
			// var x = p2Shape.body.position[0],
				// y = p2Shape.body.position[1];

		ctx.save();
		ctx.translate(x, y);
		ctx.rotate(p2Shape.body.angle);

		if(Math.abs(p2Shape.position[0])>0 || Math.abs(p2Shape.position[1])>0){
			ctx.translate(p2Shape.position[0],p2Shape.position[1])
		}

		//cricle
		if (p2Shape.type==1) {
			toW = toH = p2Shape.radius*2;
			// ctx.arc(0, 0, p2Shape.radius, 0, Math.PI * 2, true);
		};
		//plane
		if (p2Shape.type==4) {
			toW = sceneW;
			toH = 0;
			// ctx.rect(-toW/2, -toH/2, toW, toH);
		};
		//box
		if (p2Shape.type==8) {
			toW = p2Shape.width;
			toH = p2Shape.height;
			// ctx.rect(-toW/2, -toH/2, toW, toH);
		}
		//road
		if(p2Shape.cname!=undefined){
			if (p2Shape.cname.indexOf('roadLine_')>-1) {
				if(p2Shape.body.position[0]<(fFoot0.position[0]+fFoot1.position[0])/2-sceneW){
					// console.log(2)
					roadPosX+=monkeyStepWidth*3;
					p2Shape.body.position[0]=roadPosX;
				}

				ctx.lineWidth = 6;
				ctx.strokeStyle="#d7efef";
				toW=toH=100;
				ctx.moveTo(toW/2,toH/2);
				ctx.lineTo(-toW/2,-toH/2);
			}
		}


		ctx.scale(1, -1);

		if(imgObj!=undefined){
			ctx.rotate(p2Shape.angle);
			ctx.drawImage(imgObj.obj, -toW/2+imgObj.fix[0], -toH/2+imgObj.fix[1]);
		}

		if(p2Shape.positionX || p2Shape.positionY ){
			p2Shape.position=[p2Shape.positionX, p2Shape.positionY]
		}
		if(p2Shape.body.angle0!=undefined){
			// console.log(fBody.angle)
			// fBa=Math.abs(fBody.angle)>0.009 ? fBody.angle : 0;
			// p2Shape.body.angle=p2Shape.body.angle0+fBa;
		}


		ctx.stroke();
		ctx.restore();
	}
// 
	function render(){
		CSSPlugin.defaultTransformPerspective = 0;
		ctx.clearRect(0,0,sceneW,sceneH);

		ctx.save();
		ctx.translate(sceneW/2, sceneH/2);	// Translate to the center
		// ctx.scale(10, -10);		 // Zoom in and flip y axis
		ctx.scale(0.5, -0.5);		 // Zoom in and flip y axis
		// ctx.scale(1, -1);		 // Zoom in and flip y axis

		for(i in world.bodies){
			body = world.bodies[i];
			if(body.positionX || body.positionY ){
				body.position=[body.positionX, body.positionY]
				// console.log(body.positionX);
			}

			for(j in body.shapes){
				shape=body.shapes[j];
				drawCanvasShape(shape,shape.imgObj)
			}
		}
		ctx.restore();
	}

	// Animation loop
	function animate(){
		requestAnimationFrame(animate);
		if(gamePause!=1){
			world.step(1/50);
			render();
		}
	}

	//手势坐标与物理空间坐标转换
	function getPhysicsCoord(event){
		var rect = canvas.getBoundingClientRect();
		var x = event.pointers[0].clientX - rect.left;
		var y = event.pointers[0].clientY - rect.top;

		scaleX = $('canvas').width()/sceneW * 0.5;
		scaleY = -$('canvas').height()/sceneH * .5;

		x = (x - $('canvas').width() / 2) / scaleX;
		y = (y - $('canvas').height() / 2) / scaleY;

		return [x, y];
	}
