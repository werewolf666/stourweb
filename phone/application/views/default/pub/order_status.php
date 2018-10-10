<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>预订提示-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,status.css')}
    {Common::js('jquery.min.js,lib-flexible.js')}
</head>
<body>

    {request "pub/header_new/typeid/$typeid"}
    <!-- 公用顶部 -->

    <div class="order-status-bar">
        <img src="{$cmsurl}public/images/order-fail-status.png" />
    </div>

    <div class="order-fail-item">
        <h4 class="tit">失败可能原因：</h4>
        <ul class="txt">
            <li>1、网络不稳定或网络出错；</li>
            <li>2、订单信息填写错误；</li>
            <li>3、订单提交信息与实际不符；</li>
            <li>4、订单重复提交。</li>
        </ul>
    </div>

    <div class="bottom-fixed">
        <a class="confirm-order-btn" id="confirm-order-btn" href="{urldecode($_GET['redirect'])}">{if $_GET['booking']}重新预订{else}查看订单{/if}</a>
    </div>

</body>
</html>