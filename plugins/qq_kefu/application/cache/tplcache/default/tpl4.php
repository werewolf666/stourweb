<?php if($conf['pos']=='right') { ?>
<?php echo Common::css('/plugins/qq_kefu/public/kf4/css/right.css',true,false);?>
<?php } else { ?>
<?php echo Common::css('/plugins/qq_kefu/public/kf4/css/left.css',true,false);?>
<?php } ?>
<div class="st-online-box" style="<?php echo $conf['pos'];?>:<?php echo $conf['posh'];?>;top:<?php echo $conf['post'];?>;">
    <div class="st-online-show">
        <div class="online-tit">在线咨询<i class="close-zx"></i></div>
        <?php $n=1; if(is_array($group)) { foreach($group as $g) { ?>
        <dl class="online-list">
            <dt><?php echo $g['qqname'];?></dt>
            <?php $n=1; if(is_array($g['qq'])) { foreach($g['qq'] as $q) { ?>
            <dd><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $q['qqnum'];?>&site=qq&menu=yes"><?php echo $q['qqname'];?></a></dd>
            <?php $n++;}unset($n); } ?>
        </dl>
        <?php $n++;}unset($n); } ?>
    </div>
    <div class="st-online-hide"></div>
</div><!-- 在线咨询 -->
<script>
    var post='<?php echo $conf['post'];?>';
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
<?php if($conf['pos']=='left') { ?>
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
<?php } else { ?>
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
<?php } ?>
