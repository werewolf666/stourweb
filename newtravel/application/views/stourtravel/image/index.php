<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title color_background=zm8iDl >图片管理-思途CMS{$coreVersion}</title>
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
                <span class="select-box w100 ml-10 mt-5 fl">
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
                <div class="f-0 mt-6 fr">
                    <a href="javascript:;" class="btn btn-primary radius mr-10" id="upload_image">上传图片</a>
                    <a href="javascript:;" class="btn btn-primary radius mr-10" id="create_group">创建相册</a>
                    <a href="javascript:;" class="btn btn-primary radius mr-10" id="img_promote">图片加速</a>
                    {php $config=Model_Menu_New::get_by_title('图片服务器')}
                    <a href="javascript:;" class="btn btn-primary radius mr-10" id="img_server" data="{$config['url']}">图片服务器</a>
                   <!-- <a href="javascript:;" class="btn btn-primary radius mr-10" id="img_config">图库配置{Common::get_help_icon('image_url')}</a>-->
                    <a href="javascript:;" class="btn btn-primary radius mr-10" id="recycle">回收站</a>
                    <a href="javascript:;" class="btn btn-primary radius mr-10" onclick="window.location.reload()">刷新</a>
                </div>
            </div>
            <!--内容区-->
            <div class="pic-manage-body" id="pic_manage_body">
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
                    <a class="btn btn-primary radius" href="javascript:;" id="batch">批量管理</a>
                    <a class="btn btn-primary radius ml-10 hide" href="javascript:;" id="move_more">批量移动</a>
                    <a class="btn btn-primary radius ml-10 hide" href="javascript:;" id="del_more">批量删除</a>
                </div>
            </div>
        </td>
    </tr>
</table>
</body>
<script type="text/html" id="template">
    {{each items as item i }}
    <li {{if item._type=='image'}}class="type_img" data="{'group_id':{{item.group_id}},'id':{{item.id}},}" {{else}}class="type_directory" data="{'pid':{{item.pid}},'id':{{item.group_id}},}" {{/if}}>
    <div class="pic">
        <div class="img"><img src="{{item.url}}" alt="" title=""/></div>
        <i class="choice-ico hide"></i>
        {{if item.do_not==0}}
        <i class="close-ico hide"></i>
        {{/if}}
    </div>
    {{if item.do_not=='0'}}
    <input class="edit edit_input " type="text"  data="{{if item._type=='image'}}{{item.image_name.replace(/"/,'\\"')}}{{else}}{{item.group_name.replace(/"/,'\\"')}}{{/if}}" value="{{if item._type=='image'}}{{item.image_name}}{{else}}{{item.group_name}}{{/if}}" placeholder="">
    {{else}}
    <div class="txt">{{item.group_name}}</div>
    {{/if}}
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
        url: SITEURL + 'image/group_manage',
        url_param: {action: 'find',page:1,search_type:0,keyword:''},
        change: true
    };

    //上传图片
    $('#upload_image').click(function () {
        ST.Util.showBox('上传图片', SITEURL + 'image/upload_view/groupid/'+image_init_param.pid, 700, 480, null, null,document,{loadWindow: window, loadCallback: uploadInfo});
        function uploadInfo(res,bool){
            if(image_init_param.pid==res.id){
                window.image_init_param.url = SITEURL + 'image/group_manage';
                window.image_init_param.url_param={action:'find',page:1,search_type:0,keyword:''};
                window.image_init_param.change = true;
            }
        }
    });
    //创建相册
    $('#create_group').click(function () {
        ST.Util.showBox('创建相册', SITEURL + 'image/group_view', 340, 191, null, null, document, {loadWindow: window, loadCallback: add_group});
        //创建分组
        function add_group(result, bool) {
            result.data.action = 'add';
            result.data.pid = window.image_init_param.pid;
            result.data.level=parseInt('{$level}')+1;
            $.ajax({
                url: SITEURL + 'image/group_manage',
                async: false,
                type: 'POST',
                dataType: 'json',
                data: result.data,
                success: function (data) {
                    if(data > 0){
                        ST.Util.showMsg("添加成功", 4);
                        window.image_init_param.url=SITEURL + 'image/group_manage';
                        window.image_init_param.url_param={action:'find',page:1,search_type:0,keyword:''};
                        window.image_init_param.change = true;
                    }else{
                        ST.Util.showMsg("添加失败", 5);
                    }
                }
            });
        }
    });
    //图库配置
    $('#img_config').click(function(){
        ST.Util.showBox('图库配置', SITEURL + 'image/config',330,106, null, null, document, {loadWindow: window, loadCallback: imgConfig});
        function imgConfig(res,bool){
            if (bool) {
                ST.Util.showMsg(res, 1);
            } else {
                ST.Util.showMsg(res, 1);
            }
        }
    });
    //图片加速
    $('#img_promote').click(function () {
        var url = SITEURL + "image/promote/menuid/{$_GET['menuid']}";
        ST.Util.addTab('图片加速', url);
    });
    //图片服务器
    $('#img_server').click(function () {
        ST.Util.addTab('图片服务器', $(this).attr('data'));
    });
    //回收站
    $("#recycle").click(function () {
        var url = SITEURL + "image/recycle/menuid/{$_GET['menuid']}";
        ST.Util.addTab('图库管理-回收站', url);
    });
    //批量管理
    $('#batch').click(function(){
        if ($('#move_more').hasClass('hide')) {
            $('#move_more').removeClass('hide');
            $('#del_more').removeClass('hide');
            $('.type_img').find('.choice-ico').removeClass('hide');
        } else {
            $('#move_more').addClass('hide');
            $('#del_more').addClass('hide');
            $('.type_img').find('.choice-ico').addClass('hide').parents('li').removeClass('on');
        }
    });
    $('.clear_batch').click(function(){
        $('.choice-ico').addClass('hide').parents('li').removeClass('on');
        $('#move_more').addClass('hide');
        $('#del_more').addClass('hide');
    });
    //批量删除
    $('#del_more').click(function () {
        var lis = $('#select_area').find('li.on');
        if (lis.length < 1) {
            ST.Util.showMsg("没有选择要删除的图片！", 1);
            return;
        }
        ST.Util.confirmBox('提示', '是否删除所选图片', function () {
            var ids = new Array();
            lis.each(function () {
                var data=eval('('+$(this).attr('data')+')');
                ids.push(data.id);
            });
            $.post(SITEURL + 'image/image_manage', {action: 'delete', id: ids.join(',')}, function (data) {
                if(data > 0 ){
                    ST.Util.showMsg("删除成功！", 4);
                    $('#select_area').find('li.on').remove();
                }else{
                    ST.Util.showMsg("删除失败！", 5);
                }
            }, 'json');
        });
    });
    //批量移动
    $('#move_more').click(function () {
        var lis = $('#select_area').find('li.on');
        if (lis.length < 1) {
            ST.Util.showMsg("没有选择要移动的图片！", 1);
            return;
        }
        var arr = new Array();
        lis.each(function () {
            var data=eval('('+$(this).attr('data')+')');
            arr.push(data.id);
        });
        ST.Util.showBox('移动到相册', SITEURL + 'image/image_move/id/' + arr.join(','), 457, 340, null, null, document, {loadWindow: window, loadCallback: move_more});
        function move_more(result, bool) {
            if (bool) {
                //重载
                ST.Util.showMsg("移动成功！", 1);
            } else {
                ST.Util.showMsg("移动失败！", 1);
            }
            $('#select_area').find('li.on').remove();
        }
    });
    //search_btn
    $('#search_btn').click(function () {
        var keyword = $("#keyword").val();
        var search_type = $('#search_type').val();
        image_init_param.url_param.page=1;
        image_init_param.url_param.keyword = keyword;
        switch (search_type) {
            case '0':
                image_init_param.url=SITEURL + 'image/group_manage';
                image_init_param.url_param.search_type = 0;
                break;
            case '1':
                image_init_param.url=SITEURL + 'image/group_manage';
                image_init_param.url_param.search_type = 1;
                break;
            case '2':
                image_init_param.url=SITEURL + 'image/image_manage';
                image_init_param.url_param.search_type = 2;
                break;
        }
        image_init();
        $('#select_area').html('');
    });

    function image_init() {
        load_data()
    }
    //加载数据
    function load_data() {
        image_init_param.url_param.pid = image_init_param.pid;
        $.post(image_init_param.url, image_init_param.url_param, function (data) {
            render_html(data);
            if (image_init_param.url_param.search_type==0) {
                //加载页面附加信息
                page_addition(image_init_param.url_param.pid);
                //加载图片
                window.image_init_param.url = SITEURL + 'image/image_manage';
                window.image_init_param.action = 'find';
                image_init_param.url_param.search_type=2;
                load_data();
            }
        }, 'json');
    }
    //渲染模板
    function render_html(data) {
        var html = template('template', {items: data});
        if (image_init_param.url_param.search_type!=2) {
            $('#select_area').html(html);
        } else {
            $('#select_area').append(html);
        }
        //目录点击
        $('.type_directory .img').each(function(){
            $(this).click(function(){
                var data=eval('('+$(this).parents('li').attr('data')+')');
                window.image_init_param.pid=data.id;
                window.image_init_param.url=SITEURL + 'image/group_manage';
                window.image_init_param.action='find';
                window.image_init_param.url_param.search_type=0;
                window.image_init_param.change = true;
                $('#select_area').html('');
            });
        });
        //删除
        $('.pic').hover(function () {
            $(this).find('.close-ico').removeClass('hide');
        }, function () {
            $(this).find('.close-ico').addClass('hide');
        });
    }
    $('.close-ico').live('click', function () {
        var parent_node = $(this).parents('li');
        var data = eval('(' + parent_node.attr('data') + ')');
        var info = parent_node.hasClass('type_directory') ? {title: '相册','url': SITEURL + 'image/group_manage'} : {title: '图片', url: SITEURL + 'image/image_manage'};
        ST.Util.confirmBox('提示', '是否删除该' + info.title, function () {
            $.post(info.url, {action: 'delete', id: data.id});
            parent_node.remove();
        });

    });
    $('.choice-ico').live('click',function () {
        var parent = $(this).parents('li');
        parent.hasClass('on') ? parent.removeClass('on') : parent.addClass('on');
    });
    $('.edit_input').live('blur',function(){
        var checkLong = false;
        var parent_node = $(this).parents('li');
        var old_data = $(this).attr('data');
        var new_data = $(this).val().replace(/^\s*/, '').replace(/\s*$/, '');
        if ($(this).parents('li').hasClass('type_directory') && $(this).val().length > 20) {
            checkLong = true;
        }
        if (checkLong || new_data.length < 1) {
            $(this).val(old_data);
            return;
        } else {
            $(this).val(new_data).attr('data', new_data.replace(/"/, '\\"'));
        }
        var url = parent_node.hasClass('type_directory') ? 'image/group_manage' : 'image/image_manage';
        var data = eval('(' + parent_node.attr('data') + ')');
        $.post(SITEURL + url, {action: 'rename', id: data.id, name: $(this).val()}, function (data) {
        }, 'json');
    });
    function page_addition(pid) {
        if (pid > 0) {
            $.post(SITEURL + 'image/group_manage', {action: 'find_position', pid: pid}, function (data) {
                $('.addition_show').removeClass('hide');
                $('#position').html(data.position);
                if (data.level >= 3) {
                    $('#create_group').addClass('hide');
                } else {
                    $('#create_group').removeClass('hide');
                }
                //通过定位选择分组
                $('#position').find('a').click(function () {
                    var data = eval('(' + $(this).attr('data') + ')');
                    window.image_init_param.pid = data.pid;
                    window.image_init_param.url = SITEURL + 'image/group_manage';
                    window.image_init_param.url_param={action:'find',page:1,search_type:0,keyword:''};
                    window.image_init_param.change = true;
                    $('#search_type').find('option:eq(0)').attr('selected',true);
                    $('#keyword').val('');
                    $('#select_area').html('');
                    $('#move_more').addClass('hide');
                    $('#del_more').addClass('hide');
                });

            }, 'json');
        } else {
            $('.addition_show').addClass('hide');
        }
    }
    //change变动自动更新
    setInterval(function () {
        if (window.image_init_param.change) {
            window.image_init_param.change = false;
            image_init();
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

