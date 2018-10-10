<!doctype html>
<html>
<head border_font=zGXKBk >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base_new.css'); }
</head>

<body style=" overflow: hidden">

   <div class="s-main">
       <div class="attr-list">
        {loop $attridList $list}
           {if !empty($list['children'])}
            <div class="mb-20">
                <div class="pb-5">
                     {$list['attrname']}
                </div>
                <div class="clearfix">
                    {loop $list['children'] $key $row}
                    <label class="radio-label w100"><input type="checkbox" name="attrid" pid="{$list['id']}" pname="{$list['attrname']}" class="i-box" {if in_array($row['id'],$attrids)}checked="checked"{/if} value="{$row['id']}"/><span class="i-lb">{$row['attrname']}</span></label>
                    {/loop}
                </div>
            </div>
           {/if}
        {/loop}
       </div>
       <div class="clearfix text-c">
           <a href="javascript:;" class="btn btn-primary radius" id="confirm-btn">确定</a>
       </div>
   </div>

    <script>
        var id="{$id}";
        var selector="{$selector}";
        $(function(){

            window.setTimeout(function(){
                ST.Util.resizeDialog('.s-main');
            },0);

            $(document).on('click','#confirm-btn',function(){
            var attrs=[];
            var pids=[];

            $('.radio-label .i-box:checked').each(function(index,element){
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
