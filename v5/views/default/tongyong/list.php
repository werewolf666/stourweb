<!doctype html>
<html>
<head margin_html=iyvz8B >
<meta charset="utf-8">
    <title>{$searchtitle}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    {include "pub/varname"}
    {Common::css('tongyong.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,delayLoading.min.js')}
</head>

<body>

{request "pub/header"}

  <div class="big">
  	<div class="wm-1200">

    <div class="st-guide">
        {st:position action="list_crumbs" typeid="$typeid" destid="$destinfo['dest_id']" searchtitle="$_GET['keyword']"}
    </div><!--面包屑-->

      <div class="st-main-page">
        {php}$count=0;{/php}
      	<div class="st-public-list-box">
          <div class="search-type-block">
              <div class="search-type-item clearfix" {if count($chooseitem)<1}style="display:none"{else}{php}$count++;{/php}{/if}>
                  <strong class="item-hd">{__('已选条件')}：</strong>
                  <div class="item-bd">
                      <div class="item-check">
                          {loop $chooseitem $item}
                          <a class="chick-child chooseitem" data-url="{$item['url']}" href="javascript:;">{$item['itemname']}<i class="closed"></i></a>
                          {/loop}
                          <a href="javascript:;" class="clear-item clearc">{__('清空筛选条件')} </a>
                      </div>
                  </div>
              </div>
             {st:dest action="query" typeid="$typeid" flag="nextsame" row="100" pid="$destid" return="destlist"}
              <div class="search-type-item clearfix {if !count(destlist)}hide{else}{php}$count++;{/php}{/if}">
                  <strong class="item-hd">{__('目的地')}：</strong>
                  <div class="item-bd">
                      <div class="item-child">
                          <div class="child-block">
                              <div class="child-list">
                                  {loop $destlist $dest}
                                  <a {if (isset($param['destpy'])&& $param['destpy']==$dest['pinyin'])}class="active"{/if} href="{$cmsurl}{$module_pinyin}/{$dest['pinyin']}/">{$dest['kindname']}</a>
                                  {/loop}
                                  {/st}
                              </div>
                              <a class="arrow down hide" href="javascript:;">展开</a>
                          </div>
                      </div>
                  </div>
              </div>
            {st:attr action="query" flag="grouplist" typeid="$typeid" return="grouplist"}
            {loop $grouplist $group}
              {st:attr action="query" flag="childitem" row="1000" typeid="$typeid" groupid="$group['id']" return="attrlist"}
              <div class="search-type-item clearfix {if !count($attrlist)}hide no_data {else}{php}$count++;{/php}{/if} {if $count>5} max_level hide{/if}">
                  <strong class="item-hd">{$group['attrname']}：</strong>
                  <div class="item-bd">
                      <div class="item-child">
                          <div class="child-block">
                              <div class="child-list">
                                  {loop $attrlist $attr}
                                  <a href="{Model_Tongyong::get_search_url($attr['id'],'attrid',$param)}" {if Common::check_in_attr($param['attrid'],$attr['id'])!==false}class="active"{/if}>{$attr['attrname']}</a>
                                  {/loop}
                              </div>
                              <a class="arrow down hide" href="javascript:;" >展开</a>
                          </div>
                      </div>
                  </div>
              </div>
              {/st}
            {/loop}
            {/st}

          </div>
          <a class="more-item down {if $count<=5}hide{/if}" href="javascript:void(0)">更多选项</a>
          <script type="text/javascript">
              $(function(){
                  //搜索条件
                  $(".arrow").on("click",function(){
                      if( $(this).hasClass("down") )
                      {
                          $(this).removeClass("down").addClass("up");
                          $(this).text("收起");
                          $(this).parents(".child-block").css("height","auto");
                      }
                      else
                      {
                          $(this).removeClass("up").addClass("down");
                          $(this).text("展开");
                          $(this).parents(".child-block").css("height","43px")
                      }
                  });



                  $(".more-item").on("click",function(){
                      if( $(this).hasClass("down") )
                      {
                          $(this).removeClass("down").addClass("up");
                          $('.search-type-block .max_level').not('.no-data').removeClass('hide');
                          $(this).text("收起选项");

                      }
                      else
                      {
                          $(this).removeClass("up").addClass("down");
                          $('.search-type-block .max_level').not('.no-data').addClass('hide');
                          $(this).text("更多选项");

                      }

                  });

                  var length = $('.search-type-block .search-type-item').not('.hide').length;
                  length -= 1;
                  $('.search-type-block .search-type-item').not('.hide').each(function (i) {
                      if (i == length) {
                          $(this).find('.child-block').addClass('last');
                      }
                  });

                  $('.child-list').each(function(){
                      if($(this).height()>34){
                          $(this).next('.arrow').removeClass('hide');
                      }
                  })

              })

          </script>


          <div class="st-sort-menu">
            <span class="sort-sum">
              <a href="javascript:;">{__('综合排序')}</a>
              <a href="javascript:;">{__('价格')}
                  {if $param['sorttype']!=1 && $param['sorttype']!=2}
                  <i class="jg-default" data-url="{Model_Tongyong::get_search_url(1,'sorttype',$param)}"></i>
                  {/if}
                  {if $param['sorttype']==1}
                  <i class="jg-up" data-url="{Model_Tongyong::get_search_url(2,'sorttype',$param)}"></i>
                  {/if}
                  {if $param['sorttype']==2}
                  <i class="jg-down" data-url="{Model_Tongyong::get_search_url(0,'sorttype',$param)}"></i></a>
                    {/if}
              </a>
              <a href="javascript:;">{__('销量')}
                  {if $param['sorttype']!=3}
                  <i class="xl-default" data-url="{Model_Tongyong::get_search_url(3,'sorttype',$param)}"></i>
                  {/if}
                  {if $param['sorttype']==3}
                  <i class="xl-down" data-url="{Model_Tongyong::get_search_url(0,'sorttype',$param)}"></i>
                  {/if}

              </a>
              <a href="#">{__('推荐')}
                  {if $param['sorttype']!=4}
                    <i class="tj-default" data-url="{Model_Tongyong::get_search_url(4,'sorttype',$param)}"></i>
                  {/if}
                  {if $param['sorttype']==4}
                    <i class="tj-down" data-url="{Model_Tongyong::get_search_url(0,'sorttype',$param)}"></i>
                  {/if}
              </a>
            </span>
          </div><!--排序-->
          <div class="public-list-con">
            {if !empty($list)}
            <ul>
             {loop $list $p}
              <li {if $n%4==0}class="mr_0"{/if}>
                <div class="pic">
                  <img src="{Product::get_lazy_img()}" st-src="{Common::img($p['litpic'],283,193)}" alt="{$p['title']}"/>
                  <div class="buy"><a href="{$p['url']}" target="_blank" title="{$p['title']}">{__('立即预订')}</a></div>
                </div>
                <div class="js">
                	<a class="tit" href="{$p['url']}" target="_blank" title="{$p['title']}">{$p['title']}</a>
                  <p class="num">
                  <del>{__('满意度')} {$p['score']}</del>
                    <span>
                       {if !empty($p['price'])}
                        <i class="currency_sy">{Currency_Tool::symbol()}</i><b>{$p['price']}</b>{__('起')}
                       {else}
                         {__('电询')}
                       {/if}

                    </span>
                  </p>
              	</div>
              </li>
             {/loop}

            </ul>
            <div class="main_mod_page clear">
             {$pageinfo}
            </div><!-- 翻页 -->
            {else}
              <div class="no-content">
                  <p><i></i>{__('抱歉，没有找到符合条件的产品！')}</p>
              </div>
            {/if}
          </div>
        </div>

      </div>
      <!--栏目介绍-->
      {if !empty($seoinfo['jieshao'])}
      <div class="st-comm-introduce">
          <div class="st-comm-introduce-txt">
              {$seoinfo['jieshao']}
          </div>
      </div>
      {/if}
  </div>
  </div>

{request "pub/footer"}

<script>
    $(function(){
        //搜索条件去掉最后一条边框
        $(".line-search-tj dl dd em").toggle(function(){
            $(this).prev().height('24px');
            $(this).children('b').text('{__("展开")}');
            $(this).children('i').removeClass('up')
        },function(){
            $(this).prev().height('auto');
            $(this).children('b').text('{__("收起")}');
            $(this).children('i').addClass('up')
        });

        //排序方式点击
        $('.sort-sum').find('a').click(function(){
            var url = $(this).find('i').attr('data-url');
            if(url==undefined){
                url = location.href;
            }
            window.location.href = url;
        })
        //删除已选
        $(".chooseitem").find('i').click(function(){
            var url = $(this).parent().attr('data-url');
            window.location.href = url;
        })
        //清空筛选条件
        $('.clearc').click(function(){
            var url = SITEURL+'{$module_pinyin}/all/';
            window.location.href = url;
        })

    })
</script>

</body>
</html>
