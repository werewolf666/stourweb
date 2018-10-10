<!doctype html>
<html>
<head size_clear=zC0-0l >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getScript("DatePicker/WdatePicker.js,config.js"); }
    {php echo Common::getCss('base.css,email_dialog.css,base_new.css'); }
    <style>
        .info-item-block{
            padding: 0;
        }
        .info-item-block>li{
            padding: 0 0 10px;
        }
        .info-item-block>li .item-hd{
            width: 50px;
        }
        .info-item-block>li .item-bd{
            padding-left: 55px;
        }
    </style>
</head>
<body style=" width: 400px; height: 238px; overflow: hidden">

    <div class="clear">
        <form id="configfrm">
            <ul class="info-item-block">
                <li>
                    <span class="item-hd">收件箱：</span>
                    <div class="item-bd">
                        <input class="input-text" type="text" id="send_email"/>
                    </div>
                </li>
                <li>
                    <span class="item-hd">标题：</span>
                    <div class="item-bd">
                        <input class="input-text" type="text" id="send_title"/>
                    </div>
                </li>
                <li>
                    <span class="item-hd">内容：</span>
                    <div class="item-bd">
                        <textarea class="textarea va-t" id="send_content"></textarea>
                    </div>
                </li>
            </ul>
            <div class="clear text-c mt-20">
                <a href="javascript:;" class="btn btn-primary radius" id="btn_save">发送</a>
            </div>
        </form>
    </div>

<script>
    $(document).ready(function(){
        $("#btn_save").click(function(){
            var email=$("#send_email").val();
            var title=$("#send_title").val();
            var content=$("#send_content").val();
            ST.Util.showMsg('发送中...','6',60000);
            $.ajax({
                url: SITEURL+"email/ajax_sendmail",
                data:{email: email,title: title, content: content},
                type:'post',
                cache:false,
                dataType:'json',
                success:function(data){
                    ST.Util.hideMsgBox();
                    if(data.status)
                    {
                        ST.Util.showMsg('发送成功!','4',2000);
                        setTimeout(function(){ST.Util.closeDialog();},2000);
                    }else{
                        ST.Util.showMsg('发送失败!','5',2000);
                    }
                },
                error : function(ex) {
                    alert("异常！");
                }
            });
            //数据
        })
    });
</script>
</body>
</html>
