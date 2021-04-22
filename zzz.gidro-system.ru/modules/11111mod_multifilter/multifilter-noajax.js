
function getFilterUrl(){
    filter = {};
    parent = $(".multi-filter");
    // находим все фильтры и проходим по ним
    $(parent).find(".jedfilter").each(function(){
        // получим индификатор фильтра
        filter_name = $(this).attr("id");
        filter_name = filter_name.substr(7);
        //filter[filter_name] = "";
        filter[filter_name] = [];
        // чекбоксы
        if($(this).hasClass("multiselect")){
            $(this).find("input:checkbox:checked").each(function(i,e){
                value = $(this).attr("value");

                if(value!="-"){
                    //if(i>0) filter[filter_name] +=","
                    //filter[filter_name] += value;

                    filter[filter_name].push(value);
                }
            });

        }
        // радио
        if($(this).hasClass("select")){
            $(this).find("input:radio:checked").each(function(i,e){
                value = $(this).attr("value");
                if(value!="-"){
                    //if(i>0) filter[filter_name] +=","
                    //filter[filter_name] += value;
                    filter[filter_name].push(value);
                }
            });

        }
        // диапазон
        if($(this).hasClass("range")){
            val1 = Math.round($(this).find("input.val1").val());
            val2 = Math.round($(this).find("input.val2").val());
            min = Math.round($(this).find("input.val1").attr('min'));
            max = Math.round($(this).find("input.val2").attr('max'));
            // не покажем в фильтре если стоят значения макс и мин, или если они пустые
            if(val1!=min||val2!=max)
            if((val1!=""&&val1>=0)||(val2!=""&&val2>=0)){
                //filter[filter_name] += val1+","+val2;
                filter[filter_name].push(val1);
                filter[filter_name].push(val2);
            }
        }

    });
    url = "/only-";
    count=0;
    // сортируем фильтр
    filter = ksort(filter);
    for(var n in filter){
        v = filter[n];
        // сортируем значение в филтрах
        v.sort();
        if(v.length>0) {
            if(count>0) url+="+";
            url += n+"-"+v.join(',');
            count++;
        }
    }
    // проверим указывали ли мы порядок сортировки
    cururl = document.location+"";
    cururl = cururl.split("/only-")[1];
    if(cururl){
        keys = cururl.split("+");
        $.each(keys,function(i,e){
            kv = e.split("-");
            // если сортировка единственный фильтр то + не ставим
            if(kv[0]=="sort") {if (url=="/only-") url += e; else url += "+"+e; }
        });
    }
    if(url=="/only-") url = "";
    return url;
}

// сортировка массива по ключу
function ksort(w) {
	var sArr = [], tArr = [], n = 0;

	for (i in w){
		tArr[n++] = i;
	}

	tArr = tArr.sort();
	for (var i=0, n = tArr.length; i<n; i++) {
		sArr[tArr[i]] = w[tArr[i]];
	}
	return sArr;
}

// обратная сортировка массива по ключу
function krsort(w) {
	var sArr = [], tArr = [], n = 0;

	for (i in w){
		tArr[n++] = i;
	}

	tArr = tArr.sort(function(a,b){return (b > a)});
	for (var i=0, n = tArr.length; i<n; i++) {
		sArr[tArr[i]] = w[tArr[i]];
	}
	return sArr;
}

// показывает всплывающую подсказку о количестве наденых элементов
function showFilterResult(o,top){
   //$(".modalresult").css("display","none");
    if(!top) top = 0;
    offset = $(o).closest(label).position();
    if(!offset) offset = $(o).closest(".polzun").position();
    parent = $(o).closest(".jedfilter");
    width = $(parent).innerWidth();
    //width = parseInt(width.substr(0,3));

    offset.top += top;
    offset.left = width+20;
    console.log(offset);
    url = getFilterUrl();
    url = $(".multi-filter").attr("action")+url;
    $(".modalresult").css(offset);
    $(".modalresult span").html("Загрузка...");
    $.ajax({
        type:"POST",
        url:"/ajax/mod-mod_multifilter_list",
        data:{"url":url},
        success:function(result){
            $(".modalresult").css("display","block");
            if(result>0){
                $(".modalresult").html("<span>Найдено "+result+" </span><a href='"+url+"' class='jbutton small'>Показать</a>");
            } else {
                $(".modalresult").html("<span>Ничего не найдено</span>");
            }
        }
    });
}

$(document).ready(function (){
    $(".multi-filter .button-filter").on("click", function(){
        url = getFilterUrl();
        cururl = $(".multi-filter").attr("action");
        //console.log(cururl+url);
        document.location = cururl+url;

    });
    // всплывающая подсказка с количеством найденых элементов
    $(".multi-filter").append("<div class='modalresult'><span>Result</span></div>");
    $(".modalresult").on("click", function(e){
      $(".modalresult").css("display","none");
    });
    $(".jedfilter input").on("ifChanged change", function(){
        // показываем скрываем связанные поля
        label = $(this).closest("label");
        classes = $(label).attr("class").split(" ");
        for (var i = 0; i < classes.length; i++) {
            // ключи
            if (classes[i].indexOf("keyname-")==0) {
                params = classes[i].replace("keyname-", "");
                params = params.split("-");

                // включим/отключим наследуемые фильтры
                parent = ".keyparent-"+params[0]+"-"+params[1];
                if($(this).prop("checked")) {
                    $(parent).addClass("show-parents");
                } else {
                    $(parent).removeClass("show-parents");
                    // а также снимем чек
                    $(parent).find(".checked").removeClass("checked");
                    $(parent).find("input").removeAttr("checked");
                }
            }
        }
        showFilterResult(this);
    });

    // покажем все связанные фильтры
    $(".jedfilter.multiselect input").each(function(i,e){
        // показываем скрываем связанные поля
        label = $(e).closest("label");
        classes = $(label).attr("class").split(" ");
        for (var i = 0; i < classes.length; i++) {
            // ключи
            if (classes[i].indexOf("keyname-")==0) {
                params = classes[i].replace("keyname-", "");
                params = params.split("-");

                // включим/отключим наследуемые фильтры
                parent = ".keyparent-"+params[0]+"-"+params[1];
                if($(e).prop("checked")) {
                    $(parent).addClass("show-parents");
                } else {
                    $(parent).removeClass("show-parents");
                    // а также снимем чек
                    $(parent).find(".checked").removeClass("checked");
                    $(parent).find("input").removeAttr("checked");
                }
            }
        }
    });


    // оформление чекбоксов
    $('.jedfilter input').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue',
        increaseArea: '20%' // optional
    });
    // офрмление ползунков
    slider = [];
    $(".multi-filter .range").each(function(i,e){
        //var html5Slider = document.getElementById('html5');
        //$(e).find('.polzun').attr("polzun-"+i);
        $(e).find('.val1').attr("id","val1-"+i);
        $(e).find('.val2').attr("id","val2-"+i);

        slider[i] = $(e).find('.polzun')[0];
        input1 = $(e).find('.val1')[0]; // от
        input2 = $(e).find('.val2')[0]; // до
        start = $(input1).val();
        end = $(input2).val();
        min = parseInt($(input1).attr('min'));
        max = parseInt($(input2).attr('max'));
        //console.log(min+' '+max);
        noUiSlider.create(slider[i], {
            start: [ start, end ],
            connect: true,
            step:1,
            range: {
                'min': min,
                'max': max
            }
        });

        slider[i].noUiSlider.on('set', function(){
            showFilterResult(slider[i],-46);
        });

        // обновление ячеек с числами при изменении слайдера
        slider[i].noUiSlider.on('update', function( values, handle ) {
            $("#val1-"+i).val(Math.round(values[0]));
            $("#val2-"+i).val(Math.round(values[1]));
            //showFilterResult(slider[i],-46);
        });

        // обновление слайдера при изменении ячеек с числами
       $(e).find('.val1, .val2').on("change",function(){
            val1 = $("#val1-"+i).val();
            val2 = $("#val2-"+i).val();
            slider[i].noUiSlider.set([val1, val2]);
           //showFilterResult(slider[i],-46);
        });
    });
});
