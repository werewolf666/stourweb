{Common::css('header.css')}
<div class="web-top">
    <div class="wm-1200">
        <div class="notice-txt">{$GLOBALS['cfg_gonggao']}</div>
        <div class="top-login">
            <span id="loginstatus">

            </span>
            <a class="dd" href="{Common::get_main_host()}/search/order" target="_blank"><i></i>{__('订单查询')}</a>
            <dl class="dh">
                <dt><i></i>{__('网站导航')}</dt>
                <dd>
                    {st:channel action="pc" row="20"}
                      {loop $data $row}
                       <a href="{$row['url']}">{$row['title']}</a>
                      {/loop}
                    {/st}
                </dd>
            </dl>
        </div>
        <div class="scroll-order">
            <ul>
                {st:comment action="query" flag="all" row="3"}
                  {loop $data $row}
                     <li>{$row['nickname']}{$row['pltime']}{__('评论了')}{$row['productname']}</li>
                  {/loop}
                {/st}
            </ul>
        </div>
    </div>
</div><!--顶部-->

<div class="st-header">
    <div class="wm-1200">
        <div class="st-logo">
            {if !empty($GLOBALS['cfg_logo'])}
            <a title="{$GLOBALS['cfg_logotitle']}" href="{$GLOBALS['cfg_logourl']}"><img src="{Common::img($GLOBALS['cfg_logo'],298,85)}" alt="logo" /></a>
            {/if}
        </div>
        <div class="st-top-search">
            <div class="st-search-down">
                <strong id="typename"><i></i></strong>
                <ul class="st-down-select searchmodel">
                    <li data-id="0">{__('全部')}</li>
                    {loop $searchmodel $m}
                        <li data-id="{$m['id']}" data-url="{$m['url']}">{$m['modulename']}</li>
                    {/loop}

                </ul>
            </div>
            <input type="button" value="{__('搜索')}" class="st-btn" />
            <input type="text" id="st-top-search" class="st-txt searchkeyword" placeholder="{__('输入目的地、酒店、攻略')}" />
            <span id="dt-top-search-span">
                {st:hotsearch action="hot" row="3"}
                 {loop $data $row}
                <a href="{$row['url']}" target="_blank">{$row['title']}</a>
                 {/loop}
                {/st}
            </span>
        </div>
        <div class="st-link-way">
            <strong>{__('客服电话')}：</strong>
            <em>{str_replace(array(',',';',':','，'),'<br />',$GLOBALS['cfg_phone'])}</em>
        </div>
    </div>
</div><!--header-->

<div class="st-nav">
    <div class="wm-1200">
        <div class="st-menu">
            <ul class="clearfix">
                <li><a href="{$cmsurl}">{$GLOBALS['cfg_indexname']}<s></s></a></li>
                {st:channel action="pc" row="20"}
                {loop $data $row}
                <li>
                    {if $row['kind']==1}
                    <i class="st-new-ico"></i><s></s>
                    {elseif $row['kind']==2}
                    <i class="st-hot-ico"></i><s></s>
                    {elseif $row['kind']==3}
                    <i class="st-jing-ico"></i><s></s>
                    {elseif $row['kind']==4}
                    <i class="st-jian-ico"></i><s></s>
                    {/if}

                    {if !empty($row['submenu'])}
                    <i class="st-arrow-ico"></i>
                    {/if}
                    <s></s>
                    <a href="{$row['url']}">{$row['title']}</a>
                    {if !empty($row['submenu'])}
                    <div class="down-nav">
                        <div class="sub-list">
                            {loop $row['submenu'] $submenu}
                            <a href="{$submenu['url']}">{$submenu['title']}</a>
                            {/loop}
                        </div>

                    </div>
                    {/if}
                </li>
                {/loop}
            </ul>
        </div>
    </div>
</div><!--主导航-->
{Common::js('SuperSlide.min.js')}
<script>
    var SITEURL = "{$cmsurl}";
    $(function(){
        $(".searchmodel li").click(function(){
            var typeid = $(this).attr('data-id');
            var durl = $(this).attr('data-url');
            var typename = $(this).text();
            $("#typename").html(typename+'<i></i>');
            $("#typename").attr('data-id',typeid);
            $("#typename").attr('data-url',durl);

        })
        $(".searchmodel li:first").trigger('click');
        //search
        $('.st-btn').click(function(){

            var keyword = $('.searchkeyword').val();
            if(keyword == ''){
                $('.searchkeyword').focus();
                return false;
            }
            var typeid = $("#typename").attr('data-id');
            var durl = $("#typename").attr('data-url');
            if(typeid==0 || typeid==8){
                var url = SITEURL+'search/cloudsearch?keyword='+encodeURIComponent(keyword)+"&typeid="+typeid;
            }else{
                var url = "{$GLOBALS['cfg_basehost']}"+durl+'all?keyword='+encodeURIComponent(keyword);
            }

            location.href = url;
        })
        //search focus
        var topSearch={};
        topSearch.placeholder=$('#st-top-search').attr('placeholder');
        topSearch.spanHtml=$('#dt-top-search-span').html();
        $('#st-top-search').focus(function(){
            $('#st-top-search').attr('placeholder','');
            $('#dt-top-search-span').html('');
        });
        $('#st-top-search').blur(function(){
          if($(this).val()==''){
              $('#st-top-search').attr('placeholder',topSearch.placeholder);
              $('#dt-top-search-span').html(topSearch.spanHtml);
          }
        });
        //导航的选中状态
        $(".st-menu a").each(function(){
            var url= window.location.href;
            url=url.replace('index.php','');
            url=url.replace('index.html','');
            var ulink=$(this).attr("href");
            if(url==ulink)
            {
                $(this).parents('li:first').addClass('active');
            }
        })
        //登陆状态
        $.ajax({
            type:"POST",
            async:false,
            url:SITEURL+"member/login/ajax_is_login",
            dataType:'json',
            success:function(data){
                if(data.status){
                    $txt = '<a class="dl" style="padding:0" href="javascript:;">{__("你好")},</a>';
                    $txt+= '<a class="dl" href="{Common::get_main_host()}/member/">'+data.user.nickname+'</a>';
                    $txt+= '<a class="dl" href="{Common::get_main_host()}/member/login/loginout">{__("退出")}</a>';
                    //$txt+= '<a class="dl" href="{$cmsurl}member">个人中心</a>';
                }else{

                    $txt = '<a class="dl" href="{Common::get_main_host()}/member/login">{__("登录")}</a>';
                    $txt+= '<a class="zc" href="{Common::get_main_host()}/member/register">{__("免费注册")}</a>';
                }
                $("#loginstatus").html($txt);
            }
        })
    })
</script>