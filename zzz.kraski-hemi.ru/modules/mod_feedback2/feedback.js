function set_modFeedBack(){
// позволяем вводить в поле телефона только цифры и спец. символы телефонов
	/*$(".feedback_form .input_telefon .input").keypress(function(e)
    {
	  var code = e.keyCode || e.which;
	  if( code!=8 && code!=0 && code!=43 && code!=45 && code!=40 && code!=41 && (code<48 || code>57))
      {
        return false;
      }
    });*/
	// функция проверки корректности e-mail адреса
	function isValidEmail (email, strict)
	{
	 if ( !strict ) email = email.replace(/^\s+|\s+$/g, '');
	 return (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i).test(email);
	}



	// отправить сообщение
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
		});
		//console.log("error:"+error);
		if(!error) // отправить, если нет ошибок в заполнении формы
		{
				console.log("NO ERRORS");
				$(this).css("display","none");
				$(parent).find(".input-box").css("display","none");
				$(parent).find(".feedback_result").css("display","block");
				$(parent).find(".feedback_result").html("Отправляем..."); // вывести сообщение
				var form_action = $(parent).attr("action");
				var form_data = $(parent).serialize();
				//console.log("action: "+form_action);
				//console.log("data: "+form_data);
				$.ajax({
					type:"POST",
					url:form_action,
					data:form_data,
					success:function(result){
						$(parent).find(".feedback_result").html(result);// вывести сообщение
					}
				});
		}
	});
}


$(document).ready(function (){
	set_modFeedBack();


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
