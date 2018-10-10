<?php defined('SYSPATH') or die();?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
<title>配置首页</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,choose.js,imageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>

<body top_right=XLFwOs >

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td ">
                <form method="post"  id="frm" name="frm" enctype="multipart/form-data">
                    <div class="manage-nr">
                        <div class="cfg-header-bar" id="nav">
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        <!--基础信息开始-->
                        <div class="product-add-div">
                            <ul class="info-item-block">
                                <li class="list_dl">
                                    <span class="item-hd">短信提醒{Common::get_help_icon('kefu_index_free_tel_msg')}：</span>
                                    <div class="item-bd pr-10">
                                        <label class="radio-label"><input type="radio" name="free_tel_msg_open" value="1" {if $free_tel_msg_open==1}checked="checked"{/if}/>开启</label>
                                        <label class="radio-label ml-20"><input type="radio" name="free_tel_msg_open" value="0" {if $free_tel_msg_open==0}checked="checked"{/if}/>关闭</label>
                                        <textarea class="textarea mt-5" id="free_tel_msg" name="free_tel_msg">{$free_tel_msg}</textarea>
                                        <div  class="clearfix mt-10">
                                            <a class="btn btn-primary-outline short-cut" data="{#WEBNAME#}">网站名称</a>
                                            <a class="btn btn-primary-outline ml-5 short-cut" data="{#FREEPHONE#}">客户号码</a>
                                            <a class="btn btn-primary-outline ml-5 short-cut" data="{#MEMBERNAME#}">会员名称</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <!--基础信息结束-->
                            <div class="clear clearfix mt-5">
                                <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

<script>
	$(document).ready(function(){

        $("#nav").find('span').click(function(){

            Product.changeTab(this,'.product-add-div');//导航切换
        })
        $("#nav").find('span').first().trigger('click');

        //插入
        $('.short-cut').click(function(){
            var value=$(this).attr('data');
            ST.Util.insertContent(value,$("#free_tel_msg"));
        })

        $("#btn_save").click(function(){
             $.ajaxform({
                     url:SITEURL+'kefu/ajax_save_free_msg',
                     form:'#frm',
                     dataType:'json',
                     method:'post',
                     success:function(result){
                         if(result.status)
                             ST.Util.showMsg('保存成功',4);
                         else
                             ST.Util.showMsg('失败',5)
                     }
                 })
        })

    });





</script>
</body>
</html>
