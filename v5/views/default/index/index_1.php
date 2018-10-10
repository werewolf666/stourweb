<!doctype html>
<html>
<head margin_body=lyvz8B >
<meta charset="utf-8">
<title>{$seoinfo['seotitle']}-{$webname}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}"/>
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}"/>
    {/if}
    {$GLOBALS['cfg_indexcode']}
    {include "pub/varname"}
    {Common::css("base.css,index.css,extend.css",false)}
    {Common::js("jquery.min.js,base.js,common.js,SuperSlide.min.js,delayLoading.min.js")}

</head>
<body>
{request 'pub/header'}
  
  <div class="st-focus-banners">
        <div class="banners">
            <ul>
            		{st:ad action="getad" name="IndexRollingAd" pc="1" return="ad"}
            			{loop $ad['aditems'] $v}
                	<li class="banner">
                        <a href="{$v['adlink']}" target="_blank"><img src="{Product::get_lazy_img()}" original-src="{Common::img($v['adsrc'],1920,420)}" alt="{$v['adname']}" /></a>
                    </li>
                	{/loop}
                {/st}
            </ul>
        </div>
        <div class="focus">
            <ul>
            	{loop $ad['aditems'] $k}
            	<li></li>
              {/loop}
            </ul>
        </div>
    </div><!--滚动焦点图结束-->

  <div class="big">
  	<div class="wm-1200">
        {st:channel action="pc" row="20"}
        {loop $data $row}
        {if  $row['issystem'] && in_array($row['typeid'],array(1,2,3,4,5,6,8,11,13,101,104,105,106,114))}
                {include 'index/index_1/'.Model_Model::all_model($row['typeid'],'maintable')}
            {/if}
        {/loop}
    </div>
  </div>
 {request 'pub/footer'}
 {request "pub/flink/isindex/1"}
 {Common::js("fcous.js,slideTabs.js")}
    <script>
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
    </script>

</body>
</html>
