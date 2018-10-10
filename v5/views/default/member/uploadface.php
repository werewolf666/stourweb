<!doctype html>
<html>
<head padding_strong=PlMwOs >
<title>{__('用户头像上传')}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {include "pub/varname"}
<script src="//open.web.meitu.com/sources/xiuxiu.js" type="text/javascript"></script>
{Common::js('jquery.min.js')}

<script type="text/javascript">
window.onload=function(){
	
	xiuxiu.embedSWF("altContent",5,"700","500");
       /*第1个参数是加载编辑器div容器，第2个参数是编辑器类型，第3个参数是div容器宽，第4个参数是div容器高*/
	var url = "{$GLOBALS['cfg_basehost']}";
	xiuxiu.setUploadURL(url+"/member/index/ajax_uploadface");//修改为您自己的上传接收图片程序
	xiuxiu.onInit = function ()
	{
		//xiuxiu.loadPhoto("");
	}
    xiuxiu.setUploadType(2);
	xiuxiu.onUploadResponse = function (data)
	{
		if(data=='no')
		   return false;
		 
		$("#face", window.parent.document).attr("src",data);
		$("#litpic",window.parent.document).val(data);
		$(".fade", window.parent.document).remove();
		$("#upiframe", window.parent.document).remove();
		
		//alert("上传响应" + data);  可以开启调试
	}
	xiuxiu.onDebug = function (data)
    {
	//alert("错误响应" + data);
    }
}
</script>
<style type="text/css">
	html, body { height:100%; overflow:hidden; }
	body { margin:0; }
</style>
</head>
<body>
<div id="altContent">

</div>
</body>
</html>