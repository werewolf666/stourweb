<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途旅游CMS{$coreVersion}</title>
    {include 'stourtravel/public/public_js'}
    {Common::getCss('index_7.css,base.css,base_new.css')}
    {Common::getScript('echarts.js,echart-data.js,hdate/hdate.js')}
    {Common::getCss('hdate.css','js/hdate')}

</head>

<body>

<div class="iframe-container clearfix">

    <div class="main-container clearfix">

        <div class="app-manage-container">
            <div class="app-manage-bar app-product-bar">
                <span class="manage-title">产品类 - 直接营销</span>
                <i class="icon"></i>
            </div>

            <div class="app-manage-wrap">
                <ul class="app-product-manage clearfix">
                    {php}
                        $_article=array();
                        $products = Model_Menu_New::get_config_by_pid(1);
                        $products = Model_Menu_New::ordey_by_nav($products);

                    {/php}

                    {loop $products $data}

                    {php} if(in_array($data['typeid'],array(4,6,10,11,12,101,115,null))){$_article[]=$data;continue;}{/php}
                        <li class="item">
                            <div class="app-product-block">
                                <a class="app-name-bar product_item" href="javascript:;" data-url="{$data['url']}" >{$data['title']}</a>
                                {if strpos($data['datainfo'],'1') !== false}
                                    {php}
                                        if(isset($data['order_id']))
                                        {
                                            $node=Model_Menu_New::get_config_by_id($data['order_id']);
                                            $nodeUrl=$node['url'];
                                        }
                                    {/php}
                                    {if Model_Admin::check_right($node['id'])}
                                        <a class="app-info-bar data_item" href="javascript:;" data-url="{$nodeUrl}" data-name="{$data['title']}订单">
                                            <span class="data fl" >订单<i class="num unread hide"  id="channel_order_unview_{$data['typeid']}"></i></span>
                                            <span class="data fr" id="channel_order_num_{$data['typeid']}"></span>
                                        </a>
                                    {else}
                                        <a class="app-info-bar" href="javascript:;">
                                            <span class="null-info">-</span>
                                        </a>

                                    {/if}
                                {else}
                                    <a class="app-info-bar" href="javascript:;">
                                        <span class="null-info">-</span>
                                    </a>
                                {/if}
                                {if strpos($data['datainfo'],'2')!==false}
                                    {if Model_Admin::check_right($data['question_id'])}
                                        <a class="app-info-bar data_item" href="javascript:;" data-url="question/index/typeid/{$data['typeid']}/menuid/{$data['question_id']}" data-name="{$data['title']}咨询">
                                            <span class="data fl">咨询<i class="num unread hide"  id="channel_question_unans_num_{$data['typeid']}"></i></span>
                                            <span class="data fr" id="channel_question_num_{$data['typeid']}"></span>
                                        </a>
                                    {else}
                                        <a class="app-info-bar" href="javascript:;">
                                            <span class="null-info">-</span>
                                        </a>
                                    {/if}
                                {else}
                                        <a class="app-info-bar" href="javascript:;">
                                            <span class="null-info">-</span>
                                        </a>
                                {/if}

                                {if strpos($data['datainfo'],'3')!==false}
                                    {if Model_Admin::check_right($data['comment_id'])}
                                        <a class="app-info-bar data_item" href="javascript:;" data-url="{Model_Menu_New::get_commnet_url($data['typeid'])}" data-name="{$data['title']}评论">
                                            <span class="data fl">评论<i class="num unread hide" id="channel_comment_uncheck_num_{$data['typeid']}"  ></i></span>
                                            <span class="data fr" id="channel_comment_num_{$data['typeid']}"></span>
                                        </a>
                                    {else}
                                        <a class="app-info-bar" href="javascript:;">
                                            <span class="null-info">-</span>
                                        </a>
                                    {/if}
                                {else}
                                    <a class="app-info-bar" href="javascript:;">
                                        <span class="null-info">-</span>
                                    </a>
                                {/if}
                            </div>
                        </li>
                    {/loop}

                </ul>
            </div>
        </div>
        <!-- 产品类 - 直接营销 -->

        <div class="app-manage-container">
            <div class="app-manage-bar app-article-bar">
                <span class="manage-title">文章类 - 间接营销</span>
                <i class="icon"></i>
            </div>
            <div class="app-manage-wrap">
                <ul class="app-article-manage clearfix">
                    {loop $_article $data}
                    <li class="item">
                        <span class="article-info-block" href="javascript:;">

                                <a href="javascript:;" class="data fl article_item" data-url="{$data['url']}" data-name="{$nav_title}">{$data['title']}</a>
                                {if strpos($data['datainfo'],'1') !== false}
                                    {php}
                                    $total_id = "channel_order_num_".$data['typeid'];
                                    $unread_id = "channel_order_unview_".$data['typeid'];
                                    if($data['typeid']==14)
                                    {
                                        $link_url = 'order/dz/typeid/'.$data['typeid'].'/menuid/'.$data['order_id'];
                                    }
                                    else
                                    {
                                        $link_url = 'order/index/typeid/'.$data['typeid'].'/menuid/'.$data['order_id'];
                                    }
                                    $link_title = $data['title'].'订单';


                                    {/php}
                                {elseif strpos($data['datainfo'],'2')!==false}
                                    {php}
                                    $total_id = "channel_question_num_".$data['typeid'];
                                    $unread_id = "channel_question_unans_num_".$data['typeid'];
                                    $link_url = $data['typeid']==10 ? $data['url'] : "question/index/typeid/".$data['typeid']."/menuid/".$data['question_id'];
                                    $link_title = $data['title'].咨询;
                                    {/php}
                                {elseif strpos($data['datainfo'],'3')!==false}
                                    {php}
                                     $total_id = "channel_comment_num_".$data['typeid'];
                                     $unread_id = "channel_comment_uncheck_num_".$data['typeid'];
                                     $link_url = Model_Menu_New::get_commnet_url($data['typeid']);
                                     $link_title = $data['title'].'评论';
                                    {/php}
                                {/if}

                                <a href="javascript:;" class="num data_item hide" id="{$unread_id}" data-url="{$link_url}" data-name="{$link_title}"></a>
                                <a href="javascript:;" class="data data_item fr" id="{$total_id}" data-url="{$link_url}" data-name="{$link_title}"></a>




                        </span>
                    </li>
                    {/loop}
                </ul>
            </div>
        </div>
        <!-- 文章类 - 间接营销 -->

        <div class="app-manage-container">
            <div class="app-manage-bar app-marketing-bar">
                <span class="manage-title">营销类 - 辅助营销</span>
                <i class="icon"></i>
            </div>
            <div class="app-manage-wrap">
                <ul class="app-marketing-manage clearfix">
                    {loop Model_Menu_New::get_config_by_pid(7) $v}
                        <li class="item">
                            <span class="article-info-block" href="javascript:;">
                            {if $v['title']=='免费通话'}
                            <a class="data fl article_item" href="javascript:;" data-url="{$v['url']}" {if isset($v['alias_title'])}data-title="{$v['alias_title']}"{/if}>{$v['title']}</a>
                            <a href="javascript:;" class="num data_item hide" id="channel_call_untreated_num" data-url="{$v['url']}" data-name="{$v['title']}"></a>
                            {else}
                            <a class="data fl article_item" href="javascript:;" data-url="{$v['url']}" {if isset($v['alias_title'])}data-title="{$v['alias_title']}"{/if}>{$v['title']}</a>
                            {/if}
                            </span>

                        </li>
                    {/loop}
                </ul>
            </div>
        </div>
        <!-- 营销类 - 辅助营销 -->

        <div class="app-manage-container">
            <div class="app-manage-bar app-data-bar">
                <span class="manage-title">数据类 - 经营决策</span>
                <i class="icon"></i>
            </div>
            <div class="app-manage-wrap">
                <div class="clearfix">
                    <div class="app-date-content">
                        <div class="app-date-block clearfix">
                            <span class="item-hd">时间范围</span>
                            <div class="item-bd">

                                <div class="choose-start-date" >
                                    <input type="text" class="date-text" id="starttime" value="{$starttime}">
                                    <i class="date-icon"></i>
                                </div>
                                &nbsp;至&nbsp;
                                <div class="choose-start-date">
                                    <input type="text" class="date-text" id="endtime" value="{$endtime}">
                                    <i class="date-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="manage-block">

                            <!-- 订单统计 -->
                            <div class="data-count-con">
                                <div class="list-count-tit text-c font f-16 lh30 pd-5">订单统计</div>
                                <div id="order-count-box" style=" float: left; width: 100%; height:300px;">

                                </div>
                            </div>

                            <!-- 会员统计 -->
                            <div class="data-count-con">
                                <div class="list-count-tit text-c font f-16 lh30 pd-5">会员统计</div>
                                <div id="member-count-box" style=" float: left; width: 100%; height:300px;">

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 数据类 - 经营决策 -->

    </div>
    <!-- 主体内容 -->

    <div class="side-container">

        <div class="side-tab-module hide" id="authorize_info">
            <div class="side-hd">
                <span class="item on">通知公告</span>
                <span class="item">版权信息<i class="msg-icon hide"></i></span>
            </div>
            <div class="side-bd">
                <div class="tab-module-con">
                    <div class="news-notice-block clearfix">
                        <ul class="news-notice-list fl">
                            <script type="text/javascript" aynsc  src="http://www.stourweb.com/api/cms/notice/"></script>
                        </ul>
                        <a class="more-link fr" href="http://www.stourweb.com/help/fenlei-599" target="_blank">更多通知&gt;</a>
                    </div>
                </div>
                <div class="tab-module-con">
                    <div class="level-up-block clearfix">
                        <div class="msg-txt fl" id="myversion"></div>
                        <a class="level-up-btn fr" onclick="ST.Util.addTab('升级管理','{$cmsurl}systemparts/upgrade_manager/menuid/192')">立即升级</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 通知、公告 -->

        <div class="sidle-module hide" id="unauthorize_info">
            <h3 class="sidle-tit">版权信息</h3>
            <div class="sidle-con">
                <div class="piracy-warning">
                    <span class="txt"><i class="icon"></i>您所使用的系统是盗版系统，对此我们保留起诉权利，将进行严厉打击！</span>请您购买正版系统，享受专业服务。
                </div>
            </div>
            <div class="side-menu-bar">
                <a class="btn-link" href="http://www.stourweb.com/cms/goumai" target="_blank">立即购买</a>
            </div>
        </div>
        <!-- 版权信息 -->
        {if Model_Sysconfig::get_sys_conf('value','cfg_quick_menu')}
            {request 'quickmenu/sidemenu'}
        {/if}

        <!-- 常用菜单 -->
        {php}$menu = Model_Menu_New::get_config_by_pid(0,array(1,7,9));{/php}

       {loop $menu $k $v}
            {php}
              if($v['title']=='图片服务器' || $v['title'] == '城市站点')
              {
                 continue;
              }
            {/php}
            <div class="sidle-module">
                <h3 class="sidle-tit">{$v['title']}
                    {if $v['title']=='开发者'}
                    <a href="http://www.stourweb.com/help/show/766.html" target="_blank">
                     <img class="ml-5 dev-help" style="margin-top: -3px;cursor: pointer"  title="点击查看关于开发者帮助"  src="{$GLOBALS['cfg_public_url']}images/help-ico.png"/>
                    </a>
                    {/if}
                </h3>
                <div class="sidle-con">
                    {loop Model_Menu_New::get_config_by_pid($v['id']) $sub}
                        {if $sub['title']!='产品接口' ||($sub['title']=='产品接口' && $sub['child_id']) }
                            <a href="javascript:;" class="config_item item" data-url="{$sub['url']}" {if isset($sub['alias_title'])}data-title="{$sub['alias_title']}"{/if}>{$sub['title']}</a>
                        {/if}
                    {/loop}
                </div>
            </div>
        {/loop}

        <div class="side-tab-module">
            <div class="side-hd">
                <span class="item on">系统更新</span>
                <span class="item">营销文章</span>
            </div>
            <div class="side-bd">
                <div class="tab-module-con">
                    <ul class="side-article-list">
                        <script type="text/javascript" aynsc  src="http://www.stourweb.com/api/cms/version7"></script>
                    </ul>
                </div>
                <div class="tab-module-con">
                    <ul class="side-article-list">
                        <script type="text/javascript" aynsc  src="http://www.stourweb.com/api/cms/article"></script>
                    </ul>
                </div>
            </div>
        </div>
        <!-- 文章 -->

    </div>
    <!-- 边栏导航 -->

</div>
<!-- 边栏导航 -->
</body>
<script>
    var SITEURL = URL = '{php echo URL::site();}';
    //生成图表
    function queryChart(){
        var starttime = $("#starttime").val();
        var endtime = $("#endtime").val();
        var arr = starttime.split("-");
        var starttime = new Date(arr[0], arr[1], arr[2]);
        var starttimes = starttime.getTime();
        var arrs = endtime.split("-");
        var lktime = new Date(arrs[0], arrs[1], arrs[2]);
        var lktimes = lktime.getTime();


        if (starttimes >= lktimes) {
            ST.Util.showMsg("结束日期不能小于开始日期", 5, 1000);
            return false;
        }
        console.log('here');
        initChart();
    }
    //正版检测
    //检测正版授权
    function checkRightV() {
        $.ajax({
            url: SITEURL + "upgrade/ajax_check_right/systempart/{php echo Model_SystemParts::$coreSystemPartCode}",
            dataType: 'json',
            success: function (data) {
                if (data.status == 1) {
                    $('#authorize_info').removeClass('hide');
                    //获取是否有更新
                    $.ajax({
                        url: SITEURL + "upgrade/ajax_check_all_systempart_update",
                        dataType: 'json',
                        success: function (data) {
                            var newico = "<img src=\"{$GLOBALS['cfg_public_url']}images/level-up-icon.png\" />";
                            var v = "系统版本：V"+data.core_system_version;
                            if (data.status == 1) {
                                $("#myversion").html(v+newico);
                                $('.msg-icon').removeClass('hide');
                            }
                            else {
                                $("#myversion").html(v);
                            }
                        }});
                }
                else {
                    $('#unauthorize_info').removeClass('hide');
                    ST.Util.showBox('绑定授权', SITEURL + 'index/dialog_unauthorize', 520, 130, null, null, document);

                }
            }});
    }

    $(function(){

        //获取订单,问答,评论数量
        $.ajax({
            type: 'POST',
            url: SITEURL + 'index/ajax_order_num?'+Math.random(),
            dataType: 'json',
            success: function (data) {
                $.each(data, function (i, row) {
                    $("#channel_order_num_"+row.typeid).text(row.num);
                    if (parseInt(row.unviewnum) >0 ) {

                        $("#channel_order_unview_" + row.typeid).text(row.unviewnum);
                        $("#channel_order_unview_" + row.typeid).removeClass('hide');

                    }else{
                        $("#channel_order_unview_" + row.typeid).addClass('hide');
                    }

                    //评论数量
                    if(parseInt(row.comment_num) >= 0){
                        $("#channel_comment_num_" + row.typeid).text(row.comment_num);

                    }else{
                        $("#channel_comment_num_" + row.typeid).addClass('hide');
                    }
                    //未审核评论
                    if(parseInt(row.comment_uncheck_num) > 0){
                        $("#channel_comment_uncheck_num_" + row.typeid).text(row.comment_uncheck_num);
                        $("#channel_comment_uncheck_num_" + row.typeid).removeClass('hide');

                    }else{
                        $("#channel_comment_uncheck_num_" + row.typeid).addClass('hide');
                    }

                    //提问数量
                    if(parseInt(row.question_num) >= 0){
                        $("#channel_question_num_" + row.typeid).text(row.question_num);
                        $("#channel_question_num_" + row.typeid).removeClass('hide');
                    }else{
                        $("#channel_question_num_" + row.typeid).addClass('hide');
                    }
                    //未回复数量
                    if(parseInt(row.question_unans_num) > 0){
                        $("#channel_question_unans_num_" + row.typeid).text(row.question_unans_num);
                        $("#channel_question_unans_num_" + row.typeid).removeClass('hide');
                    }else{
                        $("#channel_question_unans_num_" + row.typeid).addClass('hide');
                    }
                    if(parseInt(row.channel_call_untreated_num)>0)
                    {
                        $('#channel_call_untreated_num').text(row.channel_call_untreated_num)
                        $('#channel_call_untreated_num').removeClass('hide')
                    }
                    else
                    {

                        $('#channel_call_untreated_num').addClass('hide')
                    }
                })
            }
        })

        //链接跳转
        $('.product_item,.config_item,.article_item').click(function(){
            var url = $(this).attr('data-url');
            var data_title=$(this).attr('data-title');
            var title =typeof(data_title)=='undefined'?$(this).text():data_title;
            ST.Util.addTab(title, url);

        })

        //评论,订单,咨询跳转
        $(".data_item").click(function(){
            var url = $(this).data('url');
            var title = $(this).data('name');
            ST.Util.addTab(title,url);
        })

        //chart date choose
        $('#starttime').click(function(){
            calendar.show({ id: this,ok:function(){
                queryChart();
            } })
        })
        $('#endtime').click(function(){
            calendar.show({
                id: this,
                minDay:'$starttime',
                ok:function(){
                    queryChart();
                } })
        })
        $('.date-icon').click(function(){
           $(this).parent().find('input').trigger('click');


        })

        //right check
        checkRightV();

        //浏览器判断
        function myBrowser(){
            var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
            var isOpera = userAgent.indexOf("Opera") > -1;
            if (isOpera) {
                return "Opera"
            } //判断是否Opera浏览器
            if (userAgent.indexOf("Firefox") > -1) {
                return "FF";
            } //判断是否Firefox浏览器
            if (userAgent.indexOf("Chrome") > -1){
                return "Chrome";
            }
            if (userAgent.indexOf("Safari") > -1) {
                return "Safari";
            } //判断是否Safari浏览器
            if (!!window.ActiveXObject || "ActiveXObject" in window) {
                return "IE";
            } //判断是否IE浏览器
        }

        //调用函数判断
        var mb = myBrowser();
        if ("IE" == mb) {
            function resizeWindow(){
                $(window).resize(function(){
                    $(".main-container").width($(".iframe-container").width()-435);
                })
            }
            resizeWindow();
            $(".main-container").width($(".iframe-container").width()-435);
        }

        if ("FF" == mb) {
            function resizeWindow(){
                $(window).resize(function(){
                    $(".main-container").width($(".iframe-container").width()-435);
                })
            }
            resizeWindow();
            $(".main-container").width($(".iframe-container").width()-435);
        }

        if ("Chrome" == mb) {

            function resizeWindow(){
                $(window).resize(function(){
                    $(".main-container").width($(".iframe-container").width()-420);
                })
            }
            resizeWindow();
            $(".main-container").width($(".iframe-container").width()-420);
        }

        if ("Opera" == mb) {

            function resizeWindow(){
                $(window).resize(function(){
                    $(".main-container").width($(".iframe-container").width()-435);
                })
            }
            resizeWindow();
            $(".main-container").width($(".iframe-container").width()-435);
        }

        if ("Safari" == mb) {

            function resizeWindow(){
                $(window).resize(function(){
                    $(".main-container").width($(".iframe-container").width()-435);
                })
            }
            resizeWindow();
            $(".main-container").width($(".iframe-container").width()-435);
        }


        //通知公告
        $(".side-tab-module").each(function(){

            $(this).find(".tab-module-con").eq(0).show();
            $(this).find(".side-hd .item").on("click",function(){
                $(this).addClass("on").siblings().removeClass("on");
                var index = $(this).index();
                $(this).parent().next().children(".tab-module-con").eq(index).show().siblings().hide()
            });

        });

        function scrollMt(){
            $(".news-notice-list").animate({
                "marginTop":"-30px"
            },500,function(){
                $(this).css("marginTop","0px").find("li:first").appendTo($(this))
            })
        }
        var myar = setInterval(scrollMt,3000);
        $(".news-notice-list").hover(function(){
            clearInterval(myar);
        }, function() {
            myar = setInterval(scrollMt,3000);
        });

    })



</script>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.2102&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
