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
                <strong class="bar-tit">我的等级</strong>
            </h3>
            <div class="my-level-wrap clearfix">
                <div class="lw-user-info">
                    <div class="hd">
                        <img src="{$member['litpic']}" width="84" height="84" />
                        <span>Hi，<strong>{$member['nickname']}</strong></span>
                        <em>{Common::member_rank($member['mid'],array('return'=>'current'))}</em>
                    </div>
                    <div class="bd">
                        <span>会员等级：{Common::member_rank($member['mid'],array('return'=>'rankname'))}</span>
                        <span>成长值：<em>{$grade['jifen']}</em></span>
                    </div>
                </div>
                <div class="lw-grow-up">
                    <div class="grow-up-tit">{if !is_null($grade['nextGrade'])}我的成长值：+<em>{$grade['nextGrade']['poor']}</em>即可成为{$grade['nextGrade']['name']}哦！{/if}</div>
                    <div class="actual clearfix">
                        <ul>
                            <ul>
                                {loop $grade['grade'] $k $v}
                                <li class="{if $n==$grade['total']}member-vip05 {/if} {if $k<$grade['current']}item-current {/if}">
                                    <em class="level-num">Lv.{$n}</em>
                                    <span class="level-txt">{$v['name']}</span>
                                    <span class="level-bar " {if isset($v['per'])}id="position" style="width:{$v['per']}%"{/if}></span>
                                </li>
                                {/loop}
                            </ul>
                            <i class="vip-notice" style="left:{$grade['process']}px"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- 我的等级 -->

        <div class="blank-wrap mt15">
            <h3 class="column-bar clearfix">
                <strong class="bar-tit">会员等级</strong>
            </h3>
            <div class="level-wrap">
                <p class="level-txt">会员共分{count($grade['grade'])}个等级，会员等级由“成长值”决定，经验值越高，会员等级越高。</p>
                <table class="record-table">
                    <tr>
                        <th>会员等级</th>
                        {loop $grade['grade'] $v}
                        <th>{$v['name']}</th>
                        {/loop}
                    </tr>
                    <tr>
                        <td>分值等级</td>
                        {php}$index=0;{/php}
                        {loop $grade['grade'] $i $v}
                        <td><span class="dl-num">Lv.{php} echo ++$index;{/php}</span></td>
                        {/loop}
                    </tr>
                    <tr>
                        <td>积分分值</td>
                        {loop $grade['grade'] $v}
                        <td>{$v['begin']}~{$v['end']}</td>
                        {/loop}
                    </tr>
                </table>
            </div>
        </div>
        <!-- 会员等级 -->

    </div>
</div>
<!-- 页面主体 -->
<!-- footer -->
{request "pub/footer"}
<!-- footer end -->
</body>
</html>