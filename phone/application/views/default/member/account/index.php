<div class="header_top bar-nav">
    <a class="back-link-icon" href="#pageHome" data-rel="back"></a>
    <h1 class="page-title-bar">我的账户</h1>
</div>
<!-- 公用顶部 -->
<div class="page-content">
    <div class="user-item-list">
        <ul class="list-group">
            <li>
                <a href="#editData">
                    <strong class="hd-name">{$info['nickname']}</strong>
                    <span class="set-txt fr">修改</span>
                    <i class="arrow-rig-icon"></i>
                </a>
            </li>
            <li>
                <a href="#">
                    <strong class="hd-name">会员等级</strong>
                    <span class="set-txt fr">{$info['rank']}</span>
                    <i class="arrow-rig-icon"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="user-item-list">
        <ul class="list-group">
            <li>
                <a href="#bindPhone">
                    <strong class="hd-name">绑定手机</strong>
                    <span class="set-txt fr">{if $info['mobile']}{$info['mobile']}{else}请绑定手机号码{/if}</span>
                    <i class="arrow-rig-icon"></i>
                </a>
            </li>
            <li>
                <a href="#bindMailbox">
                    <strong class="hd-name">绑定邮箱</strong>
                    <span class="set-txt fr">{if $info['email']}{$info['email']}{else}请绑定邮箱{/if}</span>
                    <i class="arrow-rig-icon"></i>
                </a>
            </li>
            <li>
                <a href="#passWord">
                    <strong class="hd-name">修改登录密码</strong>
                    <i class="arrow-rig-icon"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="user-item-list">
        <ul class="list-group">
            <li>
                <a href="#receiveAddress">
                    <strong class="hd-name">收货地址</strong>
                    <i class="arrow-rig-icon"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="user-certification">
        <a class="clearfix" href="#certification">
            <strong class="hd-name">实名认证</strong>
            {if $info['verifystatus'] == 0}
            <span class="txt fr">未认证</span>
            {elseif $info['verifystatus'] == 1}
            <span class="txt fr">审核中</span>
            {elseif $info['verifystatus'] == 2}
            <span class="txt fr"><em>{$info['truename']}<i class="hd"></i></em>已认证</span>
            {else}
            <span class="txt fr">未通过</span>
            {/if}
            <i class="arrow-rig-icon"></i>
        </a>
    </div>
    <a class="drop-out-btn" href="{$cmsurl}member/login/ajax_out" data-ajax="false">退出登录</a>
</div>
