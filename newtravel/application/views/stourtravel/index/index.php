<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$configinfo['cfg_webname']}-思途CMS{$coreVersion}</title>
    {include 'stourtravel/public/public_js'}
    {Common::getCss('index_7.css,base.css')}
    {Common::getScript('artDialog/lib/sea.js')}
    {Common::css_plugin('guide.css','howtouse')}


</head>


<body>
<!--顶部-->
<div class="header-wrapper" id="header">

    <div class="header-logo">
        <div class="lg-area">
            <img src="{$GLOBALS['cfg_public_url']}images/logo.png" />
        </div>
    </div>
    <!-- logo -->

    <div class="header-search-box">
        <input class="search-text" id="search-text" type="text" placeholder="检索功能" />
        <i class="search-btn" id="search_submit"></i>
    </div>
    <!-- 全局搜索 -->

    <div class="header-out">
        <a class="out-btn-link" href="javascript:;" id="clickout" title="退出"></a>
    </div>
    <!-- 退出登录 -->

    <div class="header-menu">
        <a class="item" href="../" target="_blank">电脑端</a>
        <a class="item" href="javascript:;" id="mobile_dialog">移动端</a>
        <a class="item" href="javascript:;" id="clearbtn" target="_blank">清缓存</a>
        <a class="item" href="javascript:;" id="howtouse" target="_blank">教程</a>
        <a class="item" href="http://www.stourweb.com/help/" target="_blank">帮助</a>
        <a class="item" href="http://www.stourweb.com/user/" target="_blank">工单</a>
        <a class="item" href="javascript:;" id="link_dialog">支持</a>
        <a class="item " id="userbtn" href="javascript:;">
            {if empty($admin_litpic)}
                <img src="{$GLOBALS['cfg_public_url']}images/default-hd-img.png">
            {else}
                <img src="{$admin_litpic}" >
            {/if}
            <span class="admin-name">{$username}</span>
        </a>
    </div>
    <!-- 操作菜单 -->
    <div id="tabs"></div>

</div>

<div class="remind-msg-tip hide">您可以通过这里再次进入教程</div>

<!--<a class="home-fixed-btn" href="javascript:;" title="主页"></a>-->



<script language="JavaScript">
window.currentTab = null;


Ext.onReady(function () {


    //创建viewpost,代表整个屏幕
    window.gbl_viewport = Ext.create('Ext.container.Viewport', {
        layout: 'border',
        overflowX: 'hidden',
        items: [
            {
                region: 'north',
                contentEl: 'header',
                border: false,
                cls: 'no-hidden'
            }
        ]
    });


    $.get("index/ajax_clear_log",function(data,status){});

    window.mainTabId='';
    //tabpanel, 放置各种页面
    window.gbl_tabs = Ext.create('Ext.tab.Panel', {
        autoScroll: false,
        region: 'center',
        border: false,
        cls: 'gbl_tabs',
        renderTo: 'tabs',
        style:'border:0px',
        bodyStyle: 'border-color:white;border-width:0px;border-style:none;border:0px',
        tabBar: {
            style: "background:#fff",
            componentCls: 'gbl-tbar',
            height: 37,
            border: 0


        },
        items: [
            {
                title: '主页',
                html: "<iframe src='{$cmsurl}index/index7' id='change_iframe' width='100%' height='100%' frameborder='0px' ></iframe>",
                fixed:true,
                listeners: {

                    show: function (tab) {
                       // $("#indexfrm").show();
                        window.currentTab = 0;
                        window.mainTabId=tab.getId();

                    }
                }
            }


        ],
        listeners: {
            afterrender: function (tab, eOpts) {
                //实现右键菜单功能
                tab.tabBar.el.on('contextmenu', function (event, htmlele) {
                    var ele = tab.tabBar.getChildByElement(htmlele),
                        index = tab.tabBar.items.indexOf(ele);
                    tab.menuIndex = index;
                    var menu = Ext.create("Ext.menu.Menu", {
                        items: [
                            {text: '返回主页', handler: tab.backIndex},
                            {text: '关闭所有', handler: tab.closeAll},
                            {text: '关闭右侧页面', handler: tab.closeRight},
                            {text: '关闭左侧页面', handler: tab.closeLeft},
                            {text: '刷新当前页面', handler: tab.refreshPage}

                        ]
                    });
                    menu.showAt(event.getXY())
                    event.preventDefault();
                    window.barmenu=menu;
                });
            },
            tabchange:function( tabPanel, newCard, oldCard, eOpts ){

                   var newId=newCard.getId();

                   if(newId!=mainTabId)
                   {
                       $(".found_box").show();
                   }
                   else
                   {
                       $(".found_box").hide();
                   }
            }

        },
        closeAll: function () {    //关闭所有
            var tab = gbl_tabs;
            tab.items.each(function (item) {
                if (item.closable)
                    tab.remove(item);
            });
        },
        closeCurrent: function ()  //关闭当前
        {
            var tab = gbl_tabs;
            if (this.items.get(this.menuIndex).closable)
                tab.remove(this.items.get(this.menuIndex));
        },
        refreshPage: function () {
            var tab = gbl_tabs;
            var panel = tab.items.get(tab.menuIndex);
            var ifm = panel.getEl().down('iframe');
            ifm.dom.contentWindow.location.reload();

        },
        closeRight: function ()   //关闭右侧
        {
            var tab = gbl_tabs;
            var i = 0;
            tab.items.each(function (item) {
                if (i > tab.menuIndex)
                    if (item.closable)
                        tab.remove(item);
                i++;
            });
        },
        closeLeft: function ()  //关闭左侧
        {
            var tab = gbl_tabs;
            var i = 0;
            tab.items.each(function (item) {
                if (i < tab.menuIndex) {
                    if (item.closable)
                        tab.remove(item);
                }
                i++;
            });
        },
        backIndex:function(){ //返回首页
            window.gbl_tabs.setActiveTab(0);
        }
    });

    //将tab面板加入视窗
    gbl_viewport.add(gbl_tabs);

    //全局设置tab 标题的宽度
    Ext.tab.Tab.prototype.maxWidth = 250;

    //index page button
    $('.home-fixed-btn').click(function(){
        window.gbl_tabs.setActiveTab(0);
    })

    //清除缓存
    $("#clearbtn").click(function () {
        $.ajax(
            {
                type: "post",
                url: SITEURL + 'index/ajax_clearcache?clear=all',
                beforeSend: function () {
                    ST.Util.showMsg('正在清除缓存,请稍后...', 6, 20000);
                },
                success: function (data) {
                    if (data == 'ok') {
                        ST.Util.showMsg('缓存清除成功', 4, 1000);
                    }
                },

                error: function () {

                    ST.Util.showMsg("请求出错,请联系管理员", 5, 1000);
                }

            }
        );

    })


    //退出
    $('#clickout').click(function () {
        ST.Util.confirmBox('退出系统', '确定退出吗?', function () {
            $('#bg').show();
            window.location.href = SITEURL + 'login/loginout';

        })

    })
    //用户管理
    $("#userbtn").click(function () {
        ST.Util.addTab('用户管理', SITEURL + 'user/list/menuid/168');
    })
    //功能搜索
    $('#search_submit').click(function(){
        var title = $('#search-text').val().trim();
        if(title.length>0){
            var url='quickmenu/search?keyword='+title;
            ST.Util.removeTab(url);
            ST.Util.addTab('检索功能', url);
        }else{
            ST.Util.showMsg('请输入您的搜索关键词',5,1000)
        }
    });

    //显示移动端
    $('#mobile_dialog').click(function(){
        ST.Util.showBox('移动端预览', SITEURL + 'index/dialog_mobile', 490, 188, null, null, document);

    })
    //联系思途
    $('#link_dialog').click(function(){
        ST.Util.showBox('联系思途', SITEURL + 'index/dialog_link', 520, 143, null, null, document);

    })



})

//添加面板
window.addTab = function (title, url, issingle, options) {
    $("#indexfrm").hide();
    var id = null;
    if (issingle) {
        var _url = encodeURI(url);
        id = _url.replace(/(\/)|(\/)|(\?)|(\#)|(\%)|(\&)|(\=)/ig, '_');
        var current_panel = window.gbl_tabs.down('#' + id);
        if (current_panel) {
            window.gbl_tabs.setActiveTab(current_panel);
            return;
        }
    }
    var item = {
        title: title,
        html: "<iframe src='" + url + "' frameborder='0' width='100%' height='100%' scrolling='auto'  border='0'/>",
        closable: true,
        id: id,
        listeners: {
            'beforeclose':function(o){
                var ele=o.getEl().down('iframe').dom;

            },
            'close': function (o) {


                var len = $('.gbl_tabs').find('.x-tab').length;
                if (len == 1) {
                    $("#indexfrm").show();
                }
            },
            show: function (item) {
                window.currentTab = item;
                item.focus();
            }
        }
    };
    Ext.apply(item, options);
    var tab = window.gbl_tabs.add(item);
    window.gbl_tabs.setActiveTab(tab);

}
window.removeTab= function (url){
    var _url = encodeURI(url);
    id = _url.replace(/(\/)|(\/)|(\?)|(\#)|(\%)|(\&)|(\=)/ig, '_');
    window.gbl_tabs.remove(id);
}
//加载artDialog

window.dialog = null;
window.d = null;
seajs.config({
    alias: {
        "jquery": "jquery-1.10.2.js"
    }
});
//定义全局dialog对象
seajs.use([PUBLICURL + 'js/artDialog/src/dialog-plus'], function (dialog) {
    window.dialog = dialog;

});
//弹出框

/*
  params为附加参数，可以是与dialog有关的所有参数，也可以是自定义参数
  其中自定义参数里有
  loadWindow: 表示回调函数的window
  loadCallback: 表示回调函数
  maxHeight:指定最高高度

 */
function floatBox(boxtitle, url, boxwidth, boxheight, closefunc, nofade,fromdocument,params) {
    boxwidth = boxwidth != '' ? boxwidth : 0;
    boxheight = boxheight != '' ? boxheight : 0;
    var func = $.isFunction(closefunc) ? closefunc : function () {
    };
    fromdocument = fromdocument ? fromdocument : null;//来源document

    var initParams={
        url: url,
        title: boxtitle,
        width: boxwidth,
        height: boxheight,
        loadDocument:fromdocument,
        onclose: function () {
            func();
        }
    }
    initParams= $.extend(initParams,params);

    var dlg = window.dialog(initParams);


    if(typeof(dlg.loadCallback)=='function'&&typeof(dlg.loadWindow)=='object')
    {
       dlg.finalResponse=function(arg,bool,isopen){
            dlg.loadCallback.call(dlg.loadWindow,arg,bool);
            if(!isopen)
              this.remove();
       }
    }

    window.d=dlg;
    if (initParams.width != 0) {
        d.width(initParams.width);
    }
    if (initParams.height!= 0) {
        d.height(initParams.height);
    }
  
    if (nofade) {
        d.show()
    } else {
        d.showModal();
    }

}

//计算报价(报价查看时使用)
function calPrice(obj) {
    var trs = $(obj).parents('tr:first');


    var tprice = 0;
    trs.find('input:text').each(function (index, element) {
        var price = parseInt($(element).val());
        if (!isNaN(price))
            tprice += price;
    });
    trs.find(".tprice").html("<font color='#FF9900'>" + tprice + "</font>元");
}

//显示/隐藏indexfrm
function showIndex() {
    var len = $('.gbl_tabs').find('.x-tab').length;
    if (len == 1) {
        return false;
    }
    if (window.currentTab == 0) {
        window.gbl_tabs.setActiveTab(1);
    }

    $("#indexfrm").toggle();
}
function hideIndex() {
    var len = $('.gbl_tabs').find('.x-tab').length;
    if (len == 1) {
        return false;
    }
    $('#indexfrm').hide();
    if (window.currentTab == 0) {
        window.gbl_tabs.setActiveTab(1);
    }
    else {
        window.gbl_tabs.setActiveTab(window.currentTab);
    }

}


//设置cookie
function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue + "; ";
}
//获取cookie
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}
//默认新版
setCookie("current_version", 1);

{if $configinfo['cfg_howtouse_prompt_disabled'] != "1"}
    setTimeout(function(){
        ST.Util.showBox('', SITEURL + 'howtouse/admin/index/prompt',550,320);
    },5000);
{/if}

function show_newbie_guide_menu_tips()
{
    $(".remind-msg-tip").removeClass('hide');
    setTimeout(function(){
        $(".remind-msg-tip").addClass('hide');
    },10000);
}

$.ajax(
    {
        type: "get",
        url: SITEURL + 'howtouse/admin/index/ajax_first_usage_guide_menu',
        dataType: 'json',
        success: function (data) {
            if (data.status == '1') {
                $("#howtouse").click(function () {
                    ST.Util.addTab(data.data.title, '{$cmsurl}howtouse/admin/index/index/menuid/' + data.data.id);
                });
            }
        }

    }
);
</script>


</body>

</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2404&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
