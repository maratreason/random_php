 $(document).ready(function () {
    // превью товара
	$(".photo-gallery .item").each(function(){
		$img = $(this).find("img");
		$img.css("display","none");
		src = $img.attr("src");
		$(this).css("background-image","url("+src+")");
	});
	
	    // модальные фото
	$('.photo-gallery a').magnificPopup({
		type: 'image',
		gallery:{
			enabled:true
		},
		//closeOnContentClick: true,
		fixedContentPos: false,
		fixedBgPos: true,
		overflowY: 'auto',
		closeBtnInside: true,
		preloader: false,
		midClick: true,
		
	});
 });
