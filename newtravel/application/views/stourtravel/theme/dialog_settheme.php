<!doctype html>
<html>
<head border_bottom=zYHwOs >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base_new.css'); }
</head>
<body style=" overflow: hidden">

<div class="s-main">
    <div class="theme-list">
        {loop $themes $theme}
         <label class="check-label mr-20"><input type="checkbox" class="i-box" value="{$theme['id']}" {if in_array($theme['id'],$selThemes)}checked="checked"{/if}/><span class="i-lb">{$theme['ztname']}</span></label>
        {/loop}
        <div class="clear-both"></div>
    </div>
    <div class="clear clearfix mt-20 text-c">
        <a href="javascript:;" id="confirm-btn" class="btn btn-primary radius">确定</a>
    </div>
</div>

<script>
    var id="{$id}"
    $(function(){
        $(document).on('click','#confirm-btn',function(){
            var arr=[];
            $(".theme-list .i-box:checked").each(function(index,element){
                var id=$(element).val();
                var ztname=$(element).siblings(".i-lb").text();
                arr.push({id:id,ztname:ztname});

            });

            ST.Util.responseDialog({id:id,data:arr},true);
        })
    })
</script>

</body>
</html>
