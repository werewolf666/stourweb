<!doctype html>
<html>
<head strong_margin=5TKwOs >
<meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    {include "pub/varname"}
    {Common::css('tongyong.css,base.css,extend.css,calendar.css')}
    {Common::js('jquery.min.js,base.js,common.js,SuperSlide.min.js,focus-slide.js,template.js')}
</head>

<body>
  {request "pub/header"}
  {if St_Functions::is_normal_app_install('coupon')}
  {request 'coupon/float_box-'.$typeid.'-'.$info['id']}
  {/if}
  <div class="big">
  	<div class="wm-1200">


    <div class="st-guide">
        {st:position action="show_crumbs" typeid="$typeid" info="$info"}
    </div><!--面包屑-->
      
      <div class="st-main-page">
      
      	<div class="st-public-show">
          <div class="lineshow-tw">
            <div class="lw-title">
              <h1>{$info['title']}
                  {loop $info['iconlist'] $icon}
                  <img src="{$icon['litpic']}" />
                  {/loop}</h1>
              <p>{$info['sellpoint']}</p>
            </div>
            <div class="focus-slide">
              <div class="imgnav" id="imgnav"> 
                <div id="img">
                    {loop $info['piclist'] $pic}
                        <img src="{Common::img($pic[0],460,312)}"/>
                    {/loop}
                  <div id="front" title="上一张"><a href="javaScript:void(0)" class="pngFix"></a></div>
                  <div id="next" title="下一张"><a href="javaScript:void(0)" class="pngFix"></a></div>
                </div>
                <div id="cbtn">
                    <i class="picSildeLeft"><img src="{$GLOBALS['cfg_public_url']}images/picSlideLeft.gif"/></i>
                    <i class="picSildeRight"><img src="{$GLOBALS['cfg_public_url']}images/picSlideRight.gif"/></i>
                    <div id="cSlideUl">
                        <ul>
                            {loop $info['piclist'] $pic}
                            <li><img src="{Common::img($pic[0],90,61)}"/></li>
                            {/loop}
                        </ul>
                    </div>
                </div> 
              </div>
            </div>
            <div class="cp-show-msg">
              <div class="jg" id="min_price_tips">
                  <span class="hide">{__('优惠价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i><b id="minprice">{$info['price']}</b>{__('起')}</span>
                  <span class="hide">{__('优惠价')}：<b>{__('电询')}</b></span>

                  <del id="lb_sellprice" style="display: none;">{__('原价')}：<i class="currency_sy">{Currency_Tool::symbol()}</i><label>{$info['sellprice']}</label></del>
                  
              </div>
              <div class="sl">
                  <span>{__('销量')}：{$info['sellnum']}</span><s>|</s><span class="myd">{__('满意度')}：{$info['score']}</span>
              </div>
              <dl class="tc">
                <dt>{__('产品类型')}：</dt>
                <dd class="type suitlist">
                    {st:tongyong action="suit" productid="$info['id']"}
                        {loop $data $s}
                        <a href="javascript:;" minprice="{$s['minprice']}" data-suitid="{$s['id']}" data-jifentprice="{$s['jifentprice']}" data-sellprice="{$s['sellprice']}" data-jifenbook="{$s['jifenbook']}">{$s['title']}</a>
                        {/loop}
                    {/st}
                </dd>
                <dd class="yd-btn">
                  <a href="javascript:;">{__('立即预订')}</a>
                </dd>
              </dl>
              <ul class="msg-ul">
                  {if !empty($info['jifentprice_info']) || !empty($info['jifenbook_info']) || !empty($info['jifencomment_info'])}
                <li>
                    <em class="item-hd">{__('积分优惠')}：</em>
                    <p class="item-bd">
                        {if !empty($info['jifentprice_info'])}
                        <span class="d-jf">{Currency_Tool::symbol()}{$info['jifentprice_info']['jifentprice']}<i></i></span>
                        {/if}

                        {if !empty($info['jifenbook_info'])}
                        <span class="s-jf">{$info['jifenbook_info']['value']}{if $info['jifenbook_info']['rewardway']==1}%{else}分{/if}<i></i></span>
                        {/if}

                        {if !empty($info['jifencomment_info'])}
                        <span class="p-jf">{$info['jifencomment_info']['value']}分<i></i></span>
                        {/if}
                    </p>
                </li>
                  {/if}
                  {if $info['suppliername']}
                  <li class="mb_0">
                      <em class="item-hd">供应商：</em>
                      <p class="item-bd">{$info['suppliername']}</p>
                  </li>
                  {/if}
                <li class="mb_0">
                    <em class="item-hd">{__('付款方式')}：</em>
                    <p class="item-bd">
                        {php $paymethod = Product::get_paytype_list();}
                        {loop $paymethod $method}
                        <img src="{$method['icon']}" />
                        {/loop}
                    </p>
                </li>
              </ul>	
            </div>
          </div>
          <div class="lineshow-con">
            <div class="tabnav-list">
              <span>{__('产品详情')}</span>
                {st:detailcontent action="get_content" pc="1" typeid="$typeid" productinfo="$info" return="tongyong"}
                {loop $tongyong $row}
                <span>{$row['chinesename']}</span>
                {/loop}
                {/st}
              <span>{__('客户评价')}</span>
              <span>{__('我要咨询')}</span>
              <a class="yd-btn yd-btn-menu hide" href="javascript:;">{__('立即预订')}</a>
            </div><!--线路导航-->
            <div class="tabbox-list">


                <div class="tabcon-list">
                    <div class="list-tit"><strong>{__('产品详情')}</strong></div>
                    <div class="list-txt">
                        {Common::content_image_width($info['content'],833,0)}
                    
                    </div>
                </div>
                {loop $tongyong $s}
                <div class="tabcon-list">
                    <div class="list-tit"><strong>{$s['chinesename']}</strong></div>
                    <div class="list-txt">
                        {Common::content_image_width($s['content'],833,0)}

                    </div>
                </div>
                {/loop}
                {include "pub/comment"}
                {include "pub/ask"}
              
            </div>
          </div>
        </div><!--详情主体-->
          <!--隐藏域-->
          <input type="hidden" id="suitid" value=""/>
          <input type="hidden" id="productid" value="{$info['id']}"/>
        
        <div class="st-sidebox">
            {st:right action="get" typeid="$typeid" data="$templetdata" pagename="show"}
        </div><!--边栏模块-->
      
      </div>
    
    </div>
  </div>

  {request "pub/footer"}

  {request "pub/flink"}
  {Common::js('floatmenu/floatmenu.js,scorll.img.js')}
  {Common::css('/res/js/floatmenu/floatmenu.css',0,0)}
  {include "member/login_order"}
</body>
<script type="text/javascript">
    $(document).ready(function(){


        //内容切换
        $.floatMenu({
            menuContain : '.tabnav-list',
            tabItem : 'span',
            chooseClass : 'on',
            contentContain : '.tabbox-list',
            itemClass : '.tabcon-list'
            }
        );
        //套餐选择
        $('.suitlist').find('a').click(function(){

            var minprice = $(this).attr('minprice');
            if(parseInt(minprice)>0){
                $('#min_price_tips').find('span:eq(0)').removeClass('hide').siblings('span').addClass('hide');
                $('#minprice').text(minprice);
            }else{
                $('#min_price_tips').find('span:eq(1)').removeClass('hide').siblings('span').addClass('hide');
            }
            var suitid = $(this).attr('data-suitid');
            var jifentprice = $(this).attr('data-jifentprice');
            var jifenbook = $(this).attr('data-jifenbook');

            var sellprice = parseInt($(this).data('sellprice'));
            if(sellprice<=0)
            {
                $("#lb_sellprice").hide();
            }else
            {
                $("#lb_sellprice").show();
                $("#lb_sellprice label").text(sellprice);
            }



            $("#suitid").val(suitid);
            $(this).addClass('on').siblings().removeClass('on');



        })
        //选中第一个
        $('.suitlist').find('a').first().trigger('click');

        //预订
        $('.yd-btn').click(function(){
            if(!is_login_order()){
                return false;
            }
            var suitid = $("#suitid").val();
            var productid = $("#productid").val();
            var pinyin = "{$module_pinyin}";
            var url = SITEURL+pinyin+"/book?suitid="+suitid+"&productid="+productid;
            window.location.href = url;
        })

        var topHeight = $('.tabnav-list').offset().top;
        $(window).scroll(function(){
            if($(document).scrollTop() >= topHeight){
                $(".yd-btn-menu").show()
            }else{
                $(".yd-btn-menu").hide();
            }
        });


    });
</script>
</html>
