<!doctype html>
<html>
<head table_bottom=bcIwOs >
    <meta charset="utf-8">
    <title>图片上传-思途CMS</title>
    {Common::js('jquery.min.js,image/plupload.full.min.js,image/zh_CN.js,common.js')}
    {include "pub/public_js"}
    {Common::css('admin_base.css,admin_base2.css,admin_style.css,image.css')}
</head>
<body>
<div class="upload_pic_box">
    <div class="tabnav">
        <span class="on" data="album">相册选择</span>
        <span data="local">本地上传</span>
        <span data="net">网络图片</span>
    </div>
    <div class="tabcon" id="album_content">
        <h3 class="back" currentGroupId="-1">
            <a href="javascript:;" id="goback">上一级</a>
            <div class="search-zoom">
                <select id="findtype" class="fl" style="height:25px;line-height: 25px; margin-right: 3px;">
                    <option value="1">图片</option>
                    <option value="2">文件夹</option>
                </select>
            <input type="text" id="searchkey" value="" placeholder="请输入图片名称" class="search-box"">
            <a href="javascript:;" class="search-btn" onclick="searchImages()"></a>
            </div>
        </h3>

        <div class="photo_xz" id="photo_xz">
            <ul id="xcxz">
                <?php foreach ($group as $v): ?>
                    <li data="<?php echo $v['group_id']; ?>">
                        <img src="<?php echo $publicPath; ?>images/wjj_ico.png"/>
                        <span><?php echo $v['group_name']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <ul id="xcnr" style="display: none"></ul>
        </div>
    </div>
    <div class="tabcon" id="local_content" style="display: none">
        <div class="photo_local">
            <dl>
                <dt>上传到：</dt>
                <dd>
                    <select name="" class="xl_select" id="group">
                        <?php foreach ($group as $v): ?>
                            <option value="<?php echo $v['group_id']; ?>"><?php echo $v['group_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </dd>
            </dl>
            <dl>
                <dt>选择图片：</dt>
                <dd>
                    <ul class="list_pic">
                    </ul>
                    <div class="file_up"><input name="" type="file" id="add"></div>
                </dd>
            </dl>
        </div>
    </div>
    <div class="tabcon" id="net_content" style="display: none">
        <div class="photo_net">
            <table width="100%" border="0">
                <tr>
                    <th width="20%" height="40" align="right" scope="row">图片地址：</th>
                    <td width="60%"><input type="text" class="ads_text"/></td>
                    <td width="20%"><a href="javascript:;" class="add_net_btn">添加</a></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="bom_cz">
        <a class="cancel_btn">取消</a>
        <a class="confirm_btn" id="submit">确定</a>
    </div>
</div>
<!--插入图片-->
</body>
<script type="text/javascript" charset="utf-8">

var public_path = '{$publicPath}';

$('.tabnav').find('span').click(function () {
    var attr = $(this).attr('data');
    $(this).addClass('on').siblings().removeClass('on');
    $('#' + attr + '_content').css('display', '').siblings('.tabcon').css('display', 'none');
});
$('body').delegate('#xcxz li','click',function(){
    $(".back").attr("currentGroupId", $(this).attr('data'));
    $("#searchkey").val("");
    getImages(1,1);
})
/*  $('#xcxz').find('li').click(function () {
 $(".back").attr("currentGroupId", $(this).attr('data'));
 $("#searchkey").val("");
 getImages(1,1);
 });*/
function getImages(p,search_type){
    $('#xcnr').css('display', 'none');
    $('#xcxz').css('display', 'none');
    $('#xcnr').attr("currentPageNo",p);
    var pid =  $(".back").attr("currentGroupId");
    var keyword = $("#searchkey").val();
    $.post(SITEURL + 'image/image_manage', {action: 'find', pid:pid ,'page':p, 'keyword':keyword,'search_type':search_type}, function (data) {

        //按图片搜索
        if(search_type == 1){
            var html = '';
            for (var i in data) {
                html += '<li><i></i><img src="' + data[i].url + '" /><em title="'+data[i].name+'">'+data[i].name+'</em></li>';
            }
            $('#xcnr').html('');
            if(p==1){
                $('#xcnr').html(html);
            }else{
                $('#xcnr').append(html);
            }
            $('#xcnr').css('display','');

        }else if(search_type == 2){//按文件夹搜索
            var html = '';
            for (var i in data) {

                html += '<li data="'+data[i].group_id+'">';
                html += '<img src="'+public_path+'images/wjj_ico.png"/>';
                html += '<span>'+data[i].group_name+'</span>';
                html += '</li>';
            }
            $('#xcxz').html(html);
            $('#xcnr').css('display', 'none');
            $('#xcxz').css('display', '');
        }




    }, 'json');
}
$('#goback').click(function () {
    if($("#searchkey").val() == "" || $(".back").attr("currentGroupId") == "-1")
    {
        $("#searchkey").val("");
        getImages(1,2);
        /*    $(".back").attr("currentGroupId", "-1");
         $("#searchkey").val("");
         $('#xcnr').html('');
         $('#xcnr').css('display', 'none');
         $('#xcxz').css('display', '');*/
    }
    else
    {
        $("#searchkey").val("");
        getImages(1,1);
    }
});
$('#xcnr').find('li').live('click', function () {
    if ($(this).hasClass('on')) {
        $(this).removeClass('on');
    } else {
        $(this).addClass('on');
    }
});
var groupid = $('#group').val();
$('#group').change(function () {
    groupid = $(this).val();
});
var uploader = new plupload.Uploader({
    //runtimes: 'flash',
    browse_button: 'add',
    url: 'temp',
    flash_swf_url: '<?php echo $publicPath;?>js/image/Moxie.swf',
    silverlight_xap_url : '<?php echo $publicPath;?>js/image/Moxie.xap',
    filters: {
        max_file_size: '2mb',
        mime_types: [
            {title: "Image files", extensions: "jpg,gif,png,jpeg"},
        ]
    },
    init: {
        BeforeUpload: function (uploader, file) {
            uploader.settings.url = SITEURL + 'image/upload/iswater/<?php echo $iswater;?>/groupid/' + groupid;
        },
        FilesAdded: function (up, files) {
            plupload.each(files, function (file) {
                var tpl = '<li id="{id}"><i></i><span><img src="{imgsrc}" /><em class="progress"><s style=" width:0%"></s><b>0%</b></em></span></li>';
                tpl = tpl.replace("{id}", file.id);
                previewImage(file, function (imgsrc) {
                    tpl = tpl.replace("{imgsrc}", imgsrc);
                    $('.list_pic').append(tpl);
                })
            });
            uploader.start();

        },
        FileUploaded: function (up, file, info) {
            $('#' + file.id).attr('data', info.response);
        },
        UploadProgress: function (up, file) {
            $('#'+file.id).find('.progress').html('<s style=" width:'+file.percent+'%"></s><b>'+file.percent+'%</b>');
        },
        Error: function(up, err) {
            console.log(err.message);
        }
    }
});
function previewImage(file, callback) {
    if (!file || !/image\//.test(file.type)) return;
    if (file.type == 'image/gif') {
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
$('.list_pic').find('i').live('click', function () {
    var parent = $(this).parents('li');
    uploader.removeFile(parent.attr('id'));
    parent.remove();
});
//取消
$('.cancel_btn').live('click', function () {
    ST.Util.closeBox();
});
$('#submit').live('click', function () {
    var data = [];
    var attr = $('.tabnav').find('span.on').attr('data');
    switch (attr) {
        case 'album':
            $('#xcnr').find('li.on').each(function () {
                data.push($(this).find('img').attr('src')+'$$'+$(this).find('em').attr('title'));
            });
            break;
        case 'local':
            $('.list_pic').find('li[data]').each(function () {
                data.push($(this).attr('data'));
            });
            break;
        case 'net':
            $('.ads_text').each(function () {
                if ($(this).val() != '') {
                    data.push($(this).val());
                }
            });
            break;
    }
    ST.Util.responseDialog({data:data},true);
});
//添加网络图片
$('.add_net_btn').live('click',function(){
    var parent=$(this).parents('tr');
    var html='<tr><th width="20%" height="40" align="right" scope="row">图片地址：</th><td width="60%"><input type="text" class="ads_text"/></td>'
        +'<td width="20%"><a href="javascript:;" class="add_net_btn">添加</a></td></tr>';
    parent.find('td:eq(1)').html('&nbsp;');
    parent.after(html);

});
//滚动加载
$('.photo_xz').scroll(function(){
    if(!$('.tabnav').find('span:eq(0)').hasClass('hide') && $('#xcnr').attr('data') && $('#xcnr').css('display')=='block'){
        var currentPageNo=$('#xcnr').attr('currentPageNo');
        var viewH =$('#xcnr').find('li:last').offset().top+parseInt(data[1])*500;
        var scrollTop =$(this).scrollTop();//滚动高度
        if(viewH-scrollTop<100){
            getImages(parseInt(currentPageNo)+1);
        }
    }
});

function searchImages()
{
    var searchval = $("#searchkey").val();
    var search_type = $('#findtype').val();
    if(searchval == "")
    {
        ST.Util.messagBox('图片/文件夹搜索', '请输入要搜索的名称', false);
        return;
    }

    getImages(1,search_type);
}
</script>
</html>
