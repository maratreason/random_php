
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
		parent = $("select[name='param[parent]']").val();
		$.ajax({
			type:"POST",
			//url:"/ajax/mod-mod_admin/pages-edit/id-"+id+"/unit-"+unit+"/parent-"+parent,
			url:"/ajax/mod-mod_admin/pages-edit/create-page/id-"+id+"/unit-"+unit+"/parent-"+parent,
			//url:location.href+"/unit-"+unit,
			success:function(result){
				$(".content").html(result);
				//$(".superadmin").css("display", "none");
				setUnitSelect();
				//initAce();
				reinitTinyMce();
				createWin();
                reload();
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
				reinitTinyMce();
				createWin();
                reload();
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
	if($.cookie('unit-admin')==0){
		$(".superadmin").css("display", "");
	} else {
		$(".superadmin").css("display", "none");
	}
}

function initAce()
{
	$('.code-editor').each(function(){
		id = $(this).attr("id");
		type = $(this).attr("type");
		var textarea = $(this).hide();
		var editor = ace.edit(id+"-area");
		editor.setTheme("ace/theme/xcode");
		editor.getSession().setMode("ace/mode/"+type);
		editor.getSession().setValue(textarea.val());
		editor.getSession().on('change', function(){
		  textarea.val(editor.getSession().getValue());
		});
	});
}

// запрещает вводить в поле все символы кроме цифр
function admin_setNumberArea()
{
	$(".number-area").keypress(function(e)
    {
	  var code = e.keyCode || e.which;
	  console.log(code);
	  if( code!=8 && code!=0&& code!=45&& code!=46 && (code<48 || code>57))
      {
        return false;
      }
    });
}
// инициализация TinyMCE
function initTinyMce()
{

	tinymce.init({
		selector: "textarea.text-editor",
		mode:"exact",
		language: "ru",
		relative_urls: false,
		remove_script_host: true,
		theme: "modern",
		setup: function(editor) { // перенаправляем событие Ctrl+S обработчику
			editor.on("init", function(){
				editor.addShortcut("ctrl+s", "", function(){ctrlS();}); // ctrlS объявлена в модуле mod_admin.js
			});

			},
		plugins: [
			"advlist autolink lists link image charmap hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code fullscreen",
			"insertdatetime media nonbreaking table contextmenu directionality",
			"emoticons template paste textcolor colorpicker textpattern moxiemanager"
		],
		menubar: false,
		toolbar1: "styleselect | toc | forecolor | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | table link anchor image media | code",
		toolbar2: "",
		image_advtab: true,
		custom_shortcuts : false,
		cleanup_on_startup: false,
		trim_span_elements: false,
		verify_html: false,
		cleanup: false,
		convert_urls: false,
		extended_valid_elements: "div[*],meta[*],span[*]",
		valid_children: "+body[meta],+div[h2|span|meta|object],+object[param|embed]"
	});

}

function reinitTinyMce(){
	initTinyMce();
	$.each($(".text-editor"),function(key, value){
		$(value).attr("id","text-editor-"+key);
		tinymce.execCommand('mceRemoveEditor', true, 'text-editor-'+key);
		tinymce.execCommand('mceAddEditor', true, 'text-editor-'+key);
	});
}

function admin_getDescription()
{
	$('.desc-button').on('click', function(){

		label = $(this).parent("label");
		desc = $(label).find(".description");
		text = $(desc).text();
		width = text.length*4;
		if(width<320) width = 320;
		//pos = $(this).offset();
		//if(pos.left+width > $(window).width()) pos.left = pos.left - (pos.left+width - $(window).width()+50);

		$(desc).css("width",width);
		//$(desc).css("left",pos.left);
		//$(desc).css("top",pos.top+11);
		if($(desc).css("display")=="none"){
			$('.description').css("display","none");
			$(desc).css("display","block");
		} else {
			$(desc).css("display","none");
		}

	});


	$('.description').on('click', function(){
		$(this).css("display","none");
	});

}

function reload(){
	setTab();
    setAdminAreas();
	setUnitSelect();
	setModuleSelect();
	setDateTimePicker();
	initAce();
	setTags();
	admin_getDescription();
	setMultiselect();
	setMultiPrice();
	setMultiImage();
    cirilic_gen();

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
		$("#tags-list").attr("value",list+",");
		console.log(list);
	});
}

// чек лист обработчик для выбора нескольких вариантов
function setMultiselect(){

    $(".select-table").each(function(i,m){
      	$(m).find(".checkbox input").on('change', function(e){
		//tid = $(this).attr("tid");
		list = "";
		$(m).find(".checkbox input").each(function(i,elem){
			tid = $(elem).attr("value");
			if($(elem).is(":checked")) list =list+","+tid;
		});
		$(m).prev(".input").attr("value",list+",");
	   });
    })
}

// выбор нескольких цен
function setMultiPrice(){

    $(".multiprices").each(function(i,m){
      	$(m).find(".values input").on('keyup', function(e){
		list = {};
		$(m).find(".values input").each(function(i,elem){
			city = $(elem).attr("data-key");
			val = $(elem).val();
			list[city] = val;
		});
		json = JSON.stringify(list);
		//console.log(json);
		$(m).find(".mainvalue").attr("value",json);
	   });
    })
}


function setTab(){
	$(".tab:nth-child(1)").css("display","table");
	tab = "<div class='tabs'>";
	$.each($(".tab"), function(i,e){
		if(tab=="") tab = ""

		name = $(e).find(".tab-name").html();
		class_name = $(e).find(".tab-name").attr("class");
		tab += "<div class='"+class_name+"' id='tab-"+i+"'>"+name+"</div>";
		$(e).find(".tab-name").remove();
		$(e).attr("id","tab-"+i+"-content");
	});
	tab += "</div>";
	$(".form").prepend(tab);


	$(".tab-name:nth-child(1)").addClass("active");
	$(".tabs").css("display","block");

	$(".tab-name").on("click", function(){
		$(".tab").css("display","none");
		$(".tab-name").removeClass("active");
		id = "#"+$(this).attr("id")+"-content";
		$(id).css("display","table");
		$(this).addClass("active");
	});
}

/*показать скрыть страницы категории*/

var hid = {};

function statusPage(id){
	var tr = $("#page-row-"+id);
	if($(tr).hasClass("page-status-1")){
		$(tr).addClass("page-status-0");
		$(tr).removeClass("page-status-1");
		$(tr).find(".btn-status img").attr("src","/templates/admin/img/icons/icon-status-.png");
	} else {
		$(tr).addClass("page-status-1");
		$(tr).removeClass("page-status-0");
		$(tr).find(".btn-status img").attr("src","/templates/admin/img/icons/icon-status.png");
	}
	var form_action = "/ajax/mod-mod_admin/pages/status-"+id;
	$.ajax({
		type:"POST",
		url:form_action,
		success:function(result){

		}
	});

}


function hideChild(){
	$(".table-page .parent-icon").on("click", function(){
		var $mainList = $(this).closest(".LIST");
		var $list = $mainList.find(">.LIST");
		var tr = $(this).closest(".row");
        var pid = $(this).attr("data-pid");
        var level = $(this).attr("data-level");
		var form_action = "/ajax/mod-mod_admin/pages/showhide-"+pid+"/level-"+level;
		if(!$(tr).hasClass("close")){
			$(tr).addClass("close");
			$list.remove();
		}
        $.ajax({
            type:"POST",
            url:form_action,
            success:function(result){
                if(result!="") {
					$(tr).after(result);
					$(tr).removeClass("close");
					$(".parent-icon").unbind("click"); // удалить события, чтобы не выполнялось по несколько раз
					hideChild();
					setAdminAreas();
					hitTab();
					setSorting();
				} else {

				}
            }
        });
	});
}

// авто генерация кирилического урл страницы
function cirilic_gen(){
    $(".cirilic-url").on("click",function(){
        form_action = "/ajax/mod-mod_cirilicurl"
        form_data = $("#page-form").serialize();
        createWin({"text":"Генерация URL страницы..."});
        parent = $(this).closest("label");
        $.ajax({
            type:"POST",
            url:form_action,
            data:form_data,
            success:function(result){
                //вернём результат в специальное поле для подгрузки javascript команд
                //$("#result").html(result);

                $(parent).find("input").val(result);
                console.log(result);
                createWin();
            }
        });
    });
}

function setSorting(){
	// сортировка страница
	$.each($(".table .isParent"), function(i,e){
		group = $(e).attr("data-id");
		Sortable.create(e, {
			handle: '.sort',
			chosenClass: "isMove",
			fallbackClass: "isMoveRow",
			forceFallback: true,
			fallbackOnBody: false,
			animation: 100,
			group:group,
			draggable: ".LIST",
			onStart:  function (evt) {

				//$list = $(evt.item).next(".LIST");
				//$list.css("opacity","0");
				/*$list.css("display","none");
				console.log(evt.item);*/
			},
			onEnd: function(evt){
				var list = "";
				var $parent = $(evt.item).closest(".isParent");
				console.log($parent);
				$.each($parent.find(">.LIST"), function(i,e){
					id = $(e).find(".row").attr("id");
					id = id.substr(9);
					if(i!=0) list +=",";
					list +=id;
				});
				console.log(list);
				var form_action = "/ajax/mod-mod_admin/pages/sorting-"+list;
				$.ajax({
		            type:"POST",
		            url:form_action,
		            success:function(result){
						console.log(result);
		            }
		        });
			}
		});
	});
}
function hitTab(){
	$(".btn-hittab").unbind("click");
	$(".btn-hittab").on("click", function(){
		var tr = $(this).closest(".row");
		var pid = $(tr).attr("id");
		pid =pid.substr(9); // id
		var form_action = "/ajax/mod-mod_admin/pages/hittab-"+pid;
		if($(this).hasClass("active")){
			$(this).removeClass("active");
			$(this).find("img").attr("src","/templates/admin/img/icons/icon-hittab.png");
		} else {
			$(this).addClass("active");
			$(this).find("img").attr("src","/templates/admin/img/icons/icon-hittab_.png");
		}
        $.ajax({
            type:"POST",
            url:form_action,
            success:function(result){
				console.log("TAB ADD!");
            }
        });
	});
}
$(function() {
	reload();
    initTinyMce();
    hideChild();
	hitTab();
	setSorting();
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
		if(e.keyCode==113/*&&ctrl == true*/)
		{
			if($.cookie('unit-admin')==0){
				$.cookie('unit-admin',1);
			} else {
				$.cookie('unit-admin',0);
			}
			setAdminAreas();

			return false;
		}
	});



	// ВЫБОР ЦВЕТА
	$.each($(".selectcolor"),function(i,e){
		var el = $(this).find(".colorpicker")[0];
		var elInput = $(this).find(".color")[0];
		var color = $(elInput).attr('value');
		if(color.indexOf("$")+1){
			$(e).find(".styleColor[data-key='"+color+"']").addClass("active");
			color = $(e).find(".styleColor[data-key='"+color+"']").attr("data-color");
		}
		if(!color) {
			// цвет по умолчанию
			color = "#FFFFFF";
			$(elInput).val(color);
		}
		var preview = $(this).find(".preview")[0];

		// пикер
		$(preview).attr("style","background-color:"+color);
		var pickr = Pickr.create({
		el: preview,
		position: 'top',
		useAsButton: true,
		disabled: false,
		default: color,
		components: {

			// Main components
			preview: true,
			opacity: true,
			hue: true,
			// Input / output Options
			interaction: {
				hex: true,
				rgba: true,
				hsla: true,
				hsva: true,
				cmyk: true,
				input: true,
				clear: false,
				save: true
			}
		},
		strings: {
		   save: 'Сохранить',  // Default for save button
		   clear: '' // Default for clear button
		},
		onChange(hsva, instance) {
			$(elInput).val(hsva.toHEX().toString());
			$(preview).attr("style","background-color:"+hsva.toHEX().toString());
			$(e).find(".styleColor").removeClass("active");
		}
		});

		// выбор из готовых
		$(this).find(".styleColor").on("click",function(){
			$(e).find(".styleColor").removeClass("active");
			$(elInput).val($(this).attr("data-key"));
			$(preview).attr("style","background-color:"+$(this).attr("data-color"));
			$(this).addClass("active");
			//pickr.setColor($(this).attr("data-color"));
		})
	});

	/*Sortable.create($('table')[0], {
		handle: '.thisIsPage',
		onEnd: function(){
			//parent = $(e).closest(".multiImages");
			//saveMultiImageList(parent);
		}
	});*/


});
