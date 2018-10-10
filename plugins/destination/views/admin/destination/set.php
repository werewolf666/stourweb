<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,choose.js,st_validate.js,jquery.colorpicker.js,imageup.js,jquery.upload.js,insurance.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    {php echo Common::getCss('destination_dialog_basicinfo.css'); }
</head>
<body>
<!--顶部-->
{php Common::getEditor('jseditor','',$sysconfig['cfg_admin_htmleditor_width'],300,'Sline','','print',true);}
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:auto;">
            <div class="manage-nr">
                <div class="w-set-con">
                    <div class="w-set-tit bom-arrow">
                        <span  data-rel="jieshao" class="on"><s></s>页面介绍{Common::get_help_icon('destination_basicinfo_jieshao',true)}</span>
                        <span data-rel="picture"><s></s>图片管理{Common::get_help_icon('destination_basicinfo_images',true)}</span>
                        <span data-rel="seo" ><s></s>优化信息{Common::get_help_icon('destination_basicinfo_seo',true)}</span>
                        <span data-rel="template"><s></s>模板设置{Common::get_help_icon('templates_name',true)}</span>
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>
                <form id="product_fm" ul_float=zq5Udk >
                    <div class="product-add-div item-one" id="item_jieshao">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">页面介绍：</span>
                                <div class="item-bd">
                                    {php Common::getEditor('jieshao',$info['jieshao'],1000,380);}
                                </div>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="id" value="{$info['id']}"/>
                    <!--seo start-->
                    <div id="item_seo" class="product-add-div item-one" style="display: none">
                        <ul class="info-item-block">
                            <li>

                                <span class="item-hd">优化标题：{Common::get_help_icon('content_seotitle',true)}</span>
                                <div class="item-bd">
                                    <input type="text" name="seotitle" class="input-text w500" value="{$info['seotitle']}">
                                </div>
                            </li>

                            <li>
                                <span class="item-hd">Tag词：{Common::get_help_icon('content_tagword',true)}</span>
                                <div class="item-bd">
                                    <input type="text" name="tagword" class="input-text w500" value="{$info['tagword']}">
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">关键词：{Common::get_help_icon('content_keywords',true)}</span>
                                <div class="item-bd">
                                    <input type="text" name="keyword" class="input-text w500" value="{$info['keyword']}">
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">描述：{Common::get_help_icon('content_description',true)}</span>
                                <div class="item-bd">
                                    <textarea class="textarea w500" name="description" cols="" rows="">{$info['description']}</textarea>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!--end start-->

                    <!--picture start-->
                    <div id="item_picture" class="product-add-div item-one" style="display: none">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">图片：</span>
                                <div class="item-bd">
                                    <div>
                                        <span id="pic_btn" class="btn btn-primary radius size-S">上传图片</span>
                                        <span class="item-text c-999 ml-10"></span>
                                    </div>
                                    <div class="up-list-div">

                                        <ul>
                                            <?php
                                            $pic_arr = explode(',', $info['piclist']);
                                            $img_index = 1;
                                            $head_index = 0;
                                            foreach ($pic_arr as $k => $v) {
                                                if (empty($v)){
                                                    continue;
                                                }
                                                $imginfo_arr = explode('||', $v);
                                                $headpic_style = $imginfo_arr[0] == $info['litpic'] ? 'style="display: block; background: green;"' : '';
                                                $head_index = $imginfo_arr[0] == $info['litpic'] ? $img_index : $head_index;
                                                $headpic_hint = $imginfo_arr[0] == $info['litpic'] ? '已设为封面' : '设为封面';
                                                $html = '<li class="img-li">';
                                                $html .= '<img class="fl" src="' . $imginfo_arr[0] . '" width="100" height="100">';
                                                $html .= '<p class="p1">';
                                                $html .= '<input type="text" class="img-name" name="imagestitle[' . $img_index . ']" value="' . $imginfo_arr[1] . '" style="width:90px">';
                                                $html .= '<input type="hidden" class="img-path" name="images[' . $img_index . ']" value="' . $imginfo_arr[0] . '">';
                                                $html.='</p>';
                                                $html.='<p class="p2">';
                                                $html.='<span class="btn-ste" onclick="Imageup.setHead(this,' . $img_index . ')" ' . $headpic_style . '>' . $headpic_hint . '</span><span class="btn-closed" onclick="Imageup.delImg(this,\'' . $imginfo_arr[0] . '\',' . $img_index . ')"></span>';
                                                $html.='</p></li>';
                                                echo $html;
                                                $img_index++;
                                            }
                                            echo '<script> window.image_index=' . $img_index . ';</script>';
                                            ?>
                                        </ul>
                                        <input type="hidden" class="headimgindex" name="imgheadindex" value="<?php  echo $head_index;  ?>">
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!--picture end-->

                    <!--template start-->
                    <div id="item_template" class="product-add-div item-one" style="display: none">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">显示模板：</span>
                                <div class="item-bd">
                                    <a href="javascript:;" data-rel="" class="i-tpl {if empty($info['templet'])}on{/if}">标准</a>
                                    {loop $templetlist $tpl}
                                    <a href="javascript:;" data-rel="{$tpl['path']}" class="i-tpl {if $info['templet']==$tpl['path']}on{/if}">{$tpl['templetname']}</a>
                                    {/loop}
                                    <input type="hidden" id="templet" name="templet" value="{$info['templet']}"/>
                                </div>
                            </li>

                        </ul>
                    </div>
                    <!--template start-->


                </form>
                <div class="clear clearfix">
                    <a class="btn btn-primary radius size-L ml-115 mt-10" id="save_btn" href="javascript:;">保存</a>
                </div>
            </div>

        </td>
    </tr>
</body>
<script>
    var id="{$id}";
    var is_saving=0;
    $(function() {
        $(document).on('click',".bom-arrow span",function(){
            var name=$(this).attr('data-rel');
            $(this).siblings().removeClass('on');
            $(".item-one").hide();
            $(this).addClass('on');
            $("#item_"+name).show();
        });

        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');

                    Imageup.genePic(temp[0],".up-list-div ul",".cover-div",temp[1]);
                }
            }
        });






        $(document).on('click',".i-tpl",function(){
            $(".i-tpl").removeClass('on');
            $(this).addClass('on');
            $('#templet').val($(this).attr('data-rel'));
        })


        $('#up_btn-button').css('backgroundImage','url("'+PUBLICURL+'images/upload-ico.png'+'")');
        $('#up_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    ListImageup.genePic(temp[0],"#pic_head",'');
                }
            }
        });

        $("#save_btn").click(function (e) {
            if (window.is_saving == 1) {
                return false;
            }
            window.is_saving = 1;
            $.ajaxform({
                url: SITEURL + "destination/admin/destination/ajax_save",
                method: "POST",
                form: "#product_fm",
                dataType: "JSON",
                success: function (result) {
                    ST.Util.showMsg('保存成功', 4)
                    window.is_saving = 0;
                }
            });
        });


    })
</script>
</html>
