function swap(image) {
	"use strict";
	var widthwindow1 = $(window).width();    
	document.getElementById("image").src = image.href;
	if(widthwindow1 >= 1024){
		$('#image').elevateZoom({
			zoomType: "inner",
			cursor: "crosshair",
			zoomWindowFadeIn: 375,
			zoomWindowFadeOut: 375
		});
	};
}; 
$(document).ready(function() {
	if ($(".ver-2 .tp-banner").length) {
		$(".ver-2 .tp-banner").revolution(
		{
			delay:9000,
			startwidth:1800,
			startheight:650,
			hideThumbs:10,
			fullWidth:"on",
			forceFullWidth:"on",
		});
	};
	if ($(".ver-3 .tp-banner").length) {
		$(".ver-3 .tp-banner").revolution(
		{
			delay:9000,
			startwidth:1800,
			startheight:700,
			hideThumbs:10,
			fullWidth:"on",
			forceFullWidth:"on",
		});
	};
	if ($(".ver-4 .tp-banner").length) {
		$(".ver-4 .tp-banner").revolution(
		{
			delay:9000,
			startwidth:1800,
			startheight:970,
			hideThumbs:10,
			fullWidth:"on",
			forceFullWidth:"on",
		});
	};
	if ($(".ver-5 .tp-banner").length) {
		$(".ver-5 .tp-banner").revolution(
		{
			delay:9000,
			startwidth:1800,
			startheight:800,
			hideThumbs:10,
			fullWidth:"on",
			forceFullWidth:"on",
		});
	};
	$(".tp-leftarrow").html("<span>P</span><span>R</span><span>E</span><span>V</span>");
	$(".tp-rightarrow").html("<span>N</span><span>E</span><span>X</span><span>T</span>");
	
	if ($(".product-img-box #image-view").length) {
		var widthwindow1 = $(window).width();
		if(widthwindow1 >= 1024){
			$("#image").elevateZoom({
				zoomType: "inner",
				cursor: "crosshair",
				zoomWindowFadeIn: 375,
				zoomWindowFadeOut: 375
			});
		};
	};
	// Tabs
	$(".tab-content").hide();
	$(".tab-content:first").show(); 
	$("ul.tabs li:first").addClass("active");
	$("ul.tabs li").on("click",function() {
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tab-content").hide();
		var activeTab = $(this).attr("rel");
		$("#"+activeTab).fadeIn(); 
	});	
	// End tabs

	// Slider Products
	var owl = $(".product-tab-content");
	$(window).on("orientationchange load resize",function() {
		owl.css({"width": $(".container").width()+24 +"px"});
	});
	owl.css({"width": $(".container").width()+24 +"px"});

	var owl1 = $(".blog-post-inner");
	$(window).on("orientationchange load resize",function() {
		owl1.css({"width": $(".container").width()+20 +"px"});
	});
	owl1.css({"width": $(".container").width()+20 +"px"});
	owl.owlCarousel({
		navigation : true, // Show next and prev buttons   
		slideSpeed : 500,
		singleItem:true,
		pagination : true,
		loop:true,
		responsiveClass:true,
		responsive:{
			0:{
				items:1 
			},

			480:{
				items:2,

			},

			767:{
				items:3,
			},

			1025:{
				items:4,
			}
		}
	});
	// End Slider Products
	$(".blog-post-inner").owlCarousel({
		navigation : true, // Show next and prev buttons   
		slideSpeed : 500,
		singleItem:true,
		pagination : true,
		loop:true,
		responsiveClass:true,
		responsive:{
			0:{
				items:1 
			},

			480:{
				items:2,

			},

			767:{
				items:2,
			},

			1025:{
				items:3,
			}
		}
	});	

	// Add Class dropdown-menu
	var menu_top_header3 = $(".header-v3 .menu-top");
	$(window).on("orientationchange load resize",function() {
		var widthwindow = $(window).width();
		if(widthwindow > 1024){
			$(".ver-2 .forcefullwidth_wrapper_tp_banner").after($(".mega-menu.mega-menu-v2 "));
			menu_top_header3.insertAfter($(".header-v3 .search.dropdown"));
			$(".mega-menu-mobile .sub-menu").addClass("dropdown-menu");
			
			$(".header-v3 .menu-top li.level-1 .menu-level2").addClass("dropdown-menu");
		}else{
			
			$(".header-v3 .menu-top li.level-1 .menu-level2").removeClass("dropdown-menu");
			$(".mega-menu-mobile .sub-menu").removeClass("dropdown-menu");
			$(".ver-2 .forcefullwidth_wrapper_tp_banner").before($(".mega-menu.mega-menu-v2 "));
			menu_top_header3.insertAfter($(".header-v3 .header-top"));
		}
		$("#googleMap").css({"height": $(".contact-form").innerHeight()+"px"});
	});

	$(".owl-controls .owl-prev").html("<i class='fa fa-angle-left'></i>");
	$(".owl-controls .owl-next").html("<i class='fa fa-angle-right'></i>");
	// Click to Hover
	$(".dropdown").hover(function() {
	  $(this).find(".dropdown-menu").stop(true, true).delay(200).fadeIn(500);
	  $(this).toggleClass("active")
	}, function() {
	  $(this).find(".dropdown-menu").stop(true, true).delay(200).fadeOut(500);
	  $(this).toggleClass("active");
	});
	
	// Click Icon Menu Mobile
	$(".icon-menu-mobile").on("click",function(){
		$(".mega-menu-mobile .navbar-nav").slideToggle();
		$(this).toggleClass("active");
	});
	$(".header-v3 .icon-menu-mobile").on("click",function(){
		menu_top_header3.slideToggle();
	});
	$("li:has(ul)").addClass("hassub");
	$("li:has(img)").addClass("images");
	$(".mega-menu-mobile .hassub a").after("<i class='fa fa-plus'></i>");

	$(".featured-product ul.tabs").each(function(){
		$(this).insertAfter($(this).parent().prev().children("h3"));
	});

	$(".ordering .list").on("click",function(){
		$(this).toggleClass("active");
		$(".products").addClass("list-item");
		$(".ordering .col").removeClass("active");
	});
	$(".ordering .col").on("click",function(){
		$(this).toggleClass("active");
		$(".products").removeClass("list-item");
		$(".ordering .list").removeClass("active");
	});

	$("ul.product-categories li.hassub a").after("<i class='fa fa-plus'></i>");
	$("ul li.hassub i").on("click",function(){
		$(this).next().slideToggle();
		$(this).toggleClass("active");
		$(this).parent().toggleClass("active");
	});
	$("#header .fa-bars").on("click",function(){
		$(".megamenu-v2").addClass("show-ef");
	});
	
	$(".megamenu-v2 .fa-times").on("click",function(){
		$(".megamenu-v2").removeClass("show-ef");
	});
	$(".carousel-control.right").html("<i class='fa fa-arrow-circle-right'></i>");
	$(".carousel-control.left").html("<i class='fa fa-arrow-circle-left'></i>");
	// googleMap
	if ($("#googleMap").length) {

	function initialize() 
	{
		var mapOptions = {
			zoom: 15,
			center: new google.maps.LatLng(20.970868,105.829099),
			mapTypeControl: true, // Hiển thị mapType
			mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			},
			zoomControl: false,
			scaleControl: false,
			scrollwheel: false,
			disableDoubleClickZoom: true,
		};
		var map = new google.maps.Map(document.getElementById("googleMap"),
			mapOptions);
		}

	google.maps.event.addDomListener(window, "load", initialize);
	};
	// End google map
	/* event more-views click see big image. */
	$("ul.tabs li:first").addClass("active");
	$(".cat-box-container-home3 .cat-box-item:gt(7)").hide();
	$(".cat-box-container-home3 .cat-box-item:last").addClass("last");
	$(".view-more-home3").on("click", function () {
	$(".cat-box-container-home3").find(".cat-box-item:hidden:lt(4)").addClass("view").slideDown();
	});
});