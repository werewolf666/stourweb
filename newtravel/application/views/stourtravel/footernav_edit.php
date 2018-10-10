<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css'); }
</head>
<body style="background-color: #fff">


	<div class="middle-con">
       <form name="frm" id="frm" action="{$action}">
        <div class="w-set-con">

           <div class="nr-list">
            	<h4 class="tit">导航名称{Common::get_help_icon('footernav_index_servername',true)}：</h4>
              <div class="txt">
              	<input type="text" id="servername" name="servername" class="set-text" value="{$serverinfo['servername']}" />

              </div>
            </div>
            
            <div class="nr-list">
            	<h4 class="tit">介绍内容：</h4>
              <div class="txt">
                  {php Common::getEditor('content',$serverinfo['content'],600,200);}
              </div>
            </div>
            
            <div class="opn-btn">
            	<a class="normal-btn" href="javascript:;">保存</a>

            </div>
            <input type="hidden" name="webid" id="webid" value="{$webid}"/>
            <input type="hidden" name="articleid" id="articleid" value="{$serverinfo['id']}"/>
       </div>
           </form>
    </div>

  
  
	<script>
        $(function(){

            $(".normal-btn").click(function(){
                var url = "{$GLOBALS['cfg_cmspath']}";
                var ajaxurl = url + 'footernav/'+"{$action}";
                $.ajaxform({
                    url: ajaxurl,
                    method: 'POST',
                    form : '#frm',
                    dataType:'json',
                    success: function (data) {


                        if(data.status)
                        {

                            ST.Util.showMsg('保存成功',4);
                            ST.Util.closeBox();//关闭当前窗口


                        }
                        else
                        {
                            ST.Util.showMsg('保存失败',5);
                        }

                    }

                });


            })
        })

	</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201707.2005&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
