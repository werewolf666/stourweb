<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    {Common::getScript('jquery-1.8.3.min.js,common.js')}
    {Common::getCss('base_new.css')}
</head>
<body style="width:520px; height: 130px;" color_div=tyvz8B >

    <div class="text-c lh-24">
        <p>请及时绑定，以获得思途CMS终身免费升级服务！</p>
        <p>授权后您将获得：免费系统升级、短信通知功能、官方帮助系统、工单反馈系统等更多增值服务</p>
        <p>思途CMS每周四更新，发布全新功能、页面及安全修护等，让您的网站永不过时。</p>
    </div>
    <div class="clearfix text-c mt-30 f-0">
        <a class="btn btn-grey-outline radius" href="http://www.stourweb.com/cms/goumai" target="_blank">获取授权</a>
        <a class="btn btn-primary radius ml-20" id="btn_bind" href="javascript:;">立即绑定</a>
    </div>

<script>
    $(function(){
        //绑定授权
        $("#btn_bind").click(function () {

            ST.Util.addTab('授权管理', 'config/authright/menuid/191');
            ST.Util.closeBox();
        })
    })
</script>
</body>
</html>