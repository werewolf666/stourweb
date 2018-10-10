{php}
$marketPlan=Model_Jifen_Price::get_used_jifentprice($info['jifentprice_id'],$typeid);
$canUseJifen=$marketPlan && $userinfo['jifen'] > $marketPlan["cfg_exchange_jifen"];
$topLimit=$marketPlan['jifentprice'] * $marketPlan["cfg_exchange_jifen"];
{/php}
<div class="booking-info-block clearfix" id="discountNode">
    <h3 class="block-tit-bar"><strong>优惠政策</strong></h3>
    <div class="block-item">
        <ul>
            {if $canUseJifen}
            <li>
                <div>
                    <p class="jf-use">使用<input type="text" name="needjifen" class="jf-num" id="useIntegral" value="0"/>积分，抵现<i class="toPrice">0</i></p>
                    <p class="jf-txt">共{$userinfo['jifen']}积分，最多可使用{$topLimit}积分抵扣{Currency_Tool::symbol()}{$marketPlan['jifentprice']}</p>
                </div>
            </li>
            {/if}
            {if St_Functions::is_normal_app_install('coupon')}
            <li>
                <a class="all" href="{$cmsurl}coupon/box_new-{$typeid}-{$info['id']}?template=convention">
                <span class="item coupon_type">
                    <strong>优惠券</strong>
                    {if empty($couponlist)}<em class="type">暂无可用优惠券</em>{/if}
                </span>
                <span class="more-type"><i class="more-ico"></i></span>
                </a>
            </li>
            <script>
                $(function () {
                    //订阅优惠信息更新
                    queue.on('discountInfo', function () {
                        var html;
                        if (window.coupon && window.coupon.id>0) {
                            bookData.extParam.couponid=window.coupon.id;
                            html = '<strong>优惠券</strong><em class="type">' + window.coupon.title + '</em>';
                        }
                        else {
                            bookData.extParam.couponid=0;
                            html = '<strong>优惠券</strong>';
                        }
                        $('.coupon_type').html(html);
                    });
                });
            </script>
            {/if}
        </ul>
    </div>
</div>
<!--选择优惠券-->

{if $canUseJifen}
<script>
//营销策略
var integral={
    topLimit:{$topLimit}, //上限分值
    unitPrice:{$marketPlan["cfg_exchange_jifen"]} //单元货币对应积分
};
$(function(){
    //积分抵现默认值
    bookData.toPrice=0;
    $('#useIntegral').change(function(){
        var value = parseInt($(this).val());
        var mxListNode=$('.mx-list');
        mxListNode.find('.integralNode').remove();
        //非整数
        if (isNaN(value)) {
            value = 0;
        }
        //防止输入负数
        value = Math.abs(value);
        if (value > integral.topLimit) {
            value = integral.topLimit;
        }
        //整倍数积分抵现
         var toPrice=Math.floor(value/integral.unitPrice);
        //积分有效性检测
        if(bookData.checkCanUse('toPrice') >= toPrice){
            value=toPrice*integral.unitPrice;
            bookData.discount.toPrice=toPrice;
            mxListNode.append('<li class="integralNode"><strong>积分抵现</strong><em>'+currency+toPrice+'</em></li>');
        }
        else
        {
            $.layer({type:1, icon:2,time:1000, text:'积分抵现超过产品总价，请重新填写积分'});
            value=0;
            toPrice=0;
            bookData.discount.toPrice=0;
        }

        $('.toPrice').text(currency+toPrice);
        $(this).val(value);
        //触发总价
        queue.emit('totalPrice');
    });
    //营销策略重置
    queue.on('resetIntegral',function(){
        //积分抵现
        bookData.discount.toPrice=0;
        $('#useIntegral').val(0);
        $('.toPrice').text(currency+bookData.discount.toPrice);
    });
    queue.on('resetDiscount',function(){
        queue.emit('resetIntegral');
    });
});
</script>
{/if}