
// создаём окно с нужным текстом и кнопками. params - объект типа {'id':id,...}
function createWin(params)
{
	if(params){
		$('.modal .modal_text ').html(params['text']);
		if(params['open-func'])params['open-func'](); // функция при открытии окна
		// соберём все кнопки
		var button="";
		$('.modal .modal_buttons').html("");
		if(params['buttons']) {$(".modal_buttons").css("display","block");} else {$(".modal_buttons").css("display","none");}
		for(var key in params['buttons']){
			btn = params['buttons'][key];
			button = "<a href='javaScript:{}' class='button inline-button "+btn['class']+"' id='modal-btn-"+key+"'>"+btn["name"]+"</a>";
			$('.modal .modal_buttons').append(button);
			// событие при нажатии 
			$("#modal-btn-"+key).on('click',btn["func"]);
		}
		
		// показываем все
		$(".modal-overlay").css("display","block");
		$(".modal").css("display","block");
		$(".modal").animate({"opacity":1},200);
		$(".modal-overlay").animate({"opacity":1},200);
	} else {
		setTimeout('$(".modal").css("display","none")',200);
		setTimeout('$(".modal-overlay").css("display","none")',200);
		$(".modal").animate({"opacity":0},200);
		$(".modal-overlay").animate({"opacity":0},200);	
	}
}

function sendForm(form){
	// сохраним изменения, при отправке AJAX не собирает изменнеия textarea
	tinymce.triggerSave();
	console.log("trigger ON");
	$.each($(".text-editor"),function(key, value){
		$(value).css("visibility","show");
		/*$(value).attr("id","text-editor-"+key);
		cont = tinyMCE.get("text-editor-"+key).getContent();
		console.log(cont);*/
	});
	var form_action = $("#"+form).attr("action");
	var form_data = $("#"+form).serialize();
	createWin({"text":"Сохранение..."});
	$.ajax({
		type:"POST",
		url:form_action,
		data:form_data,
		success:function(result){
			//вернём результат в специальное поле для подгрузки javascript команд
			$("#result").html(result);
			console.log(result);
			createWin();
		}
	});
}

function deletePage(id){
	createWin({
		'text':'Удалить страницу?',
		'open-func':function(){
						$("#page-row-"+id).addClass("delete-row");
					},
		'buttons':{"cancel":{
							"name":"Отмена",
							"func":function(){$("#page-row-"+id).removeClass("delete-row");createWin();},
							"icon":"icon-plus.png",
							"class":""
							},
					"delete":{
							"name":"Удалить",
							"func":function(){
								$.ajax({
									type:"POST",
									url:"/ajax/mod-mod_admin/pages-delete/id-"+id,
									success:function(result){
										console.log(result);
										$("#page-row-"+id).css("display","none");
										createWin();
									}});
							
							},
							"icon":"icon-plus.png",
							"class":""
							}
				  }
		});
}

function deleteModule(id,text){
	if(!text) text = 'Удалить модуль?';
	createWin({
		'text':text,
		'open-func':function(){
						$("#page-row-"+id).addClass("delete-row");
					},
		'buttons':{"cancel":{
							"name":"Отмена",
							"func":function(){$("#page-row-"+id).removeClass("delete-row");createWin();},
							"icon":"icon-plus.png",
							"class":""
							},
					"delete":{
							"name":"Удалить",
							"func":function(){
								$.ajax({
									type:"POST",
									url:"/ajax/mod-mod_admin/modules-delete/id-"+id,
									success:function(result){
										console.log(result);
										$("#page-row-"+id).css("display","none");
										createWin();
									}});
							
							},
							"icon":"icon-plus.png",
							"class":""
							}
				  }
		});
}

function deleteRecord(id,base){
	createWin({
		'text':'Удалить запись?',
		'open-func':function(){
						$("#page-row-"+id).addClass("delete-row");
					},
		'buttons':{"cancel":{
							"name":"Отмена",
							"func":function(){$("#page-row-"+id).removeClass("delete-row");createWin();},
							"icon":"icon-plus.png",
							"class":""
							},
					"delete":{
							"name":"Удалить",
							"func":function(){
								$.ajax({
									type:"POST",
									url:"/ajax/mod-mod_admin/base-"+base+"/delete-"+id,
									success:function(result){
										console.log(result);
										$("#page-row-"+id).css("display","none");
										createWin();
									}});
							
							},
							"icon":"icon-plus.png",
							"class":""
							}
				  }
		});
}

function deleteUnit(id,text){
	if(!text) text = 'Удалить Unit файл?';
	createWin({
		'text':text,
		'open-func':function(){
						$("#page-row-"+id).addClass("delete-row");
					},
		'buttons':{"cancel":{
							"name":"Отмена",
							"func":function(){$("#page-row-"+id).removeClass("delete-row");createWin();},
							"icon":"icon-plus.png",
							"class":""
							},
					"delete":{
							"name":"Удалить",
							"func":function(){
								$.ajax({
									type:"POST",
									url:"/ajax/mod-mod_admin/units-delete/file-"+id,
									success:function(result){
										console.log(result);
										$("#page-row-"+id).css("display","none");
										createWin();
									}});
							
							},
							"icon":"icon-plus.png",
							"class":""
							}
				  }
		});
}
function ctrlS(){
	//  сохраняем страницу
	if($("#page-form")[0]){
		sendForm("page-form");
	}
	//  сохраняем модуль
	if($("#module-form")[0]){
		sendForm("module-form");
	}	
	//  сохраняем юнит
	if($("#unit-form")[0]){
		sendForm("unit-form");
	}	

	//  сохраняем таблицу
	if($("#base-form")[0]){
		sendForm("base-form");
	}	
}
function setUnitSelect(){

	// для поля выбора шаблона сделаем автоматически подгрузку новых полей
	$("#unit-select").on('change', function(e){
		createWin({"text":"Загрузка полей..."});
		tinymce.triggerSave();
		unit = $(this).val();
		id = $("input[name='param[id]']").val();
		$.ajax({
			type:"POST",
			url:"/ajax/mod-mod_admin/pages-edit/id-"+id+"/unit-"+unit,
			success:function(result){
				$(".content").html(result);
				$(".superadmin").css("display", "none");
				setUnitSelect();
				initAce();
				reinitTinyMce();
				createWin();
			}});
	});
}

function setModuleSelect(){

	// для поля выбора шаблона сделаем автоматически подгрузку новых полей
	$("#module-select").on('change', function(e){
		createWin({"text":"Загрузка полей..."});
		tinymce.triggerSave();
		unit = $(this).val();
		id = $("input[name='param[id]']").val();
		$.ajax({
			type:"POST",
			url:"/ajax/mod-mod_admin/modules-edit/id-"+id+"/unit-"+unit,
			success:function(result){
				$(".content").html(result);
				$(".superadmin").css("display", "none");
				setModuleSelect();
				initAce();
				reinitTinyMce();
				createWin();
			}});
	});
}

function setDateTimePicker(){
	$('.datetime-picker').datetimepicker({
		lang:"ru",
		format:"d/m/Y H:i:s",
		mask:true
	});

}

function setAdminAreas(){
	$(".superadmin").css("display", "");
}

function reload(){
	setAdminAreas();
	setUnitSelect();
	setModuleSelect();
	setDateTimePicker();
	initAce();
	setTags();
	//reinitTinyMce(); // Переписывает инициализацию МСЕ, после чего не работает triggerSave()
}
// чек лист обработчик для выбора тэгов для статьи
function setTags(){
	$(".tags-check").on('change', function(e){
		//tid = $(this).attr("tid");
		list = "";
		$(".tags-check").each(function(i,elem){
			tid = $(elem).attr("tid");
			//console.log(">>"+tid+" >"+list);
			if($(elem).is(":checked")) list =list+","+tid;
		});
		$("#tags-list").attr("value",list);
		console.log(list);
	});
}
$(function() {
	
	reload();
	var ctrl = false;
		$(document).keyup(function(e){
		if(e.keyCode==17) ctrl = false;
	});
	
	$(document).keydown(function(e){
		if(e.keyCode==17) ctrl = true;
		// сохранение через Ctrl+S
		if(e.keyCode==83&&ctrl == true)
		{
			ctrlS();
			return false;
		}
		// показать/скрыть супер поля ctrl+f2
		if(e.keyCode==113&&ctrl == true)
		{
			if($(".superadmin").css("display")=="none"){
				$(".superadmin").css("display", "");
			} else {
				$(".superadmin").css("display", "none");
			}
			return false;
		}	
	});  

});