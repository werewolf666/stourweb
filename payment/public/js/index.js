$('document').ready(function(){
    //初始化
    if($('.payment-line').find('li.active')){
        $('#st-payment-submit').removeClass('error');
    }
    //选择支付方式
    $('.payment-on').find('li').click(function(){
        $('.payment-on').find('li').removeClass('active');
        $(this).addClass('active');
        $('#st-payment-submit').removeClass('error');
    });
    $('#st-payment-submit').click(function(){
        var selectedLi=$('.payment-on').find('li.active');
        var len=selectedLi.length;
        if(len!=1){
            return;
        }
        param.method=selectedLi.attr('data');
        $url=new Array();
        for(key in param){
            $url.push(key+'='+param[key]);
        }
        $('#st-payment-back-box').css('display','block');
        var payurl = selectedLi.attr('data-payurl');
        if(payurl!=''){
            payurl = payurl +'?'+ $url.join('&');
        }else{
            payurl = " /payment/index/confirm?"+$url.join('&');
        }
        window.open(payurl);
    });
    //支付失败
    $('.close-button').click(function(){
        $('#st-payment-back-box').css('display','none');
    });
    //支付成功
    /*$('#st-payment-back-success').click(function(){
        $.post('/payment/index/ajax_ispay',param,function(url){
            if(url!=0){
                  window.location.href=url;
            }else{
                $('#st-payment-back-box').css('display','none');
            }
        })
    });*/

});

