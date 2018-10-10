<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>图标配置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    <style>
        .pop-info-block .info-item-block,
        .pop-info-block .info-item-block>li{
            padding: 0;
        }
        .pop-info-block .info-item-block>li .item-hd{
            width: 80px;
        }
        .pop-info-block .info-item-block>li .item-bd{
            padding-left: 80px;
        }
        .up-img-area{
            max-height: 100px;
        }
    </style>
</head>
<body style=" width: 260px; height: 225px; overflow: hidden">

    <div class="pop-info-block">
        <form name="frm" id="frm">
            <ul class="info-item-block">
                <li>
                    <span class="item-hd fl">导航名称：</span>
                    <div class="item-bd">
                        <span class="item-text">{$info['m_title']}</span>
                    </div>
                </li>
                <li>
                    <span class="item-hd fl">导航图片{Common::get_help_icon('mobile_dialog_ico_m_ico',true)}：</span>
                    <div class="item-bd">
                        <div id="file_upload" class="btn-file mt-4 hide">
                            <div id="file_upload-button" class="uploadify-button " style="text-indent: -9999px; height: 25px; line-height: 25px; width: 80px; cursor: pointer">
                                <span class="uploadify-button-text">上传图片</span>
                            </div>
                        </div>
                        <a class="btn btn-primary radius size-S mt-3" id="upload_btn" href="javascript:;">上传图片</a>
                        <a class="btn btn-grey-outline size-S radius mt-3 ml-10" id="restore_default" href="javascript:;">恢复默认</a>
                        {if !empty($info['m_ico'])}
                        <div class="pt-10 pb-10" id="img"><img id="litimg" class="up-img-area" src="{$info['m_ico']}" /></div>
                        {else}
                        <div class="pt-10 pb-10" id="img"><img id="litimg" class="up-img-area" src="{php echo Common::get_menu_no_ico();}" /></div>
                        {/if}
                    </div>
                </li>
            </ul>
            <div class="clear clearfix mt-20 text-c">
                <a class="btn btn-primary radius" id="save_btn" href="javascript:;">保存</a>
            </div>
            <input type="hidden" name="litpic" id="litpic" value="{$info['m_ico']}">
            <input type="hidden" name="id" id="id" value="{$info['id']}"/>
        </form>
    </div>

  
  
	<script>
        $(function(){

            //上传图片
           //$('#file_upload-button').css('backgroundImage','url("'+PUBLICURL+'images/upload-ico.png'+'")');
            $('#upload_btn').click(function(){
                ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null,  parent.document, {loadWindow: window, loadCallback: Insert});
                function Insert(result,bool){
                    if(result.data.length>0){
                        var len=result.data.length-1;
                        var temp =result.data[len].split('$$');
                        $('#litimg')[0].src=temp[0];
                        $("#litpic").val(temp[0]);

                    }
                }
            });


            $("#save_btn").click(function(){
                var litpic = $("#litpic").val();

                var rsp = {src:litpic,id:$("#id").val(),default_ico:'{$info['default_ico']}'};
                ST.Util.responseDialog(rsp,true);
            });
            //恢复默认
            $("#restore_default").click(function(){
                $("#litpic").val('');
                $("#litimg").attr('src','{$info['default_ico']}');

            });
        })

	</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2010&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
