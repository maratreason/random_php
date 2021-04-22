    /*JED ITEM SLIDER*/
    /* адаптивная карусель продуктов*/

$(document).ready(function () {
    $(".jed-item-slider").each(function(j,slider){
        maxList = $(slider).find(".lists .list").length; 
        $(slider).attr("id","jed-item-slider-"+j)
        $(slider).find(".item").each(function(i,e){
            preview = $(e).find("img").attr("src");
            $(e).find("img").remove();
            $(e).find(".image").css("background-image", "url("+preview+")");
        });
        width = $(slider).find(".slide-area").css("width");
        $(slider).find(".lists .list").each(function(i,e){
            $(e).attr("id","list-"+j+"-"+i);
            $(e).css("width",width);
        });
        $(slider).find(".slide-area .lists").css("width", width.slice(0,-2)*maxList);

        for(i=0;i<maxList;i++){
            if(i==0) active = 'active'; else active = "";
            $(slider).find(".pointers").append('<a href="javascript:{}" id="item-point-'+j+'-'+i+'" class="selectpoint '+active+'"><span></span></a>');
            // select current
            $("#item-point-"+j+"-"+i).attr("sid",i);
            $(slider).attr("sid",0);
            $("#item-point-"+j+"-"+i).bind("click", function (){
                sid = $(this).attr("sid");
                w = $(slider).find(".slide-area").css("width");
                $(slider).find(".lists").animate({"margin-left":-sid*w.slice(0,-2)}, 700);
                $(slider).find(".selectpoint").removeClass("active");
                $(slider).find("#item-point-"+j+"-"+sid).addClass("active");
                $(slider).attr("sid",sid);
            });
        }
        
                    // mobile
             $(slider).swipe({
                swipeLeft:function(event, direction, distance, duration, fingerCount, fingerData) {
                    maxList = $(slider).find(".lists .list").length; 
                    sid = $(slider).attr("sid");                    
                    if(direction=="left"&&sid<maxList-1){
                        sid++;
                    }
                    if(direction=="right"&&sid>0){
                        sid--;
                    }                    
                        
                        w = $(slider).find(".slide-area").css("width");
                        $(slider).find(".lists").animate({"margin-left":-sid*w.slice(0,-2)}, 700);
                        $(slider).find(".selectpoint").removeClass("active");
                        $(slider).find("#item-point-"+j+"-"+sid).addClass("active");
                        $(slider).attr("sid",sid);
                    
                },
                swipeRight:function(event, direction, distance, duration, fingerCount, fingerData) {
                    maxList = $(slider).find(".lists .list").length; 
                    sid = $(slider).attr("sid");                    
                    if(direction=="left"&&sid<maxList-1){
                        sid++;
                    }
                    if(direction=="right"&&sid>0){
                        sid--;
                    }                    
                        
                        w = $(slider).find(".slide-area").css("width");
                        $(slider).find(".lists").animate({"margin-left":-sid*w.slice(0,-2)}, 700);
                        $(slider).find(".selectpoint").removeClass("active");
                        $(slider).find("#item-point-"+j+"-"+sid).addClass("active");
                        $(slider).attr("sid",sid);
                    
                }             
             });
    });
    
    // адптируем ширину под все разрешения при изменении размера окна
    $(window).resize(function(){
        $(".jed-item-slider").each(function(j,slider){
            maxList = $(slider).find(".lists .list").length;
            width = $(slider).find(".slide-area").css("width");
            sid = $(slider).attr("sid");
            $(slider).find(".lists .list").each(function(i,e){
                $(e).css("width",width);
            });  
            $(slider).find(".slide-area .lists").css("width", width.slice(0,-2)*maxList);
            $(slider).find(".lists").animate({"margin-left":-sid*width.slice(0,-2)}, 0);
        });
    });
});