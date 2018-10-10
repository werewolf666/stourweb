{if $GLOBALS['cfg_bill_open']==1}
<div class="product-msg">
    <h3 class="pm-tit"><strong class="ico06">发票信息</strong></h3>
    <div class="receipt-msg">
        <div class="rp-nav">
            <span class="on" data-needpiao="0">不需要发票</span>
            <span data-needpiao="1">需要发票</span>
        </div>
        <div class="rp-con" style="display: none">
            <dl class="pm-list">
                <dt>发票金额：</dt>
                <dd>
                    <span class="price totalprice"></span>
                    <em class="sm">发票将于您出游归来后5个工作日内开具并由快递寄出，请注意查收</em>
                </dd>
            </dl>
            <dl class="pm-list">
                <dt>发票明细：</dt>
                <dd><span>旅游费</span></dd>
            </dl>
            <dl class="pm-list">
                <dt><span class="st-star-ico">*</span>发票抬头：</dt>
                <dd><input type="text" class="rp-text" name="bill_title" placeholder="请填写个人姓名或公司名称" /></dd>
            </dl>
            <dl class="pm-list">
                <dt><span class="st-star-ico">*</span>收件人：</dt>
                <dd><input type="text" class="rp-text" name="bill_receiver" /></dd>
            </dl>
            <dl class="pm-list">
                <dt><span class="st-star-ico">*</span>联系电话：</dt>
                <dd><input type="text" class="rp-text" name="bill_phone" /></dd>
            </dl>
            <dl class="pm-list">
                <dt><span class="st-star-ico">*</span>邮寄地址：</dt>
                <dd>
                    <div id="city">
                        <select name="bill_prov" class="dest-select prov">
                            <option value="请选择">请选择</option>
                        </select>
                        <select name="bill_city" class="dest-select city">
                            <option value="请选择">请选择</option>
                        </select>
                    </div>
                    <textarea class="ads-area" name="bill_address" cols="" rows="" placeholder="请填写详细收货地址"></textarea>
                </dd>
            </dl>
        </div>
    </div>
    <!--隐藏域-->
    <input type="hidden" id="isneedbill" name="isneedbill" value="0">

</div><!--发票信息-->
{Common::js('city/jquery.cityselect.js',0)}
<script>
    $(function(){
        //是否需要发票
        $(".rp-nav span").click(function(){
            $(this).addClass('on').siblings().removeClass('on');
            var v = parseInt($(this).attr('data-needpiao'));
            if(v==1){
                $('.rp-con').show();
                $('#isneedbill').val(1);
                $("input[name='bill_title']").rules("add",{ required: true, messages: { required: "请填写发票抬头"} });
                $("input[name='bill_receiver']").rules("add",{ required: true, messages: { required: "请填写收件人"} });
                $("input[name='bill_phone']").rules("add",{ required:true,isPhone: true, messages: {required:"请填写手机号码"}});
                $("input[name='bill_address']").rules("add",{ required: true, messages: { required: "请填写收件人详细地址"} });



            }else{
                $('.rp-con').hide();
                $('#isneedbill').val(0);
                $("input[name='bill_title']").rules("remove");
                $("input[name='bill_receiver']").rules("remove");
                $("input[name='bill_address']").rules("remove");
                $("input[name='bill_phone']").rules("remove");


            }
        })

        $("#city").citySelect({
            nodata:"none",
            required:false
        });
    })
</script>
{/if}