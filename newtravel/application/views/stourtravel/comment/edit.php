<!doctype html>
<html>
<head font_float=zi9Z3j >
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,comment.css,base_new.css'); }
    {php echo Common::getScript("jquery.validate.js,hdate/hdate.js"); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,choose.js,product_add.js,imageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    {Common::getCss('hdate.css','js/hdate')}
</head>
<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td">
                <form id="frm" name="frm">
                    <div id="product_grid_panel" class="manage-nr">
                        <div class="cfg-header-bar">
                            <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                        </div>
                        <div class="product-add-div" >
                            <ul class="info-item-block">
                                <li>
                                    <span class="item-hd"><i class="c-red va-m mr-5">*</i>评论产品：</span>
                                    <div class="item-bd" id="product_box">
                                        {if empty($info['memberid'])}
                                        <a href="javascript:;"  class="btn btn-primary radius size-S mt-3 mr-10" id="product_btn">选择</a>
                                        {if !empty($info['articleid'])}
                                        <span class="choose-child-item">{$info['productname']}<i class="icon-Close" onclick="removeProduct(this)"></i></span>
                                        {/if}
                                        <input type="hidden" name="productid" id="productid" value="{$info['articleid']}"/>
                                        {else}
                                        <p class="lh-30 va-m">
                                         {$info['productname']}
                                        </p>
                                        {/if}
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd">评论星级：</span>
                                    <div class="item-bd">
                                        <div class="select-box w100">
                                            <select class="select" name="level">
                                                <option value="1" {if $info['level']==1}selected="selected"{/if}>1星</option>
                                                <option value="2" {if $info['level']==2}selected="selected"{/if}>2星</option>
                                                <option value="3" {if $info['level']==3}selected="selected"{/if}>3星</option>
                                                <option value="4" {if $info['level']==4}selected="selected"{/if}>4星</option>
                                                <option value="5" {if $info['level']==5}selected="selected"{/if}>5星</option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd"><i class="c-red va-m mr-5">*</i>评论内容：</span>
                                    <div class="item-bd">
                                        <textarea name="content" class="textarea w900">{$info['content']}</textarea>
                                    </div>
                                </li>
                                {if empty($info['memberid'])}
                                <li class="list_dl">
                                    <span class="item-hd"><i class="c-red va-m mr-5">*</i>会员昵称：</span>
                                    <div class="item-bd">
                                        <input type="text" class="input-text w200"  name="vr_nickname" id="vr_nickname" value="{$info['vr_nickname']}" >
                                        <span class="item-text ml-10 c-999">录入虚拟会员名称，用于前台显示</span>
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd">会员等级：</span>
                                    <div class="item-bd">
                                        <div class="select-box w100">
                                            <select class="select" name="vr_grade" id="vr_grade">
                                                {loop $grades $grade}
                                                <option value="{$grade['id']}" {if $info['vr_grade']==$grade['id']}selected="selected"{/if}>{$grade['name']}</option>
                                                {/loop}
                                            </select>
                                        </div>
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd">会员头像：</span>
                                    <div class="item-bd">
                                        <a href="javascript:;" id="headpic_btn" class="btn btn-primary radius size-S mt-3">上传图片</a>
                                        <div id="header_pic" class="mt-10">
                                            {if !empty($info['vr_headpic'])}
                                            <img class="up-img-area" src="{$info['vr_headpic']}">
                                            <input type="hidden" name="vr_headpic" value="{$info['vr_headpic']}">
                                            {/if}
                                        </div>
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd">评论送积分：</span>
                                    <div class="item-bd">
                                        <input type="text" class="input-text w100"  name="vr_jifencomment" id="vr_jifencomment" value="{$info['vr_jifencomment']}" >
                                    </div>
                                </li>
                                {else}
                                <li class="list_dl">
                                    <span class="item-hd">会员：</span>
                                    <div class="item-bd">{$info['member']['nickname']}</div>
                                </li>
                                {/if}
                                <li>
                                    <span class="item-hd">评论图片：</span>
                                    <div class="item-bd">
                                        <a href="javascript:;" id="pic_btn" class="btn btn-primary radius size-S mt-3">上传图片</a>
                                        <div class="up-list-div">
                                            <ul class="pic-sel">

                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd">选择评论时间：</span>
                                    <div class="item-bd">
                                        <input type="text" class="input-text w150" name="addtime" value="{if $info['addtime']}{date('Y-m-d',$info['addtime'])}{/if}" onclick=" calendar.show({ id: this})" />
                                    </div>
                                </li>
                                <li class="list_dl">
                                    <span class="item-hd">是否审核通过：</span>
                                    <div class="item-bd">
                                        <label class="radio-label"><input type="radio" name="isshow" {if $info['isshow']==1}checked="checked"{/if} value="1"/>是</label>
                                        <label class="radio-label ml-20"><input type="radio" {if empty($info['isshow'])}checked="checked"{/if} name="isshow" value="0"/>否</label>
                                    </div>
                                </li>
                           </ul>
                        </div>
                        <div class="clear clearfix mt-20">
                            <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
                        </div>
                        <input type="hidden" id="commentid" name="id" value="{$info['id']}">
                        <input type="hidden" id="typeid" name="typeid" value="{$info['typeid']}"/>
                    </div>
                </form>
            </td>
        </tr>
    </table>

<script language="JavaScript">
    var typeid = "{$info['typeid']}"

    $("#product_btn").click(function(){
        CHOOSE.setSome("选择产品",{loadCallback:setProduct,maxHeight:450,width:800},SITEURL+'comment/dialog_product_list?typeid='+typeid,true);
    });

    {if !empty($info['id'])}
        var piclist = ST.Modify.getUploadFile({$info['piclist_arr']});;
        $(".pic-sel").html(piclist);
        var litpic = $("#litpic").val();
        $(".img-li").find('img').each(function(i,item){
            if($(item).attr('src')==litpic){
                var obj = $(item).parent().find('.btn-ste')[0];
                Imageup.setHead(obj,i+1);
            }
        })
        window.image_index= $(".pic-sel").find('li').length;//已添加的图片数量
        $(".btn-ste").remove();
    {/if}

    //表单验证
    $("#frm").validate({

        ignore:[],
        focusInvalid:false,
        rules: {
            vr_nickname:
            {
                required: true

            },
            content:
            {
                required:true
            },
            vr_grade:
            {
                required:true
            },
            productid:
            {
                required:true
            }
        },
        messages: {

            vr_nickname:{
                required:"必填"
            },
            content:
            {
                required:"必填"
            },
            vr_grade:
            {
                required:"必填"
            },
            productid:
            {
                required:"必填"
            }

        },
        errUserFunc:function(element){
            var eleTop = $(element).is(':hidden')?$(element).parent().offset().top:$(element).offset().top;
            $("html,body").animate({scrollTop: eleTop}, 100);

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
                url   :  SITEURL+"comment/ajax_save",
                method  :  "POST",
                form  : "#frm",
                dataType:'json',
                success  :  function(data)
                {
                    if(data.status)
                    {
                        $("#commentid").val(data.id);
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

        /*$("#nav").find('span').click(function(){

            Product.changeTab(this,'.product-add-div');//导航切换

        })
        $("#nav").find('span').first().trigger('click');*/

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
                    $(".btn-ste").remove();
                }
            }
        })

        //上传头像
        $('#headpic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 0,0, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    var html="<img class='up-img-area' src='"+temp[0]+"' />";
                    html+="<input type='hidden' name='vr_headpic' value='"+temp[0]+"'/>"
                    $("#header_pic").html(html);
                }
            }
        })
    })

   function setProduct(result,bool)
   {
       $("#product_box .choose-child-item").remove();
       var title = result.title;
       var len=title.length;
       if(len>25)
         title=title.substr(0,25)+'...';

       var html="<span class='choose-child-item' title='"+result.title+"'>"+title+"<i class='icon-Close' onclick='$(this).parent().remove()'></i></span>";
       $("#productid").val(result.id);
       $("#product_btn").after(html);
       $("#frm").valid();
   }
   function removeProduct(ele)
   {
       $(ele).parent().remove();
       $("#productid").val('');
   }

</script>

</body>
</html>