{Common::js('city/jquery.cityselect.js',0)}
<div class="layer-wrap-content modify-box" style="display: none;">
    <div class="address-larey-block">
        <div class="user-address-tit">{__('收货地址')}<i class="close-ico"></i></div>
        <div class="user-address-block">
            <form id="modifyfrm" method="post" head_ul=Jyvz8B >
                <ul class="address-item">
                    <li>
                        <strong class="item-bt">{__('所在地区')}<i>*</i></strong>
                        <div class="item-nr" id="m_area">
                            <select class="drop-down prov fl" name="m_area_prov" id="m_area_prov">
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
    $(function(){
        //关闭弹出框
        $('.close-ico').click(function(){
            $('.modify-box').hide();
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
                    url:SITEURL+'member/index/ajax_front_modify_save_address',
                    data:$("#modifyfrm").serialize(),
                    dataType:'json',
                    success:function(data){
                        if(data.status){

                            var prov = $('#m_area_prov').val();
                            var city = $('#m_area_city').val();
                            var address =$('#m_address').val();
                            var receiver = $('#m_receiver').val();
                            var phone = $('#m_phone').val();
                            var is_default = $('#m_is_default').attr('checked') != undefined ? 1 : 0;
                            var address_text = prov+city+address+' ('+receiver+') '+phone;
                            var address_id = parseInt($('#m_address_id').val());

                            //执行的修改地址操作
                            if(address_id>0){
                                $('#address_'+address_id).html(address_text);
                                $('.close-ico').trigger('click');
                            }else{
                                var li = '<li>';
                                li+= '<label class="radio-label">';
                                li+= '<input type="radio" name="receive_address_id" default="'+is_default+'" class="radio-btn" value="'+data.insert_id+'">';
                                li+= '<span id="address_'+data.insert_id+'">'+address_text+'</span>';
                                li+= ' <span class="set-modify">';
                                if(is_default){
                                    li+= '<span class="attr">{__("默认地址")}</span>';
                                }else{
                                    li+= '<a class="set set-default" data-id="'+data.insert_id+'" href="javascript:;">{__("设为默认")}</a>';
                                }
                                li+= '</span>';
                                li+= '<a class="xg xg-btn" data-info='+data.json+' href="javascript:;">{__("修改本地址")}</a>';
                                li+='</label>';
                                li+='</li>';

                                $('#address_list').find('li:first').after(li);
                                $("#address_"+data.insert_id).parents('li:first').addClass('on').siblings().removeClass('on');
                                $("#address_"+data.insert_id).parents('li:first').find('input').attr('checked',true);
                                $('.close-ico').trigger('click');
                            }




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
    })
</script>