<!DOCTYPE html>
<html lang="en">
<head head_left=HgFwOs >
    <meta charset="UTF-8">
    <title>微信安全支付</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="/payment/public/css/base.css" rel="stylesheet" media="screen" type="text/css" />
    <link href="/payment/public/css/wxpay.css" rel="stylesheet" media="screen" type="text/css" />
    <script type="text/javascript" src="/payment/public/js/lib-flexible.js"></script>
</head>
<body>

    <div class="pay-body">
		<div class="pay-block">
			<span class="txt">应付金额</span>
			<span class="price">&yen;<?php echo $total_fee;?></span>
		</div>
		<ul class="info-order-block">
			<li>
				<div class="item-hd">订单编号：</div>
				<div class="item-bd"><?php echo $ordersn;?></div>
			</li>
			<li>
				<div class="item-hd">下单时间：</div>
				<div class="item-bd"><?php echo $addtime;?></div>
			</li>
			<li>
				<div class="item-hd">产品名称：</div>
				<div class="item-bd"><?php echo $productname;?></div>
			</li>
		</ul>
    </div>

    <div class="fix-bottom-bar">
        <a href="javascript:;" class="wx-pay-btn" onclick="callpay()">立即支付</a>
    </div>

    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall()
        {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $parameter; ?>,
                function(res){
                    if(res.err_msg == "get_brand_wcpay_request:ok"){
                        window.location.href="/payment/status/index/?<?php echo 'sign='.md5('11').'&ordersn='.$ordersn;?>";
                    }else{
                        //返回跳转到订单详情页面
                        window.location.href="/payment/status/?<?php echo 'sign='.md5('00');?>";
                    }
                }
            );
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
</body>
</html>