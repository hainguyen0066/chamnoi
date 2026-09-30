
var wow = new WOW({
	boxClass: 'wow',
	animateClass: 'animated',
	offset: 200,
	mobile: true,
	live: true
});
wow.init();

(function ($) {
	"use strict";
	$(document).ready(function () {
		$('#return-to-top').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 500);
		});
	});

	


	$('.menu-tab li').click(function () {
		$('.menu-tab  li').removeClass('active');
		$(this).addClass('active');
		var $inDex = $(this).index() + 1;
		$('.content-tab .tab').removeClass('active');
		$('.content-tab .tab:nth-child(' + $inDex + ')').addClass('active');
	});
	
	$('.nvskill-img').click(function () {
		$('.nvskill-img').removeClass('active-skill');
		var $inDex = $(this).index() + 1;
		$('.nhanvat-tab .tab').removeClass('active');
		$('.nhanvat-tab .tab:nth-child(' + $inDex + ')').addClass('active');
		$(this).addClass('active-skill');
		console.log($(this))
	});
	$('.nv-slider').slick({
		dots: true,
		infinite: true,
		speed: 500,
		fade: true,
		cssEase: 'linear'
	});
	$(".btn-login").click(function(){
		$('.pop-login').css('display','block');
		$('.pop-register').css('display','none');
		$('.pop-loginmulti').css('display','none');
		$('#mask').fadeIn();
		$('.pp-baodanh').fadeOut();
	});
	$(".btn-register").click(function(){
		$('.pop-register').css('display','block');
		$('.pop-login').css('display','none');
		$('#mask').fadeIn();
	});

	$(".pop-close").click(function(){
		$('.pop-register').fadeOut();
		$('.pop-login').fadeOut();
		$('#mask').fadeOut();
		$("body").removeClass("no-scroll");
	});


	$(".main-header-right").click(function(event) {
		event.stopPropagation();
		$(".menu-right").fadeIn();;
		// $(".menu-right").animate({right: "0%" });
		$("body").css("overflow", "hidden");
		$('#mask').fadeIn();
		$('.menuclose').fadeIn();
		$('.pop-register').fadeOut();
		$('.pop-login').fadeOut();
	});
	$(".menuclose").click(function(){
		$(".menu-right").fadeOut();
		$("body").css("overflow", "scroll");
		$('#mask').fadeOut();
		$(this).fadeOut();
	});
	// Begin: Slide
	var rev = $('.rev_slider');

	rev.on('init', function(event, slick, currentSlide) {
		var totalSlides = slick.$slides.length;
		var currentIndex = slick.currentSlide || currentSlide || 0;
		slick.$slides.removeClass('slick-sprev slick-snext slick-sprev2 slick-snext2');

		var prevIndex = (currentIndex - 1 + totalSlides) % totalSlides;
		var nextIndex = (currentIndex + 1) % totalSlides;
		var prev2Index = (currentIndex - 2 + totalSlides) % totalSlides;
		var next2Index = (currentIndex + 2) % totalSlides;

		$(slick.$slides[prevIndex]).addClass('slick-sprev');
		$(slick.$slides[nextIndex]).addClass('slick-snext');
		$(slick.$slides[prev2Index]).addClass('slick-sprev2');
		$(slick.$slides[next2Index]).addClass('slick-snext2');
	});

	rev.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
		var totalSlides = slick.$slides.length;
		slick.$slides.removeClass('slick-sprev slick-snext slick-sprev2 slick-snext2');

		var prevIndex = (nextSlide - 1 + totalSlides) % totalSlides;
		var nextIndex = (nextSlide + 1) % totalSlides;
		var prev2Index = (nextSlide - 2 + totalSlides) % totalSlides;
		var next2Index = (nextSlide + 2) % totalSlides;

		$(slick.$slides[prevIndex]).addClass('slick-sprev');
		$(slick.$slides[nextIndex]).addClass('slick-snext');
		$(slick.$slides[prev2Index]).addClass('slick-sprev2');
		$(slick.$slides[next2Index]).addClass('slick-snext2');
	});

	rev.slick({
	speed: 1000,	
	arrows: true,
	dots: true,
	focusOnSelect: true,
	infinite: true,
	autoplay: false,
	autoplaySpeed: 3000,
	pauseOnHover: true,
	pauseOnFocus: true,
	centerMode: false, // ← tắt đi
	slidesToShow: 1,   // ← chỉ show 1, CSS sẽ tự hiện prev/next
	slidesToScroll: 1,
	swipe: true,
	responsive: [
		{
			breakpoint: 1000,
			settings: {
				slidesToShow: 1,
				centerMode: false,
				variableWidth: false,
				adaptiveHeight: true
			}
		}
	]
	});
	// End: Page 04 Slide

})(jQuery);

