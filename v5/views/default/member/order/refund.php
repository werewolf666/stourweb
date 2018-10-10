<?php defined('SYSPATH') or die();?>
{template "pub/varname"}
{Common::css('base.css,user_new.css')}
{Common::load_skin()}
{Common::js('jquery.min.js,base.js')}
{Common::js('layer/layer.js',0)}

<form id="refund_frm">
<div id="apply-refund" class="apply-refund layui-layer-wrap">
    <ul class="info-item-block">
        <li>
            <span class="item-hd">退款原因：</span>
            <div class="item-bd">
                <textarea class="back-area" name="refund_reason" placeholder="请填写退款原因，不少于5个字"></textarea>
            </div>
        </li>
        <li>
            <span class="item-hd">退款金额：</span>
            <div class="item-bd">
                <span class="price">{Currency_Tool::symbol()}{$info['payprice']}</span>
            </div>
        </li>
        {if $info['refund_auto']!=1}
        <li>
            <span class="item-hd">退款方式：</span>
            <div class="item-bd">
                <label id="alipayWay" class="pay-label"><input type="radio" name="platform" value="alipay" class="item-rd" checked="">支付宝</label>
                <label id="bankWay" class="pay-label"><input type="radio" name="platform" value="bank" class="item-rd">银行卡</label>
            </div>
        </li>
        <li class="alipay-account">
            <span class="item-hd">支付宝账号：</span>
            <div class="item-bd">
                <input type="text"  name="alipay_account" class="default-text w-full" placeholder="请输入支付宝账号">
            </div>
        </li>
        <li class="bank-account">
            <span class="item-hd">持卡人：</span>
            <div class="item-bd">
                <input type="text" name="cardholder" class="default-text w-full" placeholder="请输入持卡人姓名">
            </div>
        </li>
        <li class="bank-account">
            <span class="item-hd">银行卡号：</span>
            <div class="item-bd">
                <input type="text"  name="cardnum" class="default-text w-full" placeholder="请输入银行卡号">
            </div>
        </li>
        <li class="bank-account">
            <span class="item-hd">开户银行：</span>
            <div class="item-bd">
                <input type="text" name="bank" class="default-text w-full" placeholder="请输入开户行名称">
            </div>
        </li>

        <li>
            <span class="item-hd">退款说明：</span>
            <div class="item-bd">
                <div class="item-section mt4 c-666">您使用的是银行卡或者其他支付方式（除微信、支付宝）进行的付款，因此需要您选择退款方式，申请退款成功后，退款金额会退回到您填写的账号中，并返还购买使用的积分、优惠券至会员账户。</div>
            </div>
        </li>
        {else}
        <li>
            <span class="item-hd">退款方式：</span>
            <div class="item-bd">
                <span class="item-text">{if $info['pay_online_source']=='wxpay'}微信{elseif $info['pay_online_source']=='alipay'}支付宝{/if}</span>
            </div>
        </li>
        <li>
            <span class="item-hd">退款说明：</span>
            <div class="item-bd">
                <div class="item-section mt4 c-666">您使用微信或支付宝进行的付款，申请退款成功后，退款金额会按照原路退回，并返还购买使用的积分、优惠券至会员账户。</div>
            </div>
        </li>
        {/if}

    </ul>
</div>
    <input type="hidden" name="ordersn" value="{$info['ordersn']}">
    <input type="hidden" name="refund_auto" value="{$info['refund_auto']}">
</form>
<script>

    $(function () {

        $("#alipayWay").on("click",function(){
            $(".alipay-account").show();
            $(".bank-account").hide()
        });

        $("#bankWay").on("click",function(){
            $(".bank-account").show();
            $(".alipay-account").hide()
        })

    })



</script>