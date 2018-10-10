<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>会员中心</title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css,mobilebone.css,swiper.min.css,certification.css')}
    {Common::js('lib-flexible.js')}
</head>
<body>
    <div id="pageHome" class="page out">
        <div class="header_top bar-nav">
            <a class="back-link-icon back-center" href="javascript:;"></a>
            <h1 class="page-title-bar">我的会员中心</h1>
            {if !empty($member)}
            <a class="header-message-tip {if $has_msg}new-msg{/if}" href="#sysMessage"></a>
            {/if}
        </div>
        <!-- 公用顶部 -->
        <div class="page-content">
            <div class="user-info-content">
                <div class="user-login-block">

                    
                    {if empty($member)}
                    <div class="user-login-before">
                        <a class="login-btn" href="{$cmsurl}member/login" data-ajax="false">登录</a>
                        <a class="reg-btn" href="{$cmsurl}member/register" data-ajax="false">注册</a>
                    </div>
                    {else}
                    <div class="user-login-after">
                        <a href="#myAccount">
                            <span class="user-hd-img"><img src="{$member['litpic']}" /></span>
                            <span class="user-name">{$member['nickname']}<i class="level">{$member['rank']}</i></span>
                        </a>
                    </div>
                    {/if}
                </div>
                <div class="user-shortcut-menu clearfix">
                    <a href="#myOrder" data-preventdefault="is_login" >
                        <i class="dd-icon icon"></i>
                        <em>全部订单</em>
                    </a>
                    <a href="#myOrder_needpay" data-preventdefault="is_login">
                        {if $member['number']['needpay']}
                            <i class="remind-num">{$member['number']['needpay']}</i>
                        {/if}
                        <i class="fk-icon icon"></i>
                        <em>待付款</em>
                    </a>
                    <a href="#myOrder_needconsume" data-preventdefault="is_login">
                        {if $member['number']['needconsume']}
                        <i class="remind-num">{$member['number']['needconsume']}</i>
                        {/if}
                        <i class="xf-icon icon"></i>
                        <em>待消费</em>
                    </a>
                    <a href="#myOrder_needcomment" data-preventdefault="is_login">
                        {if $member['number']['needcomment']}
                        <i class="remind-num">{$member['number']['needcomment']}</i>
                        {/if}
                        <i class="dp-icon icon"></i>
                        <em>待点评</em>
                    </a>
                </div>
            </div>
			{if !empty($member)}
             {if St_Functions::is_normal_app_install('mobiledistribution')}

			<div class="user-item-list">
                <ul class="list-group">
                    <li>
                        <a href="{$fx_url}"  data-ajax="false">
                            <i class="icon fxs-icon"></i>
                            <span class="txt"> {if !empty($fx)}分销商中心{else}成为分销商{/if}</span>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                </ul>
            </div>
			{/if}
			{/if}
            <!-- 用户信息 -->
            <div class="user-item-list">
                <ul class="list-group">
                    <li>
                        <a href="{$cmsurl}member/club" data-ajax="false">
                            <i class="icon jlb-icon"></i>
                            <span class="txt">会员俱乐部</span>
                            <em class="num"></em>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- 会员俱乐部 -->
            <div class="user-item-list">
                <ul class="list-group">
                    {if St_Functions::is_normal_app_install('coupon')}
                    <li>
                        <a href="{$cmsurl}member/coupon" data-preventdefault="is_login" >
                            <i class="icon yhq-icon"></i>
                            <span class="txt">我的优惠券</span>
                            <em class="num">{$member['number']['coupon']}</em>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                    {/if}

                    <li>
                        <a href="{$cmsurl}member/club#&{$cmsurl}member/club/score" data-ajax="false" data-preventdefault="is_login">
                            <i class="icon jf-icon"></i>
                            <span class="txt">我的积分</span>
                            {if St_Functions::is_normal_app_install('member_sign')}
                                {request "sign/check"}
                            {/if}
                            <em class="num">{$member['jifen']}</em>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>

                   <li>
                        <a href="#myWallet" data-preventdefault="is_login">
                            <i class="icon qb-icon"></i>
                            <span class="txt">我的钱包</span>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- 我的积分、优惠 -->
            <div class="user-item-list">
                <ul class="list-group">
                    <li>
                        <a href="#orderSeek">
                            <i class="icon dd-icon"></i>
                            <span class="txt">订单查询</span>

                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                    {if St_Functions::is_system_app_install(101)}
                    <li>
                        <a href="{$cmsurl}notes/member" data-preventdefault="is_login">
                            <i class="icon yj-icon"></i>
                            <span class="txt">我的游记</span>

                            <em class="num">{$member['number']['notes']}</em>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                    {/if}
                    {if St_Functions::is_system_app_install(11)}
                    <li>
                        <a href="{$cmsurl}jieban/member" data-preventdefault="is_login">
                            <i class="icon jb-icon"></i>
                            <span class="txt">我的结伴</span>
                            <em class="num">{$member['number']['jieban']}</em>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                    {/if}
                    <li>
                        <a href="{$cmsurl}member/consult/" data-preventdefault="is_login">
                            <i class="icon zx-icon"></i>
                            <span class="txt">我的咨询</span>
                            <em class="num">{$member['number']['question']}</em>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- 我的订单、发表 -->
            <div class="user-item-list">
                <ul class="list-group">
                    <li>
                        <a href="#myLinkman" data-preventdefault="is_login">
                            <i class="icon lk-icon"></i>
                            <span class="txt">我的常用旅客</span>
                            <i class="arrow-rig-icon"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- 我的常用旅客 -->
        </div>
    </div>
    <!-- 个人中心首页 -->
    <div id="myAccount" class="page out" data-url="{$cmsurl}member/account/index"  data-params="root=window&callback=callback_page"> </div>
    <!--个人资料-->
    <div id="editData" class="page out" data-url="{$cmsurl}member/account/edit"  data-params="root=window&callback=callback_page"></div>
    <!--绑定手机-->
    <div id="bindPhone" class="page out" data-url="{$cmsurl}member/account/phone"  data-params="root=window&callback=callback_page"></div>
    <!--绑定邮箱-->
    <div id="bindMailbox" data-url="{$cmsurl}member/account/email"  data-params="root=window&callback=callback_page" class="page out"></div>
    <!--修改登陆密码-->
    <div id="passWord" data-url="{$cmsurl}member/account/password"  data-params="root=window&callback=callback_page" class="page out"></div>
    <!--我的订单-->
    <div id="myOrder" data-url="{$cmsurl}member/order/list?type=-1"  data-params="root=window&callback=callback_page" class="page out"></div>
    <div id="myOrder_needpay" data-url="{$cmsurl}member/order/list?type=0"  data-params="root=window&callback=callback_page" class="page out"></div>
    <div id="myOrder_needconsume" data-url="{$cmsurl}member/order/list?type=1"  data-params="root=window&callback=callback_page" class="page out"></div>
    <div id="myOrder_needcomment" data-url="{$cmsurl}member/order/list?type=2"  data-params="root=window&callback=callback_page" class="page out"></div>
    <!--订单查询-->
    <div id="orderSeek" data-url="{$cmsurl}member/order/query"  data-params="root=window&callback=callback_page" class="page out"></div>
    <div id="myLinkman" data-url="{$cmsurl}member/linkman"  data-params="root=window&callback=callback_page" class="page out"></div>
    <!--我的钱包-->
    <div id="myWallet" data-url="{$cmsurl}member/bag/index"  data-params="root=window&callback=callback_page" class="page out"></div>
    <!--实名认证-->
    <div id="certification" data-url="{$cmsurl}member/account/certification" data-params="root=window&callback=callback_page" class="page out"></div>
    <!--收货地址-->
    <div id="receiveAddress" data-url="{$cmsurl}member/receive/address"  data-params="root=window&callback=callback_page" class="page out" style="overflow: scroll" ></div>

    <div id="sysMessage" data-url="{$cmsurl}member/message/index"  data-params="root=window&callback=callback_page" class="page out">

    </div>


    <input type="hidden" id="islogin" value="{$islogin}"/>
    <input type="hidden" id="memberid" value="{$member['mid']}"/>
    {Common::js('jquery.min.js,mobilebone.js,swiper.min.js,jquery.validate.min.js,jquery.layer.js,template.js,layer/layer.m.js')}
    <script type="text/javascript" src="http://{$GLOBALS['main_host']}/res/js/jquery.validate.addcheck.js"></script>
    <!--引入CSS-->
    <link rel="stylesheet" type="text/css" href="http://{$GLOBALS['main_host']}/res/js/webuploader/webuploader.css">
    <!--引入JS-->
    <script type="text/javascript" src="http://{$GLOBALS['main_host']}/res/js/webuploader/webuploader.min.js"></script>
    <link type="text/css" rel="stylesheet" href="{$cmsurl}public/mui/css/mui.picker.css" />
    <link type="text/css" rel="stylesheet" href="{$cmsurl}public/mui/css/mui.poppicker.css" />
    <script src="{$cmsurl}public/mui/js/mui.min.js"></script>
    <script src="{$cmsurl}public/mui/js/mui.picker.js"></script>
    <script src="{$cmsurl}public/mui/js/mui.poppicker.js"></script>
    <script src="{$cmsurl}public/mui/js/city.data-3.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var SITEURL = "{$cmsurl}";
        Mobilebone.evalScript = true;
        window.callback_page = function(pageInto, pageOut, response) {

              var contain_id = $(pageInto).attr('id');
              var url = $(pageInto).attr('data-url');
              $("#"+contain_id).load(url);
        };
        window.is_login = function(object){
            var login_status = parseInt($('#islogin').val());
            if(!login_status){
                window.location.href = "{$cmsurl}member/login"
                return true;
            }else{
                return false;
            }


        }
        $('.back-center').click(function(){
            window.location.href = SITEURL;
        })
        window.do_reload = function(){
            console.log($('#linkman-list').find('li').length);
        }
    </script>
{if St_Functions::is_normal_app_install('member_sign') && $member}
    {request "sign/index"}
{/if}
</body>

</html>

