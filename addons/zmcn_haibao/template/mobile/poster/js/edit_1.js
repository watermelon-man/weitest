$(function(){
    $(".bottomnav li:eq(0)").click(function(){
      $(".bottomnav li:eq(0) a").css("color","#04be02");
      $(this).siblings().children().css("color","black");
    });
    $(".bottomnav li:eq(1)").click(function(){
      $(".bottomnav li:eq(1) a").css("color","#04be02");
      $(this).siblings().children().css("color","black");
      $(".muban1").show();
      $(".muban1 .dropload-down").show();
      $(".muban2").hide();
      $(".muban2 .dropload-down").hide();
      $(".muban3").hide();
      $(".muban3 .dropload-down").hide();
      $(".muban4").hide();
      $(".muban4 .dropload-down").hide();  
    });
    $(".bottomnav li:eq(2)").click(function(){
      $(".bottomnav li:eq(2) a").css("color","#04be02");
      $(this).siblings().children().css("color","black");
      $(".muban1").hide();
      $(".muban1 .dropload-down").hide();
      $(".muban2").show();
       $(".muban2 .dropload-down").show();
      $(".muban3").hide();
       $(".muban3 .dropload-down").hide();
      $(".muban4").hide();
      $(".muban4 .dropload-down").hide();  
    });
    $(".bottomnav li:eq(3)").click(function(){
      $(".bottomnav li:eq(3) a").css("color","#04be02");
      $(this).siblings().children().css("color","black");
      $(".muban1").hide();
      $(".muban1 .dropload-down").hide();
      $(".muban2").hide();
      $(".muban2 .dropload-down").hide();
      $(".muban3").show();
      $(".muban3 .dropload-down").show();
      $(".muban4").hide();
      $(".muban4 .dropload-down").hide();  
    });
     $(".bottomnav li:eq(4)").click(function(){
      $(".bottomnav li:eq(4) a").css("color","#04be02");
      $(this).siblings().children().css("color","black");
      $(".muban1").hide();
      $(".muban1 .dropload-down").hide();
      $(".muban2").hide();
      $(".muban2 .dropload-down").hide();
      $(".muban3").hide();
      $(".muban3 .dropload-down").hide();
      $(".muban4").show();
      $(".muban4 .dropload-down").show();  

    });
});