<!doctype html>
<html>
<head font_margin=H9KwOs >
    <meta charset="utf-8">
    <title>基本设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,jqtransform.css,base_new.css'); }
    {php echo Common::getScript('config.js,jquery.jqtransform.js,jquery.colorpicker.js');}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    <style>
        #uploadify-queue{display: none;}
    </style>
</head>
<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <form id="configfrm">
                    <div class="w-set-con">
                        <div class="cfg-header-bar">
                            <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="window.location.reload()">刷新</a>
                        </div>
                        <div class="w-set-nr">
                            <ul class="info-item-block">
                                <li class="rowElem">
                                    <span class="item-hd">登录页背景图{Common::get_help_icon('cfg_login_bg')}：</span>
                                    <div class="item-bd">
                                        <div class="">
                                            <a href="javascript:;" class="btn btn-primary size-S radius event_file_upload" data="{title:'上传图片',callback:'upload_img'}">上传图片</a>
                                            <a href="javascript:;" class="btn btn-grey-outline size-S radius ml-10" id="reset_default_img">恢复默认</a>
                                        </div>
                                        <div class="pt-10">
                                            <input type="hidden" name="cfg_login_bg" id="cfg_login_bg" value="{$config['cfg_login_bg']}"/>
                                            <div class="logolist" style="{if !$config['cfg_login_bg']}display:none;{/if}" >
                                                <img src="{$config['cfg_login_bg']}" id="preview_login_img" class="up-img-area">
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix pt-20">
                                <input type="hidden" name="webid" id="webid" value="0">
                                <a class="btn btn-primary size-L radius ml-115" href="javascript:;" id="btn_save">保存</a>
                                <input type="hidden" name="webid" id="webid" value="0">
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

<script>
    $(document).ready(function () {
        //配置信息保存
        $("#btn_save").click(function () {
            Config.saveConfig(0);
        });
        //登录背景上传
        $('.event_file_upload').click(function(){
            var obj=eval('('+$(this).attr('data')+')');console.log(obj);
            ST.Util.showBox(obj.title, SITEURL + 'image/insert_view', 0,0, null, null, parent.document, {loadWindow: window, loadCallback: eval('('+obj.callback+')')});
            function upload_img(result,bool) {
                if(bool){
                    var src=result['data'][0].replace(/\$\$.*?$/,'');
                    $(".logolist").show();
                    $('#preview_login_img')[0].src = src;
                    $('#cfg_login_bg').val(src);
                }
            }
        });

        $('#reset_default_img').click(function(){
            var src='/uploads/background_img/login.jpg';
            $(".logolist").show();
            $('#preview_login_img')[0].src = src;
            $('#cfg_login_bg').val(src);
        });

    });
    //删除图片
    function delad() {
        var adfile = $("#cfg_login_bg").val();
        var webid = $("#webid").val();
        if (adfile == '') {
            ST.Util.showMsg('还没有上传图片', 1, 1000);
        }
        else {
            $.ajax({
                type: "post",
                data: {picturepath: adfile, webid: webid},
                url: SITEURL + "uploader/delpicture",
                success: function (data, textStatus) {
                    if (data == 'ok') {
                        //$("#preview_login_img")[0].src = SITEURL + 'public/images/pic_tem.gif';
                        $(".logolist").hide();
                        $("#cfg_login_bg").val('');
                    }
                }
            });
        }
    }
    //获取配置
    function getConfig(webid)
    {
        var fields = ['cfg_web_open','cfg_usernav_open','cfg_index_templet','cfg_login_bg'];
        Config.getConfig(webid,function(data){
            if(data.cfg_df_img!='')
            {
                $("#cfg_df_img").val(data.cfg_df_img);
                $("#nopicimg").attr('src',data.cfg_df_img);
                $("#del_btn").show();
            }
            else
            {
                $("#nopicimg").attr('src',SITEURL+'public/images/nopic.jpg');
                $("#cfg_df_img").val('');
                $("#del_btn").hide();
            }
        },fields)
    }
</script>
</body>
</html>
