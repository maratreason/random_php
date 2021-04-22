$(function(){
    function getItemsCount(json){
        count = 0;
        if(json){
            obj = JSON.parse(json);
            $.each(obj, function(i,e){
                count++;
            });
        }
        return count;
    }
    function updateBasketPanel(json){
        if(json==-1) {
            count = $(".basketPanel .count").text();
        } else {
            count = getItemsCount(json);
        }
        console.log('--'+count);
        if(count>0) $(".basketPanel .count").removeClass("disable"); else $(".basketPanel .count").addClass("disable");
        // word = "";
        // if(count==1) word = "товар";
        // if(count%10>=2&&count%10<=4) word = "товара";
        // if(count%10>=5&&count%10<=9||count%10==0) word = "товаров";
        // if(count>=11&&count<=14) word = "товаров";
        $(".basketPanel .count").html(count);
    }
    
    function selectAllItems(json){
        $(".priceValue").removeClass("add");
        obj = JSON.parse(json);
        $.each(obj, function(i,e){
            $(".priceValue[data-id="+i+"]").addClass("add");
        });
    }
    
    function calculateBasket(){
        var values = {};
        var total = 0;
        var countItems = 0;
        $.each($(".basketTable .item"), function(i,e){
            var id = $(this).attr("data-id");
            var price = $(this).find(".price").attr("data-value");
            var count = $(this).find(".count").val();
            var name = $(this).find(".name").text();
            var url = $(this).find(".name").attr("href");
            if(count<0) count = 1;
            value = price*count;
            $(this).find(".price-count").text(value.toLocaleString()+" руб.");
            if(count == 1){
                $(this).find(".price").text(count+" x "+price.toLocaleString()+" руб.");
            } else if(count>1){
                $(this).find(".price").text(count+" x "+price.toLocaleString()+" руб.");
            } else {
                $(this).find(".price").text("");
            }
            total += value;
            values[id] = {};
            values[id]['name'] = name;
            values[id]['url'] = url;
            values[id]['count'] = count;
            values[id]['price'] = price;
            values[id]['price-count'] = value;
            countItems++;
        });
        values['total'] = total;
        $(".basketTable .totalPrice").html(total.toLocaleString()+" руб.");
        // Добавление в форму корзины
        $("#form-basket .input_json .values").val(JSON.stringify(values));
        //updateBasketPanel(countItems);
        if(countItems<=0){
            $(".basketTable").css("display","none");
            $(".noBasket").css("display","block");
        } else {
            $(".basketTable").css("display","table");
            $(".noBasket").css("display","none");
        }
    }
    
    // проверяет связанные карточки которые недьзя купить без выбора хоть одного родителя
    function boundItems(){
        // отобразим связанные карточки
        $.each($(".boundItem"), function(i,e){
            bounds = $(this).attr("data-bound");
            bounds = bounds.split(",");
            count = 0;
            $.each(bounds, function(n,b){
                if(b!="")
                {                        
                    if($(".priceValue.add[data-id="+b+"]").length){
                        count++;
                    }
                }
            });
            if(count>0) {
                $(e).removeClass("disable"); 
            } else {
                $(e).addClass("disable"); 
            }
        });
    }
    //  прикрепляем событие по взаимодействию с формой загруженой с аякса
    $('.formid[data-id="form-basket"]').on("click", function(){
        calculateBasket();
    });

    // добавление по кнопке в корзину
	$(".addBasketButton").on("click", function(i,e){
        if($(this).hasClass("disable")) return false;
        id = $(this).attr("data-id");
        var el = this;
        if(!$(this).hasClass("add")){
            $(el).addClass("add");
            $.ajax({
                type:"POST",
                url:"/ajax/mod-mod_basket/add-"+id,
                success:function(result){
                    $(el).addClass("add");
                    $(el).html(" Удалить " + "<span><img src=\"/img/logo/cart-white.png\" /></span>");
                    updateBasketPanel(result);
                    selectAllItems(result);
                    if($(el).hasClass("toBasket")){
                        $(el).text("В корзине");
                    }
                }});
        } else {
            $(el).removeClass("add");
            $.ajax({
                type:"POST",
                url:"/ajax/mod-mod_basket/remove-"+id,
                success:function(result){
                    $(el).removeClass("add");
                    $(el).html(" В корзину " + "<span><img src=\"/img/logo/cart-white.png\" /></span>");
                    updateBasketPanel(result);
                    selectAllItems(result);
                    if($(el).hasClass("toBasket")){
                        $(el).text("В корзину");
                    }                    
                }});            
        }
        //boundItems();

    });
    
    // удаление из корзины
	$(".removeBasketButton").on("click", function(i,e){
        var id = $(this).closest(".item").attr("data-id");
        var tr = $(this).closest("tr.item");
        $(tr).addClass("remove");
        $.ajax({
            type:"POST",
            url:"/ajax/mod-mod_basket/remove-"+id,
            success:function(result){
                $(tr).remove();
                
                // удалим все связаные карточки
                obj = JSON.parse(result);
                $.each($(".basketTable .item"), function(i,e){
                    cid = $(this).attr("data-id");
                    count = 0;
                    $.each(obj, function(i,b){
                        if(cid==i) count++;
                    });
                    
                    if(count==0) {
                        $(this).remove(); 
                    } 
                });
                calculateBasket();
                updateBasketPanel(result);                
            }});

        return false;
    });

    // редактирование колочества
	$(".basketTable .count").on("input", function(i,e){
        var id = $(this).closest(".item").attr("data-id");
        var count = $(this).val();
        $.ajax({
            type:"POST",
            url:"/ajax/mod-mod_basket/add-"+id+"/count-"+count,
            success:function(result){

            }});
        calculateBasket();
    });
    if(window.location.pathname=="/korzina") calculateBasket();
    updateBasketPanel(-1);
    calculateBasket();
    boundItems();

});
