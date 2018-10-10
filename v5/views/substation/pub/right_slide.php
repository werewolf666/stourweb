<div class="st-sidemenu-box" style="height: 234px; right: 0px;">
    <div class="st-sidemenu-con">
        <ul>
            {if !empty($GLOBALS['cfg_weibo_url'])}
                <li class="microblog"><a href="{$GLOBALS['cfg_weibo_url']}" target="_blank">微博</a></li>
            {/if}
            {if !empty($GLOBALS['cfg_weixin_logo'])}
            <li class="wechat">微信<div class="wechat-box"><img src="{$GLOBALS['cfg_weixin_logo']}"></div></li>
            {/if}
            <li class="myorder"><a href="{Common::get_main_host()}/member/" target="_blank">我的订单</a></li>
        </ul>
        <div class="st-sidemenu-bom">
            <div class="st-backTop" id="st-backTop"><a href="javascript:void(0)">TOP</a></div>
            <div class="st-sidemenu-closed"></div>
        </div>
    </div>
    <div class="st-travel-show" style="right: -100px;"></div>
</div>
<script>
    getConsize();
    $(function(){


        $(window).resize(function(){
            getConsize()
        });


        //返回顶部
        $('#st-backTop').click(function(){$('html,body').animate({scrollTop: '0px'}, 500);});

        //隐藏显示侧边导航
        $('.st-sidemenu-closed').click(function(){
            $('.st-sidemenu-box').animate({right:'-35px'},100,function(){
                $('.st-travel-show').animate({right:'0'},200)
            })
        })

        $('.st-travel-show').click(function(){
            $(this).animate({right:'-100px'},100,function(){
                $('.st-sidemenu-box').animate({right:'0'},200)
            })
        })
    })
        //动态获取改变窗口高度
        function getConsize(){
            $('.st-sidemenu-box').height($(window).height());
        }
</script>