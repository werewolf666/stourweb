<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title> {if $ordertype=='all'}全部{$modulename}订单{elseif $ordertype=='unpay'}未付款{$modulename}订单{else}未点评{$modulename}订单{/if}-{$webname}</title>
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js')}
</head>

<body>

{request "pub/header"}

<div class="big">
    <div class="wm-1200">

        <div class="st-guide">
            <a href="{$cmsurl}">首页</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{if $ordertype=='all'}全部订单{elseif $ordertype=='unpay'}未付款订单{else}未点评订单{/if}
        </div><!--面包屑-->

        <div class="st-main-page">

            {include "member/left_menu"}

            <div class="user-order-box">
                <div class="user-home-box">
                    <div class="tabnav">
                        <span {if $ordertype=='all'}class="on"{/if} data-type="all">全部订单</span>
                        <span {if $ordertype=='unpay'}class="on"{/if} data-type="unpay">未付款订单</span>
                        <span {if $ordertype=='uncomment'}class="on"{/if} data-type="uncomment">未点评订单</span>
                    </div><!-- 订单切换 -->
                    <div class="user-home-order">
                        {if !empty($list)}
                        <div class="order-list">
                            <table width="100%" border="0">
                                <tr>
                                    <th width="50%" height="38" bgcolor="#fbfbfb" scope="col">订单信息</th>
                                    <th width="10%" height="38" bgcolor="#fbfbfb" scope="col">使用日期</th>
                                    <th width="8%" height="38" bgcolor="#fbfbfb" scope="col">数量</th>
                                    <th width="8%" height="38" bgcolor="#fbfbfb" scope="col">订单金额</th>
                                    <th width="12%" height="38" bgcolor="#fbfbfb" scope="col">订单状态</th>
                                    <th width="12%" height="38" bgcolor="#fbfbfb" scope="col">订单操作</th>
                                </tr>
                                {loop $list $row}
                                <tr>
                                    <td height="114">
                                        <div class="con">
                                            <dl>
                                                <dt><a href="{$row['producturl']}" target="_blank"><img src="{Common::img($row['litpic'],110,80)}" alt="{$row['productname']}" /></a></dt>
                                                <dd>
                                                    <a class="tit" href="{$row['producturl']}" target="_blank">{$row['productname']}</a>
                                                    <p>订单编号：{$row['ordersn']}</p>
                                                    <p>下单时间：{Common::mydate('Y-m-d H:i:s',$row['addtime'])}</p>
                                                </dd>
                                            </dl>
                                        </div>
                                    </td>
                                    <td align="center"><span class="attr">{$row['usedate']}</span></td>
                                    <td align="center"><span class="attr">{$row['dingnum']}</span></td>
                                    <td align="center"><span class="price"><i class="currency_sy">{Currency_Tool::symbol()}</i>{$row['totalprice']}</span></td>
                                    <td align="center"><span class="dfk">{$row['statusname']}</span></td>
                                    <td align="center">



                                        {if $row['status']=='1'}
                                        <a class="now-fk" href="{$cmsurl}member/index/pay?ordersn={$row['ordersn']}">立即付款</a>
                                        <a class="cancel_order now-dp" style="background:#ccc" href="javascript:;" data-orderid="{$row['id']}">取消</a>


                                        {elseif $ordertype=='unpay'}
                                        <a class="now-fk" href="{$cmsurl}member/index/pay?ordersn={$row['ordersn']}">立即付款</a>
                                        <a class="cancel_order now-dp" style="background:#ccc" href="javascript:;" data-orderid="{$row['id']}">取消</a>

                                        {elseif $row['ispinlun']=='0' && $row['status'] == '5'}
                                        <a class="now-dp" href="{$cmsurl}member/order/pinlun?ordersn={$row['ordersn']}">立即点评</a>
                                        {/if}

                                        <a class="order-ck" href="{$cmsurl}member/order/view?ordersn={$row['ordersn']}">查看订单</a>

                                    </td>
                                </tr>
                                {/loop}
                            </table>
                            <div class="main_mod_page clear">
                                {$pageinfo}
                            </div><!-- 翻页 -->
                        </div>
                        {else}
                        <div class="order-no-have"><span></span><p>没有查到相关订单信息，<a href="/" target="_blank">去逛逛</a>去哪儿玩吧！</p></div>
                        {/if}
                    </div><!-- 我的订单 -->
                </div>
            </div><!--所有订单-->

        </div>

    </div>
</div>
{Common::js('layer/layer.js')}
{request "pub/footer"}
<script>
    $(function(){

        //导航选中
        var modelpy = "{$modulepinyin}";

        var typeid = "{$typeid}";
        //导航选中
        $('#nav_'+modelpy+'order').addClass('on');


        //订单类型切换
        $(".tabnav span").click(function(){
            var orderType = $(this).attr('data-type');
            var url = SITEURL+'member/order/tongyong-'+orderType+'?typeid='+typeid;
            window.location.href = url;
        })

    })
</script>
{include "member/order/jsevent"}
</body>
</html>
