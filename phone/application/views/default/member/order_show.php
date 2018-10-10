<!doctype html>
<html>
<head strong_margin=PoKwOs >
    <meta charset="utf-8">
    <title>订单-{$info['ordersn']}</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,order.css')}
    {Common::js('jquery.min.js,lib-flexible.js,jquery.layer.js,validate.js,layer/layer.m.js')}
</head>
<body>
{request "pub/header_new/typeid/$typeid/isordershow/1"}
    <!-- 公用顶部 -->

    <div class="order-show-item">
        <div class="order-tip-info">
            <p class="num">订单编号：{$info['ordersn']}</p>
            <p class="num">下单时间：{date('Y-m-d H:i',$info['addtime'])}</p>
            <span class="status">{$info['statusname']}</span>
        </div>
    </div>
    <!-- 订单编号、时间 -->


{if $info['refund']}
<div class="order-show-item">
    <ul class="order-link-man-info">
        <li>
            <span class="item-hd">退款方式：</span>
            <div class="item-bd">{$info['refund']['platform']}</div>
        </li>
        {if $info['refund']['alipay_account']}
        <li>
            <span class="item-hd">退款账号：</span>
            <div class="item-bd">{$info['refund']['alipay_account']}</div>
        </li>
        {/if}
        {if $info['refund']['cardholder']}
        <li>
            <span class="item-hd">持卡人：</span>
            <div class="item-bd">{$info['refund']['cardholder']}</div>
        </li>
        {/if}
        {if $info['refund']['bank']}
        <li>
            <span class="item-hd">开户行：</span>
            <div class="item-bd">{$info['refund']['bank']}</div>
        </li>
        {/if}
        {if $info['refund']['cardnum']}
        <li>
            <span class="item-hd">银行卡号：</span>
            <div class="item-bd">{$info['refund']['cardnum']}</div>
        </li>
        {/if}

        <li>
            <span class="item-hd">退款金额：</span>
            <div class="item-bd" style="color: #ff6b1a">{Currency_Tool::symbol()}{$info['refund']['refund_fee']}</div>
        </li>

        <li>
            <span class="item-hd">退款原因：</span>
            <div class="item-bd">{$info['refund']['refund_reason']}</div>
        </li>
        {if $info['refund']['description']}
        <li>
            <span class="item-hd">处理结果：</span>
            <div class="item-bd">{$info['refund']['description']}</div>
        </li>
        {/if}
    </ul>
</div>
{/if}

   {if !empty($info['eticketno']) && Product::is_app_install('stourwebcms_app_supplierverifyorder') && in_array($info['status'],array(2,5))}
    <div class="order-show-item">
        <div class="order-code-info">
            <img class="code-img" src="//{$GLOBALS['main_host']}/res/vendor/qrcode/make.php?param={$info['eticketno']}" />
            <div class="code-num">消费码：{$info['eticketno']}</div>
        </div>
    </div>
  {/if}
    <!-- 二维码 -->

    <div class="order-show-item">
        <div class="order-pdt-info">
            <a class="pdt-info-tw" href="{$product['url']}">
                <div class="pic"><img src="{Common::img($info['litpic'])}" title="" alt=""></div>
                <div class="con">
                    <p class="name">{$info['productname']}</p>
                    {if !empty($info['subname'])}
                    <p class="name">{$info['subname']}</p>
                    {/if}
                    <p class="date">{$info['usedate']}使用</p>

                </div>
            </a>
            <ul class="pdt-info-list">
                {if !empty($info['price']) && !empty($info['dingnum'])}
                <li><span class="type">{$suit['suitname']}</span><span class="data">{Currency_Tool::symbol()}{$info['price']}×{$info['dingnum']}</span></li>
                {/if}
            </ul>
            {if !empty($info['iscoupon']) || $info['usejifen']}
            <ul class="pdt-info-list">
                {if !empty($info['iscoupon'])}
                <li><span class="type">优惠券</span><span class="data">{Currency_Tool::symbol()}{$info['iscoupon']['cmoney']}</span></li>
                {/if}
                {if $info['usejifen']}
                <li><span class="type">积分抵现</span><span class="data">{Currency_Tool::symbol()}{$info['jifentprice']}</span></li>
                {/if}
            </ul>
            {/if}

            <div class="pdt-info-total">
                <span>应付总额</span>
                <span>{Currency_Tool::symbol()}{$info['actual_price']}
                {if $info['paytype']==2}
                <p class="txt">在线支付(定金){Currency_Tool::symbol()}{$info['payprice']}+到店支付{Currency_Tool::symbol()}{php}echo $info['actual_price']-$info['payprice']; {/php}</p>
                {/if}
                </span>
            </div>
        </div>
    </div>
    <!-- 产品详情 -->

    <div class="order-show-item">
        <ul class="order-link-man-info">
            <li>
                <span class="item-hd">联系人：</span>
                <div class="item-bd">{$info['linkman']}</div>
            </li>
            <li>
                <span class="item-hd">联系电话：</span>
                <div class="item-bd">{$info['linktel']}</div>
            </li>
            <li>
                <span class="item-hd">电子邮箱：</span>
                <div class="item-bd">{$info['linkemail']}</div>
            </li>
            <li>
                <span class="item-hd">其它备注：</span>
                <div class="item-bd">{$info['remark']}</div>
            </li>
        </ul>
    </div>

{st:member action="order_bill" orderid="$info['id']" return="bill"}
{if !empty($bill)}
<div class="order-show-item" id="invoice-console">
    <div class="order-user-info-bar">
        <span class="hd">发票信息</span>
        <span class="bd">有<i class="more-icon"></i></span>
    </div>
</div>
{/if}
    <div class="order-show-item">
        <div class="order-user-info-bar">
            <span class="hd">支付方式</span>
            <span class="bd">{$info['paytype_name']}</span>
        </div>
    </div>
    <!-- 支付方式 -->

{if $receiver_address}
<div class="address-info">
    <div class="tit clearfix">
        <span class="name fl">{$receiver_address['receiver']}</span>
        <span class="num fr">{$receiver_address['phone']}</span>
    </div>
    <div class="txt">
        <p>{$receiver_address['province']}{$receiver_address['city']}{$receiver_address['address']} </p>
    </div>
</div>
{/if}

    <div class="order-fix-item-bar">
        <div class="order-fix-total-bar">
            <span class="price">{if $info['paytype']==2}定金支付{else}应付总额{/if}：<em class="num">{Currency_Tool::symbol()}{$info['payprice']}</em></span>

            <span class="btn-block">
              {if $info['status']=='1'}
               <a href="javascript:;" class="btn btn-grey cancel_btn">取消订单</a>
                <a href= "{Common::get_main_host()}/payment/?ordersn={$info['ordersn']}" class="btn btn-red pay_btn">立即付款</a>
             {/if}
             {if $info['status']=='0'}
                <a href="javascript:;" class="btn btn-grey cancel_btn">取消订单</a>
             {/if}
             {if $info['status']==5 && $info['ispinlun']!=1}
               <a href="{$cmsurl}member#&{$cmsurl}member/comment/index?id={$info['id']}" class="btn btn-orange comment_btn">立即评价</a>
              {/if}
              {if $info['status']==2}
               <a href="{$cmsurl}member/order/order_refund?ordersn={$info['ordersn']}" data-ajax="false" class="btn btn-blue refund_btn">申请退款</a>
              {/if}
                 {if $info['status']==6}
               <a href="javascript:;" class="btn btn-blue refund_cancel_btn">取消退款</a>
              {/if}
            </span>
        </div>
    </div>
    <!-- 总计 -->

{if !empty($bill)}
<div id="invoice-info" class="layer-container" style="display: none;">
    <div class="layer-wrap">
        <div class="layer-tit"><strong class="bt">发票信息</strong><i class="close-ico"></i></div>
        <div class="layer-block-con">
            <div class="invoice-table">
                <p class="clearfix">
                    <label>发票明细：</label>
                    <span>旅游费</span>
                </p>
                <p class="clearfix">
                    <label>发票金额：</label>
                    <span>{Currency_Tool::symbol()}{$info['payprice']}</span>
                </p>
                <p class="clearfix">
                    <label>发票抬头： </label>
                    <span>{$bill['title']}</span>
                </p>
                <p class="clearfix">
                    <label>收&nbsp;&nbsp;件&nbsp;&nbsp;人： </label>
                    <span>{$bill['receiver']}</span>
                </p>
                <p class="clearfix">
                    <label>联系电话：</label>
                    <span>{$bill['mobile']}</span>
                </p>
                <p class="clearfix">
                    <label>收货地址：</label>
                    <span>{$bill['province']} {$bill['city']} {$bill['address']}</span>
                </p>
            </div>
        </div>
    </div>
</div>
{/if}




    <script>
        var CURRENCY_SYMBOL="{Currency_Tool::symbol()}";
        var SITEURL="{URL::site()}";
        var ordersn="{$info['ordersn']}"
        var orderid="{$info['id']}";
        $(document).ready(function(){
            //查看游客信息
            $("#visitor-console").on("click",function(){
                $("#visitor-info").show();
            });
            //关闭游客信息
            $("#visitor-info .close-ico").on("click",function(){
                $("#visitor-info").hide();
            })


            $("#invoice-console").on("click",function(){
                $("#invoice-info").show();
            });
            $("#invoice-info .close-ico,#invoice-info").on("click",function(){
                $("#invoice-info").hide();
            });

            $(".cancel_btn").click(function(){

                var url = SITEURL +'cars/member/ajax_order_cancel';
                $.layer({
                    type:3,
                    text:'确定取消订单？',
                    ok:function(){
                        $.ajax({
                            type: 'GET',
                            url: url,
                            data: {orderid:orderid},
                            dataType: 'json',
                            success: function (data) {
                                if(data.status)
                                {
                                    window.location.reload();
                                }
                                //$("#startdate_con").html(data);
                            }
                        });
                    }
                });
            });
        });


        {if $info['status']==6}
        $('.refund_cancel_btn').click(function () {

            layer.open({
                content: '您确定要取消退款吗？'
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(SITEURL+'/member/order/ajax_order_refund_back', {ordersn:ordersn}, function (result) {
                        parent.layer.open({
                            content: result.message,
                            btn: ['{__("OK")}'],
                            end:function(){
                                window.location.reload();
                            }
                        });
                    }, 'json');
                }
            });

        });
        {/if}





    </script>

</body>
</html>