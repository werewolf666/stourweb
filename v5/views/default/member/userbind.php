<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>账号绑定-{$webname}</title>
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>

<body>

{request "pub/header"}
<div class="big">
    <div class="wm-1200">

        <div class="st-guide">
            <a href="{$cmsurl}">首页</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;账号绑定
        </div><!--面包屑-->

        <div class="st-main-page">
            {include "member/left_menu"}

            <div class="user-cont-box">
                <div class="safe-center">
                    <h3 class="aq-tit">账号绑定/解绑</h3>
                    <ul class="bind-con">
                        {loop $info['third'] $k $row}
                        <li>
                            <div class="part1"><span class="bind-{$row['alias']}">{$row['name']}</span></div>
                            {if isset($row['id'])}
                            <div class="part2"><span class="on-bind">{if !empty($row['nickname'])}<strong>已绑定到{$row['name']}@{$row['nickname']}</strong>{/if}</span></div>
                            {if !empty($info['email'])|| ! empty($info['mobile'])}
                            <div class="part3"><a class="clear-bind" href="javascript:" data="/member/index/ajax_userunbind?id={$row['id']}" bind="{Common::get_web_url(0)}/plugins/login_{$k}/index/index/?refer={urlencode(Common::get_web_url(0).'/member/index/userbind')}">解除绑定&gt;</a></div>
                            {/if}
                            {else}
                            <div class="part2"><span class="un-bind">未绑定</span></div>
                            <div class="part3"><a class="now-bind" href="{Common::get_web_url(0)}/plugins/login_{$k}/index/index/?refer={urlencode(Common::get_web_url(0).'/member/index/userbind')}">马上绑定</a></div>
                            {/if}
                        </li>
                        {/loop}
                    </ul>
                </div><!--账号绑定-->
            </div>

        </div>

    </div>
</div>
{request "pub/footer"}
{Common::js('layer/layer.js')}
<script>
    $(function(){
        //导航选中
        $('#nav_userbind').addClass('on');
        //解除绑定
        $('.clear-bind').click(function(){
            var url=$(this).attr('data');
            var href=$(this).attr('bind');
            var node=$(this);
            $.get(url,{},function(rs){
                if(rs){
                    layer.msg("成功解除绑定!",{
                        icon:6,
                        time:1000
                    })
                    node.attr({'href':href,'class':'now-bind'}).text('马上绑定');
                    node.parents('li').find('.part2').html('<span class="un-bind">未绑定</span>');
                }
            },'json');
        });
        {if !empty($thirdBindMsg)}
            layer.msg('{$thirdBindMsg}',{
                icon:5,
                time:3000
            })
        {/if}
    })
</script>
</body>
</html>
