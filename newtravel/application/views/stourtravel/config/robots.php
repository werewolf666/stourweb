<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>思途CMS{$coreVersion}</title>
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
                <form id="configfrm">
                    <div class="clear">
                        <div class="cfg-header-bar">
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        
                        <div class="clear">
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd">robots设置：{Common::get_help_icon('robots')}</span>
                                    <div class="item-bd pr-10">
                                        <textarea id="robots" name="robots" cols="6" rows="8" class="textarea"></textarea>
                                    </div>
                                </li>
                            </ul>
                            <div class="clear clearfix">
                                <a class="btn btn-primary radius size-L ml-115" href="javascript:;" id="btn_save">保存</a>
                                <!-- <a class="cancel" href="#">取消</a>-->
                            </div>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    </table>

    <input type="hidden" id="webid" value="0">
  
  
	<script>

	$(document).ready(function(){



        //配置信息保存
        $("#btn_save").click(function(){
           saveConfig();
        })

        getConfig();

        //默认读取配置

		});


       //获取配置
        function getConfig()
        {
            var url = SITEURL+"config/ajax_getrobots";

            $.ajax({
                type:'POST',
                url:url,
                dataType:'json',
                success:function(data){

                  $("#robots").html(data.robots);


                }
            })


        }
        //保存配置
        function saveConfig()
        {
            var url = SITEURL+"config/ajax_saverobots";
            var frmdata = $("#configfrm").serialize();
            $.ajax({
                type:'POST',
                url:url,
                data:frmdata,
                dataType:'json',
                success:function(data){

                    if(data.status==true)
                    {

                        ST.Util.showMsg('保存成功',4);
                    }




                }
            })
        }




	</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201711.0102&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
