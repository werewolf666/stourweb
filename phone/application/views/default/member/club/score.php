
    <div class="header_top bar-nav">
        <a class="back-link-icon" href="#clubHome" data-rel="back"></a>
        <h1 class="page-title-bar">我的积分</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <div class="score-hd">
            <a class="user-center" href="{$cmsurl}member/index" data-ajax="false">
                <img class="user-hd-img" src="{$member['litpic']}" alt="" title="" />
                <span>{$member['nickname']}</span>
            </a>
            <p class="num">
                <i class="ico"></i>
                <span>可用积分</span>
                <em>{$member['jifen']}</em>
            </p>
            <!--<p class="date">到期时间：2017-01-01  00:00 </p>-->
        </div>
        {if St_Functions::is_system_app_install(107)}
        <div class="score-link">
            <ul class="clearfix">
                <li>
                    <a data-ajax="false" href="{$cmsurl}integral">
                        <i class="ico-1"></i>
                        <span>积分兑换</span>
                    </a>
                </li>
                <li>
                    <a href="{$cmsurl}member/club/member_task">
                        <i class="ico-2"></i>
                        <span>赚取积分</span>
                    </a>
                </li>
            </ul>
        </div>
        {else}
        <div class="score-link score-link-one">
            <ul class="clearfix">
                <li>
                    <a href="{$cmsurl}member/club/member_task">
                        <i class="ico-2"></i>
                        <span>赚取积分</span>
                    </a>
                </li>
            </ul>
        </div>
        {/if}

        <div class="score-list">
            <a class="det" href="{$cmsurl}member/club/score_detail">
                <span>积分明细</span>
                <i class="arrow-rig-icon"></i>
            </a>
            <ul>
                {loop $log $v}
                <li>
                    <div class="txt">
                        <strong class="name">{$v['content']}</strong>
                        <span class="date">{date('Y-m-d H:i',$v['addtime'])}</span>
                    </div>
                    <div class="num">
                        {if $v['type']==1}
                        <p class="green">-{$v['jifen']}</p>
                        {else}
                        <p class="red">+{$v['jifen']}</p>
                        {/if}
                    </div>
                </li>
                {/loop}
            </ul>
        </div>

    </div>
