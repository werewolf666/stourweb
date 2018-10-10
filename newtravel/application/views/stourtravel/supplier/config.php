<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title color_right=bfGwOs >思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("jquery.validate.js"); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,choose.js,product_add.js,imageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
   <style>
        .error{
            color:red;
            padding-left:5px;
        }
        .hide{
            display: none;
        }

    </style>

</head>
<body style="background-color: #fff">
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td">
            <form id="frm" name="frm">
            <div id="product_grid_panel" class="manage-nr">
                <div class="w-set-con">
                    <div class="cfg-header-bar" id="nav">

                        
                        <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>
                <div class="product-add-div" >
                    <ul class="info-item-block">
                            <li>
                                <span class="item-hd">供应商{Common::get_help_icon('supplier_field_display')}：</span>
                                <div class="item-bd">
                                    <div class="on-off">
                                        <label class="radio-label"><input type="radio" name="display" value="1" {if $config['value']==1} checked="checked" {/if}>显示</label>
                                        <label class="radio-label ml-20"><input type="radio" name="display"  {if $config['value']==0} checked="checked" {/if}  value="0">隐藏</label>
                                    </div>
                                </div>
                            </li>
                    </ul>
            	</div>
                <div class="clear clearfix">
                    <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
                </div>
            </form>
        </td>
    </tr>
</table>


<script>

    $(function () {

        //保存
        $("#btn_save").click(function(){
            $.ajax({
                type:'post',
                dataType:'json',
                url:SITEURL+"supplier/ajax_save_config",
                data:$('#frm').serialize(),
                success:function (data) {
                    if(data.status)
                    {
                        ST.Util.showMsg('保存成功!','4',2000);
                    }
                    else
                    {
                        ST.Util.showMsg(data.msg,'5',2000);
                    }
                }
            })
        })

    })

</script>

</body>
</html>