document.addEventListener("DOMContentLoaded", function(){
var data_url = "";

function getFilterUrl(o,top){
    filter = {};
    parent = $(".multi-filter");
    // находим все фильтры и проходим по ним
    $(parent).find(".jedfilter").each(function(){
        // получим индификатор фильтра
        filter_name = $(this).attr("id");
        filter_name = filter_name.substr(7);
        filter[filter_name] = [];
        // чекбоксы
        if($(this).hasClass("multiselect")){
            $(this).find("input:checkbox:checked").each(function(i,e){
                value = $(this).attr("value");
                filter[filter_name].push(value);
            });

        }

        // диапазон
        if($(this).hasClass("range")){

            val1 = Math.round($(this).find("input.val1").attr("value"));
            val2 = Math.round($(this).find("input.val2").attr("value"));

            min = Math.round($(this).find("input.val1").attr('min'));
            max = Math.round($(this).find("input.val2").attr('max'));
            if(val1!=min||val2!=max){
                filter[filter_name].push(val1);
                filter[filter_name].push(val2);
            }
        }

    });


    if(!top) top = 0;
    offset = $(o).closest('label').position();
    if(!offset) offset = $(o).closest(".polzun").position();
    parent = $(o).closest(".jedfilter");
    width = $(parent).innerWidth();
    //width = parseInt(width.substr(0,3));

    offset.top += top;
    offset.left = width+20;
    //url = $(".multi-filter").attr("action")+url;
    $(".modalresult").css(offset);
    $(".modalresult span").html("Загрузка...");

    //отпраивим на сервер и получим результат JSON
    action = $(".multi-filter").attr("action");

    $.ajax({
        type:"POST",
        url:"/ajax/mod-mod_multifilter_list",
        data:{"filter":filter,"action":action},
        success: function (result) {
            data_url =  JSON.parse(result);
            // console.log(data_url);
            $(".modalresult").css("display","inherit");
            if(data_url['count']>-1){
                $(".modalresult").html("<span>Найдено "+data_url['count']+" </span><a href='"+data_url['url']+"' class='jbutton small'>Показать</a>");
            } else {
                $(".modalresult").html("<span>Ничего не найдено</span>");
            }
        }
    });

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
    url = getFilterUrl();
    url = $(".multi-filter").attr("action")+url;
    $(".modalresult").css(offset);
    $(".modalresult span").html("Загрузка...");
    $.ajax({
        type:"POST",
        url:"/ajax/mod-mod_multifilter_list",
        data:{"url":url},
        success:function(result){
            $(".modalresult").css("display","inherit");
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
        document.location = data_url['url'];
    });

    // всплывающая подсказка с количеством найденых элементов
    $(".multi-filter").append("<div class='modalresult'><span>Result</span></div>");
    $(".modalresult").on("click", function(e){
      $(".modalresult").css("display","none");
    });

    $(".jedfilter input").on("ifChanged change", function(){
        getFilterUrl(this);
    });

    // покажем все связанные фильтры
    /*
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
*/

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
            //showFilterResult(slider[i],-46);
            getFilterUrl(slider[i],-46);
        });

        // обновление ячеек с числами при изменении слайдера
        slider[i].noUiSlider.on('update', function( values, handle ) {
            $("#val1-"+i).attr("value",Math.round(+values[0]));
            $("#val2-"+i).attr("value",Math.round(+values[1]));
        });

        // обновление слайдера при изменении ячеек с числами
       $(e).find('.val1, .val2').on("change",function(){
            val1 = $("#val1-"+i).val();
            val2 = $("#val2-"+i).val();
            slider[i].noUiSlider.set([val1, val2]);
           //showFilterResult(slider[i],-46);
        });
    });

    // скрытие больших фильтров
    $(".jedfilter .showAll").on("click", function(i,e){
        parent = $(this).closest(".jedfilter");
        if($(parent).find(".down").hasClass("active")){
            $(parent).find(".down").removeClass("active");
            $(this).html("Все варианты");
        } else {
            $(parent).find(".down").addClass("active");
            $(this).html("Скрыть");
        }
    });
});
});