<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{__('收货地址')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js,jquery.validate.addcheck.js')}
</head>

<body>
{request "pub/header"}
    <div class="big">
        <div class="wm-1200">

            <div class="st-guide">
                <a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('收货地址')}
            </div><!--面包屑-->

            <div class="st-main-page">

                {include "member/left_menu"}
                <!-- 会员中心导航 -->

                <div class="user-cont-box">
                    <div class="user-address">
                        <div class="user-address-wrap">

                            {if count($address_list)<20}
                                <div class="user-address-tit">{__('新增收货地址')}</div>
                                <div class="user-address-block">
                                <form id="addfrm" method="post" script_div=tyvz8B >
                                    <ul class="address-item">
                                    <li>
                                        <strong class="item-bt">{__('所在地区')}<i>*</i></strong>
                                        <div class="item-nr" id="city">
                                            <select name="area_prov" id="area_prov" class="drop-down prov fl">
                                                <option value="请选择省">{__('请选择省')}</option>
                                            </select>
                                            <select name="area_city" id="area_city" class="drop-down ml5 city fl">
                                                <option value="请选择市">{__('请选择市')}</option>
                                            </select>
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-bt">{__('详细地址')}<i>*</i></strong>
                                        <div class="item-nr">
                                            <textarea name="address" id="address" class="ads-textarea fl" placeholder="{__('建议您如实填写详细收货地址，例如街道名称，门牌号码，楼层和房间号等信息')}"></textarea>
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-bt">{__('邮政编码')}<i>*</i></strong>
                                        <div class="item-nr">
                                            <input type="text" name="postcode" id="postcode" class="default-text fl" />
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-bt">{__('收件人')}<i>*</i></strong>
                                        <div class="item-nr">
                                            <input type="text" name="receiver" id="receiver" class="default-text  fl" />
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-bt">{__('联系电话')}<i>*</i></strong>
                                        <div class="item-nr">
                                            <input type="text" name="phone" id="phone" class="default-text  fl" />
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-bt">&nbsp;</strong>
                                        <div class="item-nr">
                                            <label class="radio-label"><input type="checkbox" name="is_default" class="check-btn" value="1" />{__('设置默认收货地址')}</label>
                                        </div>
                                    </li>
                                    <li>
                                        <strong class="item-bt">&nbsp;</strong>
                                        <div class="item-nr">
                                            <a class="save-btn add-save" href="javascript:;">{__('保存')}</a>
                                        </div>
                                    </li>
                                </ul>
                                </form>
                            </div>
                            {/if}
                            <div class="user-address-block mt50">
                                <div class="address-save-txt">{__('已保存了')}{count($address_list)}{__('条')}{__('地址')}，{__('还能保存')}<?php echo 20-count($address_list);?>{__('条')}{__('地址')}</div>
                                <table class="address-table-list">
                                    <tr class="tr-hd">
                                        <th width="10%">{__('收货人')}</th>
                                        <th width="35%">{__('收货地址')}</th>
                                        <th width="10%">{__('邮政编码')}</th>
                                        <th width="15%">{__('联系电话')}</th>
                                        <th width="10%">{__('操作')}</th>
                                        <th width="10%">&nbsp;</th>
                                    </tr>
                                    {loop $address_list $address}
                                    <tr class="tr-bd">
                                        <td>{$address['receiver']}</td>
                                        <td>
                                            <div class="ads-txt">{$address['province']}{$address['city']}{$address['address']}</div>
                                        </td>
                                        <td>{$address['postcode']}</td>
                                        <td>{$address['phone']}</td>
                                        <td>
                                            <div class="cz">
                                                <a class="revise modify" href="javascript:;" data-info='{json_encode($address)}'>{__('修改')}</a>|<a class="delete delete-btn" href="javascript:;" data-id="{$address['id']}">{__('删除')}</a>
                                            </div>
                                        </td>
                                        <td>
                                            {if $address['is_default']}
                                                <a class="default-btn"  href="javascript:;">{__('默认地址')}</a>
                                            {else}
                                                <a class="default-set set_default" data-id="{$address['id']}" href="javascript:;">{__('设为默认')}</a>
                                            {/if}
                                        </td>
                                    </tr>
                                    {/loop}

                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- 收货地址 -->
                </div>

            </div>

        </div>
    </div>

{request "pub/footer"}
{Common::js('layer/layer.js')}
{Common::js('city/jquery.cityselect.js',0)}
<div class="layer-wrap-content modify-box hide">
    <div class="address-larey-block">
        <div class="user-address-tit">{__('修改收货地址')}<i class="close-ico"></i></div>
        <div class="user-address-block">
            <form id="modifyfrm" method="post">
            <ul class="address-item">
                <li>
                    <strong class="item-bt">{__('所在地区')}<i>*</i></strong>
                    <div class="item-nr" id="m_area">
                        <select class="drop-down prov fl" name="m_area_prov">
                            <option value="请选择省">{__('请选择省')}</option>

                        </select>
                        <select class="drop-down ml5 city fl" name="m_area_city" id="m_area_city">
                            <option value="请选择市">{__('请选择市')}</option>

                        </select>
                    </div>
                </li>
                <li>
                    <strong class="item-bt">{__('详细地址')}<i>*</i></strong>
                    <div class="item-nr">
                        <textarea name="m_address" id="m_address" class="ads-textarea fl" placeholder="{__('建议您如实填写详细收货地址，例如街道名称，门牌号码，楼层和房间号等信息')}"></textarea>
                    </div>
                </li>
                <li>
                    <strong class="item-bt">{__('邮政编码')}<i>*</i></strong>
                    <div class="item-nr">
                        <input type="text" class="default-text fl" name="m_postcode" id="m_postcode">
                    </div>
                </li>
                <li>
                    <strong class="item-bt">{__('收件人')}<i>*</i></strong>
                    <div class="item-nr">
                        <input type="text" class="default-text fl" id="m_receiver" name="m_receiver">
                    </div>
                </li>
                <li>
                    <strong class="item-bt">{__('联系电话')}<i>*</i></strong>
                    <div class="item-nr">
                        <input type="text" class="default-text fl" name="m_phone" id="m_phone">
                    </div>
                </li>
                <li>
                    <strong class="item-bt">&nbsp;</strong>
                    <div class="item-nr">
                        <label class="radio-label"><input type="checkbox" name="m_is_default" id="m_is_default" class="check-btn" value="1">{__('设置默认收货地址')}</label>
                    </div>
                </li>
                <li>
                    <strong class="item-bt">&nbsp;</strong>
                    <div class="item-nr">
                        <input type="hidden" id="m_address_id" name="m_address_id" value=""/>
                        <a class="save-btn m-save" href="javascript:;">{__('保存')}</a>
                    </div>
                </li>
            </ul>
            </form>
        </div>
    </div>
</div>
<script>
    $("#city").citySelect({
        nodata:"none",
        required:false
    });


    //添加
    $("#addfrm").validate({

        submitHandler:function(form){
            $.ajax({
                type:'POST',
                url:SITEURL+'member/index/ajax_save_address',
                data:$("#addfrm").serialize(),
                dataType:'json',
                success:function(data){
                    if(data.status){
                        layer.msg("{__('save_success')}", {
                            icon: 6,
                            time: 1000
                        })
                        location.reload();
                    }else{
                        layer.msg("{__('save_failure')}", {
                            icon: 5,
                            time: 1000

                        })

                    }
                }
            })
            return false;
        } ,
        errorClass:'st-ts-text',
        errorElement:'span',
        highlight: function(element, errorClass, validClass) {
            $(element).attr('style','border:1px solid red');
        },
        unhighlight:function(element, errorClass){
            $(element).attr('style','');
        },
        errorPlacement:function(error,element){
            $(element).parent().append(error)

        },
        rules: {
            area_prov:
            {
                required:true
            },
            area_city:
            {
                required: true
            },
            phone:
            {
                required:true,
                isMobile:true
            },
            postcode:
            {
                required:true,
                isZipCode:true
            },
			 receiver:
            {
                required:true
            },
            recipient:
            {
                required:true
            },
            address:
            {
                required:true
            }


        },
        messages: {
            area_prov: {
                required:"{__('请选择省份')}"
            },
            area_city:
            {
                required:"{__('请选择城市')}"
            },
            phone:
            {
                required:"{__('请填写联系手机')}",
                isMobile:"{__('请填写正确的手机号码')}"
            },
            postcode:
            {
                required:"{__('请填写邮政编码')}",
                isZipCode:"{__('请填写正确的邮政编码')}"
            },
            receiver:
            {
                required:"{__('收件人不能为空')}"
            },
            address:
            {
                required:"{__('请填写详细地址')}"
            }
        }



    });

    //导航选中
    $("#nav_consignees_address").addClass('on');

    //关闭修改框
    $('.close-ico').click(function(){
        $('.modify-box').hide();
    })

    //添加地址保存
    $('.add-save').click(function(){

        $("#addfrm").submit();

    })

    //修改
    $('.modify').click(function(){

        var info = $(this).data('info');
        $("#m_area_prov").val(info.province);
        $('#m_area_city').val(info.city);
        $('#m_address').val(info.address);
        $('#m_postcode').val(info.postcode);
        $('#m_receiver').val(info.receiver);
        $('#m_phone').val(info.phone);
        $("#m_address_id").val(info.id);
        if(info.is_default==1){
            $("#m_is_default").attr('checked',true);
        }else{
            $("#m_is_default").attr('checked',false);
        }



        $('#m_area').citySelect({
            nodata:"none",
            prov:info.province,
            city:info.city,
            required:false
        })
        //显示修改框
        $('.modify-box').show();
        $('.modify-box').removeClass('hide');



    })


    //修改保存
    $('.m-save').click(function(){
        $('#modifyfrm').submit();
    })

    //修改提交
    $("#modifyfrm").validate({

        submitHandler:function(form){
            $.ajax({
                type:'POST',
                url:SITEURL+'member/index/ajax_modify_save_address',
                data:$("#modifyfrm").serialize(),
                dataType:'json',
                success:function(data){
                    if(data.status){
                        layer.msg("{__('save_success')}", {
                            icon: 6,
                            time: 1000
                        })
                        location.reload();
                    }else{
                        layer.msg("{__('save_failure')}", {
                            icon: 5,
                            time: 1000

                        })

                    }
                }
            })
            return false;
        } ,
        errorClass:'st-ts-text',
        errorElement:'span',
        highlight: function(element, errorClass, validClass) {
            $(element).attr('style','border:1px solid red');
        },
        unhighlight:function(element, errorClass){
            $(element).attr('style','');
        },
        errorPlacement:function(error,element){
            $(element).parent().append(error)

        },
        rules: {
            m_area_prov:
            {
                required:true
            },
            m_area_city:
            {
                required: true
            },
            m_phone:
            {
                required:true,
                isMobile:true
            },
            m_postcode:
            {
                required:true,
                isZipCode:true
            },
            m_receiver:
            {
                required:true
            },
            m_address:
            {
                required:true
            }


        },
        messages: {
            m_area_prov: {
                required:"{__('请选择省份')}"
            },
            m_area_city:
            {
                required:"{__('请选择城市')}"
            },
            m_phone:
            {
                required:"{__('请填写联系手机')}",
                isMobile:"{__('请填写正确的手机号码')}"
            },
            m_postcode:
            {
                required:"{__('请填写邮政编码')}",
                isZipCode:"{__('请填写正确的邮政编码')}"
            },
            m_receiver:
            {
                required:"{__('收件人不能为空')}"
            },
            m_address:
            {
                required:"{__('请填写详细地址')}"
            }
        }



    });

    //设置默认
   $('.set_default').click(function(){
       var address_id = $(this).data('id');
       $.post(SITEURL+'member/index/ajax_modify_save_address',{m_address_id:address_id,m_is_default:1},function(json){
           var data = eval("("+json+")");
           if(data.status){
               layer.msg("{__('save_success')}", {
                   icon: 6,
                   time: 1000
               })
               location.reload();
           }else{
               layer.msg("{__('save_failure')}", {
                   icon: 5,
                   time: 1000

               })

           }
       })
   })

    //删除
    $(document).delegate('.delete-btn','click',function(){


        var obj = this;

        layer.confirm('{__("address_delete_content")}', {
            icon: 3,
            btn: ['{__("Abort")}','{__("OK")}'], //按钮,
            btn1:function(){
                layer.closeAll();
            },
            btn2:function(){
                var address_id = $(obj).data('id');
                $.ajax({
                    type:'POST',
                    url:SITEURL+'member/index/ajax_del_address',
                    data:{'address_id':address_id},
                    dataType:'json',
                    success:function(data){
                        if(data.status){
                            $(obj).parents('tr:first').remove();
                            layer.msg("{__('delete_success')}", {
                                icon: 6,
                                time: 1000
                            })

                        }
                        else
                        {
                            layer.msg('{__("operate_failure")}', {
                                icon: 5,
                                time: 3000
                            })
                        }
                    }
                });
            },
            cancel: function(index, layero){
                layer.closeAll();

            }
        });


    })

    //删除地址
    $('.delete-btn').click(function(){
        var address_id = $(this).data('id');


    })

</script>

</body>
</html>
