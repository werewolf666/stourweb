<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途旅游CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,index_5.css,home.css,base_new.css'); }
</head>

<body style="width: 670px;height: 383px;overflow: hidden">
<div class="shortcut-nav-content">
    <div class="shortcut-nav-block clearfix">
        <div class="sum-nav-block fl">
            <h3>系统功能</h3>
            <div class="nav-list" id="menu_nav">
                <ul>
                    {loop $menu $levelOne}
                    <li>
                        <a class="wrap-next pl25" href="javascript:;">{$levelOne['title']}<i class="arrow-ico"></i></a>
                        <ul class="hide">
                            {loop $levelOne['son'] $levelTwo}
                            <li>
                                {if isset($levelTwo['son'])}
                                <ul>
                                    <li>
                                        <a class="wrap-next pl38" href="javascript:;">{$levelTwo['title']}<i class="arrow-ico"></i></a>

                                        <ul class="hide">
                                            {loop $levelTwo['son'] $levelThree}
                                            <li>
                                                {if isset($levelThree['son'])}
                                                <a class="wrap-next pl52" href="javascript:;">{$levelThree['title']}<i class="arrow-ico"></i></a>
                                                <ul class="hide">
                                                    {loop $levelThree['son'] $levelFour}
                                                    <li><label class="check pl66"><input name="checkboxed" type="checkbox" value="{$levelFour['id']}" />{$levelFour['title']}</label></li>
                                                    {/loop}
                                                </ul>
                                                {else}
                                                <label class="check pl52"><input name="checkboxed" type="checkbox" value="{$levelThree['id']}" />{$levelThree['title']}</label>
                                                {/if}
                                            </li>
                                            {/loop}
                                        </ul>
                                    </li>
                                </ul>
                                {else}
                                <label class="check pl38"><input name="checkboxed" type="checkbox" value="{$levelTwo['id']}" />{$levelTwo['title']}</label>
                                {/if}
                            </li>
                            {/loop}
                        </ul>
                    </li>
                    {/loop}
                </ul>
            </div>
        </div>
        <div class="change-block">
            <a class="btn btn-grey-outline radius add-menu" href="javascript:;">添加&gt;&gt;</a>
            <a class="btn btn-grey-outline radius mt-20 del-menu" href="javascript:;">&lt;&lt;移除</a>
        </div>
        <div class="sum-nav-block fr">
            <h3>常用菜单</h3>
            <div class="nav-list">
                <ul id="quick_nav" class="item-list">
                    {loop $quickmenu $v}
                    <li menu_id="{$v['menu'][0]}">
                        <label class="check pl15"><input name="checkboxed" type="checkbox" value="{$v['menu'][0]}" />{$v['menu'][1]}<em class="path">{$v['path']}</em></label>
                    </li>
                    {/loop}
                </ul>
            </div>
        </div>
    </div>
    <div class="clearfix mt-30 f-0 text-c">
        <a class="btn btn-grey-outline radius" href="javasctipt:;" id="cancel">取消</a>
        <a class="btn btn-primary radius ml-20" href="javasctipt:;" id="submit">确定</a>
    </div>
</div>
<!-- 快捷菜单添加 -->
<script>
    $(".wrap-next").toggle(function(){
        $(this).next("ul").removeClass('hide')
        $(this).next("ul").slideDown(200);

        $(this).children("i").addClass("up")
    },function(){
        $(this).next("ul").addClass('hide')
        $(this).next("ul").slideUp(200);

        $(this).children("i").removeClass("up")
    })
    //添加快捷菜单
    $('.add-menu').click(function(){
        var id=[];
       $('#menu_nav').find('input[name="checkboxed"]:checked').each(function(){
            id.push($(this).val());
        })
        $.post( SITEURL +'quickmenu/ajax_parent',{data:id.join(',')},function(data){
            show_menu(data);
        },'json')
    });

    function show_menu(quickMenu){
        var html='';
        for (var i in quickMenu){
            var exists=$('#quick_nav').find('li[menu_id="'+quickMenu[i]['menu'][0]+'"]').length;
            if(!exists){
                html+='<li menu_id="'+quickMenu[i]['menu'][0]+'">';
                html+='<label class="check pl15"><input name="checkboxed" type="checkbox" value="'+quickMenu[i]['menu'][0]+'" />'+quickMenu[i]['menu'][1]+'<em class="path">'+quickMenu[i]['path']+'</em></label>';
                html+='</li>'
            }
        }
        $('#quick_nav').append(html);
    }
    $('.del-menu').click(function(){
        $('#quick_nav').find('input[name="checkboxed"]:checked').each(function(){
            $(this).parents('li').remove();
        })
    });
    //取消
    $('#cancel').live('click', function () {
        ST.Util.closeBox();
    });
    //提交
    $('#submit').live('click', function () {
        var Y=this;
        var id=[];
        $('#quick_nav').find('input[name="checkboxed"]').each(function(){
            id.push($(this).val());
        })
        $.ajax({
            url : SITEURL +'quickmenu/ajax_save',
            type : 'post',
            async: false,
            dataType:"json",
            data : {data:id.join(',')},
            success : function(data){
                Y.param=data;
            }
        });
        ST.Util.responseDialog(Y.param);
    });
</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2304&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
