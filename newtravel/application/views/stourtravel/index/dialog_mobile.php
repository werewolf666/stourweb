<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    {Common::getCss('base.css')}
</head>
<body style="width:490px">
<div class="pop-preview-box">
    <div class="pop-preview-container clearfix">
        <div class="pc-preview-wrap fl">
            <h4 class="tit">电脑演示预览</h4>
            <p class="txt">通过电脑浏览器预览移动端页面效果</p>
            <a class="link" href="{$mobile_url}" target="_blank">{$mobile_url}</a>
            <a class="now-btn" href="{$mobile_url}" target="_blank">立即访问</a>
        </div>
        <div class="wap-preview-wrap fl">
            <h4 class="tit">手机扫码预览</h4>
            <p class="txt">使用手机扫描下方二维码预览页面效果</p>
            <div class="qr-code"><img src="{$GLOBALS['cfg_basehost']}/res/vendor/qrcode/make.php?param={urlencode($mobile_url)}" /></div>
        </div>
    </div>

</div>
</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2010&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
