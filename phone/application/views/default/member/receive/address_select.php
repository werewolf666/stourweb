<div class="write-address">
    <strong>填写收货地址</strong>
    <i class="check-box" name="selectAddress"></i>
</div>
<!-- 填写收货地址 -->
{if $address}
<div class="address-have hide" id="selectAddress">
    <a class="address-item" href="{$cmsurl}member/receive/select_list">
        <i class="ads-ico"></i>
        <div class="mid-nr">
            <p class="msg clearfix">
                <span class="fl">{$address['receiver']}</span>
                <span class="fr">{$address['phone']}<em class="mr">默认</em></span>
            </p>
            <p class="txt">{$address['province']}{$address['city']}{$address['address']} {$address['postcode']}</p>
        </div>
        <i class="more-ico"></i>
    </a>
</div>
<script>
    $(function(){
        address.id={$address['id']};
    });
</script>
<!-- 有收货地址 -->
{else}
<div class="address-none hide" id="selectAddress">
    <a class="address-tr" href="{$cmsurl}member/receive/select_list">
        <i class="ads-ico"></i>
        <span class="txt">没有可用地址，添加收货地址</span>
        <i class="more-ico" ></i>
    </a>
</div>
<!-- 无收货地址 -->
{/if}
<script>
    var address={id:0};
    function selectAddress(){
        var html='<a class="address-item" href="{$cmsurl}member/receive/select_list">';
        html+='<i class="ads-ico"></i>';
        html+='<div class="mid-nr">';
        html+='<p class="msg clearfix">';
        html+='<span class="fl">'+arguments[1][0]+'</span>';
        html+='<span class="fr">'+arguments[1][1];
        if(parseInt(arguments[1][3])>0){
            html+='<em class="mr">默认</em></span>';
        }
        html+='</p>';
        html+='<p class="txt">'+arguments[1][2]+'</p>';
        html+='</div>';
        html+='<i class="more-ico"></i>';
        html+='</a>';
        $('#selectAddress').html(html);
        address.id=arguments[0];
        history.go(-1);
    }
</script>
