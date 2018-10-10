<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,attrid_dialog_setattrid.css'); }
</head>
<body >
   <div class="s-main">
       <div class="attr-list">
            <div class="con-row">
                <div class="con-list">
                    <ul>
                        <li>
                            {loop $products $key $row}
                            <span class="item"><input type="checkbox" name="typeid"  pname="{$list['modulename']}" class="i-box" {if in_array($row['id'],$typeids)}checked="checked"{/if} value="{$row['id']}"/><label class="i-lb">{$row['modulename']}</label></span>
                            {/loop}
                            <div class="clear-both"></div>
                        </li>
                    </ul>
                </div>
            </div>
       </div>
       <div class="save-con">
           <a href="javascript:;" class="confirm-btn">确定</a>
       </div>
   </div>
<script>
     var id="{$id}";
     var selector="{$selector}"
     $(function(){

         window.setTimeout(function(){
             ST.Util.resizeDialog('.s-main');
         },0);

           $(document).on('click','.confirm-btn',function(){
                  var attrs=[];
                  var pids=[];
                  $('.item .i-box:checked').each(function(index,element){

                         var attrname=$(element).siblings('.i-lb:first').text();
                         var id=$(element).val();
                         attrs.push({id:id,attrname:attrname});
                  });

                 ST.Util.responseDialog({id:id,data:attrs,selector:selector},true);
           })






     })
</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1302&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
