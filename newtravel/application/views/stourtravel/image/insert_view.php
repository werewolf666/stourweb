<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>移动到组-思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,picmanage.css'); }
    <script type="text/javascript" src="{$publicPath}js/image/plupload.full.min.js"></script>
    <script type="text/javascript" src="{$publicPath}js/image/zh_CN.js"></script>
    <script>
        var height=$('.ui-dialog-content',window.parent.document).height();
        $('iframe',parent.document).each(function(){
            if(/insert_view/.test($(this).attr('src'))){
               $(this).parents('.ui-dialog-body').css('padding',0);
               height = $(this).parent().height();
            }
        });


    </script>
</head>
<script>
    var public_path = '{$publicPath}';
</script>
<body bottom_top=5ZGwOs >
<div class="pic-manage-container" id="picManageContainer">
    <div class="manage-tab-wrap">
        <div class="manage-tab-nav">
            <span class="item on" data="album">相册选择</span>
            <span class="item" data="local">本地上传</span>
            <span class="item" data="net">网络图片</span>
        </div>
        <div class="manage-tab-box">
            <div class="manage-tab-bd" id="album_content">
                <div class="manage-tab-bar">
                    <span class="col-tit">选择相册照片</span>
                    <a class="back-link" href="#"><i class="back-icon"></i>上一级</a>
                    <div class="search-pic-box fr">
                        <select class="select-box fl" id="search_type">
                            <option value="0" selected>全部</option>
                            <option value="1">文件夹</option>
                            <option value="2">图片</option>
                        </select>
                        <input type="text" class="search-text fl" id="search_keys" placeholder="输入文件夹名称/图片名称" />
                        <input type="button" class="search-btn fl" />
                    </div>
                </div>
                <div class="manage-tab-block">
                    <div class="manage-file-wrap">
                        <ul class="manage-file-list clearfix" id="select_content">

                        </ul>
                    </div>
                </div>
            </div>
            <div class="manage-tab-bd" id="local_content" style="display: none;">
                <div class="manage-tab-bar">
                    <span class="col-tit">上传到</span>
                    <div class="move-group-box">
                        <div class="move-group-input">

                            <input type="text" id="groupInputArea" data-id="{$group[0]['group_id']}" class="input-text w200" value="{$group[0]['group_name']}" readonly>
                            <i class="arrow-icon"></i>
                        </div>
                        <ul id="moveListContainer" class="move-list-container">
                            {loop $group $item}
                            <li><span class="group-title {if $item['_depth']==1}group-3{elseif $item['_depth']==2}group-5{/if}" data-id="{$item['group_id']}" data-name="{$item['group_name']}">{if $item['_depth']>0}<i class="level-icon"></i>{/if}{$item['group_name']}</span></li>
                            {/loop}
                        </ul>
                    </div>
                    <span class="col-tit">上传图片大小每张不超过2M</span>
                </div>
                <div class="manage-tab-block">
                    <div class="manage-file-wrap">
                        <ul class="manage-file-list clearfix" id="local_upload">

                            <li class="add-item" id="add_again" style="display: none">
                                <i class="add-icon"></i>
                                <div class="add-txt">添加照片</div>
                            </li>
                        </ul>
                    </div>
                    <div class="add-img-area" id="add">
                        <a class="add-img-btn" href="javascript:;"><i class="add-img-icon"></i>选择照片</a>
                        <p class="ts-txt">按住Ctrl可多选照片</p>
                    </div>
                </div>
            </div>
            <div class="manage-tab-bd" id="net_content" style="display: none;">
                <div class="manage-tab-bar">
                    <span class="col-tit">输入链接地址，如 http://www.stourweb.com/Public/Home/images/top_logo.png</span>
                </div>
                <div class="manage-tab-block">
                    <div class="manage-net-box">
                        <ul id="net_url">
                            <li>
                                <span class="item-hd">图片地址</span>
                                <div class="item-bd">
                                    <input type="text" class="link-value" value="" placeholder="" />
                                </div>
                            </li>
                            <li id="add_net_li">
                                <span class="item-hd"></span>
                                <div class="item-bd">
                                    <a class="add-link-btn" href="javascript:;"><i class="link-icon"></i>添加链接</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="manage-btn-block">
            <a class="btn btn-primary" id="submit" href="#">确定</a>
            <a class="btn btn-default" id="cancel" href="#">取消</a>
        </div>
    </div>
</div>
<!--插入图片-->
</body>
<script>
    $('.manage-tab-block').css('height',height-150);
    /*********相册选择**********/
    var page=1;
    var pid=0;
    var queue=new Array();
    var go_back=new Array();
    $(function () {
        $('.manage-tab-nav').find('span').click(function () {
            var attr = $(this).attr('data');
            $(this).addClass('on').siblings().removeClass('on');
            $('#' + attr + '_content').css('display', '').siblings('.manage-tab-bd').css('display', 'none');
        });
    });
    //
    getImages(1);
    //加载对应PID下的目录及图片
    function getImages(p){
        page=p;
        if(p==1){
            ST.Util.showMsg('加载中...',6,100000)
        }
        var search_keys = $("#search_keys").val();
        var search_type = $('#search_type').val();
        if(page==1){
            $('#select_content').find('li.pic-item,li.file-item').remove();
            //文件夹
            if(search_type!=2){
                //搜索图片
                getImageGroup(search_keys,search_type);
            }
        }
        if(search_type==1){
            //搜索文件夹
            return true;
        }
        $.post(SITEURL + 'image/image_manage', {action: 'find', pid:pid,'page':p, 'keyword':search_keys,'search_type':search_type}, function (data) {
             var html='';
             for(var i in data){
                 html+='<li class="pic-item" data="'+data[i].url+'$$'+data[i]['image_name']+'">';
                 html+='<i class="active-icon"></i>';
                 html+='<div class="pic-img"><img src="'+data[i].url+'"/></div>';
                 html+='<div class="pic-name" title="'+data[i].image_name+'">'+data[i].image_name+'</div>';
                 html+='</li>';
             }
            $('#select_content').append(html);
            ST.Util.hideMsgBox();
        }, 'json');
    }
    function getImageGroup(search_keys,search_type){
        $.post(SITEURL + 'image/group_manage', {'action': 'find','pid':pid,'keyword':search_keys,'search_type':search_type}, function (data) {
            var groupHtml='';
            for (var i in data) {
                groupHtml += '<li class="file-item" data="{\'pid\':'+data[i]['group_id']+',\'goBack\':'+data[i]['pid']+'}">';
                groupHtml += '<div class="file-icon"></div>';
                groupHtml += '<div class="file-name">' + data[i]['group_name'] + '</div>';
                groupHtml += '</li>';
            }
            $('#select_content').prepend(groupHtml);
            ST.Util.hideMsgBox();
        }, 'json');
    }
    //下级目录
    $('#select_content .file-item').live('click', function () {
        var data = eval('(' + $(this).attr('data') + ')');
        pid = data.pid;
        go_back.push(data.goBack);
        $("#search_keys").val('');
        getImages(1);
    });
    //上一级
    $('.back-link').click(function () {
        if (go_back.length < 1) {
            return true;
        }
        pid=go_back.pop();
        getImages(1);
    });
    //选择相册图片
    $('#select_content .pic-item').live('click', function () {
        var data = $(this).attr('data');
        if ($(this).hasClass('active-item')) {
            var default_data=new Array();
            $(this).removeClass('active-item');
            for(var i in queue){
                if(queue[i]==data){
                    delete queue[i];
                }else{
                    default_data.push(queue[i]);
                }
            }
            queue=default_data;


        } else {
            $(this).addClass('active-item')
            queue.push(data);
        }

    });
    $('.search-btn').click(function(){
        getImages(1);
    });
    //滚动加载
    var setTime='';
    $('#album_content').find('.manage-tab-block').scroll(function () {
        var viewH = $(this).height();
        var contentH = $('#select_content').height();//内容高度
        var scrollTop = $(this).scrollTop();//滚动高度
        if (contentH - scrollTop - viewH < 210 && page > -1) {
            clearTimeout(setTime);
            setTime = setTimeout(function () {
                getImages(page + 1);
            }, 300)
        }
    });
    /*********本地上传***********/
    $('#groupInputArea').click(function(e){
        e.stopPropagation();
       $('#moveListContainer').css('display','block');
    });

    $('#moveListContainer').find('span').each(function(){
        $(this).click(function(){
            $('#groupInputArea').attr('data-id',$(this).attr('data-id'));
            $('#groupInputArea').val($(this).attr('data-name'));
        });
    });

    $('body').click(function(){
        $('#moveListContainer').css('display','none');
    });
    var uploader = new plupload.Uploader({
        //runtimes: 'flash',
        browse_button: ['add','add_again'],
        url: 'temp',
        flash_swf_url: '{$publicPath;}js/image/Moxie.swf',
        silverlight_xap_url : '{$publicPath;}js/image/Moxie.xap',
        filters: {
            max_file_size: '2mb',
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,png,jpeg,ico"},
            ]
        },
        init: {
            BeforeUpload: function (uploader, file) {
                uploader.settings.url = SITEURL + 'image/upload/iswater/0/groupid/' + $('#groupInputArea').attr('data-id');
            },
            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    var tpl = '<li class="pic-item" id="{id}" name="{name}"><div class="pic-img"><div class="progress-wrap"><span class="progress-bar" style=" width: 0%"></span><em class="txt">上传中 45%</em></div><img src="{imgsrc}" /></div><div class="pic-name">{name}</div></li>';
                    tpl = tpl.replace("{id}", file.id);
                    previewImage(file, function (imgsrc) {
                        tpl = tpl.replace("{imgsrc}", imgsrc).replace(/\{name\}/g,file['name'].replace(/\.[a-zA-Z]{3,4}$/,''));
                        $('#local_upload').prepend(tpl);
                        $('#add_again').css('display','block');
                        $('#add').css('display','none');
                    })
                });
                uploader.start();

            },
            FileUploaded: function (up, file, info) {
                $('#' + file.id).attr('data', info.response);
            },
            UploadProgress: function (up, file) {
                if (file.percent < 100) {
                    $('#' + file.id).find('.progress-wrap').html('<span class="progress-bar" style=" width: ' + file.percent + '%"></span><em class="txt">上传中 ' + file.percent + '%</em>');
                } else {
                    $('#' + file.id).find('.progress-wrap').html('<span class="progress-bar" style=" width: ' + file.percent + '%"></span><em class="txt">已上传 </em>');
                }
            },
            Error: function(up, err) {
                if(err.code==-600){
                    ST.Util.showMsg('每张上传图片不超过2M',5,1000);
                }
            }
        }
    });
    function previewImage(file, callback) {
        if (!file || !/image\//.test(file.type)) return;
        if (file.type == 'image/gif' || file.type == 'image/x-icon') {
            var fr = new mOxie.FileReader();
            fr.onload = function () {
                callback(fr.result);
                fr.destroy();
                fr = null;
            }
            fr.readAsDataURL(file.getSource());
        } else {
            var preloader = new mOxie.Image();
            preloader.onload = function () {
                preloader.downsize(86, 86);
                var imgsrc = preloader.type == 'image/jpeg' ? preloader.getAsDataURL('image/jpeg', 80) : preloader.getAsDataURL(); //得到图片src,实
                callback && callback(imgsrc);
                preloader.destroy();
                preloader = null;
            };
            preloader.load(file.getSource());
        }
    }
    uploader.init();
    /***********网络图片**************/
    $('.add-link-btn').click(function(){
        var tpl='<li><span class="item-hd">图片地址</span><div class="item-bd"><input type="text" class="link-value" value="" placeholder="" /></div></li>';
        $('#add_net_li').before(tpl);

    });

    /*************提交***************/
    $('#submit').live('click', function () {
        var data = [];
        var attr = $('.manage-tab-nav').find('span.on').attr('data');
        switch (attr) {
            case 'album':
                data=queue;
                break;
            case 'local':
                $('#local_upload').find('li[data]').each(function () {
                    data.push($(this).attr('data')+'$$'+$(this).attr('name'));
                });
                break;
            case 'net':
                $('#net_url').find('.link-value').each(function () {
                    if ($(this).val() != '') {
                        data.push($(this).val());
                    }
                });
                break;
        }
        ST.Util.responseDialog({data:data},true);
    });
    //取消
    $('#cancel').live('click', function () {
        ST.Util.closeBox();
    });

</script>

</html>
