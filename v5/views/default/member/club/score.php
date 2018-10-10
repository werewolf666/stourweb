<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{__('我的积分')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('header-club.css,club.css,base.css,extend.css',false)}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>
<body top_bottom=5WIwOs >
{request "member/club/header"}
<!-- header end -->
<div class="main-container grey-f7">
    <div class="wm-1200">
        <div class="blank-wrap">
            <h3 class="column-bar clearfix">
                <strong class="bar-tit">我的积分</strong>
            </h3>
            <div class="clearfix">
                <div class="jf-info">
                    <span class="ky-jf">可用积分：<em>{$default['member']['jifen']}</em></span>
                    {if St_Functions::is_normal_app_install('system_integral')}<a class="dh-link" href="/integral/">积分兑换&gt;</a>{/if}
                    <!--<span class="dq-date">到期时间：2017-01-01  00:00 </span>-->
                </div>
                <div class="record-wrap">
                    <div class="record-bar">
                        <a href="/member/club/score/"><span {if $default['type']==0}class="on"{/if}>所有记录</span></a>
                        <a href="/member/club/score/?type=2"><span {if $default['type']==2}class="on"{/if}>获取记录</span></a></a>
                        <a href="/member/club/score/?type=1"><span {if $default['type']==1}class="on"{/if}>使用积分</span></a>
                    </div>
                    <div class="record-box">
                        <div class="record-list"  style="display: block">
                            <table class="record-table">
                                <tr>
                                    <th width="20%">时间</th>
                                    <th width="15%">类型</th>
                                    <th width="15%">分值</th>
                                    <th width="50%">详情</th>
                                </tr>
                                {loop $result $v}
                                <tr>
                                    <td>{$v['addtime']}</td>
                                    <td>{$v['typeMsg']}</td>
                                    <td>{if $v['type']==1}<span class="xf">-{else}<span class="hq">{/if}{$v['jifen']}</span></td>
                                    <td><span class="ud">{$v['content']}</span></td>
                                </tr>
                                {/loop}
                            </table>
                            <div class="main_mod_page clear" id="page">
                                {$page}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 我的积分 -->
    </div>
</div>
<!-- 页面主体 -->
<!-- footer -->
{request "pub/footer"}
<!-- footer end -->
</body>
<script>
    $('#page').find('a[data]').click(function(){
        window.location.href=$(this).attr('data');
    });
</script>
</html>