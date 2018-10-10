<div id="myAccount" class="page out">
    <div class="header_top bar-nav">
        <a class="back-link-icon" href="#&clubHome"></a>
        <h1 class="page-title-bar">赚取积分</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <div class="eranScore-hd">
            <a class="user-center">
                <img class="user-hd-img" src="{$member['litpic']}" alt="" title="" />
                <div class="txt">
                    <span class="name">{$member['nickname']}</span>
                    <p class="num">
                        <i class="ico"></i>
                        <span>可用积分</span>
                        <em>{$member['jifen']}</em>
                    </p>
                </div>
            </a>
        </div>

        <div class="task-nav">
            <ul class="clearfix">
                <li class="on"><a href="#" data="task-daily">日常任务</a></li>
                <li ><a href="#" data="new-daily">新手任务</a></li>
            </ul>
        </div>
        <div class="task-list">
            <div class="task-daily">
                <ul>
                    {if !empty($daily_strategy['sys_member_login'])}
                    <li class="clearfix">
                        <i class="ico ico-1"></i>
                        <strong>登录送积分</strong>
                        <span>每天登录一次获赠{$daily_strategy['sys_member_login']['value']}积分</span>
                    </li>
                    {/if}
                    {if St_Functions::is_normal_app_install('member_sign')}
                    <li class="clearfix">
                        <i class="ico ico-2"></i>
                        <strong>我要签到</strong>
                        <span>每天签到获赠更多积分</span>
                    </li>
                    {/if}
                    <li class="clearfix">
                        <i class="ico ico-3"></i>
                        <strong>预订产品</strong>
                        <span>成功预定产品可获赠积分</span>
                    </li>
                    <li class="clearfix">
                        <i class="ico ico-4"></i>
                        <strong>评论产品</strong>
                        <span>成功评论可获赠积分</span>
                    </li>
                    {if St_Functions::is_normal_app_install('system_notes') && !empty($daily_strategy['sys_write_notes'])}
                    <li class="clearfix">
                        <i class="ico ico-5"></i>
                        <strong>发布游记</strong>
                        <span>发表一篇可获得{$daily_strategy['sys_write_notes']['value']}积分</span>
                    </li>
                    {/if}
                    {if St_Functions::is_normal_app_install('system_jieban') && !empty($daily_strategy['sys_write_jieban'])}
                    <li class="clearfix">
                        <i class="ico ico-6"></i>
                        <strong>发布结伴</strong>
                        <span>发布一次结伴可获赠{$daily_strategy['sys_write_jieban']['value']}积分</span>
                    </li>
                    {/if}
                    {if !empty($daily_strategy['sys_write_wenda'])}
                    <li class="clearfix">
                        <i class="ico ico-7"></i>
                        <strong>提交问题</strong>
                        <span>成功提问一次可获赠{$daily_strategy['sys_write_wenda']['value']}积分</span>
                    </li>
                    {/if}
                </ul>
            </div>
            <div class="new-daily" style="display: none;">
                <ul>
                    {loop $strategy $s}
                    <li>
                        <i class="ico ico-{$newer[$s['label']['icon']]}"></i>
                        <strong>{$s['title']}</strong>
                        <span class="num">+{$s['value']}</span>
                        {if isset($newer[$s['label']]['complete'])}
                        {if $newer[$s['label']]['complete']}
                        <span class="state"><a>已完成</a></span>
                        {else}
                        <span class="state"><a class="receive" href="#{$newer[$s['label']]['bind']}">领取任务</a></span>
                        {/if}
                        {/if}
                    </li>
                    {/loop}
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    $('.task-nav').find('li a').click(function(){
        var data=$(this).attr('data');
        $(this).parent().addClass('on').siblings().removeClass('on');
        $('.'+data).css('display','block').siblings('div').css('display','none');
    });
</script>