<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>积分策略添加/修改</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,style.css,base_new.css'); }
    {php echo Common::getScript("jquery.validate.js,datetimepicker/jquery.datetimepicker.full.js"); }
    {php echo Common::getCss('jquery.datetimepicker.css','js/datetimepicker'); }

</head>

<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td ">
                <div class="cfg-header-bar">
                    <div class="cfg-header-tab">
                        {if !empty($info['id'])}
                        <span class="item on">基础信息</span>
                        <span class="item">高级</span>
                        {/if}
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>
                <form id="frm">
                    <div class="info-item-container">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd"><i class="mr-5 c-red va-m">*</i>策略名称：</span>
                                <div class="item-bd">
                                    {if $info['issystem']==1}
                                    <label class="radio-label">{$info['title']}</label>
                                    {else}
                                    <input type="text" name="title" class="default-text wid_460" value="{$info['title']}" />
                                    {/if}
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">开关状态：</span>
                                <div class="item-bd">
                                    <label class="radio-label"><input type="radio" name="isopen" value="1" {if $info['isopen']==1}checked="checked"{/if}/>开启</label>
                                    <label class="radio-label ml-20"><input type="radio" name="isopen" value="0" {if $info['isopen']==0}checked="checked"{/if}>关闭</label>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">策略来源：</span>
                                <div class="item-bd">
                                    <label class="radio-label">{if $info['issystem']==1}内置策略{else}自定义策略{/if}</label>
                                </div>
                            </li>
                            {if $info['issystem']!=1}
                            <li id="product_main">
                                <span class="item-hd">针对产品：</span>
                                <div class="item-bd" id="product_con_1">
                                    <div class="apply-tab-nav">
                                        {loop $orderable_products $product}
                                        <label class="check-label mr-20"><input type="checkbox" name="typeid[]" value="{$product['id']}"  {if in_array($product['id'],$info['typeid_arr'])}checked="checked"{/if}>{$product['modulename']}</label>
                                        {/loop}
                                    </div>
                                </div>
                            </li>
                            {else}
                            <li id="product_main">
                                <span class="item-hd">针对产品：</span>
                                <div class="item-bd" id="product_con_1">
                                    <div class="apply-tab-nav">

                                        <label class="radio-label">{$info['typeid_names']}</label>

                                    </div>
                                </div>
                            </li>
                            {/if}
                            <li>
                                <span class="item-hd">有效期：</span>
                                <div class="item-bd {if $info['issystem']==1}disabled{/if}">
                                    <label class="radio-label mr-20"><input type="radio" name="expiration_type" value="0" {if $info['expiration_type']==0}checked="checked"{/if}/>一直有效</label>
                                    <label class="radio-label mr-20">
                                        <input type="radio" name="expiration_type" value="1" {if $info['expiration_type']==1}checked="checked"{/if}/>区间有效
                                        <div class="choose-start-date ml-5">
                                            <input type="text" class="date-text" id="starttime" name="starttime" {if $info['expiration_type']==1} value="{$info['starttime']}"{/if}/>
                                            <i class="date-icon"></i>
                                        </div>
                                        &nbsp;至&nbsp;
                                        <div class="choose-start-date">
                                            <input type="text" class="date-text" id="endtime_1" name="endtime_1" {if $info['expiration_type']==1} value="{$info['endtime']}"{/if}/>
                                            <i class="date-icon"></i>
                                        </div>
                                    </label>
                                    <label class="radio-label mr-20">
                                        <input type="radio" name="expiration_type" value="2" {if $info['expiration_type']==2}checked="checked"{/if}/>截止至
                                        <div class="choose-start-date ml-5">
                                            <input type="text" class="date-text" id="endtime_2" name="endtime_2" {if $info['expiration_type']==2} value="{$info['endtime']}"{/if}/>
                                            <i class="date-icon"></i>
                                        </div>
                                    </label>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">上限分值：</span>
                                <div class="item-bd">
                                    <span class="item-text">最多可使用<input type="text" class="input-text w100 ml-5 mr-5" id="txt_limit" name="toplimit" value="{$info['toplimit']}"/>积分抵用<span id="txt_limit_exchange"></span>元</span>
                                    <span class="item-text c-primary ml-20">当前积分换算比例：{Model_Sysconfig::get_configs(0,'cfg_exchange_jifen',true)}积分 =1元</span>
                                    <a class="btn btn-primary radius size-S ml-10" href="javascript:;" id="btn_config">去修改</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="info-item-container" style="display:none">
                        <ul>
                            <li>
                                <strong class="item-hd">策略ID：</strong>
                                <div class="item-bd">
                                    <span class="id-number">{$info['id']}</span>
                                </div>
                            </li>
                            <li>
                                <strong class="item-hd">调用标识{Common::get_help_icon('jifen_jifentprice_label')}：</strong>
                                <div class="item-bd">
                                    <label class="radio-label">{$info['label']}</label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="id" id="jifen_id" value="{$info['id']}"/>
                    <input type="hidden" name="prev_typeid" id="prev_typeid" value="{$info['typeid']}"/>
                </form>
                <!-- 积分抵现 -->
                <div class="clear clearfix mt-5">
                    <a class="btn btn-primary radius size-L ml-115" id="save_btn" href="javascript:;">保存</a>
                </div>
            </td>
        </tr>
    </table>

<script>

    var jifen_exchange="{Model_Sysconfig::get_configs(0,'cfg_exchange_jifen',true)}";
    jifen_exchange=parseInt(jifen_exchange);

    $(document).ready(function(){

        //切换
        $(".cfg-header-tab .item").click(function(){
            $(this).addClass('on').siblings().removeClass('on');
            $(".info-item-container").eq($(this).index()).show().siblings().hide();
        });
         //禁用某些按钮
        $(".disabled input").attr('disabled',true);

        //section切换
        $("#section_main input:radio").click(function(){
               var num = $("#section_main input:radio:checked").val();
               if(num==1)
               {
                   $("#product_main").show();
               }
               else
               {
                   $("#product_main").hide();
               }
        })
        $("#section_main input:radio:checked").trigger('click');

        //添加产品按钮
        $("#add_btn").click(function(){
            var typeid = $("#product_con_1 input:checked").val();
            var jifenid = $("#jifen_id").val();
            if(!typeid)
            {
                ST.Util.showMsg("请选择产品", 5, 1000);
                return;
            }
            if(!jifenid)
            {
                ST.Util.showMsg("请先保存策略,然后才能添加产品", 5, 1000);
                return;
            }
            var params={loadCallback: chooseProduct,loadWindow:window};
            var url= SITEURL+"jifen/dialog_get_tprice_products/typeid/"+typeid+'/jifenid/'+jifenid;
            ST.Util.showBox('选择产品',url,'600','430',null,null,document,params);
        });
        //切换产品
        $("#product_con_1 input:radio").change(function(){
             loadProducts(1);
        });

        //搜索
        $("#search_btn").click(function(){
            loadProducts(1);
        });
        loadProducts(1);

        //奖励方式切换
        $("#rewardway_con input:radio").change(function(){
             var val=$("#rewardway_con input:radio:checked").val();
             if(val==1)
             {
                 $("#percent_icon").show();
             }
             else
             {
                 $("#percent_icon").hide();
             }
        });
        $("#rewardway_con input:radio").trigger('change');

        //提交表单
        $("#save_btn").click(function(){
            $("#frm").submit();
        })


        //时间选择
        $('#starttime').datetimepicker({
            format:'Y-m-d',
            timepicker:false,
            minDate:'-1970/01/01',
            onShow: function (ct, ele) {
                var endDate = $("#endtime_1").val();
                var maxDate = endDate?endDate:false;
                this.setOptions({maxDate:maxDate});
            }
        });

        $("#endtime_1").datetimepicker({
            format: 'Y-m-d',
            onShow: function (ct, ele) {
                var startDate = $("#starttime").val();
                var minDate = startDate?startDate:false;
                var currentDate = $(ele).val();
                startDate =currentDate?currentDate:startDate;
                this.setOptions({minDate:minDate,startDate:startDate});
            },
            timepicker: false
        });
        $("#endtime_2").datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            minDate:'-1970/01/01'
        });

        //积分上限设置
        $("#txt_limit").keyup(function(){
            var num=parseInt($(this).val());
            num=!num?0:num;
            var exchange= Math.floor(num/jifen_exchange);
            exchange=!exchange?0:exchange;
            $("#txt_limit_exchange").text(exchange);
        });
        $("#txt_limit").trigger('keyup');

        //转到配置
        $("#btn_config").click(function(){
           // var url=SITEURL+"jifen/config/{if isset($_GET['menuid'])}menuid/{$_GET['menuid']}/{/if}";
           // ST.Util.addTab('积分设置',url,1);
            $(".leftnav a").each(function(){
                 var url=$(this).data('url');
                 if(url&&url.indexOf('jifen/config')!=-1)
                 {
                     $(this).trigger('click');
                 }
            });
        });

        //表单验证
        jQuery.validator.addMethod("islabel", function(value, element) {
            var v = /^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){0,19}$/;
            return this.optional(element) || (v.test(value));
        }, "标识应该为字母数字或下划线的组合，且必须以字母开头");
        $("#frm").validate({
            focusInvalid:false,
            rules: {
                title:
                {
                    required: true
                },
                label:
                {
                    required:true,
                    islabel:true,
                    remote:
                    {
                        type:"POST",
                        url:SITEURL+'jifen/ajax_check_tprice_label',
                        data:
                        {
                            label:function()
                            {
                                return $("#label").val()
                            },
                            id:function()
                            {
                                return $("#jifen_id").val();
                            }
                        }
                    }
                },
                toplimit:{
                    required:{
                        depends: function(element) {
                            var isopen=$("input[name=isopen]:checked").val();
                            return isopen==1?true:false;
                        }
                    },
                    min:{
                        param: 1,
                        depends: function(element) {
                            var isopen=$("input[name=isopen]:checked").val();
                            return isopen==1?true:false;
                        }
                    }
                }

            },
            messages: {
                title:{
                    required:"请输入标题"
                },
                label:{
                    required:"请输入标识",
                    remote:'该标识已被使用'
                },
                toplimit:{
                    required:'请输入上限分值',
                    min:'值必须大于0'
                }
            },
            submitHandler:function(form){
                $.ajaxform({
                    url   :  SITEURL+"jifen/ajax_tprice_save",
                    method  :  "POST",
                    form  : "#frm",
                    dataType:'json',
                    success  :  function(data)
                    {
                        if(data.status)
                        {
                            $("#jifen_id").val(data.id);
                            ST.Util.showMsg('保存成功!','4',2000);
                        }
                        else
                        {
                            ST.Util.showMsg(data.msg,'5',2000);
                        }
                    }});
                return false;//阻止常规提交
            }
        });


    });

    function chooseProduct()
    {
        loadProducts();
    }

    function loadProducts(page)
    {
        var jifenid = $("#jifen_id").val();
        var typeid = $("#product_con_1 input:checked").val();
        var keyword = $("#search_input").val();
        var url=SITEURL+'jifen/ajax_get_jifentprice_products';
        $.ajax({
            type: "post",
            url: url,
            dataType: 'json',
            data: {page: page,typeid:typeid,jifenid:jifenid,keyword:keyword},
            success: function (result, textStatus){
                genList(result);
            }
        });
    }

    function genList(result)
    {
        var html='';

        for(var i in result.list)
        {
            var row=result.list[i];
            html+='<tr class="tb-item"><td>'+row['series']+'</td>'+
            '<td><a class="tit" href="'+row['url']+'" target="_blank">'+row['title']+'</a></td>'+
            '<td><a class="delete" href="javascript:;" onclick="removeJifen(this,'+row.typeid+','+row.id+')" >移除</a></td></tr>';
        }

        $("#dlg_tb .tb-item").remove();
        $("#dlg_tb").append(html);

        var pageHtml = ST.Util.page(result.pagesize, result.page, result.total, 5);
        $("#page_info").html(pageHtml);
        $("#page_info a").click(function () {
            var page = $(this).attr('page');
            loadProducts(page);
        });
    }
    function removeJifen(ele,typeid,id)
    {
        var jifenid = $("#jifen_id").val();
        var url=SITEURL+'jifen/ajax_remove_tprice';

        ST.Util.confirmBox("提示","确定移除？",function(){
            $.ajax({
                type: "post",
                url: url,
                dataType: 'json',
                data: {typeid:typeid,jifenid:jifenid,productid:id},
                success: function (result, textStatus){
                    $(ele).parents('tr:first').remove();
                }
            });
        })
    }

</script>
</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.1302&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
