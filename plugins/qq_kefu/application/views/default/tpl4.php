{if $conf['pos']=='right'}
{Common::css('/plugins/qq_kefu/public/kf4/css/right.css',true,false)}
{else}
{Common::css('/plugins/qq_kefu/public/kf4/css/left.css',true,false)}
{/if}
<div class="st-online-box" style="{$conf['pos']}:{$conf['posh']};top:{$conf['post']};">
    <div class="st-online-show">
        <div class="online-tit">在线咨询<i class="close-zx"></i></div>
        {loop $group $g}
        <dl class="online-list">
            <dt>{$g['qqname']}</dt>
            {loop $g['qq'] $q}
            <dd><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin={$q['qqnum']}&site=qq&menu=yes">{$q['qqname']}</a></dd>
            {/loop}
        </dl>
        {/loop}
    </div>
    <div class="st-online-hide"></div>
</div><!-- 在线咨询 -->


<script>
    var post='{$conf['post']}';
    var postVal=0;
    if(post.indexOf('%')==-1)
    {
        postVal=parseFloat(post);
    }
    else
    {
        postVal=$(window).height()*parseFloat(post)/100;
    }


</script>


{if $conf['pos']=='left'}
<script>
        $(function(){
            //在线客服
            $('.close-zx').on('click',function(){
                $('.st-online-show').fadeOut(300,function(){
                    $('.st-online-hide').show().animate({left:'-10px'},200);
                });
            })

            $('.st-online-hide').on('click',function(){
                $(this).animate({left:'-54px'},200,function(){
                    $(this).hide().prev().fadeIn(300)
                });
            })

            $(window).scroll(function () {
                var offsetTop = $(window).scrollTop() + postVal + "px";
                $('.st-online-box').animate({top: offsetTop}, {duration: 500, queue: false});
            });
        })
</script>
{else}
<script>
    $(function(){
        //在线客服
        $('.close-zx').on('click',function(){
            $('.st-online-show').fadeOut(300,function(){
                $('.st-online-hide').show().animate({right:'-10px'},200);
            });
        })

        $('.st-online-hide').on('click',function(){
            $(this).animate({right:'-54px'},200,function(){
                $(this).hide().prev().fadeIn(300)
            });
        })

        $(window).scroll(function () {
            var offsetTop = $(window).scrollTop() + postVal + "px";
            $('.st-online-box').animate({top: offsetTop}, {duration: 500, queue: false});
        });
    })
</script>
{/if}
