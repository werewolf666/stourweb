<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title clear_script=yyvz8B >网站Logo</title>
    {template 'stourtravel/public/public_min_js'}
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
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        <div class="w-set-nr">
                            <ul class="info-item-block">
                                <li class="">
                                    <span class="item-hd">无图设置{Common::get_help_icon('cfg_df_img')}：</span>
                                    <div class="item-bd">
                                        <a href="javascript:;" id="pic_btn" class="btn btn-primary radius size-S mt-3" name="file_upload"/>上传图片</a>
                                        <a class="btn btn-grey-outline radius size-S mt-3 ml-5" href="javascript:;" id="del_btn">恢复默认</a>
                                        <div class="mt-10">
                                            <img id="nopicimg" src="{$GLOBALS['cfg_df_img']}" class="up-img-area" />
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix">
                                <input type="hidden" id="webid" value="0">
                                <input type="hidden" name="webid" id="webid" value="0">
                                <input type="hidden" name="cfg_df_img" id="cfg_df_img" value=""/>
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
            getConfig(webid);//重新读取配置

        })

        //配置信息保存
        $("#btn_save").click(function(){

            var webid= $("#webid").val();
            Config.saveConfig(webid);
        })


        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    $('#nopicimg').attr('src',temp[0]);
                    $('#cfg_df_img').val(temp[0]);
                    //$('#del_btn').css('display','inline-block');
                    var webid= $("#webid").val();
                    Config.saveConfig(webid);

                }
            }
        });




        //删除图片
        $("#del_btn").click(function(){
            var webid= $("#webid").val();
            $("#nopicimg").attr('src',SITEURL+'public/images/nopic.jpg');
            $("#cfg_df_img").val('');
            //$("#del_btn").hide();
            Config.saveConfig(webid);
        });
        
        getConfig(0);


     });


       //获取配置
        function getConfig(webid)
        {
            var fields = 'cfg_df_img';
            Config.getConfig(webid,function(data){
                if(data.cfg_df_img!='')
                {
                    $("#cfg_df_img").val(data.cfg_df_img);
                    $("#nopicimg").attr('src',data.cfg_df_img);
                    //$("#del_btn").show();
                }
                else
                {
                    $("#nopicimg").attr('src',SITEURL+'public/images/nopic.jpg');
                    $("#cfg_df_img").val('');
                    //$("#del_btn").hide();
                }
            },fields)
        }


    </script>

</body>
</html>
