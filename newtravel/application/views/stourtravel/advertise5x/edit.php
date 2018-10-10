<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>广告添加/修改</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("product_add.js,choose.js,imageup.js,template.js");}
</head>

<body bottom_float=zq5Udk >

<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td ">

            <form method="post" name="product_frm" id="product_frm">
                <div class="manage-nr">
                    <div class="cfg-header-bar" id="nav">
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10"
                           onclick="window.location.reload()">刷新</a>
                        {if !$info}
                        <a href="javascript:;" id="developer" class="fr btn btn-primary radius mr-10 mt-6">开发者</a>
                        <a href="javascript:;" id="senior" class="fr btn btn-primary radius mr-10 mt-6">高级</a>
                        {/if}
                    </div>
                    <!--基础信息开始-->
                    <div class="product-add-div">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">显示状态：</span>
                                <div class="item-bd">
                                    <label class="radio-label mr-20"><input type="radio" name="is_show" {if !$info ||$info['is_show']!=0}checked="checked"{/if}
                                        value="1"/>开启</label>
                                    <label class="radio-label mr-20"><input type="radio" name="is_show" {if $info && $info['is_show']==0}checked="checked"{/if}
                                        value="0"/>关闭</label>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">显示位置：</span>
                                {if $info['id']}
                                <div class="item-bd">
                                    <?php $count = count($position); ?>
                                    {loop $position $k $item}
                                    <span class="item-text">{$item['name']}</span>{if $k!=($count-1)}&nbsp;&nbsp;&gt;{/if}
                                    {/loop}
                                    {else}
                                    <div class="item-bd">
                                        <span class="select-box w100">
                                            <select name="is_pc" class="select" id="is_pc" onchange="change_platform()">
                                                {loop $platform $item}
                                                {if $item==1}
                                                <option value="1">电脑端</option>
                                                {/if}
                                                {if $item==0}
                                                <option value="0">移动端</option>
                                                {/if}
                                                {/loop}
                                            </select>
                                        </span>
                                        <span class="select-box w100 ml-5 hide" id="webid_span">
                                            <select name="webid" class="select" id="webid" onchange="change_web_id()">
                                                {php array_unshift($weblist,array('webname'=>'主站','webid'=>0))}
                                            </select>
                                        </span>
                                        <span class="select-box w100 ml-5 hide" id="mould_span"
                                              onchange="change_mould()">
                                            <select name="mould" id="mould" class="select"></select>
                                        </span>
                                        <span class="select-box inline  ml-5 hide" id="page_span">
                                            <select name="page" id="page" class="select" onchange="change_page()">

                                            </select>
                                        </span>
                                        <span class="select-box w150 ml-5 hide" id="position_span">
                                            <select name="position" id="position" class="select"
                                                    onchange="change_position()">

                                            </select>
                                        </span>
                                    </div>
                                    {/if}
                            </li>
                            <li {if !$info['id']}class="hide"{/if} id="flag">
                            <span class="item-hd">显示类型：</span>
                            <div class="item-bd">
                                <span class="item-text">{if $info['flag']==3}视频{elseif $info['flag']==2}多图{else}单图{/if}</span>
                            </div>
                            </li>
                            <li {if !$info['id']}class="hide"{/if} id="size">
                            <span class="item-hd">广告尺寸：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['size']}</span>
                            </div>
                            </li>
                            <li {if !($info['id'] && $info['flag']!=3)}class="hide"{/if} id="img_content">
                            <span class="item-hd">广告图片：</span>
                            <div class="item-bd">
                                <a href="javascript:;" class="btn btn-primary radius size-S mt-3 upload_item"
                                   data="{title:'上传图片','callback':'upload_image'}">上传图片</a>
                                <div class="mt-10 pr-20">
                                    <table class="table table-border table-bordered">
                                        <thead>
                                        <tr class="text-c">
                                            <th width="10%">排序</th>
                                            <th width="10%">广告内容</th>
                                            <th width="20%">广告标题</th>
                                            <th width="50%">广告链接</th>
                                            <th width="10%">管理</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {if $info['flag']!=3}
                                        {loop $info['image'] $v}
                                        <tr>
                                            <td><input type="text" name="adorder[]"
                                                       class="input-text text-c {if $info['flag']==1}hide{/if}"
                                                       value="{$v[3]}"/></td>
                                            <td>
                                                <div class="example-image-block">
                                                    <a class="example-image-link"><img class="example-image"
                                                                                       src="{$v[0]}"/></a>
                                                </div>
                                                <input type="hidden" name="adsrc[]" value="{$v[0]}"/></td>
                                            <td><input type="text" class="input-text" name="adname[]" value="{$v[1]}"/>
                                            </td>
                                            <td><input type="text" class="input-text" name="adlink[]" value="{$v[2]}"/>
                                            </td>
                                            <td class="text-c"><a class="btn-link ads_delete" href="javascript:;">删除</a>
                                            </td>
                                        </tr>
                                        {/loop}
                                        {/if}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            </li>
                            <li {if !($info['id'] && $info['flag']==3)}class="hide"{/if} id="video_content">
                            <span class="item-hd">广告视频：</span>
                            <div class="item-bd">
                                <a href="javascript:;" class="btn btn-primary radius size-S mt-3 upload_item"
                                   data="{title:'上传图片','callback':'upload_video'}">上传视频封面</a>
                                <div class="mt-10 pr-20">
                                    <table class="table table-border table-bordered">
                                        <thead>
                                        <tr class="text-c">
                                            <th width="10%">视频封面</th>
                                            <th width="40%">视频标题</th>
                                            <th width="40%">视频地址</th>
                                            <th width="10%">管理</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {if $info['flag']==3}
                                        {loop $info['image'] $v}
                                        <tr>
                                            <td>
                                                <div class="example-image-block">
                                                    <a class="example-image-link"><img class="example-image"
                                                                                       src="{$v[0]}"/></a>
                                                </div>
                                                <input type="hidden" name="adsrc[]" value="{$v[0]}"/><input type="text"
                                                                                                            name="adorder[]"
                                                                                                            class="input-text text-c hide"
                                                                                                            value=""/>
                                            </td>
                                            <td><input type="text" class="input-text" name="adname[]" value="{$v[1]}"/>
                                            </td>
                                            <td><input type="text" class="input-text" name="adlink[]" value="{$v[2]}"/>
                                            </td>
                                            <td class="text-c"><a class="btn-link ads_delete" href="javascript:;">删除</a>
                                            </td>
                                        </tr>
                                        {/loop}
                                        {/if}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            </li>
                        </ul>
                        <!-- 高级设置 -->
                        <div class="clear clearfix mt-5">
                            <input type="hidden" name="id" id="id" value="{$info['id']}"/>
                            <input type="hidden" name="flag" id="input_flag" value="{$info['flag']}"/>
                            <a class="btn btn-primary size-L radius ml-115" id="btn_save" href="javascript:;">保存</a>
                        </div>
                    </div>
                    <!-- 基础信息结束 -->
                </div>
            </form>
        </td>
    </tr>
</table>
</body>
<script type="text/html" id="img_template">
    {{each images as value i }}
    <tr>
        <td><input type="text" name="adorder[]" class="input-text text-c {{if flag==1}}hide{{/if}}" value=""/></td>
        <td>
            <div class="example-image-block"><a class="example-image-link"><img class="example-image"
                                                                                src="{{value}}"></a></div>
            <input type="hidden" name="adsrc[]" value="{{value}}"/></td>
        <td><input type="text" class="input-text" name="adname[]" value=""/></td>
        <td><input type="text" class="input-text" name="adlink[]" value=""/></td>
        <td class="text-c"><a class="btn-link ads_delete" href="javascript:;">删除</a></td>
    </tr>
    {{/each}}
</script>
<script type="text/html" id="video_template">
    <tr>
        <td>
            <div class="example-image-block"><a class="example-image-link"><img class="example-image" src="{{images}}"></a>
            </div>
            <input type="hidden" name="video_src[]" value="{{images}}"/><input type="text" name="video_order[]"
                                                                               class="input-text text-c hide" value=""/>
        </td>
        <td><input type="text" class="input-text" name="video_name[]"/></td>
        <td><input type="text" class="input-text" name="video_link[]"/></td>
        <td class="text-c"><a class="btn-link" href="javascript:;">删除</a></td>
    </tr>

</script>
{if !$info}
<script>
    var flag;
    var pageConfig = eval('({Common::format_page_name()})');
    var ads = eval('({json_encode($data)})');
    var web_site = eval('({json_encode($weblist)})');
    var use_template = eval('{$template}');
    function change_platform() {
        var is_pc = $('#is_pc').val();
        var web = new Array(); //确定站点
        var web_html = '';
        for (var i in ads) {
            if (ads[i]['is_pc'] == is_pc) {
                web.push(ads[i]['webid']);
            }
        }
        web = $.unique(web);
        for (var j in web_site)
            for (var i in web) {
                if (web_site[j]['webid'] == web[i]) {
                    web_html += '<option value="' + web_site[j]['webid'] + '">' + web_site[j]['webname'] + '</option>'
                    break;
                }
            }
        $('#webid').html(web_html);
        $('#webid_span').removeClass('hide');
        change_web_id();
    }
    $('#is_pc').change();
    function change_web_id() {
        var page_pid = new Array();
        var web_id = $('#webid').val();
        var is_pc = $('#is_pc').val();
        var mould_html = '';
        for (var i in ads) {
            if (ads[i]['is_pc'] == is_pc && ads[i]['webid'] == web_id) {
                for (var j in pageConfig['page']) {
                    if (pageConfig['page'][j]['page_name'] == ads[i]['prefix']) {
                        page_pid.push(pageConfig['page'][j]['pid']);
                        break;
                    }
                }
            }
        }
        page_pid = $.unique(page_pid);
        for (var j in pageConfig['mould']) {
            for (var i in page_pid) {
                if (pageConfig['mould'][j]['id'] == page_pid[i]) {
                    mould_html += '<option value="' + pageConfig['mould'][j]['id'] + '">' + pageConfig['mould'][j]['name'] + '</option>'
                    break;
                }
            }
        }
        $('#mould').html(mould_html);
        $('#mould_span').removeClass('hide');
        change_mould();
    }
    function change_mould() {
        var web_id = $('#webid').val();
        var is_pc = $('#is_pc').val();
        var mould_id = $('#mould').val();
        var page = new Array();
        var page_html = '';
        var template_page = {};
        var reg = /^install_templet_name:[a-zA-Z0-9_]+$/;
        for (var i in ads) {
            if (ads[i]['is_pc'] == is_pc && ads[i]['webid'] == web_id) {
                if ($.inArray(ads[i]['prefix'], page) == -1) {
                    page.push(ads[i]['prefix']);
                    if (reg.test(ads[i]['remark'])) {
                        for(var j in use_template){
                           if(use_template[j]['handle_advertise_name']==ads[i]['remark']){
                               template_page[ads[i]['prefix']] = use_template[j]['name'];
                               break;
                           }
                        }
                    } else {
                        template_page[ads[i]['prefix']] = '';
                    }
                }
            }
        }
        page = $.unique(page);
        for (var j in page) {
            for (var i in pageConfig['page']) {
                if (pageConfig['page'][i]['pid'] == mould_id && page[j] == pageConfig['page'][i]['page_name']) {
                    var template_name='';
                    if (template_page[pageConfig['page'][i]['page_name']].length > 0) {
                        template_name = '-' + template_page[pageConfig['page'][i]['page_name']];
                    }
                    page_html += '<option value="' + pageConfig['page'][i]['page_name'] + '">' + pageConfig['page'][i]['name'] + template_name+'</option>'
                    break;
                }
            }
        }
        $('#page').html(page_html);
        $('#page_span').removeClass('hide');
        change_page();
    }

    function change_page() {
        var web_id = $('#webid').val();
        var is_pc = $('#is_pc').val();
        var page = $('#page').val();
        var position_html = '';
        for (var i in ads) {
            if (ads[i]['is_pc'] == is_pc && ads[i]['webid'] == web_id && ads[i]['prefix'] == page) {
                var data = "{'id':" + ads[i]['id'] + ",'flag':" + ads[i]['flag'] + ",'size':'" + ads[i]['size'] + "'}";
                position_html += '<option value="' + data + '">' + ads[i]['position'] + '</option>';
            }
        }
        $('#position').html(position_html);
        $('#position_span').removeClass('hide');
        change_position()
    }
    function change_position() {
        var data = eval('(' + $('#position').val() + ')');
        if (data['flag'] == 1) {
            $('#flag').removeClass('hide').find('.item-text').text('单图');
        } else if (data['flag'] == 2) {
            $('#flag').removeClass('hide').find('.item-text').text('多图');
        } else {
            $('#flag').removeClass('hide').find('.item-text').text('视频');
        }
        $('#size').removeClass('hide').find('.item-text').text(data['size']);

        if (data['flag'] == 3) {
            $('#img_content').addClass('hide');
            $('#video_content').removeClass().find('tbody').html('');
        } else {
            $('#video_content').addClass('hide');
            $('#img_content').removeClass().find('tbody').html('');
        }
        flag = data['flag'];
        $('#id').val(data['id']);
        $('#input_flag').val(flag);
    }
    //高级
    $('#senior').click(function () {
        var url = SITEURL + "advertise5x/senior/menuid/{$_GET['menuid']}/";
        ST.Util.addTab('广告高级', url);
    });
    $('#developer').click(function () {
        var url = SITEURL + "advertise5x/developer/menuid/{$_GET['menuid']}/";
        ST.Util.addTab('开发者', url);
    });

</script>
{/if}
<script>
    //图片上传
    $('.upload_item').click(function () {
        var data = eval('(' + $(this).attr('data') + ')');
        ST.Util.showBox(data.title, SITEURL + 'image/insert_view/iswater/0', 0, 0, null, null, document, {
            loadWindow: window,
            loadCallback: eval('(' + data.callback + ')')
        });
        function upload_image(result, bool) {
            if (result.data.length > 0) {
                $data = new Array();
                $image = new Array();
                for (i = 0; i < result.data.length; i++) {
                    var temp = result.data[i].split('$$');
                    $image.push(temp[0]);
                }
                if ($image.length > 0 && $('#input_flag').val() == 1) {
                    $data.images = [$image[0]];
                    $data.flag = 1;
                    $('#img_content').find('tbody').html(template('img_template', $data));
                } else {
                    $data.images = $image;
                    $data.flag = 2;
                    $('#img_content').find('tbody').append(template('img_template', $data));
                }
            }
        }

        function upload_video(result, bool) {
            if (result.data.length > 0) {
                var $data = new Array();
                var $image;
                for (i = 0; i < result.data.length; i++) {
                    var temp = result.data[i].split('$$');
                    $image = temp[0];
                }
                $data.images = $image;
                $('#video_content').find('tbody').html(template('video_template', $data));
            }
        }

    });
    $('.ads_delete').live('click', function () {
        var obj = $(this);
        var d = parent.window.dialog({
            title: '提示',
            content: '<div class="confirm-box center">确定删除？</div>',
            cancelValue: '取消',
            okValue: '确定',
            width: 250,
            ok: function () {
                obj.parent().parent().remove();
            },
            cancel: function () {

            }
        });
        d.showModal();
    });
    //保存
    $("#btn_save").click(function () {console.log(flag);
        var ads_type=$('#input_flag').val();
        var message;
        var bool = true;
        if (ads_type != 3) {
            if (!$('input[name="adsrc[]"]').val()) {
                message = '请上传图片';
                bool = false;
            }
        } else {
            if (!$('input[name="video_src[]"]').val()) {
                message = '请上传视频封面';
                bool = false;
            }
            if (bool && !$('input[name="video_link[]"]').val()) {
                message = '请填写视频地址';
                bool = false;
            }
        }

        if (!bool) {
            var d = parent.window.dialog({
                title: '提示',
                content: '<div class="confirm-box center">' + message + '</div>',
                okValue: '确定',
                width: 250,
                ok: function () {
                }
            });
            d.showModal();
            return;
        }
        $.ajaxform({
            url: SITEURL + "advertise5x/ajax_save",
            method: "POST",
            form: "#product_frm",
            dataType: 'json',
            success: function (data) {
                ST.Util.showMsg('保存成功!', '4', 2000);
            }
        });
    })
</script>
</html>
