<div class="user-side-menu">
    <ul>
        <li><a id="nav_index"  href="/member"><i class="st-user-icon user-home-icon"></i>{__('会员首页')}</a></li>
        <li>
            <a class="side-menu-group" href="javascript:;">
                <i class="st-user-icon user-order-icon"></i>{__('我的订单')}<i class="arrow-icon"></i>
            </a>

            <div class="son">

                <a id="nav_allorder" href="/member/order/all">{__('全部订单')}</a>
                {loop Model_Model::get_used_model() $row}
                  {if $row['issystem']==1}
                     {php}
                         $order_url = '';

                         if(empty($row['is_install_model']))
                         {
                            $order_url = "/member/order/".$row['pinyin'];
                         }
                         else
                         {
                            $order_url = "/member/order/plugin_list?typeid=".$row['id'];
                         }
                     {/php}
                    <a id="nav_{$row['pinyin']}order" href="{$order_url}">{$row['modulename']}{__('订单')}</a>
                  {else}
                    <a id="nav_{$row['pinyin']}order" href="/member/order/tongyong?typeid={$row['typeid']}">{$row['modulename']}{__('订单')}</a>
                  {/if}
                {/loop}
                {if St_Functions::is_normal_app_install('system_integral')}
                <a id="nav_jifenorder" href="/member/order/plugin_list?typeid=107">{__('积分商城订单')}</a>
                {/if}
            </div>
        </li>
        {if St_Functions::is_normal_app_install('coupon')}
        <li><a id="nav_mycoupon" href="/member/coupon"><i class="st-user-icon user-coupon-icon"></i>{__('我的优惠券')}</a></li>
        {/if}

		 {if St_Functions::is_system_app_install(101)}
		   <li><a id="nav_mynotes" href="/notes/member/mynotes"><i class="st-user-icon user-yj-icon"></i>{__('我的游记')}</a></li>
         {elseif St_Functions::is_model_exist(101)}
            <li><a id="nav_mynotes" href="/member/index/mynotes"><i class="st-user-icon user-yj-icon"></i>{__('我的游记')}</a></li>
         {/if}
       
         {if St_Functions::is_system_app_install(11)}
		    <li><a id="nav_myjieban" href="/jieban/member/"><i class="st-user-icon user-jb-icon"></i>{__('我的结伴')}</a></li>
         {elseif St_Functions::is_model_exist(11)}
            <li><a id="nav_myjieban" href="/member/index/myjieban"><i class="st-user-icon user-jb-icon"></i>{__('我的结伴')}</a></li>
         {/if}
        {if St_Functions::is_system_app_install(106)}
            {request "member/guide/index/left_menu"}
        {/if}
        <li><a id="nav_myquestion"  href="/member/index/myquestion"><i class="st-user-icon user-zx-icon"></i>{__('我的咨询')}</a></li>
        <li><a id="nav_myjifen"  href="/member/club/score"><i class="st-user-icon user-jf-icon"></i>{__('我的积分')}</a></li>
        <li><a id="nav_money" href="/member/bag/index"><i class="st-user-icon user-qb-icon"></i>我的钱包</a></li>
        <li>

            <a class="side-menu-group" href="javascript:;">
                <i class="st-user-icon user-center-icon"></i>个人中心<i class="arrow-icon"></i>
            </a>
            <div class="son">
                <a id="nav_userinfo" href="/member/index/userinfo">{__('个人资料')}</a>
                <a id="nav_safecenter" href="/member/index/safecenter">{__('账号安全')}</a>
                <a id="nav_userbind" href="/member/index/userbind">{__('账号绑定')}</a>
                <a id="nav_consignees_address" href="/member/index/address">{__('收货地址')}</a>
            </div>
        </li>
        <li><a href="/member/index/linkman" id="nav_linkman"><i class="st-user-icon user-msg-icon"></i>{__('常用旅客')}</a></li>
        <li>
            <a class="side-menu-group" href="javascript:;">
                <i class="st-user-icon user-message-icon"></i>我的消息<i class="arrow-icon"></i>
            </a>
            <div class="son">
                <a href="/member/message/index" id="nav_message_index">系统消息</a>
            </div>
        </li>
    </ul>
</div>
<script>
    $(function(){
        $(".side-menu-group").on("click",function(){
            if( !$(this).hasClass("up") )
            {
                $(this).addClass("up").next(".son").addClass("hide");
            }
            else
            {
                $(this).removeClass("up").next(".son").removeClass("hide");
            }
        })
    })
</script>