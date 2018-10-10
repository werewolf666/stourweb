<?php echo Common::css('/plugins/qq_kefu/public/kf7/css/blue.css,/plugins/qq_kefu/public/js/layer/skin/layer.css',true,false);?>
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
        <?php $n=1; if(is_array($group)) { foreach($group as $row) { ?>
        <div class="foot-online-qq">
            <strong><?php echo $row['qqname'];?></strong>
            <?php $n=1; if(is_array($row['qq'])) { foreach($row['qq'] as $subqq) { ?>
            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $subqq['qqnum'];?>&site=qq&menu=yes"><?php echo $subqq['qqname'];?></a>
            <?php $n++;}unset($n); } ?>
        </div>
        <?php $n++;}unset($n); } ?>
        <?php if(!empty($Glb['cfg_phone'])) { ?>
        <div class="foot-phone-num">
            <strong>客服电话：</strong>
            <span><?php echo $Glb['cfg_phone'];?></span>
        </div>
        <?php } ?>
        <?php if(!empty($Glb['cfg_phone_400'])) { ?>
        <div class="foot-phone-num">
            <strong>400电话：</strong>
            <span><?php echo $Glb['cfg_phone_400'];?></span>
        </div>
        <?php } ?>
        <div class="foot-gz">
            <strong>关注我们：</strong>
            <?php if(!empty($Glb['cfg_weixin_logo'])) { ?>
            <div class="wechat-link">
                <a href="javascript:;">官方微信</a>
                <div class="wechat-pic"><img src="<?php echo $Glb['cfg_weixin_logo'];?>" /></div>
            </div>
            <?php } ?>
            <?php if(!empty($Glb['cfg_weibo_url'])) { ?>
            <a class="blog-link" target="_blank" href="<?php echo $Glb['cfg_weibo_url'];?>">官方微博</a>
            <?php } ?>
        </div>
        <div class="st-foot-close"></div>
    </div>
    <div class="st-foot-flexible-block"></div>
