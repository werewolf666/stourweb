<!doctype html>
<html>
<head head_top=XIHwOs >
    <meta charset="utf-8">
    <title>选择收货地址</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link type="text/css" rel="stylesheet" href="{$tpl}/mobilPhone3.0/css/style-new.css" />
    <script type="text/javascript" src="{$tpl}/mobilPhone3.0/js/lib-flexible.js"></script>
</head>

<body>

<div class="header_top bar-nav">
    <a class="back-link-icon" href="#pageHome"></a>
    <h1 class="page-title-bar">选择收货地址</h1>
</div>
<!-- 公用顶部 -->

<div class="addrss-container">
    <ul class="addrss-wrap">
        {loop $address $v}
        <li style="cursor: pointer" onclick="selectAddress({$v['id']},['{$v['receiver']}','{$v['phone']}','{$v['province']}{$v['city']}{$v['address']} {$v['postcode']}','{$v['is_default']}'])">
            <div class="info-bar">
                <span class="name">{$v['receiver']}</span>
                <span class="num">{$v['phone']}</span>
            </div>
            <div class="addrss-bar">
                {if $v['is_default']}<em class="label-cur">[默认地址]</em>{/if}
                <span class="show-addrss">{$v['province']}{$v['city']}{$v['address']} {$v['postcode']}</span>
            </div>
        </li>
        {/loop}
    </ul>
</div>
<!-- 选择收货地址 -->
<div class="bottom-fix-bar">
    <a class="addrss-fix-btn fix-btn" href="/phone/member/receive/update" data-reload="true">添加新地址</a>
</div>
</body>
</html>