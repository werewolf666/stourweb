<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{__('会员中心')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css',false)}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>
<body>
{request "pub/header"}
<div class="big">
<div class="wm-1200">

<div class="st-guide">
    <a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('会员中心')}
</div><!--面包屑-->
<div class="st-main-page">
{include "member/left_menu"}
<div class="user-order-box">
    <div class="user-home-box">
        {if (empty($info['email']) || empty($info['mobile'])||empty($info['nickname'])||empty($info['truename'])||empty($info['cardid'])||empty($info['address']))}
        <div class="hint-msg-box">
            <span class="close-btn">{__('关闭')}</span>
            <p class="hint-txt">
                {if empty($info['email']) || empty($info['mobile'])}
                {__('温馨提示：请完善<a href="/member/index/userinfo">手机/邮箱</a>信息，避免错过产品预定联系等重要通知!')}
                {elseif (empty($info['nickname'])||empty($info['truename'])||empty($info['cardid'])||empty($info['address']))}
                {__('温馨提醒：请完善<a href="/member/index/userinfo">个人资料</a>信息，体验更便捷的产品预定流程！')}
                {/if}
            </p>
        </div>
        <script>
            $(function(){
                $('.close-btn').click(function(){
                    $('.hint-msg-box').hide(500);
                })
            })
        </script>
        {/if}
        <div class="user-home-msg">
            <div class="user-msg-con">
                <div class="user-pic"><div class="level"><a href="/member/club/rank">{Common::member_rank($info['mid'],array('return'=>'current'))}</a></div><a href="/member/index/userinfo"><img src="{$info['litpic']}" width="118" height="118" /></a></div>
                <div class="user-txt">
                    <p class="name">{$info['nickname']}</p>
                    <p class="item-bar">{__('会员等级')}：{Common::member_rank($info['mid'],array('return'=>'rankname'))}</p>
                    <p class="item-bar">{__('登录邮箱')}：
                        {if $info['email']}{$info['email']}{else}{__('未绑定')}<a class="rz-no" href="{$cmsurl}member/index/modify_email?change=0">{__('立即绑定')}</a>{/if}</p>
                    <p class="item-bar">{__('手机号码')}：
                        {if $info['mobile']}{$info['mobile']}{else}{__('未绑定')}<a class="rz-no" href="{$cmsurl}member/index/modify_phone?change=0">{__('立即绑定')}</a>{/if}</p>
                    <p class="item-bar">真实姓名：
                        {if $info['verifystatus']==2}{$info['truename']}{else}{if $info['verifystatus']==1}审核中 {elseif  $info['verifystatus']==3}审核失败 {else}未实名{/if}
                        <a class="rz-no" href="{$cmsurl}member/index/modify_idcard">实名认证</a>{/if}</p>
                </div>
            </div><!-- 账号信息 -->
            <div class="user-msg-right">
                <div class="user-msg-tj">
                <ul class="clearfix">
                    <li class="my-jf" data-url="/member/order/all-unpay">
                        <em></em>
                        <span>{__('未付款')}</span>

                    </li>
                    <li class="un-fk" data-url="/member/order/all-uncomment">
                        <em></em>
                        <span>{__('未点评')}</span>

                    </li>
                    <li class="un-dp" data-url="/member/index/myquestion">
                        <em></em>
                        <span>{__('我的咨询')}</span>
                    </li>
                    {if St_Functions::is_normal_app_install('system_integral')}
                    <li class="my-sc" data-url="/integral">
                        <em></em>
                        <span>积分商城</span>
                    </li>
                    {/if}
                    {if St_Functions::is_normal_app_install('integral_award')}
                    <li class="my-hd" data-url="/award">
                        <em></em>
                        <span>积分活动</span>
                    </li>
                    {/if}
                </ul>
            </div><!-- 订单信息 -->
                <div class="user-info-exchange">
                <ul class="clearfix">
                    <li><em>我的余额：</em><strong>{Currency_Tool::symbol()}{php echo $info['money']-$info['money_frozen']}</strong></li>

                    <li><em>我的积分：</em><strong>{$info['jifen']}</strong></li>
<!--                    <li><em>我的余额：</em><strong>¥6525</strong></li>-->
                    {if isset($info['coupon'])}
                    <li class="last-li"><em>优惠券：</em><strong>{$info['coupon']}张</strong></li>
                    {/if}
                </ul>
            </div>
            </div>

        </div>
        <div class="user-home-order">
            <div class="order-tit">{__('最新订单')}<a class="more" href="/member/order/all">查看更多&gt;</a></div>
            {if !empty($neworder)}
            <div class="order-list">
                <table width="100%" border="0">
                    <tr>
                        <th width="55%" height="38" scope="col">{__('订单信息')}</th>
                        <th width="15%" height="38" scope="col">{__('订单金额')}</th>
                        <th width="15%" height="38" scope="col">{__('订单状态')}</th>
                        <th width="15%" height="38" scope="col">{__('订单操作')}</th>
                    </tr>
                    {loop $neworder $order}
                    <tr>
                        <td height="114">
                            <div class="con">
                                <dl>
                                    <dt><a href="{if $order['is_standard_product']}{$order['producturl']}{else}{$cmsurl}member/order/view?ordersn={$order['ordersn']}{/if}" target="_blank"><img src="{Common::img($order['litpic'],110,80)}" width="110" height="80" alt="{$order['title']}" /></a></dt>
                                    <dd>
                                        <a class="tit" href="{if $order['is_standard_product']}{$order['producturl']}{else}{$cmsurl}member/order/view?ordersn={$order['ordersn']}{/if}" target="_blank">{$order['productname']}</a>
                                        <p>{__('订单编号')}：{$order['ordersn']}</p>
                                        <p>{__('下单时间')}：{Common::mydate('Y-m-d H:i:s',$order['addtime'])}</p>
                                    </dd>
                                </dl>
                            </div>
                        </td>
                        {if $order['typeid']!=107}
                        <td align="center"><span class="price"><i class="currency_sy">{Currency_Tool::symbol()}</i>{$order['totalprice']}</span></td>
                        {else}
                        <td align="center"><span class="price">{$order['needjifen']}&nbsp;积分</span></td>
                        {/if}
                        <td align="center"><span class="dfk">{$order['statusname']}</span></td>
                        <td align="center">


                            {if $order['status']=='1'&&$order['pid']==0}
                            <a class="now-fk" href="{$cmsurl}member/index/pay?ordersn={$order['ordersn']}">{__('立即付款')}</a>
                            <a class="cancel_order now-dp" style="background:#ccc" href="javascript:;" data-orderid="{$order['id']}">{__('取消')}</a>
                            {elseif $order['status']=='5' && $order['ispinlun']!=1 && $order['is_commentable']}
                             <a class="now-dp" href="{$cmsurl}member/order/pinlun?ordersn={$order['ordersn']}">{__('立即点评')}</a>
                            {/if}

                            <a class="order-ck" href="{$cmsurl}member/order/view?ordersn={$order['ordersn']}">{__('查看订单')}</a>


                        </td>
                    </tr>
                    {/loop}

                </table>
            </div>
            {else}
                <div class="order-no-have"><span></span><p>{__('您的订单空空如也')}，<a href="/">{__('去逛逛')}</a>{__('去哪儿玩吧')}！</p></div>
            {/if}
        </div><!-- 我的订单 -->
        <div class="guess-you-like">
            <div class="like-tit">{__('猜你喜欢的')}</div>
            <div class="like-list">
                <ul>

                     {st:line action="query" flag="order" row="4" return="recline"}
                        {loop $recline $line}
                        <li {if $n%4==0}class="mr_0"{/if}>
                            <div class="pic"><a href="{$line['url']}" target="_blank"><img src="{Common::img($line['litpic'])}" alt="{$line['title']}" /></a></div>
                            <div class="con">
                                <a href="{$line['url']}" target="_blank">{$line['title']}</a>
                                <p>
                                    {if !empty($line['sellprice'])}
                                    <del>{__('市场价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i>{$line['sellprice']}</del>
                                    {/if}
                                    {if !empty($line['price'])}
                                        <span><i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$line['price']}</b>{__('元')}{__('起')}</span>
                                    {else}
                                        <span>{__('电询')}</span>
                                    {/if}
                                </p>
                            </div>
                        </li>
                       {/loop}
                </ul>
            </div>
        </div><!-- 猜你喜欢的 -->
    </div>
</div><!--会员首页-->

</div>

</div>
</div>
{Common::js('layer/layer.js')}
{request "pub/footer"}
<script>
    $(function(){
        $("#nav_index").addClass('on');

        $(".user-msg-tj li").click(function(){
            var url = $(this).attr('data-url');
            if(url!=''){
                location.href = url;
            }
        })


    })
</script>
{include "member/order/jsevent"}
</body>
</html>
