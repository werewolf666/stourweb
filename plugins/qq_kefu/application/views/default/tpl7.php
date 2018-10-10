{Common::css('/plugins/qq_kefu/public/kf7/css/blue.css,/plugins/qq_kefu/public/js/layer/skin/layer.css',true,false)}
    <script>
        $(function(){
            //隐藏底部客服
            $(".st-foot-close").click(function(){
                $(this).parents(".st-footer-kf-block").animate({
                    left:"-100%",
                    right:"100%"
                },200,function(){
                    $(".st-foot-flexible-block").animate({
                        left:"0"
                    },200)
                });
            });
            //呼出底部客服
            $(".st-foot-flexible-block").click(function(){
                $(".st-foot-flexible-block").animate({
                    left:"-62px"
                },200,function(){
                    $(".st-footer-kf-block").animate({
                        left:"0",
                        right:"0"
                    },200)
                })
            })

        })
    </script>


    <div class="st-footer-kf-block clearfix">
        {loop $group $row}
        <div class="foot-online-qq">
            <strong>{$row['qqname']}</strong>
            {loop $row['qq'] $subqq}
            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin={$subqq['qqnum']}&site=qq&menu=yes">{$subqq['qqname']}</a>
            {/loop}
        </div>
        {/loop}



        {if !empty($Glb['cfg_phone'])}
        <div class="foot-phone-num">
            <strong>客服电话：</strong>
            <span>{$Glb['cfg_phone']}</span>
        </div>
        {/if}


        <div class="foot-gz">
            <strong>关注我们：</strong>
            {if !empty($Glb['cfg_weixin_logo'])}
            <div class="wechat-link">
                <a href="javascript:;">官方微信</a>
                <div class="wechat-pic"><img src="{$Glb['cfg_weixin_logo']}" /></div>
            </div>
            {/if}
            {if !empty($Glb['cfg_weibo_url'])}
            <a class="blog-link" target="_blank" href="{$Glb['cfg_weibo_url']}">官方微博</a>
            {/if}
        </div>
        <div class="st-foot-close"></div>
    </div>

    <div class="st-foot-flexible-block"></div>

