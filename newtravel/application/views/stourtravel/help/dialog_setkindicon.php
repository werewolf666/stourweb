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
       <div class="icon-content">
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

                         var pid=$(element).attr('pid');
                         var pname=$(element).attr('pname');
                         if($.inArray(pid,pids)==-1)
                         {
                             attrs.push({id:pid,attrname:pname});
                             pids.push(pid);
                         }
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
