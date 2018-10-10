/**
 * Created by Administrator on 15-11-11.
 */



(function($){

    var Order ={

        cancel:function(datetime){
            var startTime = new Date(datetime);

            var EndTime=startTime.getTime();

            var d=new Date();

            var timer_rt = window.setInterval(function(){
                var NowTime = new Date();
                var nMS = window.parseInt(EndTime)-window.parseInt(NowTime.getTime());
                var nD = Math.floor(nMS/(1000 * 60 * 60 * 24));
                var nH = Math.floor(nMS/(1000*60*60)) % 24;
                var nM = Math.floor(nMS/(1000*60)) % 60;
                var nS = Math.floor(nMS/1000) % 60;
                if (nMS < 0){
                    $(".dao").hide();
                    $(".daoend").show();
                }else{
                    $(".dao").show();
                    $(".daoend").hide();
                    $(".RemainD").text(nD);
                    $(".RemainH").text(nH);
                    $(".RemainM").text(nM);
                    $(".RemainS").text(nS);
                }
            }, 1000);
        }


    }
    window.Order = Order;
})(jQuery)


