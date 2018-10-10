{st:member action="address" memberid="$userInfo['mid']" return="address_list"}
<div class="product-msg">
    <h3 class="pm-tit"><strong class="ico09">收货地址</strong>{if count($address_list)<20}<a class="set-address-link add-address" href="javascript:;">+使用新地址</a>{/if}</h3>
    <div class="ads-item-block clear">

        <ul id="address_list">
            <li class="on">
                <label class="radio-label">
                    <input type="radio" name="receive_address_id" checked class="radio-btn" value="0"/>
                    <span>不需要收货地址</span>
                </label>
            </li>
            {loop $address_list $address}
            <li {if $n>5}style="display:none"{/if}>
            <label class="radio-label">
                <input type="radio" name="receive_address_id" default="{$address['is_default']}" class="radio-btn" value="{$address['id']}">
                <span id="address_{$address['id']}">{$address['province']}{$address['city']}{$address['address']} （{$address['receiver']}）{$address['phone']}</span>
                <span class="set-modify">
                {if $address['is_default']}
                    <span class="attr">默认地址</span>
                {else}
                    <a class="set set-default" data-id="{$address['id']}" href="javascript:;">设为默认</a>
                {/if}
                </span>

                <a class="xg xg-btn" data-info='{json_encode($address)}' href="javascript:;">修改本地址</a>
            </label>
            </li>
            {/loop}

        </ul>
        {if count($address_list)>5}
        <a class="show-all-ads" href="javascript:;">显示全部地址</a>
        {/if}
    </div>
</div>



<script>
    $(function(){
        //收货地址
        $('.radio-label input').click(function(){

            $(this).parents('ul').find('li').removeClass('on');
            $(this).parents('li:first').addClass('on');
        })
        $("#address_list").find('li:first').trigger('click');


        //添加新地址
        $(".add-address").click(function(){
            $("#m_area_prov").val('');
            $('#m_area_city').val('');
            $('#m_address').val('');
            $('#m_postcode').val('');
            $('#m_receiver').val('');
            $('#m_phone').val('');
            $("#m_address_id").val(0);
            $('.modify-box').show();
            $('#m_is_default').attr('checked',false);

            $('#m_area').citySelect({
                nodata:"none",
                required:false
            })
        })
        //修改地址
        $('body').delegate('.xg-btn','click',function(){
            var info = $(this).data('info');

            $("#m_area_prov").val(info.province);
            $('#m_area_city').val(info.city);
            $('#m_address').val(info.address);
            $('#m_postcode').val(info.postcode);
            $('#m_receiver').val(info.receiver);
            $('#m_phone').val(info.phone);
            $("#m_address_id").val(info.id);
            if(parseInt(info.is_default)==1){
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

        })

        //查看更多
        $('.show-all-ads').click(function(){
            if($(this).text()=='显示全部地址'){
                $('#address_list').find('li').show();
                $(this).text('收起地址');
            }else{
                $('#address_list').find('li:gt(5)').hide();
                $(this).text('显示全部地址');
            }

        })
        //设为默认
        $('body').delegate('.set-default','click',function(){

            var address_id = $(this).data('id');
            var url =  SITEURL+'member/index/ajax_front_modify_save_address';
            var that = this;
            $.ajax({
                type:'POST',
                url:url,
                dataType:'json',
                data:{m_address_id:address_id,m_is_default:1},
                success:function(data){
                    if(data.status){

                        var orignal_address_id = $("input[default='1']").parent().find('input[name="receive_address_id"]').val();
                        var h = '<a class="set set-default" data-id="'+orignal_address_id+'" href="javascript:;">设为默认</a>';
                        $("input[default='1']").parent().find('.set-modify').html(h);
                        $("input[default='1']").attr('default',0);

                        $(that).parents('.radio-label').first().find("input[name='receive_address_id']").attr('default','1');
                        //设置新的info

                        $(that).parents('.radio-label').first().find('.xg-btn').data('info', eval('(' + data.json + ')'));




                        $current_html = '<span class="attr">默认地址</span>';
                        $(that).parent().first().html($current_html);





                    }
                }
            })


        })

    })

</script>