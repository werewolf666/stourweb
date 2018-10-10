<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>操作指引-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,base_new.css'); }
    {php echo Common::css_plugin('guide.css','howtouse'); }

</head>
<body style="overflow:hidden" class="mall">
<table class="content-tab" margin_border=zab5Rj >
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            <!--左侧导航区-->
            <div class="menu-left">
                <div class="global_nav">
                    <div class="kj_tit">新手教程</div>
                </div>
                <div class="nav-tab-a leftnav">
                    <a href="javascript:;" class='active' >新手教程</a>
                </div>
            </div>
            <script>
                $(document).ready(function (e) {
                    //导航点击
                    $(".leftnav").find('a').click(function () {
                        var url = $(this).attr('data-url');
                        if (typeof(url) == 'undefined') {
                            return;
                        }
                        var data_title=$(this).attr('data_title');
                        var title = typeof(data_title)=='undefined'?$(this).html():data_title;
                        ST.Util.addTab(title, url);
                    })
                })
            </script>
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">
            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tr>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="btn btn-primary radius" onclick="window.location.reload()">刷新</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="novice-guide-page">
                <div class="novice-guide-box">
                    <h3 class="tit">欢迎使用思途CMS</h3>
                    <p class="txt">通过新手教程，助您快速上手，使用本系统！</p>
                </div>
                <div class="novice-guide-container">
                    <ul class="novice-guide-wrapper">
                        <li>
                            <a class="item" href="javascript:ST.Util.addTab('首页设置', '{$cmsurl}config/base/menuid/{$step1_menu['id']}');">
                                <i class="num-icon">1</i>
                                <div class="info">
                                    <h4 class="tit">站点设置</h4>
                                    <p class="txt">定位网站、命名站点、首页优化设置。</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="item" href="javascript:ST.Util.addTab('全局目的地', '{$cmsurl}destination/destination/menuid/{$step2_menu['id']}');">
                                <i class="num-icon">2</i>
                                <div class="info">
                                    <h4 class="tit">全局目的地设置</h4>
                                    <p class="txt">线路、酒店、景点等产品都有具体的目的，设置后实现产品和信息关联；全局目的地设置，将为信息建立清晰的树型关系，利于站点优化。</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="item" href="javascript:ST.Util.addTab('全局出发地', '{$cmsurl}startplace/index/menuid/{$step3_menu['id']}');">
                                <i class="num-icon">3</i>
                                <div class="info">
                                    <h4 class="tit">出发地设置</h4>
                                    <p class="txt">线路、租车等通常会有不同的出发地，设置后方便用户对产品的选择。同时，出发地设置，可实现多城市业务开展的需要。</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="item" href="javascript:ST.Util.addTab('支付接口', '{$cmsurl}payset/alipay/menuid/{$step4_menu['id']}');">
                                <i class="num-icon">4</i>
                                <div class="info">
                                    <h4 class="tit">支付接口设置</h4>
                                    <p class="txt">选择和绑定您的支付接口，实现在线收款。</p>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="item" href="javascript:ST.Util.addTab('短信接口', '{$cmsurl}sms/index/menuid/{$step5_menu['id']}');">
                                <i class="num-icon">5</i>
                                <div class="info">
                                    <h4 class="tit">短信接口设置</h4>
                                    <p class="txt">
                                        开启短信接口，便于获取访客联系方式，将注册、购买成败等信息发给用户。因此，你需要开启短信接口，编写短信内容，并购买短信数量。添加产品，设定好价格后，访客就可以注册、下单、购买您的产品了！
                                    </p>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
    </tr>
</table>
</body>

</html>