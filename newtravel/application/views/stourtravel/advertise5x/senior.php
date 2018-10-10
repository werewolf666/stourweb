<!doctype html>
<html>
<head float_padding=XFJwOs >
    <meta charset="utf-8">
    <title>广告添加/修改</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("product_add.js,choose.js,imageup.js,template.js");}
</head>

<body>

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
                        <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                    </div>
                    <!--基础信息开始-->
                    <div class="product-add-div">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">显示位置：</span>
                                <div class="item-bd">
                                        <span class="select-box w100">
                                            <select name="is_pc" id="is_pc" class="select" onchange="change_platform()">
                                                <option selected="" value="1">电脑端</option>
                                                <option value="0">移动端</option>
                                            </select>
                                        </span>
                                    <span class="select-box w100 ml-5">
                                            <select name="webid" class="select" id="webid" onchange="change_web_id()">
                                                <option selected="selected"  value="0">主站</option>
                                                {loop $weblist $k}
                                                   <option value="{$k['webid']}">{$k['webname']}</option>
                                                {/loop}
                                            </select>
                                        </span>
                                    <span class="select-box w100 ml-5">
                                            <select name="mould" id="mould" class="select" onchange="change_mould()">

                                            </select>
                                        </span>
                                    <span class="select-box w100 ml-5">
                                            <select name="page" id="page" class="select"></select>
                                     </span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd"><span class="pl-5 c-red">*</span>位置命名：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w210 is_required" name="position" placeholder="请输入位置命名">
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">显示类型{Common::get_help_icon('advertise_flag')}：</span>
                                <div class="item-bd">
                                    <label class="radio-label mr-20"><input type="radio" name="flag" value="1" checked>单图</label>
                                    <label class="radio-label mr-20"><input type="radio" name="flag" value="2">多图</label>
                                    <label class="radio-label mr-20"><input type="radio" name="flag" value="3">视频</label>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd"><span class="pl-5 c-red">*</span>广告尺寸：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w100 is_required is_number" name="width" placeholder="宽">
                                    <input type="text" class="input-text w100 ml-5 is_required is_number" name="height" placeholder="高">
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">目的地：</span>
                                <div class="item-bd">
                                    <a href="javascript:;" class="btn btn-primary radius size-S mt-3 fl" onclick="Product.getDest(this,'.dest-sel',4)" title="选择">选择</a>
                                    <div class="ml-10 dest-sel fl"></div>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd"><span class="pl-5 c-red">*</span>调用标识{Common::get_help_icon('advertise_custom_label')}：</span>
                                <div class="item-bd">
                                    <input type="text" name="custom_label" class="input-text w210 is_required" placeholder="调用标识">
                                </div>
                            </li>
                        </ul>
                        <!-- 高级设置 -->
                        <div class="clear clearfix mt-5">
                            <input type="hidden" name="id" id="id" value=""/>
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
<script>
    var pageConfig = eval('({Common::format_page_name()})');
    change_platform();
    function change_platform() {
        $('#webid').find('option:eq(0)').attr('selected', true);
        change_web_id();
    }
    function change_web_id() {
        var html = '';
        for (var i in pageConfig['mould']) {
            var item = pageConfig['mould'][i];
            html += '<option value="' + item['id'] + '">' + item['name'] + '</option>'
        }
        $('#mould').html(html);
        change_mould();
    }
    function change_mould() {
        var html = '';
        var pid = $('#mould').val();
        for (var i in pageConfig['page']) {
            var item = pageConfig['page'][i];
            if (item['pid'] == pid) {
                html += '<option value="' + item['page_name'] + '">' + item['name'] + '</option>'
            }
        }
        $('#page').html(html);
    }
    $('.is_required').each(function(){
        $(this).keyup(function(){
            if($(this).val().length>0){
                $(this).removeClass('b-error');
            }
        });
    });
    $('.is_number').each(function () {
        $(this).blur(function () {
            var val = parseInt($(this).val());
            val = isNaN(val) ? 0 : Math.abs(val);
            $(this).val(val);
        });
    });
    //保存
    $("#btn_save").click(function () {
        var is_submit = true;
        //检测必填
        $('.is_required').each(function () {
            if ($(this).val().length < 1) {
                if (is_submit) {
                    is_submit = false;
                }
                $(this).addClass('b-error')
            }
        });
        if (!is_submit) {
            return;
        }
        $.ajaxform({
            url: SITEURL + "advertise5x/ajax_save_senior",
            method: "POST",
            form: "#product_frm",
            dataType: 'json',
            success: function (data) {
                if (data.status) {
                    if (data.message && data.message > 0) {
                        $('#id').val(data.message);
                    }
                    ST.Util.showMsg('保存成功', 4, 2000);
                } else {
                    var message = data.message == 'custom_label' ? '调用标识已存在' : '位置命名已存在';
                    ST.Util.showMsg(message, 5, 2000);
                }

            }
        });
    })
</script>
</html>
