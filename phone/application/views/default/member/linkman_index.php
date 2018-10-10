<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>常用联系人-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js,common.js');}
    <script>
        $(function () {
            $('#my-st-slide').offCanvas('close');
        })
    </script>
</head>

<body>
{request "pub/header/typeid/$typeid/islinkman/1"}

<section>
    <div class="mid_content">
        <div class="linkman_page">
            <h3 class="tit"><a href="{$cmsurl}member/linkman/update?action=add">+ 添加常用联系人</a></h3>
            <ul class="linkman_list">
                {loop $data $v}
                <li>
                    <a href="{$v['url']}">
                    <strong>{$v['linkman']}</strong>
                    <strong>性别：{if $v['sex']==1}男{else}女{/if}</strong>
                    <span>手机号码：{$v['mobile']}</span>
                    <span>身份证号：{$v['idcard']}</span>
                    </a>
                </li>
                {/loop}
            </ul>
        </div>
        <!--选择联系人-->
    </div>
</section>

</body>
</html>
