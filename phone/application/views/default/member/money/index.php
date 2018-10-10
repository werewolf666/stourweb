
<div class="header_top bar-nav">
    <a class="back-link-icon"  href="#pageHome" data-rel="back"></a>
    <h1 class="page-title-bar">我的钱包</h1>
</div>
<!-- 公用顶部 -->
<div class="wallet-box">
    <div class="money">
        <p>现金余额</p>
        <p class="num">{Currency_Tool::symbol()}{php echo number_format($member['money']-$member['money_frozen'],2)}</p>
    </div>
    <div class="detail">
        <a href="{$cmsurl}member/bag/record">
            <i class="mx-ico"></i>
            <span class="txt">收支明细</span>
            <i class="more-ico"></i>
        </a>
    </div>
    <div class="btn">
        <a href="{$cmsurl}member/bag/way">
            <i class="tx-ico"></i>
            <span>提现</span>
        </a>
    </div>
</div>
