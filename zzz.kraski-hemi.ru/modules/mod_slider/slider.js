/* jed-slider 2.0 {nikon@chelny.com} */
var sliderDelay = 10000;
var sliderTimer = [];
$(document).ready(function (){

	createSlider();
});



function autoSlide(slider, delay, flag){
    if(delay>0){
        $slider = $(".slider-"+slider);
        if(flag==1) {
            slide = $slider.attr("current");
            slide++;
            setSlider(slider, slide);
        }
        clearTimeout(sliderTimer[slider]);
        sliderTimer[slider] = setTimeout("autoSlide("+slider+","+delay+",1)", delay);
    } else {
        clearTimeout(sliderTimer[slider]);
    }
}
$(window).on('resize',function() {
    $(".jed-slider .slide").each(function (s, e) {
        images = $(this).attr("data-images");
        images  = images.split(";");
        width = document.body.clientWidth;
        if(width>980) img = 0;
        if(width<=980) img = 1;
        if(width<=600) img = 2;
        $(this).attr("style","background-image:url("+images[img]+")");
    });
});
function createSlider(){

    $(".jed-slider").each(function (j,slider) {

        // обозначим все слайдеры
        count = 0;
        $(this).find(".slide").each(function (s, e) {
            $(this).addClass("slide-" + s);
            count = s;
			if(count==0){
				if($(this).find("video").is("video")){
					v1 = $(this).find("video")[0];
					v1.play();
				}
			}
			// подгружаем разные картинки для устройств
            images = $(this).attr("data-images");
            images  = images.split(";");
            width = document.body.clientWidth;
            if(width>980) img = 0;
            if(width<=980) img = 1;
            if(width<=600) img = 2;
            $(this).attr("style","background-image:url("+images[img]+")");
        });

        $(this).addClass("slider-" + j);
        $(this).attr("count", count);
        $(this).attr("current", 0);

        // если слайдов больше одного то будем проигрывать
        if(count>0){
            // повесим события на эелементы цправления
            $(this).find(".slide-left").on("click",function () {
                n = parseInt($(this).closest(".jed-slider").attr("current"));
                setSlider(j,n-1);
            });

            $(this).find(".slide-right").on("click",function () {
                n = parseInt($(this).closest(".jed-slider").attr("current"));
                setSlider(j,n+1);
            });

            // установим swipe для мобильных устройств
           $(this).on('flick', function(e) {
                    n = parseInt($(slider).attr("current"));
                    if (1 == e.direction) {
                        autoSlide(j,0);
                        setSlider(j,n-1);
                        console.log("swipe-left");
                    }
                    else {
                        autoSlide(j,0);
                        setSlider(j,n+1);
                        console.log("swipe-right");
                    }

            });
            // при наведении будем включать и отключать автослайд
            $(this).on("mouseenter",function () {
                //autoSlide(j,0);
            });
            $(this).on("mouseleave",function () {
                //autoSlide(j,sliderDelay);
            });
            // включем сдайдер когда картинки загрузятся
            $(slider).find(".jed-slider-arrow").css("opacity","1");
            autoSlide(j,sliderDelay);
            /*
            $(this).imagesLoaded( function() {
                //setSlider(j,0);

            });*/
        } else {
            // спрячем все управление если у нас только один слайдер
            $(this).find(".slide-left").css("display","none");
            $(this).find(".slide-right").css("display","none");
        }
    });

}

function setSlider(slider,slide){

    $slider = $(".slider-"+slider);
    count = $slider.attr("count");

    // проверим существует ли данный слайд
    if(slide>count) slide = 0;
    if(slide<0) slide = count;
    // установим текущий слайд
    $slider.attr("current",slide);

    $slide = $slider.find(".slide-"+slide);
    $active = $slider.find(".slide.active");

    $slide.css("opacity","0");
    $slide.addClass("show");
    $slide.animate({"opacity": "1"}, 500, "linear", function () {
        $active.removeClass("active");
        $slide.addClass("active");
        $slide.removeClass("show");

		if($slide.find("video").is("video")){
			v1 = $slide.find("video")[0];
			v1.play();
		}
		if($active.find("video").is("video")){
			v2 = $active.find("video")[0];
			v2.pause();
			v2.currentTime = 0;
		}

        // если видео
        if($slide.hasClass("video")){

        }
    });
}
