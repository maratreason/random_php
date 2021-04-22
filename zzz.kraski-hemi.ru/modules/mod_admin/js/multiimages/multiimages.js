
// сохраняем в поле
function saveMultiImageList(parent){
	    var out = "[";
		block = $(parent).find(".multiimages-block");
		$.each($(block).find(".multi-image-area"), function(i,obj){
			if(i!=0) out +=",";
			caption = $(obj).find(".image-caption").val();
			image = $(obj).find(".image-input").val();
			out += '["'+image+'","'+caption+'"]';
			
            //out += $(obj).val();
        });
		out +="]";
		$(parent).find(".multiimage-input").val(out);
		
}
// создание полей для мулти загрузки картинок
function setMultiImage(){

    // генерация полей
    $.each($(".multiImages"), function(index, obj){
        block = $(obj).find(".multiimages-block");
        $(block).html("");
        
        //parent = $(obj).parent("label"); 
        images = $(obj).find(".multiimage-input").val();
		//if(images=="") images = '[["",""]]';
        name = $(obj).find(".multiimage-input").attr("name");
		
		if(images!="")images = JSON.parse(images);
        //images = images.split(";");
       // console.log(images);
        // генерация кнопки "новое поле"
        addBtn = "<a href='javascript:;' class='button add-new-image'>Добавить картинку</a>";
        $(block).append(addBtn);
        var out = "<div class='multi-image-table' id='multi-image-"+index+"'>";
		var count = 0;
		//if(images.length>0)
		$.each(images, function(i,value){
			
			if(count==0||i==0) {
				//out += "<div class='row'>";	
			}
			count++;
            out += "<div class='multi-image-area'><label>";
			out += "<input name='' type='text' value='"+value[1]+"' class='input image-caption'/>";
			out += "<div class='image' style='background-image:url("+value[0]+")'></div>";
			
			out += "<input name='' type='text' value='"+value[0]+"' class='input image-input ' id='image-"+name+"-"+i+"'/>";
            out += "<a href='javascript:;' class='button browse-image' onclick='moxman.browse({fields:\"image-"+name+"-"+i+"\",no_host: true})'><img src='/templates/admin/img/icons/icon-browse.png' width='24' height='24' /></a>";
            out += "<a href='javascript:;' class='button browse-image delete-image' onclick=''> X </a></div></label>";
			if(count==4) {
				//out += "</div>";
				count = 0;
			}
        });
		out += "</div>";
        $(block).append(out);
        /*Подключаем сортировку*/
        $.each($(".multi-image-table"), function(i,e){
            Sortable.create(e, { 
                handle: '.image',
                onEnd: function(){
                    parent = $(e).closest(".multiImages");
                    saveMultiImageList(parent);
                }
            });
        });        
        
	});
    
    // событие кнопки Добавить
    $(".add-new-image").on('click',function(){
        parent = $(this).closest(".multiImages");
        images = $(parent).find(".multiimage-input").val();
		if(images!="") {
			images = JSON.parse(images); 
			images.push(["",""]);
			images = JSON.stringify(images);
		} else {
			images = '[["",""]]';
		}
		
		
        $(parent).find(".multiimage-input").val(images);
        setMultiImage();
    });

    // событие кнопки Удалить
    $(".delete-image").on('click',function(){
        parent = $(this).closest(".multiImages");
		image = $(this).closest(".multi-image-area");
		$(image).remove();
        
		saveMultiImageList(parent);
    });    
    // события на изменения полей
    $(".multiimages-block .image-input, .multiimages-block .image-caption").on("change keyup", function(){
        parent = $(this).closest(".multiImages");
		// обновим картинку
		image = $(this).closest(".multi-image-area").find(".image-input").val();
		imgBlock = $(this).closest(".multi-image-area").find(".image");
		$(imgBlock).css("background-image","url("+image+")");
		//сохраним
		saveMultiImageList(parent);

    });
}