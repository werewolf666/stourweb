<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('base.css,tongyong.css,swiper.min.css')}
    {Common::js('lib-flexible.js,swiper.min.js,jquery.min.js,delayLoading.min.js')}
</head>

<body>

    {request "pub/header_new/typeid/$typeid/isshowpage/1"}
     
    <div class="swiper-container st-photo-container" >
        <ul class="swiper-wrapper">
        {loop $info['piclist'] $pic}
            <li class="swiper-slide">
                <a class="item" href="javascript:;"><img class="swiper-lazy" data-src="{Common::img($pic[0],750,375)}"></a>
                <div class="swiper-lazy-preloader"></div>
            </li>
        {/loop}
        </ul>
        <div class="swiper-pagination"></div>
    </div>
    <!--轮播图-->
      
      <div class="ty-show-top">
      	<p class="tit">{$info['title']}</p>
      	<p class="txt">{$info['sellpoint']}</p>
      	<p class="price">
            {if !empty($info['price'])}
            <span class="jg"><i class="currency_sy">{Currency_Tool::symbol()}</i><span class="num">{$info['price']}</span>起</span>
            {else}
            <span class="jg">电询</span>
            {/if}
            <del class="del">原价:<i class="currency_sy">{Currency_Tool::symbol()}</i>{$info['sellprice']}</del>
        </p>
        {if !empty($minsuit)}
        <p class="jf"> {if !empty($info['jifentprice_info'])}积分抵现:{$info['jifentprice_info']['jifentprice']}&nbsp;&nbsp;{/if}{if !empty($info['jifencomment_info'])}评论送积分:{$info['jifencomment_info']['value']}&nbsp;&nbsp;{/if}{if !empty($info['jifenbook_info'])}预订送积分:{$info['jifenbook_info']['value']}{if $info['jifenbook_info']['rewardway']==1}%{else}分{/if}{/if}</p>
       	{/if}
        <ul class="info">
            <li class="item">
                <span class="num">{$info['sellnum']}</span>
                <span class="unit">销量</span>
            </li>
            <li class="item">
                <span class="num">{$info['satisfyscore']}%</span>
                <span class="unit">满意度</span>
            </li>
            <li class="item link pl">
                <span class="num">{$info['commentnum']}</span>
                <span class="unit">人点评</span>
                <i class="more-icon"></i>
            </li>
            <li class="item link question">
                <span class="num">{Model_Question::get_question_num($typeid,$info['id'])}</span>
                <span class="unit">人咨询</span>
                <i class="more-icon"></i>
            </li>
        </ul>
      </div>
      <!--顶部介绍-->
      
      <!-- <div class="cp_show_msg">
        <div class="opt_type">选择产品类型<i>&gt;</i></div>
      </div> -->
      <!--产品信息-->


        <!--优惠券-->
        {if St_Functions::is_normal_app_install('coupon')}
        {request "coupon/float_box-$typeid-".$info['id']}
        {/if}

      <div class="ty-info-container">
        <h3 class="ty-info-bar">
            <span class="title-txt">产品介绍</span>
        </h3>
        <div class="ty-info-wrapper clearfix">
        	{$info['content']}
        </div>
      </div>
	    {st:detailcontent action="get_content" typeid="$typeid" productinfo="$info" return="tongyong"}
        {loop $tongyong $row}
      <div class="ty-info-container">
        <h3 class="ty-info-bar">
            <span class="title-txt">{$row['chinesename']}</span>
        </h3>
        <div class="ty-info-wrapper clearfix">
            {$row['content']}
        </div>
      </div>
        {/loop}
        {/st}

    </div>
    
  </section>

{request 'pub/footer'}
  
  <div class="bom_link_box">
    <div class="bom_fixed">
        <a href="tel:{$GLOBALS['cfg_m_phone']}">电话咨询</a>
        <a class="on order" data-id="{$info['id']}" href="javascript:;">立即预定</a>
    </div>
  </div>
<script>
    var pinyin="{$pinyin}";
    var id="{$info['id']}";
    $(function(){

        //详情页滚动图
        var mySwiper = new Swiper('.st-photo-container', {
            autoplay: 5000,
            pagination : '.swiper-pagination',
            lazyLoading : true,
            observer: true,
            observeParents: true
        });

        $('.pl').click(function(){
            var url = SITEURL+"pub/comment/id/{$info['id']}/typeid/{$typeid}";
            window.location.href = url;
        })
        //预订按钮
        $('.order').click(function(){
            var productid = $(this).attr('data-id');
            url = SITEURL+pinyin+'/book/id/'+id;
            window.location.href = url;
        })

        $(".opt_type").click(function(){
            url = SITEURL+pinyin+'/book/id/'+id;
            window.location.href = url;
        });
        //发表评论
        $('.pl').click(function(){
            var url = SITEURL+"pub/comment/id/{$info['id']}/typeid/{$typeid}";
            window.location.href = url;
        })
        //问答页面
        $('.question').click(function(){
            var url = SITEURL+"question/product_question_list?articleid={$info['id']}&typeid={$typeid}";
            window.location.href = url;
        })
    });

</script>
</body>
</html>
