<!doctype html>
<html>
<head div_clear=zC0-0l >
    <meta charset="utf-8">
    <title><?php echo $info['title'];?>-<?php echo $info['cfg_webname'];?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link type="text/css" href="/phone/public/css/amazeui.css" rel="stylesheet" />
    <link type="text/css" href="/phone/public/css/style.css" rel="stylesheet" />
    <link type="text/css" href="/phone/public/css/extend.css" rel="stylesheet" />
    <script type="text/javascript" src="/phone/public/js/jquery.min.js"></script>
    <script type="text/javascript" src="/phone/public/js/amazeui.js"></script>
</head>
<body>
<div class="mid_content">
    <div class="success_page">
        <div class="suc_box">
            <?php switch ($info['sign']): ?><?php case '11': ?>
            <?php case '12': ?>
                <div class="success_pic"><img src="/phone/public/images/success.png"/></div>
                <?php break; ?>
            <?php default: ?>
                <div class="success_pic"><img src="/phone/public/images/error.png"/></div>
            <?php endswitch; ?>
            <p><?php echo $info['msg']; ?></p>
            <a class="back" href="/phone/">首页</a>&nbsp;<a class="back" href="/member#&myOrder">订单中心</a>
        </div>
    </div>
</div>
</body>
</html>
