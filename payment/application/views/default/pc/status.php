<link href="/payment/public/css/pay.css" rel="stylesheet" media="screen" type="text/css" />
<div class="big">
    <div class="wm-1200">
        <div class="st-guide">
            <a href="/">首页</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;订单状态
        </div>
        <!--面包屑-->
        <div class="st-main-page">
            <?php switch ($info['sign']): ?><?php case '11': ?>
                <?php case '13':?>
                <!-- 支付、预订成功提示 -->
                <div class="st-payment-ts">
                    <div class="payment-success-box">
                        <div class="success-con">
                            <h3><?php echo $info['title'];?>！</h3>
                            <ul>
                                <li><span>订单号：</span><?php echo $info['ordersn']; ?></li>
                                <li><span>产品名称：</span><a href="<?php echo $info['show']; ?>"><?php echo $info['productname']; ?></a></li>
                                <li><span>支付金额：</span><em>&yen;<?php echo number_format($info['total'], 2); ?></em></li>
                            </ul>
                            <div class="btn-box">
                                <a class="back-home-btn" href="/">返回首页</a>
                                <a class="check-order-btn"
                                   href="/member/order/view?ordersn=<?php echo $info['ordersn']; ?>">查看订单</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php break; ?>
            <?php case '00': ?>
            <?php case '01': ?>
                <!-- 支付错误提示 -->
                <div class="st-payment-ts">
                    <div class="payment-lose-box">
                        <div class="lose-con">
                            <h3>支付失败！</h3>
                            <div class="txt"><?php echo $info['msg']; ?></div>
                            <div class="btn-box">
                                <a class="again-pay-btn" href="/payment/?ordersn=<?php echo $info['ordersn']; ?>">重新支付</a>
                                <a class="check-order-btn" href="/member/order/view?ordersn=<?php echo $info['ordersn']; ?>">查看订单</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 信息提示 -->
            <?php break; ?>
            <?php default: ?>
                <div class="st-payment-ts">
                    <div class="payment-success-box">
                        <div class="success-con">
                            <h3><?php echo $info['title']; ?></h3>
                            <div class="txt"><?php echo $info['msg']; ?></div>
                            <div class="btn-box">
                                <a class="back-home-btn" href="/">返回首页</a>
                                <a class="check-order-btn"
                                   href="/member/order/view?ordersn=<?php echo $info['ordersn']; ?>">查看订单</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endswitch; ?>
        </div>
    </div>
</div>