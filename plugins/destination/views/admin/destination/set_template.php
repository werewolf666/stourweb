<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,style.css,destination_dialog_basicinfo.css'); }
</head>
<body left_bottom=bcIwOs >
<div class="s-main">
    <div class="main-body">
        <div class="nav-list">
            <div class="item-one" id="item_template">
                <table>
                    <tr><td><a href="javascript:;" data-rel="{'templet':'', 'templet_name':'标准'}" class="i-tpl {if empty($info['templet'])}on{/if}">标准</a>
                            {loop $templetlist $tpl}
                            <a href="javascript:;" data-rel="{'templet':'{$tpl['path']}', 'templet_name':'{$tpl['templetname']}'}" class="i-tpl {if $info['templet']==$tpl['path']}on{/if}">{$tpl['templetname']}</a>
                            {/loop}
                        </td></tr>
                </table>
            </div>

        </div>
    </div>
</div>
</body>
<script>
    $(document).on('click',".i-tpl",function(){
        $(this).addClass('on').siblings().removeClass('on');
        ST.Util.responseDialog({id:'{$info["id"]}',data:eval('('+$(this).attr('data-rel')+')')},true);
    });

</script>
</html>
