
<div class="page" id="bag_way">
		<div class="header_top bar-nav">
	        <a class="back-link-icon"  href="#myWallet" data-rel="back"></a>
	        <h1 class="page-title-bar">提现</h1>
	    </div>
	    <!-- 公用顶部 -->
	    <div class="pick-up-wrap">
			<ul class="pick-up-group">
                {if in_array('bank',$way)}
                <li>
                    <a class="item-a" href="{$cmsurl}member/bag/withdraw?way=bank" data-reload="true">
                        <span class="item-hd">银行卡提现</span>
                        <i class="more-icon"></i>
                    </a>
                </li>
                {/if}

                {if in_array('alipay',$way)}
				<li>
					<a class="item-a" href="{$cmsurl}member/bag/withdraw?way=alipay" data-reload="true">
                    	<span class="item-hd">支付宝提现</span>
                    	<i class="more-icon"></i>
					</a>
                </li>
                {/if}
                {if in_array('weixin',$way)}
                <li>
                    <a class="item-a" href="{$cmsurl}member/bag/withdraw?way=weixin" data-reload="true">
                        <span class="item-hd">微信提现</span>
                        <i class="more-icon"></i>
                    </a>
                </li>
                {/if}

			</ul>
		</div>
    </div>

