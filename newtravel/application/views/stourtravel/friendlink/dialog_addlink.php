<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,base_new.css,friendlink_dialog_addlink.css'); }
    {php echo Common::getScript('jquery.validate.js');}
</head>
<body >
   <div class="s-main">
       <div class="content-in">
           <form id="_fm">
               <table>
                   <tr>
                       <td width="72" class="text-r"><label class="un-blank">*</label>网站名称：</td>
                       <td><input type="text" id="web_name" name="webname" class="input-text w300"/></td>
                   </tr>
                   <tr>
                       <td class="text-r"><label class="un-blank">*</label>URL地址：</td>
                       <td><input type="text" id="web_url" name="weburl" class="input-text w300"/></td>
                   </tr>
                   <tr>
                       <td class="text-r">站点：</td>
                       <td>
                           <select name="webid" id="web_id"  class="select-box inline">
                               <option value="0">主站</option>
                               {loop $weblist $web}
                                  <option value="{$web['id']}">{$web['webname']}</option>
                               {/loop}
                           </select>
                        </td>
                    </tr>
               </table>
           </form>
       </div>
       <div class="clearfix text-c mt-25">
       <a href="javascript:;" class="btn radius" id="cancel_btn">取消</a>
           <a href="javascript:;" id="confirm_btn" class="btn btn-primary  radius ml-20">确定</a>
       </div>
   </div>
<script>
     var id="{$id}"
     $(function(){

           $("#_fm").validate({
               rules:{
                   'webname':{
                       required:true
                   },
                   'weburl':{
                       required:true
                   }
               },
               messages:
               {
                   'webname':{
                       required:'必填'
                   },
                   'weburl':{
                       required:'必填'
                   }
               },
               submitHandler:function(form)
               {
                   var webname=$("#web_name").val();
                   var weburl=$("#web_url").val();
                   var webid=$("#web_id").val();
                   ST.Util.responseDialog({status:0,data:{sitename:webname,siteurl:weburl,webid:webid}},true);
               }

           });


           $(document).on('click','#confirm_btn',function(){
                  $("#_fm").submit();
           })


          $("#cancel_btn").click(function(){
             ST.Util.closeDialog();
          });





     })
</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2601&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
