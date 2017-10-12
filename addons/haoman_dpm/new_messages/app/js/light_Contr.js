$(function(){
    level = parseInt(screen_light_level);
    var opacityLevel = 0;
    switch (level){
        case 1:
            opacityLevel = 0;
            break;
        case 2:
            opacityLevel =0.2;
            break;
        case 3:
            opacityLevel =0.4;
            break;
        case 4:
            opacityLevel = 0.6;
            break;
        case 5:
            opacityLevel = 0.8;
            break;
        default:
            opacityLevel = 0;
            break;
    }

    var _opacityDom = "<div class='mark' style='background-color: rgba(0,0,0,"+opacityLevel+");'></div>"
    $("body").append(_opacityDom);

});