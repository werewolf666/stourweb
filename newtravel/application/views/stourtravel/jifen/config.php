<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>积分兑换设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('config.js,jquery.validate.js');}
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
                <div>
                    <form id="configfrm">
                        <div class="cfg-header-bar">
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">积分名称：</span>
                                <div class="item-bd">
                                    <input type="text" name="cfg_jifen_name" id="cfg_jifen_name" class="input-text w100" value="">
                                    <span class="ml-10" style="color:#999">*设置积分显示的名称，不能为空</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">积分现金换算：</span>
                                <div class="item-bd">
                                    <input type="text" name="cfg_exchange_jifen" id="cfg_exchange_jifen" class="input-text w100" value="">
                                    <span class="item-text">积分=1元</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">积分有效期：</span>
                                <div class="item-bd">
                                    <input type="text" name="cfg_jifen_longevity" id="cfg_jifen_longevity" class="input-text w100" value="">
                                    <span class="item-text">年</span>
                                    <span class="item-text c-999 ml-10">*设置积分有效期。设置为0时，表示不过期；设置1年，从1月1日-12月31日为1年，到期则清除掉当年日期内的所有积分</span>
                                </div>
                            </li>
                        </ul>
                        <div class="clear clearfix mt-5">
                            <a class="btn btn-primary radius size-L mt-5 ml-115" href="javascript:;" id="btn_save">保存</a>
                            <!-- <a class="cancel" href="#">取消</a>-->
                            <input type="hidden" name="webid" id="webid" value="0">
                        </div>
                    </form>
                </div>
            </td>
      </tr>
    </table>

	<script>

	$(document).ready(function(){

        //配置信息保存
        $("#btn_save").click(function(){
            $("#configfrm").submit();
        })

        $("#configfrm").validate({
            focusInvalid:false,
            rules: {
                cfg_jifen_name:
                {
                    required: true
                }
                ,cfg_exchange_jifen:{
                    digits:true,
                    min:0
                }
                ,cfg_jifen_longevity:{
                    digits:true,
                    min:0
                }
            },
            messages: {
                cfg_jifen_name:{
                    required:"必填"
                },
                cfg_exchange_jifen:{
                    digits:"只能输入正整数",
                    min:"不得小于0"
                },
                cfg_jifen_longevity:{
                    digits:"只能输入正整数",
                    min:"不得小于0"
                }
            },
           /* errorPlacement:function(error,element){
                if(element.is('#day_before') || element.is('#time_before')) {
                    $(element).siblings('.error-lb').append(error);
                }
                else
                {
                    $(element).parent().append(error)
                }

            },*/
            submitHandler:function(form){
                var webid= 0
                Config.saveConfig(webid);
                return false;//阻止常规提交
            }
        });

        //文件上传
        var webid=0;
        setTimeout(function(){
            $('#file_upload').uploadify({
                'formData'     : {
                    'webid':webid,
                    'isAd':true,
                    uploadcookie:"<?php echo Cookie::get('username')?>"
                },

                'swf'      : PUBLICURL+'js/uploadify/uploadify.swf',
                'uploader' : SITEURL+'uploader/uploadfile',
                'buttonImage' : PUBLICURL+'images/upload-ico.png',
                'fileSizeLimit' : '512KB',
                'fileTypeDesc' : 'Image Files',
                'fileTypeExts' : '*.gif; *.jpg; *.png',
                'cancelImg' : PUBLICURL+'js/uploadify/uploadify-cancel.png',
                'multi' : false,
                'removeCompleted' : true,
                'height':25,
                'width':80,
                'wmode ':'transparent',
                'removeTimeout':0.2,

                onUploadSuccess:function(file,data,response){


                    var obj = $.parseJSON(data);
                    //var obj = eval('('+data+')');
                    if(obj.bigpic!=''){
                        $('#wximg')[0].src=obj.bigpic;
                        $('#cfg_weixin_logo').val(obj.bigpic);

                    }

                }

            });
        },10)

        getConfig(0);


     });


       //获取配置
        function getConfig(webid)
        {
            var fields = ['cfg_jifen_name','cfg_jifen_longevity','cfg_exchange_jifen'];
            Config.getConfig(webid,function(data){
                $("#cfg_jifen_name").val(data.cfg_jifen_name);
                $("#cfg_jifen_longevity").val(data.cfg_jifen_longevity);
                $("#cfg_exchange_jifen").val(data.cfg_exchange_jifen);
            },fields)
        }


    </script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1502&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
