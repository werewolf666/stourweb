<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title color_clear=zywbVl >图片库配置-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,image.css,base.css,base2.css,plist.css'); }
</head>
<body>
<div class="pic_library">
    <div class="library_con">
        <div class="tabcon" style="display: block">
            <table width="100%" border="0">
                <tr>
                    <th width="20%" scope="row">图片域名：</th>
                    <td width="80%"><input name="cfg_m_img_url" type="text" class="xc_text" value="<?php echo $config['cfg_m_img_url'];?>" /></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="btn">
        <a class="cancel_btn" href="#">取消</a>
        <a class="confirm_btn" href="#">确定</a>
    </div>
</div>
<!--图库配置-->
</body>
<script type="text/javascript" charset="utf-8">
    //取消
    $('.cancel_btn').live('click', function () {
        ST.Util.closeBox();
    });

    //确定
    $('.confirm_btn').click(function () {
        var value=$('input[name="cfg_m_img_url"]').val();
        if(value.length<1){
            ST.Util.showMsg('请填写图片域名', 1);
            return;
        }
        $.ajax({
            type: "POST",
            url: SITEURL + 'image/config/set/',
            data:{'cfg_m_img_url':value},
            async:false,
            success:function (data) {
                var bool=false;
                if (data == 'success') {
                    data='配置成功';
                    bool=true;
                }
                ST.Util.responseDialog(data,bool);
            }
        });
    });
</script>
</html>
