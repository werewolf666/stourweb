<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单提交失败-{$webname}</title>
    {Common::css('base.css,extend.css')}
    {include "pub/varname"}
    {Common::js('jquery.min.js,base.js,common.js')}

</head>

<body>

{request "pub/header"}



<div class="big">
    <div class="wm-1200">
        <div class="st-main-page mt20">
            <div class="st-payment-ts">
                <div class="payment-lose-box">
                    <div class="lose-con">
                        <h3>订单提交失败，请重新提交</h3>
                        <div class="txt">导致失败的原因可能是：1.网络不稳定或网络出错；2.订单<br>信息填写错误；3.订单提交信息与实际不符。</div>
                        <div class="btn-box">
                            <a class="again-pay-btn" href="{$GLOBALS['cfg_basehost']}">返回首页</a>
                            <a class="check-order-btn" href="{$referurl}">返回上一页</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{request "pub/footer"}

</body>
</html>
