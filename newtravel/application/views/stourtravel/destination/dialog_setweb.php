<!doctype html>
<html>
<head div_right=XLFwOs >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,listimageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    {php echo Common::getCss('base.css,destination_dialog_setweb.css,base_new.css'); }
</head>
<body style="width:580px;height: 199px;overflow: hidden">

    <div class="s-main">
        <div class="pop-info-block clearfix">
            <span class="item-hd">子站域名：</span>
            <div class="item-bd">
                <input type="text" class="input-text w300" name="weburl" />
                <div class="c-666 lh-24">
                    <p class="c-primary mt-5 mb-10">注：开启子站必须提前解析并绑定目标子站域名，否则子站无法正常访问。</p>
                    开启子站后，该目的地下所有内容都会被目标子站所替换，例如：<br />
                    主站域名为www.stourweb.com，需开启子站目的地为“九寨沟（jiuzhaigou）”则申请解析<br />
                    并绑定的子站域名为：jiuzhaigou.stourweb.com
                </div>
            </div>
        </div>
        <div class="text-c clear clearfix mt-30">
            <a href="javascript:;" id="cancel-btn" class="btn btn-grey-outline radius">取消</a>
            <a href="javascript:;" id="confirm-btn" class="btn btn-primary radius ml-15">确定</a>
        </div>
    </div>

<script>
    var id="{$id}";
    var pinyin="{$pinyin}";
    $(function() {


        var domsiteurl = document.domain;
        var urlarr = domsiteurl.split('.');
        if(urlarr.length == 3){
            domsiteurl = urlarr[1]+'.'+urlarr[2];
        }
        domsiteurl = 'http://'+pinyin+'.'+domsiteurl;
        $("input[name=weburl]").val(domsiteurl);


        $(document).on('click','#confirm-btn',function(){
            var weburl=$("input[name=weburl]").val();
            ST.Util.responseDialog({id:id,weburl:weburl},true);
        })

        $(document).on('click','#cancel-btn',function(){
            ST.Util.closeDialog();
        })

        //alert($(".s-main").height());
        //ST.Util.resizeDialog(".s-main",true)


    })


</script>

</body>
</html>
