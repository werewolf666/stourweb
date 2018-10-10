<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title size_font=zGXKBk >积分策略添加/修改</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('base.css,jf.css,style.css,base_new.css'); }
    {php echo Common::getScript("jquery.validate.js"); }
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
                        {/if}
                        {if !empty($info['id'])}
                        <span class="item">高级</span>
                        {/if}
                    </div>
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>
                <form id="frm">
                    <div class="info-item-container">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd"><i class="c-red va-m mr-5">*</i>策略名称：</span>
                                <div class="item-bd">
                                    {if $info['issystem']==1}
                                    <label class="radio-label">{$info['title']}</label>
                                    {else}
                                      <input type="text" name="title" class="input-text w500" value="{$info['title']}" />
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
                            <li id="section_main">
                                <span class="item-hd">送分场景：</span>
                                <div class="item-bd" >
                                    {if $info['issystem']==1}
                                    <label class="radio-label">{$info['section_name']}</label>
                                    {else}
                                    <label class="radio-label"><input type="radio"  name="section" value="1" {if $info['section']==1}checked="checked"{/if}/>产品预订</label>
                                    <label class="radio-label ml-30"><input type="radio" name="section" value="0" {if $info['section']==0}checked="checked"{/if}>其他策略</label>
                                    {/if}
                                </div>
                            </li>
                            {if $info['issystem']==1}
                            <li id="product_main">
                                <span class="item-hd">针对产品：</span>
                                <div class="item-bd" id="product_con_1">
                                    <div class="apply-tab-nav" >
                                        <label class="radio-label mr-30">{$info['typeid_names']}</label>
                                    </div>
                                </div>
                            </li>
                            {else}
                             <li id="product_main">
                                <span class="item-hd">应用到：</span>
                                <div class="item-bd" id="product_con_1">
                                    <div class="apply-tab-nav {if $info['issystem']==1}disabled{/if}" >
                                        {loop $orderable_products $product}
                                        <label class="radio-label mr-30"><input type="checkbox" name="typeid[]" value="{$product['id']}" {if in_array($product['id'],$info['typeid_arr'])}checked="checked"{/if}>{$product['modulename']}</label>
                                        {/loop}
                                    </div>
                                </div>
                             </li>
                            {/if}

                            <li>
                                <span class="item-hd">送分方式{Common::get_help_icon('jifen_index_value')}：</span>
                                <div class="item-bd {if $info['issystem']==1 && in_array('rewardway',$info['disable_fields'])}disabled{/if}" id="rewardway_con">
                                    <label class="radio-label"><input type="radio" name="rewardway" value="0" {if $info['rewardway']==0}checked="checked"{/if}/>按分值</label>
                                    <label class="radio-label ml-20"><input type="radio" name="rewardway" value="1" {if $info['rewardway']==1}checked="checked"{/if}>按百分比</label>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd rewardway-text">{if $info['rewardway']==1}奖励百分比{else}送分分值{/if}：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w100" name="value" value="{$info['value']}" />
                                    <span id="percent_icon">&nbsp;&nbsp;%</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">送分周期{Common::get_help_icon('jifen_index_frequency_type')}：</span>
                                <div class="item-bd {if $info['issystem']==1 && in_array('frequency_type',$info['disable_fields'])}disabled{/if}" id="frequency_type_con">
                                    <span {if in_array(1,$info['frequency_type_exclude'])}class="disabled"{/if}><label class="radio-label mr-30"><input type="radio" name="frequency_type" value="1" {if $info['frequency_type']==1}checked="checked"{/if}/>仅一次</label></span>
                                    <span {if in_array(0,$info['frequency_type_exclude'])}class="disabled"{/if}><label class="radio-label mr-30"><input type="radio" name="frequency_type" value="0" {if $info['frequency_type']==0}checked="checked"{/if}/>每次</label></span>
                                    <span {if in_array(2,$info['frequency_type_exclude'])}class="disabled"{/if}><label class="radio-label mr-30">
                                        <input type="radio" name="frequency_type" value="2" {if $info['frequency_type']==2}checked="checked"{/if}/>每天仅
                                        <input type="text" class="input-text w80" name="frequency_2" value="{if $info['frequency_type']==2}{$info['frequency']}{/if}" />次
                                    </label></span>
                                    <span {if in_array(3,$info['frequency_type_exclude'])}class="disabled"{/if}>
                                        <label class="radio-label">
                                            <input type="radio" name="frequency_type" value="3" {if $info['frequency_type']==3}checked="checked"{/if}/>共
                                            <input type="text" class="input-text w80" name="frequency_3" value="{if $info['frequency_type']==3}{$info['frequency']}{/if}" />次
                                        </label>
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="info-item-container" style="display:none">
                        <ul class="info-item-block">
                            <li>
                                <span class="item-hd">策略ID：</span>
                                <div class="item-bd">
                                    <span class="id-number">{$info['id']}</span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">调用标识{Common::get_help_icon('jifen_index_label')}：</span>
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

    var issystem="{$info['issystem']}"
    $(document).ready(function(){
         //禁用某些按钮
        disableElements();

        //切换
        $(".cfg-header-tab .item").click(function(){
            $(this).addClass('on').siblings().removeClass('on');
            $(".info-item-container").eq($(this).index()).show().siblings().hide();
        });

        //section切换
        $("#section_main input:radio").click(function(){
                if(issystem=='1')
                {
                    return;
                }
               var num = $("#section_main input:radio:checked").val();
               if(num==1)
               {
                   $("#product_main").show();
                   $("#frequency_type_con").addClass('disabled');
                   $("#frequency_type_con input[value=0]").trigger('click');
                   disableElements();
               }
               else
               {
                   $("#product_main").hide();
                   $("#frequency_type_con").removeClass('disabled');
                   $("#frequency_type_con input").removeAttr('disabled');
                   disableElements();
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
            var url= SITEURL+"jifen/dialog_get_products/typeid/"+typeid+'/jifenid/'+jifenid;
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
                 $('.rewardway-text').text('奖励百分比：');
                 $("#percent_icon").show();
             }
             else
             {
                 $('.rewardway-text').text('送分分值：');
                 $("#percent_icon").hide();
             }
        });
        $("#rewardway_con input:radio").trigger('change');

        //提交表单
        $("#save_btn").click(function(){
            $("#frm").submit();
        })



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
                        url:SITEURL+'jifen/ajax_check_label',
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
                value:{
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
                value:{
                    required:'请输入分值',
                    min:'值必须大于0'
                }
            },
            submitHandler:function(form){
                $.ajaxform({
                    url   :  SITEURL+"jifen/ajax_save",
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
        var url=SITEURL+'jifen/ajax_get_jifen_products';
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
        var url=SITEURL+'jifen/ajax_remove_jifen';

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

    function disableElements()
    {
        $(".disabled input").attr('disabled',true);
    }


</script>
</body>
</html>
