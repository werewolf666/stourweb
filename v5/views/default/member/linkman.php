<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{__('常用联系人')}-{$webname}</title>
    {include "pub/varname"}
    {Common::css('user.css,base.css,extend.css')}
    {Common::js('jquery.min.js,base.js,common.js,jquery.validate.js,jquery.cookie.js,jquery.validate.addcheck.js')}
</head>

<body>

{request "pub/header"}
  
  <div class="big">
  	<div class="wm-1200">
    
    	<div class="st-guide">
      	<a href="{$cmsurl}">{__('首页')}</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;{__('常用地址')}
      </div><!--面包屑-->
      
      <div class="st-main-page">
          {include "member/left_menu"}
        <div class="user-cont-box">
        	<div class="add-linkman-box">
          	<div class="linkman-tit">{__('常用旅客')}</div>
            <div class="linkman-con">
            	<div class="linkman-list">
                <form id="linkmanfrm" method="post" ul_padding=PoKwOs >
                <table width="100%" border="0" id="linktable">

                 {st:member action="linkman" memberid="$mid"}
                    {if !empty($data[0]['linkman'])}
                    {loop $data $row}
                      <tr>
                        <td width="40%" height="60"><span class="st-star-ico fl">*</span><span class="child"><em>{__('姓名')}：</em><input type="text" name="t_name[{$n}]" class="lm-text" value="{$row['linkman']}" /></span></td>
                        <td width="50%">
                        <span class="st-star-ico fl">*</span>
                        <span class="child">
                          <em>证件号：</em>
                            <select name="t_cardtype[{$n}]" id="t_cardtype_{$n}">

                              <option value="身份证" {if $row['cardtype']=='身份证'}selected="selected"{/if}>{__('身份证')}</option>
                                <option value="护照" {if $row['cardtype']=='护照'}selected="selected"{/if}>{__('护照')}</option>
                              <option value="台胞证" {if $row['cardtype']=='台胞证'}selected="selected"{/if}>{__('台胞证')}</option>
                              <option value="港澳通行证" {if $row['cardtype']=='港澳通行证'}selected="selected"{/if}>{__('港澳通行证')}</option>
                              <option value="军官证" {if $row['cardtype']=='军官证'}selected="selected"{/if}>{__('军官证')}</option>
                                <option value="出生日期" {if $row['cardtype']=='出生日期'}selected="selected"{/if}>{__('出生日期')}</option>
                            </select>
                            <input type="text" data-id="{$row['id']}" class="lm-text" id="t_cardno_{$n}" name="t_cardno[{$n}]" value="{$row['idcard']}" />
                          </span>

                        </td>
                        <td width="10%" align="center"><span class="st-delete-ico"></span></td>
                      </tr>
                    {/loop}
                    {/if}
                {/st}

                </table>

                <div class="add-linkman-btn"><a href="javascript:;" class="addman"><i>+</i>{__('添加')}</a></div>
              </div>
              <div class="keep-linkman"><a href="javascript:;" class="save">{__('保 存')}</a></div>
            </div>
          </div>
        </div>
      </div>
    
    </div>
  </div>
  
{request "pub/footer"}
{Common::js('layer/layer.js')}
<script>
    $(function(){
        //导航选中
        $("#nav_linkman").addClass('on');

        $("#linkmanfrm").validate({

            submitHandler:function(form){
                $.ajax({
                    type:'POST',
                    url:SITEURL+'member/index/ajax_do_save_linkman',
                    data:$("#linkmanfrm").serialize(),
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
                });
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

            }



        });
        dynamic_event();

        //保存
        $(".save").click(function(){
            $("#linkmanfrm").submit();
        });

        //删除
        $(document).delegate('.st-delete-ico','click',function(){

            var obj = this;
            if($(this).attr('data-type') == 'js'){
                $(obj).parents('tr:first').remove();
                return false;
            }


            layer.confirm('{__("linkman_delete_content")}', {
                icon: 3,
                btn: ['{__("Abort")}','{__("OK")}'], //按钮,
                btn1:function(){
                    layer.closeAll();
                },
                btn2:function(){
                    var linkman = $(obj).parents('tr:first').find('.lm-text:first').val();
                    var code = $(obj).parents('tr:first').find('.lm-text:last').val();
                    $.ajax({
                        type:'POST',
                        url:SITEURL+'member/index/ajax_do_del_linkman',
                        data:{'linkman':linkman,'code':code},
                        dataType:'json',
                        success:function(data){
                            if(data.status){
                                $(obj).parents('tr:first').remove();
                            }
                            else
                            {
                                layer.msg("{__('操作失败,刷新在试试')}！", {
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


        });


        //添加游客
        $(".addman").click(function(){
          var num = Number($("#linktable tr").length)+1;
          var html =' <tr>';
              html+=' <td width="40%" height="60"><span class="st-star-ico fl">*</span><span class="child"><em>姓名：</em><input type="text" name="t_name['+num+']" class="lm-text" value="" /></span></td>';
              html+='<td width="50%">';
              html+='<span class="st-star-ico fl">*</span>';
              html+='<span class="child">';
              html+='<em>{__("证件号")}：</em>';
              html+='<select name="t_cardtype['+num+']" id="t_cardtype_'+num+'">';

              html+='<option value="身份证">{__("身份证")}</option>';
              html+='<option value="护照">{__("护照")}</option>';
              html+='<option value="台胞证">{__("台胞证")}</option>';
              html+='<option value="港澳通行证">{__("港澳通行证")}</option>';
              html+='<option value="军官证">{__("军官证")}</option>';
              html+='<option value="出生日期">{__("出生日期")}</option>';

              html+='</select>';
              html+='<input type="text" class="lm-text" data-id="" id="t_cardno_'+num+'" name="t_cardno['+num+']" value="" />';
              html+='</span>';
              //html+='<span class="st-ts-text"></span>';
              html+='</td>';
              html+='<td width="10%" align="center"><span class="st-delete-ico" data-type="js"></span></td>';
              html+='</tr>';
            $("#linktable").append(html);
            dynamic_event();

        });

    });

    function dynamic_event(){
        //动态添加游客姓名
        $("input[name^='t_name']").each(
            function(i,obj) {

                $(obj).rules("remove");
                $(obj).rules("add", {required: true,
                messages: {
                    required: "{__('请输入姓名')}"

                } });
            }
        );
        //证件类型
        $("input[name^='t_cardno']").each(
            function(i,obj) {
                $(obj).rules("remove");
                $(obj).rules("add", {

                    required: true,
                    alnum:true,
                    isIDCard:true,
                    remote:{
                        url: SITEURL+'member/index/ajax_check_cardno',
                        type: 'post',
                        data: {
                            cardno: function() {
                                return $(obj).val();
                            },
                            cardtype:function(){
                                return $(obj).parent().find('select').val()
                            },
                            cardid:function(){
                                return $(obj).attr('data-id');
                            }
                        }
                    },
                    messages: {
                        required: "{__('请输入证件号')}",
                        remote:"{__('证件号重复')}",
                        alnum:"只能输数字英文",
                        isIDCard:"身份证不正确"

                    } });
            }
        );

        //身份证验证
        $("select[name^='t_cardtype']").each(function(i,obj){
            $('#linkmanfrm').on('change',$(obj),function(){
                var id = $(obj).attr('id').replace('t_cardtype_', '');
                if ($(obj).val() != '身份证') {
                    $('#t_cardno_' + id).rules("remove", 'isIDCard');
                }else{
                    $('#t_cardno_' + id).rules('add', { isIDCard: true, messages: {isIDCard: "身份证不正确"}});
                }
            });
            $(obj).change();
        });
    }
</script>
</body>
</html>
