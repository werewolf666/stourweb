/**
 *
 * Created by xuqiang on 2017/1/5.
 *
 */
(function(){

    $(".side-menu-group").on("click",function(){
        if( !$(this).hasClass("up") )
        {
            $(this).addClass("up").next(".son").addClass("hide");
        }
        else
        {
            $(this).removeClass("up").next(".son").removeClass("hide");
        }
    })

})();