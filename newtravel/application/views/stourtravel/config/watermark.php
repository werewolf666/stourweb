<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>水印设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css,jqtransform.css'); }
    {php echo Common::getScript('config.js,jquery.jqtransform.js,jquery.colorpicker.js');}
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
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                    <div class="w-set-nr">

                        <ul class="info-item-block">
                            <li class="rowElem">
                                <span class="item-hd">功能开关：</span>
                                <div class="item-bd">
                                    <label class="radio-label mr-20"><input type="radio" name="photo_markon" value="1" {if $markinfo['photo_markon']==1}checked{/if}>开启</label>
                                    <label class="radio-label"><input type="radio" name="photo_markon" value="0" {if $markinfo['photo_markon']==0}checked{/if}>关闭</label>
                                </div>
                            </li>
                            <li class="rowElem">
                                <span class="item-hd">水印类型{Common::get_help_icon('config_watermark_photo_marktyp')}：</span>
                                <div class="item-bd">
                                    <label id="wz" class="radio-label mr-20" for="photo_marktype_text">
                                        <input type="radio"  name="photo_marktype" id="photo_marktype_text" value="text" {if $markinfo['photo_marktype']=='text'}checked{/if}>文字
                                    </label>
                                    <label id="tp" class="radio-label mr-20" for="photo_marktype_img">
                                        <input type="radio"  name="photo_marktype" id="photo_marktype_img" value="img" {if $markinfo['photo_marktype']=='img'}checked{/if}>图片
                                    </label>
                                </div>
                            </li>
                            <li class="writing">
                                <ul class="info-item-block">
                                    <li>
                                        <span class="item-hd">水印文字{Common::get_help_icon('config_watermark_photo_marktext')}：</span>
                                        <input type="text" name="photo_marktext" id="photo_marktext" class="input-text size-S w200" value="{$markinfo['photo_marktext']}" />
                                    </li>
                                    <li>
                                        <span class="item-hd">文字大小{Common::get_help_icon('config_watermark_photo_fontsize')}：</span>
                                        <input type="text" name="photo_fontsize" id="photo_fontsize" class="input-text size-S w200" value="{$markinfo['photo_fontsize']}" /><span class="ml-5">px</span>
                                    </li>
                                    <li>
                                        <span class="item-hd">文字颜色{Common::get_help_icon('config_watermark_photo_fontcolor')}：</span>
                                        <input type="text" name="photo_fontcolor" id="photo_fontcolor" class="input-text w200 size-S colorpicker" value="{$markinfo['photo_fontcolor']}" />
                                    </li>
                                </ul>
                            </li>
                            <li class="picture product-add-div" style="{if (empty($markinfo['photo_marktype']) || $markinfo['photo_marktype']=='text')} display:none;{/if}">
                                <ul class="info-item-block" style=" padding: 0">
                                    <li>
                                        <span class="item-hd">水印图片{Common::get_help_icon('config_watermark_photo_markimage')}：</span>
                                        <div class="item-bd">
                                            <a href="javascript:;" id="pic_btn" name="file_upload" class="btn btn-primary size-S radius mt-5" >上传图片</a>
                                            <div class="pt-10">
                                                <img id="markimg" src="{$markinfo['markimgurl']}?{time()}" class="up-img-area" />
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="pellucidity">
                                <span class="item-hd">生成条件{Common::get_help_icon('config_watermark_photo_condition')}：</span>
                                <div class="item-bd">
                                    <span class="item-text va-m mr-20">宽:<input type="text" name="photo_condition[]" class="input-text size-S va-m ml-5 mr-5 w60" value="{$markinfo['photo_condition'][0]}" title="宽"  placeholder="宽" />px</span>
                                    <span class="item-text va-m mr-10">高:<input type="text" name="photo_condition[]" class="input-text size-S va-m ml-5 mr-5 w60" value="{$markinfo['photo_condition'][1]}" title="高"  placeholder="高" />px</span>
                                    <span class="item-text c-999">*限制生成水印的最小图片尺寸，大于等于设置的尺寸才生成。如：设置100*100，则与相同或大于的才处理。都设为0，则全部加水印。一个值为0则，不限制为0的一边。</span>
                                </div>
                            </li>
                            <!--<div class="pellucidity">
                              <label>&nbsp;&nbsp;&nbsp;&nbsp;透明度：</label>
                              <input type="text" id="photo_diaphaneity" name="photo_diaphaneity" class="set-text-xh set-text-bz2" value="{$markinfo['photo_diaphaneity']}" />
                              <span class="ml-5"></span>
                            </div>-->
                            <li>
                                <span class="item-hd">水印位置{Common::get_help_icon('config_watermark_photo_waterpos')}：</span>
                                <div class="item-bd">
                                    <table class="table table-border table-bordered w300 mt-10" class="w500" border="0" cellspacing="0" cellpadding="0">
                                        <tr class="text-c">
                                            <td width="100"><label class="radio-label"><input type="radio"  name="photo_waterpos" value="1" {if $markinfo['photo_waterpos']==1}checked{/if}>顶部居左</label></td>
                                            <td width="100"><label class="radio-label"><input type="radio"  name="photo_waterpos" value="2" {if $markinfo['photo_waterpos']==2}checked{/if}>顶部居中</label></td>
                                            <td width="100"><label class="radio-label"><input type="radio"  name="photo_waterpos" value="3" {if $markinfo['photo_waterpos']==3}checked{/if}>顶部居右</label></td>
                                        </tr>
                                        <tr class="text-c">
                                            <td><label class="radio-label"><input type="radio"  name="photo_waterpos" value="4" {if $markinfo['photo_waterpos']==4}checked{/if}>中间居左</label></td>
                                            <td><label class="radio-label"><input type="radio"  name="photo_waterpos" value="5" {if $markinfo['photo_waterpos']==5}checked{/if}>图片中心</label></td>
                                            <td><label class="radio-label"><input type="radio"  name="photo_waterpos" value="6" {if $markinfo['photo_waterpos']==6}checked{/if}>中间居右</label></td>
                                        </tr>
                                        <tr class="text-c">
                                            <td><label class="radio-label"><input type="radio"  name="photo_waterpos" value="7" {if $markinfo['photo_waterpos']==7}checked{/if}>底部居左</label></td>
                                            <td><label class="radio-label"><input type="radio"  name="photo_waterpos" value="8" {if $markinfo['photo_waterpos']==8}checked{/if}>底部中心</label></td>
                                            <td><label class="radio-label"><input type="radio"  name="photo_waterpos" value="9" {if $markinfo['photo_waterpos']==9}checked{/if}>底部居右</label></td>
                                        </tr>
                                        <tr class="text-c">
                                            <td colspan="3">
                                                <label class="radio-label"><input type="radio"  name="photo_waterpos" value="0" {if $markinfo['photo_waterpos']==0}checked{/if}>随机生成</label>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </li>
                        </ul>

                        <div class="clear clearfix mt-5">
                            <a class="btn btn-primary size-L radius ml-115" href="javascript:;" id="btn_save">保存</a>
                            <!-- <a class="cancel" href="#">取消</a>-->
                            <input type="hidden" name="webid" id="webid" value="0">
                            <input type="hidden" name="photo_markimg" id="photo_markimg" value="{$markinfo['photo_markimg']}">
                        </div>

                        <div id="colorlist"></div>

                    </div>
                </div>
            </form>

        </td>
    </tr>
</table>



<script>

    $(document).ready(function(){

        //文字和图片切换
        $("#tp").click(function(){

            $(".picture").show()
            $(".writing").hide();

        })
        $("#wz").click(function(){
            $(".writing").show()
            $(".picture").hide()
        })

        var v = $("input[name='photo_marktype']:checked").val();
        if(v == 'img')
        {
            $("#tp").trigger('click');
        }


        //配置信息保存
        $("#btn_save").click(function(){

            var url = SITEURL+"config/ajax_savewatermark";
            var frmdata = $("#configfrm").serialize();
            $.ajax({
                type:'POST',
                url:url,
                dataType:'json',
                data:frmdata,
                success:function(data){

                    if(data.status==true)
                    {
                        ST.Util.showMsg('保存成功',4);
                    }




                }
            })


        })

        //文件上传
        var webid=$("#webid").val();


        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    if(!temp[0].match(/\.png$/i))
                    {
                        ST.Util.showMsg('只能选择png格式的图片!',5,1500);
                    }
                    else
                    {
                        $('#markimg').attr('src',temp[0]);
                        $('#photo_markimg').val(temp[0]);
                    }
                }
            }
        });





        //jq

        //颜色选择

        $(".colorpicker").colorpicker({
            ishex:false,
            success:function(o,color){
                $(o).val(color)
            },
            reset:function(o){

            }
        });

    })










</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.3105&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
