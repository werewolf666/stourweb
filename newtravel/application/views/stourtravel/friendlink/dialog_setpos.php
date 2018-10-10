<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,friendlink_dialog_setpos.css'); }
    {php echo Common::getScript('jquery.validate.js');}
</head>
<body >
   <div class="s-main">
       <div class="content-in">
           <span class="item"><input type="checkbox" class="i-box" id="choose_all"><label class="i-lb">全选</label></span>
           {loop $posArr $key $pos}
           <span class="item"><input type="checkbox" class="i-box" value="{$key}" {if in_array($key,$typeArr)}checked="checked"{/if}/><label class="i-lb">{$pos}</label></span>
           {/loop}
           <div class="clear-both"></div>
       </div>
       <div class="save-con">
           <a href="javascript:;" class="confirm-btn">确定</a>
       </div>
   </div>
<script>
     var id={$id};
     $(function(){
         $(".confirm-btn").click(function(){
             var types=[];
             $(".i-box:checked").each(function(index,ele){
                  types.push($(ele).val());
             });
             typeStr=types.join(',');
             ST.Util.responseDialog({typestr:typeStr,id:id},true);
         });

         $("#choose_all").click(function(){
              if($(this).is(':checked'))
              {
                  $(".i-box").attr('checked',true);
              }
              else
              {
                  $(".i-box").attr('checked',false);
              }
         });

     })
</script>

</body>
</html>
