<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$params['keyword']}__搜索结果-{$webname}</title>
    {Common::css('header.css,base.css')}
    {Common::css_plugin('cloud_search.css','global_search')}
    {Common::js('jquery.min.js,base.js,common.js,delayLoading.min.js')}

</head>

<body left_color=zOo3gm >
{include "pub/search_header"}

<div class="big">
    <div class="wm-1200">

        <div class="st-guide">
            <a href="/">首页</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;
            <a href="javascript:;">{$shortname}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;
            {$params['keyword']}
        </div>
        <!-- 面包屑 -->
        <div class="search-type-block">
            {if $search_items}
            <div class="search-type-item clearfix choose_items_box">
                <strong class="item-hd">已选条件：</strong>
                <div class="item-bd">
                    <div class="item-check">
                        {loop $search_items $item}
                        <a class="chick-child"  data-id="{$item['id']}"  data-type="{$item['type']}"   href="javascript:;">{$item['attrname']}<i class="closed"></i></a>
                        {/loop}
                        <a class="clear-item"  onclick="change_search()" href="javascript:;">清空筛选条件</a>
                    </div>
                </div>
            </div>
            {/if}

            <div class="search-type-item clearfix">
                <strong class="item-hd">相关目的地：</strong>
                <div class="item-bd">
                    <div class="item-child">
                        <div class="child-block">
                            <ul class="child-list">
                                {loop $destlist $dest}
                                <li class="item" ><a  href="javascript:;" class="choose_dest  {if $dest['id']==$params['destid']} active{/if}"  data-id="{$dest['id']}" >{$dest['kindname']}</a></li>
                                {/loop}
                            </ul>
                            {if count($destlist)>8}
                            <a class="arrow down" href="javascript:;">展开</a>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>


            {php}$timeArr = Model_Jieban::get_time_arr(){/php}
            <div class="search-type-item clearfix">
                <strong class="item-hd">行程日期：</strong>
                <div class="item-bd">
                    <div class="item-child">
                        <div class="child-block">
                            <ul class="child-list">
                                {loop $timeArr $time}
                                {if $n>1}
                                <li class="item" ><a  href="javascript:;" class="choose_startdate  {if $time['id']==$params['timeid']} active {/if}"  data-id="{$time['id']}" >{$time['tagname']}</a></li>
                                {/if}
                                {/loop}
                            </ul>
                            {if count($timeArr)>8}
                            <a class="arrow down" href="javascript:;">展开</a>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>

            <div class="search-type-item clearfix">
                <strong class="item-hd">行程天数：</strong>
                <div class="item-bd">
                    <div class="item-child">
                        <div class="child-block">
                            <ul class="child-list">
                                <li class="item" ><a  href="javascript:;" class="choose_days  {if $params['dayid']=='1_2'} active{/if}"  data-id="1_2" >1-2天</a></li>
                                <li class="item" ><a  href="javascript:;" class="choose_days  {if $params['dayid']=='2_5'} active{/if}"  data-id="2_5" >2-5天</a></li>
                                <li class="item" ><a  href="javascript:;" class="choose_days  {if $params['dayid']=='5_8'} active{/if}"  data-id="5_8" >5-8天</a></li>
                                <li class="item" ><a  href="javascript:;" class="choose_days  {if $params['dayid']=='8_12'} active{/if}"  data-id="8_12" >8-12天</a></li>
                                <li class="item" ><a  href="javascript:;" class="choose_days  {if $params['dayid']=='12_0'} active{/if}"  data-id="12_0" >12天以上</a></li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>

            {st:attr action="query" flag="grouplist" typeid="$typeid" return="grouplist"}
            {php}$index=1;{/php}
            {loop $grouplist $group}
            <div class="search-type-item clearfix {if $index>2} hide{/if}">
                <strong class="item-hd">{$group['attrname']}：</strong>
                <div class="item-bd">
                    <div class="item-child">
                        <div class="child-block">
                            <ul class="child-list">
                                {st:attr action="query" flag="childitem" typeid="$typeid" groupid="$group['id']" return="attrlist"}
                                {loop $attrlist $attr}
                                <li class="item" ><a  class="choose_attr {if in_array($attr['id'],$attr_list)} active{/if}" data-id="{$attr['id']}"   href="javascript:;" >{$attr['attrname']}</a></li>
                                {/loop}
                            </ul>
                            {if count($attrlist)>8}
                            <a class="arrow down" href="javascript:;">展开</a>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            {php}$index++;{/php}
            {/loop}

            {if count($grouplist)>2}
            <a href="javascript:;" id="searchConsoleBtn" class="search-console-btn down">展开更多搜索</a>
            {/if}
        </div>
        <!-- 搜索条件 -->
        {if $list}
        <div class="search-results-container">
            <ul class="search-results-list">
                {loop $list $l}
                <li class="clearfix">
                    <div class="hd">
                        <a target="_blank" href="{$l['url']}" class="pic">
                            <img src="{Product::get_lazy_img()}"  st-src="{Common::img($l['litpic'],200,136)}" alt="{$l['title']}" title="{$l['title']}" width="200" height="136" />
                        </a>
                    </div>
                    <div class="info">
                        <a class="tit" target="_blank" href="{$l['url']}">{$l['title']}</a>
                        <div class="txt">
                            {if $l['description']}
                            {$l['description']}
                            {/if}
                        </div>
                        <div class="attr">
                            {loop $l['attrlist'] $attr}
                            <span class="item">{$attr['attrname']}</span>
                            {/loop}
                        </div>
                        <div class="label">
                            <span class="item">{date('Y-m-d',$l['addtime'])}发布</span>
                        </div>
                    </div>
                    <div class="sum">
                        <i class="icon"></i>
                        <span class="read"><b class="num">{$l['shownum']}</b>人已阅读</span>
                    </div>
                </li>
                {/loop}
            </ul>
            <div class="main_mod_page clear">
                {$pageinfo}
            </div>
        </div>
        {else}
        <div class="no-content">
            <p><i></i>抱歉，没有找到符合条件的内容！</p>
        </div>
        {/if}
        <!-- 搜索列表 -->

    </div>
</div>

{request "pub/footer"}

<script>

    var pinyin = '{$params['pinyin']}';
    var search_keyword = '{$params['keyword']}';


    $(function(){
        search_init();
        //选择模块
        $('.choose_model').click(function () {
            if(!$(this).hasClass('active'))
            {
                pinyin = $(this).attr('data-pinyin');
                change_search();
            }
        });
        //选择目的地
        $('.choose_dest').click(function () {
            $('.choose_dest').removeClass('active');
            $(this).addClass('active');
            go_search();
        });
        //选择属性
        $('.choose_attr').click(function () {
            $(this).parents('.child-list').find('.choose_attr').removeClass('active');
            $(this).addClass('active');
            go_search();
        });

        //选择出游天数
        $('.choose_days').click(function () {
            $(this).parents('.child-list').find('.choose_days').removeClass('active');
            $(this).addClass('active');
            go_search();
        });

        //选择日期
        $('.choose_startdate').click(function () {
            $(this).parents('.child-list').find('.choose_startdate').removeClass('active');
            $(this).addClass('active');
            go_search();
        });


        //取消筛选
        $('.chick-child .closed').click(function () {
            var type = $(this).parent().attr('data-type');
            var id = $(this).parent().attr('data-id');
            $(this).parent().remove();
            $('.choose_'+type).each(function () {
                if($(this).attr('data-id')==id)
                {
                    $(this).removeClass('active');
                }
            });
            go_search();

        });






        //搜索条件
        $(".arrow").on("click",function(){
            if( $(this).hasClass("down") )
            {
                $(this).removeClass("down").addClass("up");
                $(this).text("收起");
                $(this).prev(".child-list").css("height","auto")
            }
            else
            {
                $(this).removeClass("up").addClass("down");
                $(this).text("展开");
                $(this).prev(".child-list").css("height","24px")
            }
        });

        $("#searchConsoleBtn").on("click",function(){
            if( $(this).hasClass("down") ){
                $('.search-type-item').removeClass('hide');
                $(this).removeClass("down").addClass("up").text("收起")
            }
            else
            {
                var num = 0 ;
                $('.search-type-item').each(function (index,obj) {
                    if(!$(obj).hasClass('choose_items_box'))
                    {
                        num++;
                    }
                    if(num>5)
                    {
                        $(obj).addClass('hide')
                    }
                });
                $(this).removeClass("up").addClass("down").text("展开更多搜索")
            }
        })

    });

    //属性，目的地搜索
    function go_search()
    {
        var destid = 0;
        var dayid = 0;
        var timeid = 0;

        var attrlist = [];
        $('.choose_dest').each(function () {
            if($(this).hasClass('active'))
            {
                destid = $(this).attr('data-id');
            }
        });
        $('.choose_attr').each(function () {
            if($(this).hasClass('active'))
            {
                var  attr = $(this).attr('data-id');
                attrlist.push(attr);
            }
        });

        $('.choose_days').each(function () {
            if($(this).hasClass('active'))
            {
                dayid = $(this).attr('data-id');
            }
        });
        $('.choose_startdate').each(function () {
            if($(this).hasClass('active'))
            {
                timeid = $(this).attr('data-id');
            }
        });

        attrlist = attrlist.join('_');
        if(!attrlist)
        {
            attrlist=0;
        }
        var url = SITEURL+'query/'+pinyin+'-'+destid+'-'+dayid+'-'+timeid+'-'+attrlist+'?keyword='+search_keyword;
        window.location.href= url;
    }


    //切换筛选模块或者关键词
    function  change_search()
    {
        $('.choose_dest,.choose_attr').removeClass('active');
        $('.choose_items_box').remove();

        var url = SITEURL+'query/'+pinyin+'?keyword='+search_keyword;
        window.location.href= url;
    }

    //页面初始化
    function search_init()
    {
        $('.choose_model').each(function ()
        {
            if($(this).attr('data-pinyin')==pinyin)
            {
                $(this).addClass('active');
            }

        });
        $('.searchmodel li').each(function () {
            if($(this).attr('data-pinyin')==pinyin)
            {
                $(this).trigger('click');
            }
        });
        $('#st-top-search').val(search_keyword);

    }


</script>

</body>
</html>
