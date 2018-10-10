<!doctype html>
<html>
<head font_size=z0hy5k >
    <meta charset="utf-8">
    <title>我的应用-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,mall.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,slideTabs.js"); }
    <script>
        $(function(){


            $('.plug-tab-display').switchTab({
                titCell:  ".plug-tabnav span",
                mainCell: ".plug-tabcon"
            });

        })
    </script>
</head>
<body style="overflow:hidden" class="mall">
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">
            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tr>
                            <td class="pro-search head-td-lt">
                            </td>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="content-block app-info-content">
                <div class="plug-msg-con">
                    <div class="pic"><img src="{$info['litpic']}" alt="{$info['name']}" /></div>
                    <div class="bt">{$info['name']}</div>
                    <div class="jg">&yen;{$info['price']}</div>
                    <ul class="attr-list">
                        {if !empty($info['integralPrice'])}
                        <li>积分可抵现 &yen {$info['integralPrice']} 元 </li>
                        {/if}
                        <li>应用ID：{$info['nId']}</li>
                        <li>版本：{$info['version']}</li>
                        <li>需要后台版本：{$info['backstageVersion']}</li>
                        <li>作者：{$info['author']}</li>
                        <li>团队成员：{$info['team']}</li>
                        <li>贡献者：{$info['contributor']}</li>
                        <li>活跃安装：{$info['num']}</li>
                        <li>最近更新：{$info['modtime']}</li>
                        <li>分类标签：{$info['tag']}</li>
                    </ul>
                </div>
                <div class="plug-tab-display">
                    <div class="plug-tabnav">
                        <span>应用描述</span>
                        <span>界面展示</span>
                        <span>修订历史</span>
                        <span>帮助说明</span>
                        <span>其他备注</span>
                    </div>
                    <div class="plug-tabcon">
                        {$info['applicationDescription']}
                    </div>
                    <div class="plug-tabcon">
                        {$info['interfacesPic']}
                    </div>
                    <div class="plug-tabcon">
                        {$info['revise']}
                    </div>
                    <div class="plug-tabcon">
                        {$info['commonProblem']}
                    </div>
                    <div class="plug-tabcon">
                        {$info['otherRemarks']}
                    </div>
                </div>
            </div>

        </td>
    </tr>
</table>
</body>
<script>
    $(window).resize(function(){
        sizeHeight()
    })
    function sizeHeight()
    {
        var pmHeight = $(window).height();
        var gdHeight = 50;
        $('.content-block').height(pmHeight-gdHeight);
    }
    sizeHeight();
</script>
</html>