<div class="product-msg">
    <h3 class="pm-tit"><strong class="ico07">支付方式</strong></h3>
    <div class="pay-way">
        {php $paytypeArr=explode(',',$GLOBALS['cfg_pay_type']);}
        {if in_array(1,$paytypeArr)}
        <span data-paytype="1"><img src="{$GLOBALS['cfg_public_url']}images/pay_zfb.gif" /></span>
        {/if}
        {if in_array(2,$paytypeArr)}
        <span data-paytype="2"><img src="{$GLOBALS['cfg_public_url']}images/pay_kq.gif" /></span>
        {/if}
        {if in_array(3,$paytypeArr)}
        <span data-paytype="3"><img src="{$GLOBALS['cfg_public_url']}images/pay_hc.gif" /></span>
        {/if}
        {if in_array(4,$paytypeArr)}
        <span data-paytype="4"><img src="{$GLOBALS['cfg_public_url']}images/pay_yl.gif" /></span>
        {/if}
        {if in_array(5,$paytypeArr)}
        <span data-paytype="5"><img src="{$GLOBALS['cfg_public_url']}images/pay_qb.gif" /></span>
        {/if}
        {if in_array(7,$paytypeArr)}
        <span data-paytype="6"><img src="{$GLOBALS['cfg_public_url']}images/pay_bb.gif" /></span>
        {/if}
        {if in_array(8,$paytypeArr)}
        <span data-paytype="8"><img src="{$GLOBALS['cfg_public_url']}images/pay_wx.gif" /></span>
        {/if}

        {if in_array(6,$paytypeArr)}
         <span data-paytype="6"><img src="{$GLOBALS['cfg_public_url']}images/pay_xianxia.gif"></span>
        {/if}
        <input type="hidden" id="paytype" name="paytype" value="0"/>
    </div>
    <div class="st-linedown-text" style="display: none">
        <div class="tit">该产品支持线下付款，<span>查看线下支付说明</span></div>
        <div class="con" style="display: none">
           {$GLOBALS['cfg_pay_xianxia']}
        </div>
    </div>
</div><!--支付方式-->
<script>
    $(function(){
        $('.pay-way').find('span').click(function(){
            var paytype = $(this).attr('data-paytype');
            $('#paytype').val(paytype);
            $(this).addClass('on').siblings().removeClass('on');
            if(paytype==6){
                $('.st-linedown-text').show();
            }else{
                $('.st-linedown-text').hide();
            }
        })
        $('.pay-way').find('span').first().trigger('click');
        //线下付款详情显示
        $(".st-linedown-text .tit span").click(function(){
            $(this).toggleClass('on');
            $('.st-linedown-text .con').toggle();
        })
    })
</script>
