
<div class="integral integral_content" id="integral" data="total:{$jifen['userjifen']},exchange:{$jifen['exchange']}">
    <p class="tl">优惠政策<span></span></p>

{if St_Functions::is_normal_app_install('coupon')}
    <p class="block-item">
                    <span class="item coupon_type">
                        <strong>优惠券</strong>
                        {if empty($couponlist)}
                            <em class="type">暂无可用优惠券</em>
                        {/if}
                    </span>
        {if !empty($couponlist)}
        <span class="more-type fr use_coupon_btn">点击选择<i class="more-ico" ></i></span>
        {/if}
    </p>
{/if}

    {if $jifen['isopen']}
    <div class="integrel-item jifen-con tline" id="integral_obj">
        <div>使用 <input type="number" class="line-txt" id="pub_needjifen" name="needjifen"/> 积分抵扣<span class="h-light">{Currency_Tool::symbol()}</span><span id="txt_jifentprice" class="h-light">0</span></div>
        <div class="ht">共{$jifen['userjifen']}积分，最多可以使用<span id="txt_jifentprice_limit"></span>积分抵扣{Currency_Tool::symbol()}<span id="txt_jifentprice_price"></span></div>
    </div>
    {/if}
</div>
{if empty($couponlist)}
<input type="hidden" value="1" id="is_empty_coupon">
{/if}

{if $jifen['isopen']}
<script>
    $('document').ready(function () {
        //积分判断
         var limit_jifen=parseInt($("#jifentprice_limit").val());
         if($("#integral_obj").length>0 && limit_jifen>0)
         {
             $("#integral_obj").show();
             var limit = $("#jifentprice_limit").val();
             var price = $("#jifentprice_price").val();
             $("#txt_jifentprice_limit").html(limit);
             $("#txt_jifentprice_price").html(price);
         }
         else
         {
             $("#integral_obj").hide();
         }

        $("#pub_needjifen").change(function(){

            jifentprice_check();
            jifentprice_update();
            if(typeof(get_total_price)=='function')
            {
                get_total_price(1);
            }
        });



    });
</script>
{/if}

<script>

    //计算积分抵现
    function jifentprice_calculate()
    {

        var  max_jifen=parseInt($("#max_useful_jifen").val());
        //var  limit_jifen=parseInt(jifentprice_limit);
        var  exchange = parseInt($("#jifentprice_exchange").val());
        if(!max_jifen||!exchange)
            return 0;
        var needjifen=parseInt($("#pub_needjifen").val());
        if(!needjifen||needjifen<=0)
            return 0;
        var price=Math.floor(needjifen/exchange);
        return price;
    }
    //刷新积分抵现
    function jifentprice_update()
    {
        var price=jifentprice_calculate();
        var needjifen=parseInt($("#pub_needjifen").val());
        $("#txt_jifentprice").text(price);

        var result=jifentprice_check(true);
        if(result)
        {
            $("#needjifen").val(needjifen);
        }else
        {
            $("#needjifen").val(0);
        }
    }

    //验证积分抵现
    function jifentprice_check(isreturn)
    {
        var  max_jifen=parseInt($("#max_useful_jifen").val());
        var needjifen=$("#pub_needjifen").val();
        needjifen =parseInt(needjifen);
        var status=false;
        if(!needjifen)
        {
            status = true;
        }
        else if(needjifen<=max_jifen)
        {
            status = true;
        }

        if(!isreturn&&!status)
        {
            layer.open({
                content: '超过抵扣上限',
                btn: ['{__("OK")}']
            });
            jifentprice_reset();
        }
        return status;

    }

    /**
     * 清除优惠券
     */
    function clear_coupon()
    {
        $('#couponid').val(0);
        var is_empty_coupon = $('#is_empty_coupon').val();
        $('.coupon-list li i').removeClass('use-label');
        $('.coupon-list li').attr('is_check',0);
        $('.use_coupon_btn').html('点击选择<i class="more-ico" ></i>');
        if(is_empty_coupon==1)
        {
            $('.coupon_type').html('<strong>优惠券</strong><em class="type">暂无可用优惠券</em>');
        }
        else
        {
            $('.coupon_type').html('<strong>优惠券</strong>');
        }

    }
    //重设积分,即积分置0
    function jifentprice_reset()
    {
        $("#pub_needjifen").val(0);
        jifentprice_update();
    }

</script>


