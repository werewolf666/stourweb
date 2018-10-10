<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{__('赚取积分')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('header-club.css,club.css,base.css,extend.css',false)}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>
<body>
{request "member/club/header"}
<!-- header end -->
<div class="main-container grey-f7">
    <div class="wm-1200">

        <div class="blank-wrap">
            <h3 class="column-bar clearfix">
                <strong class="bar-tit">日常任务</strong>
            </h3>
            <div class="daily-tasks">
                <ul class="clearfix">
                    {if St_Functions::is_normal_app_install('system_notes')}
                    <li>
                        <a>
                            <i class="icon yj-hd"></i>
                            <em>发布游记</em>
                        </a>
                    </li>
                    {/if}
                    <li>
                        <a>
                            <i class="icon yd-hd"></i>
                            <em>预订产品</em>
                        </a>
                    </li>
                    <li>
                        <a>
                            <i class="icon dl-hd"></i>
                            <em>登录送分</em>
                        </a>
                    </li>
                    {if St_Functions::is_normal_app_install('system_jieban')}
                    <li>
                        <a>
                            <i class="icon jb-hd"></i>
                            <em>发布结伴</em>
                        </a>
                    </li>
                    {/if}
                    <li>
                        <a>
                            <i class="icon pl-hd"></i>
                            <em>评论产品</em>
                        </a>
                    </li>
                    <li>
                        <a>
                            <i class="icon wt-hd"></i>
                            <em>提交问题</em>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 日常任务 -->

        <div class="blank-wrap mt25">
            <h3 class="column-bar clearfix">
                <strong class="bar-tit">新手任务</strong>
            </h3>
            <div class="tasks-content">
                <table class="tasks-table">
                    <tr>
                        <th width="20%">获得方式</th>
                        <th width="20%">获得积分</th>
                        <th width="40%">说明</th>
                        <th width="20%">操作</th>
                    </tr>
                    {loop $default['newerTask'] $v}
                    <tr>
                        <td>{$v['title']}</td>
                        <td><span class="jf-num">+{$v['value']}</span></td>
                        <td><span class="js-txt">{$v['title']}，即可获得获赠积分</span></td>
                        <td>{if $v['noFirst']}<a class="tasks-end">已完成</a>{else}<a class="tasks-star" href="{if in_array($v['label'],array('sys_member_bind_qq','sys_member_bind_sina_weibo','sys_member_bind_weixin'))}/member/index/userbind{else}/member/index/userinfo{/if}">领取任务</a>{/if}</td>
                    </tr>
                    {/loop}
                </table>
            </div>
        </div>
        <!-- 新手任务 -->
    </div>
</div>
<!-- footer -->
{request "pub/footer"}
<!-- footer end -->
</body>
</html>