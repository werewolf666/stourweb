<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>申请退款-{$info['ordersn']}</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,refund.css')}
    {Common::js('jquery.min.js,lib-flexible.js,jquery.layer.js,validate.js,layer/layer.m.js')}
</head>

<body>

<div class="header_top bar-nav">
    <a class="back-link-icon" href="javascript:;"></a>
    <h1 class="page-title-bar">申请退款</h1>
</div>
<!-- 公用顶部 -->
<form id="frm">
    <div class="page-content">
        <div class="return-show-item">
            <p class="msg-reason">
                <strong class="item-hd">退款原因：</strong>
                <textarea class="reason" name="refund_reason" placeholder="请输入退款原因，不少于5个字"></textarea>
            </p>
        </div>
        <div class="return-show-item">
            <p class="msg">
                <strong class="item-hd">退款金额：</strong>
                <span class="price">{Currency_Tool::symbol()}{$info['payprice']}</span>
            </p>
        </div>
        {if $info['refund_auto']!=1}
        <div class="return-show-item">
            <p class="msg clearfix">
                <strong class="item-hd">退款方式：</strong>
                <span class="type">
                    <em class="active" data-id="alipay"><i class="ico"></i>支付宝</em>
                    <em data-id="bank"><i class="ico" ></i>银行卡</em>
                </span>
            </p>
            <div class="pay-group">
                <p class="msg" data-id="alipay">
                <strong class="item-hd">支付宝账号：</strong>
                <input type="text" name="alipay_account" placeholder="请输入支付宝账号" />
                </p>
                <p class="msg" data-id="bank" style="display: none">
                    <strong class="item-hd">&emsp;持卡人：</strong>
                    <input type="text" name="cardholder" placeholder="请输入持卡人姓名" />
                </p>
                <p class="msg" data-id="bank" style="display: none">
                    <strong class="item-hd">银行卡号：</strong>
                    <input type="text" name="cardnum" placeholder="请输入银行卡号" />
                </p>
                <p class="msg" data-id="bank" style="display: none">
                    <strong class="item-hd">开户银行：</strong>
                    <input type="text" name="bank" placeholder="请输入开户银行" />
                </p>
            </div>
        </div>
        <div class="return-show-item">
            <p class="msg clearfix">
                <strong class="item-hd">退款说明：</strong>
                <span class="text">您使用的是银行卡或者其他支付方式（除微信、支付宝）进行的付款，因此需要您选择退款方式，申请退款成功后，退款金额会退回到您填写的账号中，并返还购买使用的积分、优惠券至会员账户。</span>
            </p>
        </div>
        {else}
        <div class="return-show-item">
            <p class="msg clearfix">
                <strong class="item-hd">退款方式：</strong>
                <span class="text">{if $info['pay_online_source']=='wxpay'}微信{elseif $info['pay_online_source']=='alipay'}支付宝{/if}</span>
            </p>
        </div>
        <div class="return-show-item">
            <p class="msg clearfix">
                <strong class="item-hd">退款说明：</strong>
                <span class="text">您使用微信或支付宝进行的付款，申请退款成功后，退款金额会按照原路退回，并返还购买使用的积分、优惠券至会员账户。</span>
            </p>
        </div>
        {/if}
    </div>
    <input name="platform" value="alipay" type="hidden">
    <input type="hidden" name="ordersn" value="{$info['ordersn']}">
    <input type="hidden" name="refund_auto" value="{$info['refund_auto']}">
</form>
<div class="footer">
    <a href="javascript:;" class="sub-btn">提交</a>
</div>



<script type="text/javascript">
    $("body,html").css("height","100%");
    $(function () {
        //切换方式
        $('.type em').click(function () {
            var platform = $(this).attr('data-id');
            $('input[name=platform]').val(platform);
            $('.type em').removeClass('active');
            $(this).addClass('active');
            $('.pay-group .msg').hide();
            $('.pay-group .msg[data-id='+platform+']').show();
        });

        $('.sub-btn').click(function () {

            if(!check_refund_frm())
            {

                return false;
            }
            var data = $(frm).serialize();
            refund_status(data);
        })
    });


    {if $info['status']==2}
    /**
     *
     * @param frm_data 表单验证
     */
    function check_refund_frm()
    {
        var refund_reason = $('#frm').find('textarea[name=refund_reason]').val();
        if(refund_reason.replace(/(^\s*)|(\s*$)/g, "").length<5)
        {
            layer.open( {content: '退款原因不能少于五个字'})
            return false;
        }
        var refund_auto = $('#frm').find('input[name=refund_auto]').val();
        var platform = $('#frm').find('input[name=platform]').val();
        if(refund_auto!=1)
        {
            if(platform=='alipay')
            {
                var alipay_account = $('#frm').find('input[name=alipay_account]').val();
                if(alipay_account.replace(/(^\s*)|(\s*$)/g, "").length<5)
                {
                    layer.open( {content: '请填写正确的支付宝账号'})
                    return false;
                }
            }
            else if(platform=='bank')
            {
                var cardholder = $('#frm').find('input[name=cardholder]').val();
                var cardnum = $('#frm').find('input[name=cardnum]').val();
                var bank = $('#frm').find('input[name=bank]').val();
                if(cardholder.length<1||cardnum.length<10||bank.length<2)
                {
                    layer.open( {content: '请填写正确的银行卡信息'})
                    return false;
                }
            }
        }
        return true;
    }
    function refund_status(data) {
        $.post('{$cmsurl}member/order/ajax_order_refund', data, function (result) {
            parent.layer.open({
                content: result.message,
                btn: ['{__("OK")}'],
                end:function(){
                    window.location = '{$cmsurl}member/order/show?id={$info['id']}';
                }
            });
        }, 'json');
    }
    {/if}

</script>

</body>
</html>