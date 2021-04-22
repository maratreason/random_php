 $(document).ready(function () {
	jSlider = "";
	$('.block-content input, .block-content textarea').on('keypress', function(){
		jSlider = "{";
		flag = 0;
		$('.block-content').each(function(i,elem){
			img = $(this).find(".slide-image input").val();
			title = $(this).find(".slide-title input").val();
			text = $(this).find(".slide-text textarea").val();
			link = $(this).find(".slide-link input").val();
			if(flag){
				jSlider += ",";
			}
			flag = 1;
			jSlider += '"'+i+'":{"image":"'+img+'","title":"'+title+'","text":"'+text+'","link":"'+link+'"}';
			
			/*href=$(this).attr('href');
			$(this).attr('href','JavaScript:{}');
			$(this).attr('url',href);	// создаём новый атрибут и в него записываем href*/
		});
		jSlider += "}";
		console.log(jSlider);
		$(".slide-array textarea").val(jSlider);
	});	
	
});