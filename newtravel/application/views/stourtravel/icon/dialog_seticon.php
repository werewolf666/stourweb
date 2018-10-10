<!doctype html>
<html>
<head size_font=zuxSjk >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,icon_dialog_seticon.css,base_new.css'); }
</head>
<body>

   <div class="s-main">
       <div class="icon-list">
        {loop $icons $icon}
           <span class="icon-item {if in_array($icon['id'],$selIcons)}on{/if}" data-rel="{$icon['id']}"><img src="{$icon['picurl']}" height="15" /></span>
        {/loop}
           <div class="clear-both"></div>
       </div>
       <div class="clear clearfix text-c mt-10">
           <a href="javascript:;" id="confirm-btn" class="btn btn-primary radius size-L">确定</a>
       </div>
   </div>

<script>
     var id="{$id}";
     var selector="{$selector}";
     $(function(){
         window.setTimeout(function(){
             ST.Util.resizeDialog('.s-main');
         },0);
           $(document).on('click','.icon-item',function(){
                 if($(this).is('.on'))
                 {
                     $(this).removeClass('on');
                 }
                 else
                 {
                     $(this).addClass('on');
                 }
          });

           $(document).on('click','#confirm-btn',function(){
                var data=[];
                $(".icon-list .icon-item.on").each(function(index,element){
                     var id=$(element).attr("data-rel");
                     var url=$(element).find('img').attr('src');
                     data.push({id:id,url:url});
                });
               ST.Util.responseDialog({id:id,data:data,selector:selector},true);
           })

     })
</script>

</body>
</html>
