<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>应用商城-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,plist.css,mall.css'); }
    {php echo Common::getScript("template.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
</head>
<body style="overflow:hidden" class="mall">
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td" style="overflow:hidden">
            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tr>
                            <td class="pro-search head-td-lt"></td>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="page-plug">
                <div class="tab-box">
                    <div class="tab-block" id="app-tag">
                        <span class="app-search active" data="0">全部</span>
                        {loop $tag['typeTag'] $v}
                        <span class="app-search" data="{$v['id']}">{$v['name']}</span>
                        {/loop}
                    </div>
                    <div class="search-block">
                        <input type="text" id="app-keyword" class="search-text" placeholder="搜索插件"/>
                        <input type="button" class="app-search search-btn cursor"/>
                    </div>
                </div>
                <!-- 筛选搜索 -->
                <div class="content-block">
                    <div class="jieshao-tit">应用为思途CMS添加新功能，您可以选择并直接安装思途CMS插件应用。购买后，请到【我的应用】进行安装等管理</div>
                    <div class="plug-listbox">
                        <ul id="app-list">
                        </ul>
                    </div>
                </div>
                <!-- 插件列表 -->
            </div>
            <div id="page"></div>
        </td>
    </tr>
</table>
</body>
<scirpt id="apps" type="text/html">
    {{each app as v i}}
    <li>
        <div class="listCon">
            <div class="listPic"><img src="{{v.litpic}}"/><span>版本：{{v.version}}</span></div>
            <div class="listTxt cursor app_info" data="mall/info/menuid/196/number/{{v.number}}">
                <h3 title="{{v.name}}">{{v.name}}</h3>

                <p>{{v.summary}}</p>
                <a>更多详情 &gt;</a>
            </div>
        </div>
        <div class="list-data">
            <span class="num">安装量：{{v.num}}</span>
            <span class="writer">作者：{{v.author}}</span>
            <span class="price">&yen;{{v.price}}</span>
            {{if v.isBuy===1 }}
            {{if v.appStatus!==0 }}
            <a class="now-gm-btn installed-btn">已安装</a>
            {{else}}
            <a class="now-gm-btn install-btn cursor" onclick="ST.Util.addTab('我的应用','mall/app/menuid/193')">安装</a>
            {{/if}}
            {{else}}
            <a class="now-gm-btn cursor" onclick="app_buy('{{v.number}}','{{v.name}}','{{v.price}}')">立即购买</a>
            {{/if}}
        </div>
    </li>
    {{/each}}
</scirpt>
<script>
    var page = 1;
    var baseParam = {'page': page, 'size': 9}
    get_data(baseParam);
    function get_data(param) {
        $.post(SITEURL + 'mall/read', param, function (rs) {
            //初始化
            $('#app-list').html('');
            $("#page").html('');
            if (!rs.success) {
                return false;
            }
            var html = template('apps', rs);
            $('#app-list').html(html);
            var pageHtml = ST.Util.page(rs.size, rs.page, rs.count, 10);
            $("#page").html(pageHtml);
            $('.app_info').on('click', function () {
                ST.Util.addTab("应用详情", SITEURL + $(this).attr('data'));
            });
        }, 'json');
    }
    function get_params(obj) {
        obj.keyword = $('#app-keyword').val();
        obj.type = $('#app-tag').find('.active').attr('data');
        return obj;
    }
    //点击分页
    $("#page a").live('click', function () {
        baseParam.page = $(this).attr('page');
        baseParam = get_params(baseParam);
        get_data(baseParam);
    });
    //标签切换
    $('.app-search').click(function () {
        var param = {'size': baseParam.size, 'page': page};
        $(this).addClass('active').siblings().removeClass('active');
        param = get_params(param);
        get_data(param);
    });
    //购买应用
    function app_buy(number,name,price) {
        ST.Util.confirmBox('购买确认','<div style="width: 350px; text-align: center; line-height: 25px;"><h1 style=" font-size: 14px;">请确认购买应用</h1><div style="margin: 10px 0;"><p><span class="name">应用名称：</span>'+name+'<p><p><span class="name">应用价格：</span><em style="color: #f00;font-style: normal">RMB '+price+'</em><p><p style="text-align: left;color: #A1A1A1; height: 50px; margin: 20px 0;">请注意！应用是以购买授权的方式进行分发，属于是一次性授权消费。所有的应用购买后都不支持退款。</p><p style="color: #f00; font-size: 14px;">购买支付金额：'+price+'元</p></div></div>',function(){
            $.ajax({
                type: 'post',
                async: false,
                url: SITEURL + "mall/ajax_app_buy",
                data: "number=" + number,
                dataType: 'json',
                success: function (rs) {
                    if (rs.status === 1) {
                        if(rs.isfree==1){
                            ST.Util.showMsg('购买成功！', 4);
                        }else{
                            parent.open(rs.url, "_blank");
                        }
                    } else {
                        ST.Util.showMsg(rs.msg, 5, 3000);
                    }
                }
            });
        });
    }
    $(window).resize(function(){
        sizeHeight()
    })
    function sizeHeight()
    {
        var pmHeight = $(window).height();
        var gdHeight = 150;
        $('.page-plug').height(pmHeight-gdHeight);
    }
    sizeHeight();
</script>
</html>