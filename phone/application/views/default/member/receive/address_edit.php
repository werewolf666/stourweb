<div id="editAddrss" class="page out">
    <div class="header_top bar-nav">
        <a class="back-link-icon"  href="javascript:;" onclick="history.go(-1)"></a>
        <h1 class="page-title-bar">{$title}地址</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <form id="frm">
        <div class="user-item-list">
            <ul class="list-group">
                <li>
                    <strong class="hd-name">收货人</strong>
                    <input type="text" name="receiver" class="set-txt fr" value="{$info['receiver']}" placeholder="请填写真实姓名">
                </li>
                <li>
                    <strong class="hd-name">联系电话</strong>
                    <input type="text" name="phone" class="set-txt fr" value="{$info['phone']}" placeholder="请填写有效电话">
                </li>
                <li>
                    <strong class="hd-name">邮政编码</strong>
                    <input type="text" name="postcode" class="set-txt fr"  value="{$info['postcode']}" placeholder="请填写邮政编码">
                </li>
                <li>
                    <a id="selectCity" href="#">
                        <strong class="hd-name">所在地区</strong>
                        <span class="set-txt fr" id="showCity">{$info['province']} {$info['city']}</span>
                        <input type="hidden" name="province" id="province" value="{$info['province']}"/>
                        <input type="hidden" name="city" id="city" value="{$info['city']}"/>
                        <i class="arrow-rig-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div id='cityResult3' class="ui-alert"></div>
        <textarea class="show-addrss-info" id="address" name="address" placeholder="请填写详细地址，不少于5个字">{$info['address']}</textarea>
        <div class="set-default-addrss clearfix">
            <em class="set-tit">设为默认</em>
            <span class="check-label-item {if $info['is_default']}checked{/if} fr"><i class="icon"></i>默认地址</span>
            <input type="hidden" name="is_default" value="{$info['is_default']}"/>
        </div>
        <input type="hidden" name="id" value="{$info['id']}">
        <div class="error-txt" style="display: none"><i class="ico"></i><span class="errormsg"></div>
        </form>
        <div class="bottom-fix-bar">
            <a class="addrss-fix-btn fix-btn save_address" href="javascript:;">保存</a>
        </div>
    </div>
</div>
<!-- 编辑地址 -->
<script>
    //级联选择
    (function($, doc) {
        $.init();
        $.ready(function() {
            //级联示例
            var cityPicker3 = new $.PopPicker({
                layer: 3
            });
            cityPicker3.setData(cityData3);
            var showCityPickerButton = doc.getElementById('selectCity');
            var showCity = doc.getElementById('showCity');
            showCityPickerButton.addEventListener('tap', function(event) {
                cityPicker3.show(function(items) {
                    showCity.innerText = (items[0] || {}).text + " " + (items[1] || {}).text;
                    doc.getElementById('province').value=items[0]['text'];
                    doc.getElementById('city').value=items[1]['text'];
                    doc.getElementById('address').innerText=typeof(items[2]['text'])=='undefined'?'':items[2]['text'];
                });
            }, false);
        });
    })(mui, document);
    $('.check-label-item').click(function(){
        if($(this).hasClass('checked')){
            $('input[name="is_default"]').val(0);
            $(this).removeClass('checked');
        }else{
            $('input[name="is_default"]').val(1);
            $(this).addClass('checked');
        }
    });
    $('.save_address').click(function(){
        $('#frm').submit();
    });
    $('#frm').validate({
        rules: {
            receiver: {
                required: true
            },
            phone: {
                required: true,
                digits: true,
                mobile:true
            },
            postcode: {
                required: true,
                digits: true,
                postCode:true
            },
            province: {
                required: true
            },
            city: {
                required: true
            },
            address: {
                required: true
            }
        },
        messages: {
            receiver: {
                required: '请填写收货人'
            },
            phone: {
                required: '请填写联系电话',
                digits: '请输入正确的联系电话',
                mobile:"请输入正确的联系电话"
            },
            postcode: {
                required: '请填写邮政编码',
                digits: '请输入正确的邮政编码',
                postCode:'请输入正确的邮政编码'
            },
            province: {
                required: '请选择所在地区'
            },
            city: {
                required: '请选择所在地区'
            },
            address: {
                required: '请填写详细地址'
            }
        },
        errorPlacement: function (error, element) {
            var content = $('.errormsg').html();
            if (content == '') {
                error.appendTo($('.errormsg'));
            }
        },
        showErrors: function (errorMap, errorList) {
            if (errorList.length < 1) {
                $('.errormsg:eq(0)').html('');
                $('.error-txt').hide();
            } else {
                this.defaultShowErrors();
                $('.error-txt').show();
            }
        },
        submitHandler: function (form) {
            var frmdata = $("#frm").serialize();
            $.ajax({
                type: 'POST',
                url: SITEURL + 'member/receive/ajax_save',
                data: frmdata,
                dataType: 'json',
                success: function (data) {
                    if (data.status) {
                        $.layer({
                            type: 1,
                            icon: 1,
                            text: '保存成功',
                            time: 1000
                        });
                        //返回上一页面并动态刷新
                        var url = '{$cmsurl}member#&receiveAddress';
                        setTimeout(function () {
                            window.location.href = url;
                        }, 1000)
                    } else {
                        $.layer({
                            type: 1,
                            icon: 2,
                            text: data.msg,
                            time: 1000
                        })
                    }
                }
            })
        }
    });
</script>
