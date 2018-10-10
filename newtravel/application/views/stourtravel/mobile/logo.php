<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>手机站系统参数设置</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('config.js');}
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>

<body>
	<table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
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
                            <div id="m_logo_div"  class="mitem">
                                <ul class="info-item-block">
                                    <li>
                                        <span class="item-hd">网站Logo{Common::get_help_icon('cfg_m_logo')}：</span>
                                        <div class="item-bd">
                                            <div title="279x66">
                                                <a href="javascript:;" class="btn btn-primary radius size-S mt-5 event_file_upload" data="{title:'上传图片',callback:'upload_img'}">上传图片</a>
                                                <a class="btn btn-grey-outline radius size-S mt-5 ml-5" id="delete_btn" onClick="del_m_log()")>恢复默认</a>
                                            </div>
                                            <div class="logolist pt-20">
                                                <img src="" id="m_adimg" class="up-img-area">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="clear clearfix mt-5">
                                <a class="btn btn-primary size-L radius w100 ml-115" href="javascript:;" id="btn_save">保存</a>
                                <input type="hidden" name="webid" id="webid" value="0">
                                <input type="hidden" name="cfg_m_logo" id="cfg_m_logo" value=""/>
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
            var webid = $("#webid").val();
            Config.saveConfig(webid, function () {
//                $.ajax(
//                    {
//                        type: "post",
//                        url: SITEURL + 'systemparts/ajax_further_processing',
//                        dataType: 'json',
//                        beforeSend: function () {
//                            //ST.Util.showMsg('正在完成后续处理,请稍后...', 6, 60000);
//                        },
//                        success: function (data) {
//                            if (data.status) {
//                              //  ST.Util.showMsg('保存成功!', 4, 1000);
//                            }
//                        }
//
//                    }
//                );
            });
        });
        //logo上传
        $('.event_file_upload').click(function () {
            var obj = eval('(' + $(this).attr('data') + ')');
            console.log(obj);
            ST.Util.showBox(obj.title, SITEURL + 'image/insert_view', 0, 0, null, null, parent.document, {
                loadWindow: window,
                loadCallback: eval('(' + obj.callback + ')')
            });
            function upload_img(result, bool) {
                if (bool) {
                    var src = result['data'][0].replace(/\$\$.*?$/, '');
                    $('#m_adimg').attr('src', src);
                    $('#cfg_m_logo').val(src);
                }
            }
        });
        //初始化
        getConfig(0);
    });
    //获取配置
    function getConfig(webid) {
        Config.getConfig(webid, function (data) {
            $("#cfg_m_logo").val(data.cfg_m_logo);
            if (data.cfg_m_logo != '') {
                $("#m_adimg").attr('src', data.cfg_m_logo);
            }
            else {
                $("#m_adimg").attr('src', SITEURL + 'public/images/pic_tem.gif');
            }
        }, 'cfg_m_logo')
    }

    //删除图片
    function del_m_log() {
        var adfile = $("#cfg_m_logo").val();
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
                        $("#m_adimg")[0].src = SITEURL + 'public/images/pic_tem.gif';
                        $("#cfg_m_logo").val('');
                    }
                }

            });
        }
    }
</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201711.0101&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
