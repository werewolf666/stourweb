<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>图片管理-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,gallery.css,base_new.css'); }
    {php echo Common::getScript("template.js");}
</head>
<body>

<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
        </td>
        <td valign="top" class="content-rt-td">
            <div class="cfg-header-bar clearfix">
                <span class="select-box fl w100 ml-10 mt-5">
                    <select class="select" id="search_type">
                        <option value="0">全部</option>
                        <option value="1">文件夹</option>
                        <option value="2">图片</option>
                    </select>
                </span>
                <span class="cfg-header-search">
                    <input class="search-text" type="text" name="" placeholder="文件夹名称/图片名称" id="keyword"/>
                    <a href="javascript:;" class="search-btn" id="search_btn">搜索</a>
                </span>
                <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
            </div>
            <!--内容区-->
            <div class="pic-manage-body">
                <!--面包屑-->
                <div class="guide addition_show clear_batch hide" id="position">

                </div>
                <!--内容选择区-->
                <div class="recycle-list">
                    <ul class="clearfix" id="select_area">

                    </ul>
                </div>
            </div>
            <!--底部选择项-->
            <div class="st-bottom-console-bar clear clearfix addition_show hide">
                <div class="fl f-0">
                    <a class="btn btn-primary radius" href="javascript:;" id="select_all">全选</a>
                    <a class="btn btn-primary radius ml-10" href="javascript:;" id="select_reverse">反选</a>
                    <a class="btn btn-primary radius ml-10 addition_btn" href="javascript:;" data="restore">还原</a>
                    <a class="btn btn-primary radius ml-10 addition_btn" href="javascript:;" data="delete">删除</a>
                </div>
            </div>
        </td>
    </tr>
</table>
</body>
<script type="text/html" id="template">
    {{each items as item i }}
    <li {{if item._type=='image'}}class="type_img" data="{'id':{{item.id}} }" {{else}}class="type_directory" data="{'id':{{item.id}} }" {{/if}}>
        <div class="pic">
            <div class="img"><img src="{{item.url}}" alt="" title=""/></div>
            <i class="choice-ico"></i>
            <i class="close-ico hide"></i>
        </div>
        <div class="txt">{{item.name}}</div>
    </li>
    {{/each}}
</script>
<script>
    $(function () {
        function getHeight() {
            $("#pic_manage_body").height($(window).height() - 79)
        }

        getHeight();
        $(window).resize(function () {
            getHeight();
        })
    });

    var image_init_param = {
        pid: 0,
        url: SITEURL + 'image/ajax_recycle',
        url_param: {action: 'find',page:1,search_type:0,keyword:''},
        change: true
    };

    //加载数据
    function load_data(type) {
        $.post(image_init_param.url, image_init_param.url_param, function (data) {
            if(image_init_param.url_param.page==1 && data.length>0){
               $('.addition_show').removeClass('hide');
            }
            render_html(data,type);
        }, 'json');
    }
    //渲染模板
    function render_html(data,type) {
        var html = template('template', {items: data});
        if (image_init_param.url_param.page==1) {
            $('#select_area').html(html);
        } else {
            $('#select_area').append(html);
        }
    }
    $('.choice-ico').live('click',function(){
        var parent=$(this).parents('li');
        parent.hasClass('on')?parent.removeClass('on'):parent.addClass('on');
    });
    //全选
    $('#select_all').click(function () {
        $('#select_area').find('li').each(function () {
            $(this).addClass('on');
        });
    });
   //反选
    $('#select_reverse').click(function () {
        $('#select_area').find('li').each(function () {
            if ($(this).hasClass('on')) {
                $(this).removeClass('on');
            } else {
                $(this).addClass('on');
            }
        });
    });

    //还原与删除
    $('.addition_btn').click(function () {
        var data = new Array();
        var info;
        $('#select_area').find('li.on').each(function () {
            info=eval('(' + $(this).attr('data') + ')');
            data.push(info.id);
        });
        var action=$(this).attr('data');
        info= action=='restore' ?{url:'image/ajax_restore',title:'还原'}:{url:'image/ajax_delete',title:'删除'};
        if(data.length<1){
            ST.Util.showMsg("请选择至少一条数据",5);
            return;
        }
        ST.Util.confirmBox('提示', '确定' + info.title+'所选项?', function () {
            $.post(SITEURL + info.url, {data: data}, function (result) {
                if (result.status) {
                    $('#select_area').find('li.on').each(function () {
                        $(this).remove();
                    });
                }
            }, 'json');
        });
    });

    $('#search_btn').click(function () {
        var keyword = $("#keyword").val();
        var search_type = $('#search_type').val();
        image_init_param.url_param.page=1;
        image_init_param.url_param.keyword = keyword;
        switch (search_type) {
            case '0':
                image_init_param.url_param.search_type = 0;
                break;
            case '1':
                image_init_param.url_param.search_type = 1;
                break;
            case '2':
                image_init_param.url_param.search_type = 2;
                break;
        }
        load_data();
    });
    //change变动自动更新
    setInterval(function () {
        if (window.image_init_param.change) {
            window.image_init_param.change = false;
            load_data();
        }
    }, 100);
    //滚动加载
    var setTime;
    $('#pic_manage_body').scroll(function () {
        var viewH = $(this).height();
        var contentH = $('#select_area').height();//内容高度
        var scrollTop = $(this).scrollTop();//滚动高度
        if (contentH - viewH - scrollTop < 100 &&  window.image_init_param.url_param.page>-1) {
            clearTimeout(setTime);
            setTime =setTimeout(function(){
                window.image_init_param.url_param.page+=1;
                load_data();
            },300)
        }
    });
</script>
</html>

<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0813&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
