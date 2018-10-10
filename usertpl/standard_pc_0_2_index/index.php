<!doctype html>
<html>
<head>
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
    {Common::css("base.css,index_2.css,extend.css")}
    {Common::js("jquery.min.js,base.js,common.js,SuperSlide.min.js,slideTabs.js,delayLoading.min.js")}

<script>
    $(function(){
        $('.con_list,.car_con_list,.article_con').switchTab({trigger:'hover'});
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
</head>

<body>
{request "pub/header"}

    <!--滚动焦点图开始-->
    <div class="st-focus-banners">
        <div class="banners">
            <ul>
              {st:ad action="getad" name="Index2RollingAd" pc="1" return="ad"}
               {loop $ad['aditems'] $v}
                <li class="banner"><a href="{$v['adlink']}" target="_blank"><img src="{Product::get_lazy_img()}" original-src="{Common::img($v['adsrc'],1920,420)}" /></a></li>
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
    </div>
    <!--滚动焦点图结束-->
  
  
  <div class="top_pz_box">
  	<div class="child ico01">{__('深度旅行线路')}</div>
  	<div class="child ico02">{__('专业精品小团')}</div>
  	<div class="child ico03">{__('全程细心指导')}</div>
  	<div class="child ico04">{__('全面安全保障')}</div>
  </div>
  <!--品质保证-->
  
  <div class="big">
  	<div class="wm-1200">
        {st:channel action="pc" row="20"}
        {loop $data $row}
            {if $row['typeid']< 14 && $row['issystem'] && !in_array($row['typeid'],array(0,6,7,9,10,11,12))}
                {include 'standard_pc_0_2_index/index_2/'.Model_Model::all_model($row['typeid'],'maintable')}
            {/if}
        {/loop}
    </div>
  </div>
{request "pub/footer"}
{request "pub/flink/isindex/1"}

</body>
</html>
