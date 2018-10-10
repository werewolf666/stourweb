<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>网站favico</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('config.js');}
</head>
<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td"  valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <form id="configfrm" script_ul=Kyvz8B >
                    <div class="w-set-con">
                        <div class="cfg-header-bar">
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        <div class="w-set-nr">
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">网站头像{Common::get_help_icon('cfg_webico')}：</span>
                                    <div class="item-bd">
                                        <button id="pic_btn" class="btn btn-primary size-S radius" name="file_upload" type="button" value="上传图片">上传图片</button>
                                        <span class="item-text c-999 ml-10">建议上传尺寸32*32px，格式为ico</span>
                                        <div class="logolist mt-10">
                                            <img class="up-img-area" src="{$GLOBALS['cfg_basehost']}/favicon.ico" id="adimg">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix">
                                <input type="hidden" name="webid" id="webid" value="0">
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

	<script>

	$(document).ready(function(){


          //子站切换点击
        $(".web-set").find('a').click(function(){
            var webid = $(this).attr('data-webid');
            $("#webid").val($(this).attr('data-webid'));
            $("#webname").html($(this).html());
            $(this).addClass('on').siblings().removeClass('on');



        })
        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    var allow=/\.ico$/.test(temp[0]);
                    if(!allow){
                        ST.Util.showMsg('请选择或上传ico格式图片',5,1500);
                        return;
                    }
                    $.post( SITEURL +'config/ajax_webico',{'file':temp[0]},function(data){
                        $('#adimg').attr('src',"{$GLOBALS['cfg_basehost']}/favicon.ico?"+Math.random());
                    },'json');
                }
            }
        });

     });
    </script>

</body>
</html>
