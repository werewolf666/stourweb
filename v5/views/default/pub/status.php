<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$status['title']}-{$webname}</title>
    {Common::css("base.css")}
    {Common::js("jquery.min.js,base.js,common.js")}
    <link href="/payment/public/css/pay.css" rel="stylesheet" media="screen" type="text/css"/>
</head>
<body>
{request 'pub/header'}
<div class="big">
    <div class="wm-1200">
        <div class="st-guide">
            <a href="{$status['indexurl']}">首页</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{$status['title']}
        </div>
        <!--面包屑-->
        <div class="st-main-page">
            {if $status['result']}
            <div class="st-payment-ts">
                <div class="payment-success-box">
                    <div class="success-con">
                        <h3>{$status['msgtitle']}</h3>
                        <ul>
                            {loop $status['msg'] $v}
                            <li><span>{$v[0]}：</span>{$v[1]}</li>
                            {/loop}
                        </ul>
                        <div class="btn-box">
                            <a class="back-home-btn" href="{$status['indexurl']}">返回首页</a>
                            <a class="check-order-btn" href="{$status['referurl']}">查看订单</a>
                        </div>
                    </div>
                </div>
            </div>
            {else}
            <div class="st-payment-ts">
                <div class="payment-lose-box">
                    <div class="lose-con">
                        <h3>{$status['msgtitle']}</h3>

                        <div class="txt">{$status['msg']}</div>
                        <div class="btn-box">
                            <a class="again-pay-btn" href="{$status['indexurl']}">返回首页</a>
                            <a class="check-order-btn" href="{$status['referurl']}">上一页</a>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>
<!-- footer -->
{request "pub/footer"}
<!-- footer end -->
</body>
</html>