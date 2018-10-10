<!doctype html>
<html>
<head float_size=z0hy5k >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,base_new.css'); }
    {php echo Common::getScript("jquery.validate.js"); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,choose.js,product_add.js,imageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
   <style>
        .error{
            color:red;
            padding-left:5px;
        }
        .hide{
            display: none;
        }

    </style>

</head>
<body style="background-color: #fff">
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td">
            <form id="frm" name="frm">
            <div id="product_grid_panel" class="manage-nr">
                <div class="w-set-con">
                    <div class="cfg-header-bar" id="nav">
                    	<div class="cfg-header-tab">
                    		<span class="item on" id="basic"><s></s>基础信息</span>
	                        <span class="item" data-id="jieshao"><s></s>介绍</span>
	                        <span class="item" data-id="tupian"><s></s>图片</span>
	                        <span class="item" data-id="extend"><s></s>扩展</span>
	                        {if !empty($qua)}
	                        <span class="item" data-id="qualify"><s></s>资质验证</span>
	                        {/if}
                    	</div>
                        
                        <a href="javascript:;" class="fr btn btn-primary radius mr-10 mt-6" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>
                        <div class="product-add-div" >
                            <ul class="info-item-block">
                            <li>
                                <span class="item-hd">所属分类{Common::get_help_icon('supplier_field_kindid')}：</span>
                                <div class="item-bd">
                                    <span class="select-box w150">
                                        <select class="select" name="kindid" id="">
                                            <option value="0">默认</option>
                                            {loop $kind $v}
                                            <option value="{$v['id']}" {if $v['id']==$info['kindid'] }selected="selected"{/if}>{$v['kindname']}</option>
                                            {/loop}
                                        </select>
                                    </span>
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">供应商名称：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w500" name="suppliername" id="suppliername" value="{$info['suppliername']}" >
                                </div>
                            </li>
                            <li>
                                <span class="item-hd">供应商类型{Common::get_help_icon('supplier_field_suppliertype')}：</span>
                                <div class="item-bd">
                                    <span class="select-box w150">
                                        <select class="select" name="suppliertype">
                                            <option value="0" {if $info['suppliertype']==0 }selected="selected"{/if}>平台供应商</option>
                                            <option value="1" {if $info['suppliertype']==1 }selected="selected"{/if}>第三方供应商</option>
                                        </select>
                                    </span>
                                </div>
                            </li>
                            <li class="list_dl">
                                <span class="item-hd">认证状态：</span>
                                <div class="item-bd">
                                    <span class="select-box w150">
                                        <select class="select" name="verifystatus">
                                            <option value="0" {if $info['verifystatus']==0}selected="selected"{/if}>未认证</option>
                                            <option value="1" {if $info['verifystatus']==1}selected="selected"{/if}>审核中</option>
                                            <option value="2" {if $info['verifystatus']==2}selected="selected"{/if}>未通过</option>
                                            <option value="3" {if $info['verifystatus']==3}selected="selected"{/if}>已认证</option>
                                        </select>
                                    </span>
                                </div>
                            </li>
                            <li class="list_dl">
                                <span class="item-hd">联系人：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w200"  name="linkman" id="linkman" value="{$info['linkman']}" >
                                </div>
                            </li>
                            <li class="list_dl">
                                <span class="item-hd">座机：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w200" name="telephone" id="telephone" value="{$info['telephone']}" >
                                </div>
                            </li>

                            <li class="list_dl">
                                <span class="item-hd">手机：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w200" name="mobile" id="mobile" value="{$info['mobile']}" >
                                </div>
                            </li>
                                <li class="list_dl">
                                    <span class="item-hd">传真：</span>
                                    <div class="item-bd">
                                        <input type="text" class="input-text w200" name="fax" id="fax" value="{$info['fax']}">
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd">邮箱：</span>
                                    <div class="item-bd">
                                        <input type="text" class="input-text w200" name="email" id="fax" value="{$info['email']}">
                                    </div>
                                </li>

                                <li class="list_dl">
                                <span class="item-hd">QQ：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w200" name="qq" id="qq" value="{$info['qq']}" >
                                </div>
                            </li>
                            <li class="list_dl">
                                <span class="item-hd">地址：</span>
                                <div class="item-bd">
                                    <input type="text" class="input-text w200" name="address" id="address" value="{$info['address']}" >
                                </div>
                            </li>
                            <li class="list_dl">
                                <span class="item-hd">坐标{Common::get_help_icon('supplier_field_lng_lat')}：</span>
                                <div class="item-bd">
                                    <span class="item-text">经度(Lng):</span>
                                    <input type="text" name="lng" id="lng"  class="input-text w200" value="{$info['lng']}" />
                                    <span class="item-text ml-20">纬度(Lat):</span>
                                    <input type="text" name="lat" id="lat" class="input-text w200" value="{$info['lat']}"  />
                                    <a href="javascript:;" class="btn btn-primary radius size-S ml-5" onclick="Product.Coordinates(700,500)"  title="选择">选择</a>
                                </div>
                            </li>
                            <li class="list_dl">
                                <span class="item-hd">目的地{Common::get_help_icon('supplier_field_finaldestid')}：</span>
                                <div class="item-bd">
                                    <a href="javascript:;" class="btn btn-primary radius size-S mt-3" onclick="Product.getDest(this,'.dest-sel',0)"  title="选择">选择</a>
                                    <div class="save-value-div mt-2 ml-10 dest-sel">
                                        {loop $info['kindlist_arr'] $k $v}
                                            <span class="{if $info['finaldestid']==$v['id']}finaldest{/if}" title="{if $info['finaldestid']==$v['id']}最终目的地{/if}" ><s onclick="$(this).parent('span').remove()"></s>{$v['kindname']}<input type="hidden" class="lk" name="kindlist[]" value="{$v['id']}"/>
                                            {if $info['finaldestid']==$v['id']}<input type="hidden" class="fk" name="finaldestid" value="{$info['finaldestid']}"/>{/if}</span>
                                        {/loop}
                                    </div>
                                </div>
                            </li>
                           </ul>
                        </div>
                        <div class="product-add-div" data-id="tupian">
                    		<ul class="info-item-block">
                    			<li>
                    				<span class="item-hd">供应商图片：</span>
                    				<div class="item-bd">
                    					<div id="pic_btn" class="btn btn-primary radius size-S mt-4">上传图片</div>
                    					<div class="clear"></div>
                    					<div class="up-list-div">
                                            <input type="hidden" class="headimgindex" name="imgheadindex" value=""/>
                                            <input type="hidden" name="litpic" id="litpic" value="{$info['litpic']}"/>
                                            <ul class="pic-sel">
													
                                            </ul>
                                        </div>
                    				</div>
                    			</li>
                    		</ul>    
                        </div>
                        <div class="product-add-div pd-20" data-id="jieshao">
                            {php Common::getEditor('content',$info['content'],$sysconfig['cfg_admin_htmleditor_width'],400);}
                        </div>
                        <div class="product-add-div pb-5" data-id="extend">
                        	<ul class="info-item-block">
                        		<li><p class="lh-30 c-primary pl-75">注意：本功能是用于供应商开发接入，标准用户不需要进行配置</p></li>
                        		<li>
                        			<span class="item-hd">账户{Common::get_help_icon('supplier_field_account')}：</span>
                        			<div class="item-bd">
                        				<input type="text" class="input-text w400" name="account" autocomplete="off" id="account" value="{$info['account']}" >
                        			</div>
                        		</li>
                        		<li>
                        			<span class="item-hd">密码{Common::get_help_icon('supplier_field_password')}：</span>
                        			<div class="item-bd">
                        				<input type="password" class="input-text w400" name="password" autocomplete="off" id="password" >
                        			</div>
                        		</li>
                        	
                            	{if !empty($info_qualification)}
	                        		<li><p class="lh-30 c-primary pl-75">供应商资质认证信息</p></li>
	                        		<li>
	                        			<span class="item-hd">认证方式：</span>
	                        			<div class="item-bd">
	                        				<span class="item-text">
		                        				{if $info_qualification['qualification_type']=='0'}
		                                        echo '旅行社工作名片';
		                                        {else if $info_qualification['qualification_type']=='1'}
		                                        echo '经营许可证(备案登记证)';
		                                        {else}
		                                        echo '营业执照(副本)';
		                                        {/if}
	                                        </span>
	                        			</div>
	                        		</li>
	                        		<li>
	                        			<span class="item-hd">密码1：</span>
	                        			<div class="item-bd">
	                        				<input type="password" class="input-text w400" name="password" autocomplete="off" id="password" >
	                        			</div>
	                        		</li>
	                        	{/if}
	                        </ul>   
                            	 
                        </div>
                        
                        {if !empty($qua)}
                        <div class="product-add-div" data-id="qualify">
							<ul class="info-item-block">
								<li><p class="lh-30 c-primary pl-75">注意：本功能是用于供应商资质验证，标准用户不需要进行配置</p></li>
								<li>
									<span class="item-hd">认证方式：</span>
									<div class="item-bd">
										<div class="authority_type">
											<label class="radio-label mr-5" for="v1">
												<input class="verify-type" type="radio" name="verifytype" id="v1" data-type="card" checked value="旅行社工作名片">旅行社工作名片
											</label>
											<label class="radio-label mr-5" for="v2">
												<input class="verify-type" type="radio" name="verifytype" id="v2" data-type="license" value="经营许可证">经营许可证
											</label>
											<label class="radio-label mr-5" for="v3">
												<input class="verify-type" type="radio" name="verifytype" id="v3" data-type="certify" value="营业执照(副本)">营业执照(副本)
											</label>
										</div>
									</div>
								</li>

                                <li class="card optial">
                                    <span class="item-hd">名片图片：</span>
                                    <div class="item-bd">
                                        <img src="{$qua['mp_litpic']}" width="215" height="136">
                                        &nbsp;&nbsp;<a class="btn-link" href="{$qua['mp_litpic']}" target="_blank">查看</a>
                                    </div>
                                </li>

                                <li class="license hide optial">
                                    <span class="item-hd">许可证号码：</span>
                                    <div class="item-bd">
                                        <span class="item-text">{$qua['licenseno']}</span>
                                    </div>
                                </li>
                                <li class="license hide optial">
                                    <span class="item-hd">许可证图片：</span>
                                    <div class="item-bd">
                                        <img src="{$qua['xk_litpic']}" width="215" height="136">
                                        &nbsp;&nbsp;<a class="btn-link" href="{$qua['xk_litpic']}" target="_blank">查看</a>
                                    </div>
                                </li>

                                <li class="certify hide optial">
                                    <span class="item-hd">营业执照：</span>
                                    <div class="item-bd">
                                        <span class="item-text">{$qua['certifyno']}</span>
                                    </div>
                                </li>
                                <li class="certify hide optial">
                                    <span class="item-hd">营业执照图片</span>
                                    <div class="item-bd">
                                        <img src="{$qua['zz_litpic']}" width="215" height="136">
                                        &nbsp;&nbsp;<a class="btn-link" href="{$qua['zz_litpic']}" target="_blank">查看</a>
                                    </div>
                                </li>

								<li>
									<span class="item-hd">法人代表：</span>
									<div class="item-bd">
										<span class="item-text">{$qua['reprent']}</span>
									</div>
								</li>
								<li>
									<span class="item-hd">供应商名称：</span>
									<div class="item-bd">
										<span class="item-text">{$qua['suppliername']}</span>
									</div>
								</li>
								<li>
									<span class="item-hd">公司地址：</span>
									<div class="item-bd">
										<span class="item-text">{$qua['address']}</span>
									</div>
								</li>
								<li>
									<span class="item-hd">申请产品：</span>
									<div class="item-bd">
										<span class="item-text">
											{loop $apply_product $p}
		                                     <label>{$p['kindname']}&nbsp;&nbsp;</label>
		                                 	{/loop}
		                                </span>
									</div>
								</li>


								<li>
									<span class="item-hd">是否通过审核：</span>
									<div class="item-bd">
										<span class="item-text verify">
											<label class="radio-label mr-5" for="c1">
												<input type="radio" name="vstatus" id="c1" {if $info['verifystatus']==1}checked{/if} value="1">待审核
											</label>
											<label class="radio-label mr-5" for="c3">
												<input type="radio" name="vstatus" id="c3" {if $info['verifystatus']==3}checked{/if} value="3">通过
											</label>
											<label class="radio-label mr-5" for="c2">
												<input type="radio" name="vstatus" id="c2" {if $info['verifystatus']==2}checked{/if} value="2">不通过
											</label>
										</span>
									</div>
								</li>
								<li class="{if $info['verifystatus']!=3}hide{/if}" id="product_right">
									<span class="item-hd">供应商权限：</span>
									<div class="item-bd">
										<span class="item-text">
											{php}$r_kind = explode(',',$info['authorization']){/php}
			                                 {loop $product_list $p}
			                                    <label><input type="checkbox" class="right" name="authorization[]" value="{$p['id']}" {if in_array($p['id'],$r_kind)}checked="checked"{/if}>{$p['modulename']}</label>
			                                 {/loop}
										</span>
									</div>
								</li>
								<li class="reason {if $info['verifystatus']!=2}hide{/if}">
									<span class="item-hd">未通过原因：</span>
									<div class="item-bd">
										<input type="text" name="reason" id="reason" class="input-text w400" value="{$info['reason']}"/>
									</div>
								</li>
							</ul>	  
                        </div>
                        {/if}
					    <div class="clear clearfix">
                            <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
                        </div>
						
                        <dl class="list_dl">
                            <dt class="wid_90">&nbsp;</dt>
                            <dd>
                                <input type="hidden" id="id" name="id" value="{$info['id']}">
                                <input type="hidden" name="action" value="{$action}">
                                <input type="hidden" name="kind_right" id="kind_right" value="{$action}">
                                <input type="hidden" name="litpic" id="litpic" value="{$info['litpic']}">
                            </dd>
                        </dl>
            	</div>
            </form>
        </td>
    </tr>
</table>


<script language="JavaScript">



    var action='{$action}';

    {if $action=='edit'}
        var piclist = ST.Modify.getUploadFile({$info['piclist_arr']});
        $(".pic-sel").html(piclist);
        var litpic = $("#litpic").val();
        $(".img-li").find('img').each(function(i,item){

            if($(item).attr('src')==litpic){
                var obj = $(item).parent().find('.btn-ste')[0];
                Imageup.setHead(obj,i+1);
            }
        })
        window.image_index= $(".pic-sel").find('li').length;//已添加的图片数量
    {/if}

    //认证选择
        $('.authority_type').find('input').click(function(){
            var type = $(this).attr('data-type');
            $('.optial').hide();
            $('.'+type).show();
        })
    //验证是否通过
        $('.verify').find('input').click(function(){
            if($(this).val()==2){
                $('.reason').show();
            }else{
                $('.reason').hide();
            }
        })

    //通过
        $("#c3").click(function(){
            $("#product_right").show();
        })
        $("#c1").click(function(){
            $("#product_right").hide();
        })

        $("#c2").click(function(){
            $("#product_right").hide();
        });

    //表单验证
    $("#frm").validate({

        focusInvalid:false,
        rules: {
            suppliername:
            {
                required: true

            },
            /*account:{
               required:true
            },*/
            linkman: {
                required: true

            }
        },
        messages: {

            suppliername:{
                required:"请输入供应商名称"

            },
            /*account:{
                required:"账户不能为空"
            },*/
            linkman: {
                required:"请输入联系人"

            }

        },
        errUserFunc:function(element){


        },
        submitHandler:function(form){

            var right = [];
            $(".right").each(function(i,obj){
                if($(obj).attr('checked')=='checked'){
                    right.push($(obj).val());
                }
            })

            $("#kind_right").val(right.join(','));


            $.ajaxform({
                url   :  SITEURL+"supplier/ajax_save",
                method  :  "POST",
                form  : "#frm",
                dataType:'json',
                success  :  function(data)
                {
                    if(data.status)
                    {
                        $("#id").val(data.productid);
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

    $(function(){

        $("#nav").find('span').click(function(){

            Product.changeTab(this,'.product-add-div');//导航切换

        })
        $("#nav").find('span').first().trigger('click');


        //保存
        $("#btn_save").click(function(){


            $("#frm").submit();

            return false;

        })

        //上传图片
        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    Imageup.genePic(temp[0],".up-list-div ul",".cover-div");
                }
            }
        });

        //验证方式切换
        $('.verify-type').change(function(){
            var type = $(this).data('type');
            switch(type){
                case 'license':
                    $('.info-item-block').find('.license').removeClass('hide');
                    $('.info-item-block').find('.card').addClass('hide');
                    $('.info-item-block').find('.certify').addClass('hide');
                    break;
                case 'certify':
                    $('.info-item-block').find('.certify').removeClass('hide');
                    $('.info-item-block').find('.card').addClass('hide');
                    $('.info-item-block').find('.license').addClass('hide');
                    break;
                default:
                    $('.info-item-block').find('.card').removeClass('hide');
                    $('.info-item-block').find('.license').addClass('hide');
                    $('.info-item-block').find('.certify').addClass('hide');
                    break;
            }
        });

    })

</script>

</body>
</html>