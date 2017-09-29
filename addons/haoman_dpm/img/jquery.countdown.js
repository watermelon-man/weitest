jQuery.fn.countdown = function(userOptions) {
	// Default options
	var options = {
		stepTime: 60,
		format: "dd:hh:mm:ss",
		startTime: "01:12:32:55",
		digitImages: 6,
		digitWidth: 53,
		digitHeight: 77,
		timerEnd: function() {},
		image: "digits.png"
	};
	var digits = [],
	interval;

	var createDigits = function(where) {
		var c = 0;
		for (var i = 0; i < options.startTime.length; i++) {

			if (parseInt(options.startTime.substring(i, i + 1)) >= 0) {
				elem = $('<div class="cntDigit cnt_' + i + '" />').css({
					height: options.digitHeight * options.digitImages * 10,
					float: 'left',
					backgroundImage: 'url(\'' + options.image + '\')',
					width: options.digitWidth
				});
				digits.push(elem);
				margin(c, -((parseInt(options.startTime.substring(i, i + 1)) * options.digitHeight * options.digitImages)), true);
				digits[c].__max = 9;
				
				switch (options.format.substring(i, i + 1)) {
				case 'h':
					//digits[c].__max = (c % 2 == 0) ? 2 : 9;
					//if (c % 2 == 0) digits[c].__condmax = 4;
					digits[c].__max = (c % 2 == 0) ? 2 : 3;
					if (c % 2 == 0) digits[c].__condmax = 2;
					break;
				case 'd':
					digits[c].__max = 9;
					break;
				case 'm':
				case 's':
					digits[c].__max = (c % 2 == 0) ? 5 : 9;
				}++c;
			} else{ 
				if (i==2) var tt = "天";
				if (i==5) var tt = "时";
				if (i==8) var tt = "分";

				//elem = $('<div class="cntSeparator cnt_' + i + '"/>').css({float: 'left'}).text(options.startTime.substring(i, i + 1));
				elem = $('<div class="cntSeparator cnt_' + i + '"/>').css({float: 'left'}).text(tt);
		
			}

			where.append(elem);
		}
		where.append('<div class="cntSeparator cnt_11" style="float:left">秒</div>' )
	};

	var margin = function(elem, val, creat) {
		if (val !== undefined) {
			return digits[elem].css({
				'marginTop': val + 'px'
			});
		} else {
			return parseInt(digits[elem].css('marginTop').replace('px', ''));
		}
	};

	var moveStep = function(elem) {

		digits[elem]._digitInitial = -(digits[elem].__max * options.digitHeight * options.digitImages);
		return function _move() {
			mtop = margin(elem) + options.digitHeight;
			if (mtop == options.digitHeight) {
				margin(elem, digits[elem]._digitInitial);
				if (elem > 0) moveStep(elem - 1)();
				else {
					clearInterval(interval);
					for (var i = 0; i < digits.length; i++) margin(i, 0);
					options.timerEnd();
					return;
				}
				if ((elem > 0) && (digits[elem].__condmax !== undefined) && (digits[elem - 1]._digitInitial == margin(elem - 1))) margin(elem, -(digits[elem].__condmax * options.digitHeight * options.digitImages));
				return;
			}

			margin(elem, mtop);
			if (margin(elem) / options.digitHeight % options.digitImages != 0) setTimeout(_move, options.stepTime);

			if (mtop == 0) digits[elem].__ismax = true;
		}
	};

	$.extend(options, userOptions);
	this.css({
		height: options.digitHeight,
		overflow: 'hidden'
	});
	createDigits(this);

	interval = setInterval(moveStep(digits.length - 1), 1000);
};