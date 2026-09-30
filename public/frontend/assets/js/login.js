
(function($) {
	"use strict";
	$(document).ready(function() {
	// Begin: Back to top

	$(".btn-login").click(function(){
		$('.pop-login').css('display','block');
		$('.popup-register').css('display','none');
		$('.pop-loginmulti').css('display','none');
		$('#mask').fadeIn();
		$('.pp-baodanh').fadeOut();
	});
	$(".btn-register").click(function(){
		$('.popup-register').css('display','block');
		$('.pop-login').css('display','none');
		$('#mask').fadeIn();
	});

	$(".form-login .close").click(function(){
		$('.popup-register').fadeOut();
		$('.pop-login').fadeOut();
		$('#mask').fadeOut();
		$("body").removeClass("no-scroll");
	});

	  
    });	


})(jQuery);
