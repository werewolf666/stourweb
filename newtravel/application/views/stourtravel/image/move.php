<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>移动到组-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,image.css,base.css,base2.css,plist.css'); }
</head>
<body>
<div class="move_file_box">
    <ul id="content">
        <?php foreach ($group as $v): ?>
            <li {if $v['has_directory']}data-url="image/image_move/id/{$ids}/group_pid/{$v['group_id']}" {else} data="{$v['group_id']}"{/if}>
      	<span>
          <i></i>
          <img src="<?php echo $publicPath; ?>images/wjj_ico.png"/>
        </span>
                <input type="text" value="<?php echo $v['group_name'] ?>" class="pic_name" disabled
                       style=" background:#fff">
            </li>
        <?php endforeach; ?>
        {if $group_parent["level"]<=3}
        <li>
            <img id="newAdd" src="<?php echo $publicPath; ?>images/add_file_bg.png"/>
        </li>
        {/if}
    </ul>
    <div class="btn">
        <a class="cancel_btn" href="#">取消</a>
        <a class="confirm_btn" href="#">确定</a>
    </div>
    <input type="hidden" id="ids"  value="<?php echo $ids;?>"/>
</div>
<!--移动到相册-->
</body>
<script type="text/javascript" charset="utf-8">
    var dirIndex = 0;
    $('#newAdd').find('input').bind('focus');
    $('#newAdd').click(function () {
        var tpl = '<li data="{id}" class="new"><span><i></i><img src="<?php echo $publicPath;?>images/wjj_ico.png"></span><input type="text" value="未命名" class="pic_name"></li>';
        $.ajax({
            type: "POST",
            url: SITEURL + 'image/group_manage',
            dataType: 'json',
            data: {action: 'add', name: '未命名', description: '','pid':'{$group_parent["group_pid"]}','level':'{$group_parent["level"]}'},
            success: function (id) {
                tpl = tpl.replace('{id}', id);
                $('#content').find('li:last').before(tpl);
                $('#content').find('li[data]:last input').focus();
            }
        });
        dirIndex++;
    });


    $('#content').find('li.new input').live('focus', function () {
        $(this).css('border-color', '#43aee4');
    });
    $('#content').find('li.new input').live('blur', function () {
        var val = $(this).val();
        if (val == '') {
            return;
        }
        $.post(SITEURL + 'image/group_manage', {action: 'rename', name: val, id: $(this).parents('li').attr('data')});
        $(this).css('border-color', '#ffffff');
    });
    //取消
    $('.cancel_btn').live('click', function () {
        ST.Util.closeBox();
    });
    //选择组
    $('#content').find('li img').live('click', function () {
        $(this).parents('li[data]').addClass('on').siblings('li').removeClass('on');
    });
    //
    $('.confirm_btn').click(function(){
        var pid=$('#content').find('li.on').attr('data');
        $.post(SITEURL + 'image/image_manage',{pid:pid,action:'move',id:$('#ids').val()},function(nums){
            ST.Util.responseDialog({pid:pid},nums>0?true:false);
        },'json');
    });

    $('li[data-url]').live('click',function(){
        window.location.href=SITEURL+$(this).attr('data-url');
    });
</script>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201707.2001&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
