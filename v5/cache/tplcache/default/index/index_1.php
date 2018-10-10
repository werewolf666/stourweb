<!doctype html> <html> <head> <meta charset="utf-8"> <title><?php echo $seoinfo['seotitle'];?>-<?php echo $webname;?></title> <?php if($seoinfo['keyword']) { ?> <meta name="keywords" content="<?php echo $seoinfo['keyword'];?>"/> <?php } ?> <?php if($seoinfo['description']) { ?> <meta name="description" content="<?php echo $seoinfo['description'];?>"/> <?php } ?> <?php echo $GLOBALS['cfg_indexcode'];?> <?php echo  Stourweb_View::template("pub/varname");  ?> <?php echo Common::css("base.css,index.css,extend.css",false);?> <?php echo Common::js("jquery.min.js,base.js,common.js,SuperSlide.min.js,delayLoading.min.js");?> </head> <body> <?php echo Request::factory('pub/header')->execute()->body(); ?> <div class="st-focus-banners"> <div class="banners"> <ul> <?php require_once ("/www/web/testdata_souxw_com/public_html/taglib/ad.php");$ad_tag = new Taglib_Ad();if (method_exists($ad_tag, 'getad')) {$ad = $ad_tag->getad(array('action'=>'getad','name'=>'IndexRollingAd','pc'=>'1','return'=>'ad',));}?> <?php $n=1; if(is_array($ad['aditems'])) { foreach($ad['aditems'] as $v) { ?> <li class="banner"><a href="<?php echo $v['adlink'];?>" target="_blank"><img src="<?php echo Product::get_lazy_img();?>" original-src="<?php echo Common::img($v['adsrc'],1920,420);?>" alt="<?php echo $v['adname'];?>" /></a></li> <?php $n++;}unset($n); } ?> </ul> </div> <div class="focus"> <ul> <?php $n=1; if(is_array($ad['aditems'])) { foreach($ad['aditems'] as $k) { ?> <li></li> <?php $n++;}unset($n); } ?> </ul> </div> </div><!--滚动焦点图结束--> <div class="big"> <div class="wm-1200"> <?php require_once ("/www/web/testdata_souxw_com/public_html/taglib/channel.php");$channel_tag = new Taglib_Channel();if (method_exists($channel_tag, 'pc')) {$data = $channel_tag->pc(array('action'=>'pc','row'=>'20',));}?> <?php $n=1; if(is_array($data)) { foreach($data as $row) { ?> <?php if($row['issystem'] && in_array($row['typeid'],array(1,2,3,4,5,6,8,11,13,101,104,105,106))) { ?> <?php echo  Stourweb_View::template('index/index_1/'.Model_Model::all_model($row['typeid'],'maintable'));  ?> <?php } ?> <?php $n++;}unset($n); } ?> </div> </div> <?php echo Request::factory('pub/footer')->execute()->body(); ?> <?php echo Request::factory("pub/flink")->execute()->body(); ?> <?php echo Common::js("fcous.js,slideTabs.js");?> <script>
        $(function(){
            var offsetLeft = new Array();
            var windowWidth = $(window).width();
            function get_width(){
                //设置"down-nav"宽度为浏览器宽度
                $(".down-nav").width($(window).width());
                $(".st-menu li").hover(function(){
                    var liWidth = $(this).width()/2;
                    $(this).addClass("this-hover");
                    offsetLeft = $(this).offset().left;
                    //获取当前选中li下的sub-list宽度
                    var sub_list_width = $(this).children(".down-nav").children(".sub-list").width();
                    $(this).children(".down-nav").children(".sub-list").css("width",sub_list_width);
                    $(this).children(".down-nav").css("left",-offsetLeft);
                    $(this).children(".down-nav").children(".sub-list").css("left",offsetLeft-sub_list_width/2+liWidth);
                    var offsetRight = windowWidth-offsetLeft;
                    var side_width = (windowWidth - 1200)/2;
                    if(sub_list_width > offsetRight){
                        $(this).children(".down-nav").children(".sub-list").css({
                            "left":offsetLeft-sub_list_width/2+liWidth,
                            "right":side_width,
                            "width":"auto"
                        });
                    }
                    if(side_width > offsetLeft-sub_list_width/2+liWidth){
                        $(this).children(".down-nav").children(".sub-list").css({
                            "left":side_width,
                            "right":side_width,
                            "width":"auto"
                        });
                    }
                },function(){
                    $(this).removeClass("this-hover");
                });
            };
            get_width();
            $(window).resize(function(){
                get_width();
            });
            $('.st-slideTab').switchTab();
//首页焦点图
            $('.st-focus-banners').slide({
                mainCell:".banners ul",
                titCell:".focus li",
                effect:"fold",
                interTime: 5000,
                delayTime: 1000,
                autoPlay:true,
                switchLoad:"original-src"
            });
        })
    </script> </body> </html>
