// функция проверки корректности e-mail адреса
function isValidEmail (email, strict)
{
 if ( !strict ) email = email.replace(/^\s+|\s+$/g, '');
 return (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i).test(email);
}

function setFormActions(form){

	$('.input_date .datetime').datetimepicker({
		timepicker: false,
		lang: "ru",
		format: "d.m.Y",
		mask: false
	});
    $(".formid").removeClass("disable");
    $('.input_file input[type="file"]').on('change', function (event, files, label) {
		parent = $(this).closest(".input_file");
		var file_name = this.value.replace(/\\/g, '/').replace(/.*\//, '')
	    $(parent).find('.file-name').text(file_name);
	});

    $.each($('.feedback_form input[name="url"]'), function(){
        url = window.location.pathname;
        $(this).val(url);
    });

	// модальные окна с формой
	$('.form-modal').magnificPopup({
		type: 'inline',
		fixedContentPos: false,
		fixedBgPos: true,
		overflowY: 'auto',
		closeBtnInside: true,
		preloader: false,
		midClick: true,
		removalDelay: 300,
		mainClass: 'my-mfp-slide-bottom',
	callbacks: {
			elementParse: function(item) {
		// записываем доп инфо
		title = $(item.el).attr("data-title");
		desc = $(item.el).attr("data-desc");
		info = $(item.el).attr("data-info");
		default_value = "";
				$(item.src).find("input[name=info]").val(info);
		if(title) {
		  $(item.src).find(".title").html(title);
		} else {
		  // значение по умолчанию
		  default_value = $(item.src).find(".title").attr("data-default");
		  $(item.src).find(".title").html(default_value);
		}

		if(desc) {
		  $(item.src).find(".desc").html(desc);
		} else {
		  // значение по умолчанию
		  default_value = $(item.src).find(".desc").attr("data-default");
		  $(item.src).find(".desc").html(default_value);
		}

		//возвращаем форму в исходное состояние
			layer = $(item.src).closest(".feedback");
			$(layer).find("form").css("display","block");
			$(layer).find(".title").css("display","block");
			$(layer).find(".desc").css("display","block");
			$(layer).find(".results .loading").css("display","none");
			$(layer).find(".results .result").css("display","none");
			}
	}
	});

	// отправить сообщение
	$(".feedback_button").unbind("click");
	$('.feedback_button').on('click', function(btn){
		parent = $(this).closest("form");
		$(parent).find(".err_msg").html("");
		$(parent).find(".err_msg").css("display","none");
		$(parent).find(".input").removeClass("error");
		error = 0;
		$(parent).find(".input-box").each(function(i,elem){
			// проверка полей обязательных для заполнения
			if($(this).hasClass("input_require")) // данное поле должно быть заполнено!
			{
				//console.log(elem);
				if($(this).find(".input").val()=="")
				{
					$(this).find(".err_msg").html("Обязательно для заполнения");
					$(this).find(".err_msg").css("display","table");
					$(this).find(".input").addClass("error");
					error = 1;
				}
			}

			// проверка полей телефонов
			if($(this).hasClass("input_telefon"))
			{
				telefon = $(this).find(".input").val();
				if(telefon=="")
				{
					$(this).find(".err_msg").html("Неверный формат телефона");
					$(this).find(".err_msg").css("display","table");
					$(this).find(".input").addClass("error");
					error = 1;
				}
			}

			// проверка полей email адресов
			if($(this).hasClass("input_email")&&$(this).hasClass("input_require"))
			{
				email = $(this).find(".input").val();
				if(!isValidEmail(email))
				{
					$(this).find(".err_msg").html("Не верный формат E-mail");
					$(this).find(".err_msg").css("display","table");
					$(this).find(".input").addClass("error");
					error = 1;
				}
			}
            // проверка поля сообщения
			if($(this).hasClass("input_text"))
			{
				text = $(this).find("textarea").val();
                if(text.indexOf("http:")>=0||text.indexOf("https:")>=0||text.indexOf("www")>=0){
                    $(this).find("label").append("<div class='err_msg'></div>");
                    $(this).find(".err_msg").html("Текст сообщения не может содержать ссылок!");
					$(this).find(".err_msg").css("display","table");
					$(this).find(".input").addClass("error");
                    error = 1;
                }
			}
		});
		//console.log("error:"+error);
		if(!error) // отправить, если нет ошибок в заполнении формы
		{
				console.log("NO ERRORS");
				//$(this).css("display","none");
				layer = $(this).closest(".feedback");
				$(layer).find("form").css("display","none");
				$(layer).find(".title").css("display","none");
				$(layer).find(".desc").css("display","none");
				$(layer).find(".results .loading").css("display","block");
				$(parent).find(".results .result").css("display","none");
				var form_action = $(parent).attr("action");
                var formData = new FormData($(parent)[0]);
                $(parent).find("input").each(function(){
                  if ($(this).attr('type') != 'file') {
					  if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio'){
						if ($(this).attr('checked')) formData.append($(this).attr('name'), $(this).val()); // помещаем все значения полей в объект
					} else {
						formData.append($(this).attr('name'), $(this).val()); // помещаем все значения полей в объект
					}
                  }
                });
				//var form_data = $(parent).serialize();
				$.ajax({
					type:"POST",
					url:form_action,
					data:formData,
                    contentType: false,
					processData: false,
					success:function(result){
						$(layer).find(".results .loading").css("display","none");
						$(layer).find(".results .result").css("display","block");
						$(layer).find(".results .result").html(result);// вывести сообщение
                        //yaCounter44566269.reachGoal('callbackOrder');
					}
				});
		}
	});
}


$(document).ready(function (){
	count = $('.formid').length-1;
	$('.formid').each(function(i,e){
		var id = $(this).attr("data-id");
		var page_id = $(this).attr("data-page-id");
		var tourl = "/ajax/mod-mod_feedback";

		$.ajax({
			type: 'POST',
			url: tourl,
			data: "getform="+id+"&pageid="+page_id,
			success: function(data){
				$(e).html(data);
				// после загрузки последней формы инициализируем все события форм
				setFormActions();
			}
		});
	});


	// маски
	// телефон
	/*$(".feedback_form .input_telefon .input").mask("+7 (999) 999-99-99",
	{
		completed: function(){
			parent = $(this).parent();
			$(parent).find(".err_msg").css("display","none");
			$(parent).find(".err_msg").html("");
			$(parent).find(".input").removeClass("error");
		}
	});*/

});
