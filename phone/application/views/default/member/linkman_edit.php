<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$info['action']}联系人-{$GLOBALS['cfg_webname']}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js,common.js,jquery.validate.min.js,layer/layer.m.js');}
    <script>
    var SITEURL = "{$cmsurl}";
</script>
</head>

<body>

	<header>
  	<div class="header_top">
      <div class="st_back"><a href="{$cmsurl}member/linkman/"></a></div>
      <h1 class="tit">{$info['action']}联系人</h1>
    </div>
  </header>
  
  <section>
    
  	<div class="mid_content">
        <form id="form">
            <input type="hidden" name="id" value="{$info['id']}">
            <div class="linkman_page">
                <ul class="revise_lm">
                    <li>
                    <span>姓名：</span>
                    <input type="text" name="linkman" value="{$info['linkman']}" />
                   </li>
                    <li class="radio">
                        <span>性别：</span>
                        <input type="hidden" name="sex" id="sex" value="{$info['sex']}">
                        <a data-value="1" href="javascript:;" {if $info['sex']==1}class="on"{/if}>男</a>
                        <a data-value="0" href="javascript:;" {if $info['sex']==0}class="on"{/if}>女</a>
                    </li>
                    <li>
                    <span>身份证号：</span>
                    <input type="text" name="idcard" id="idcard" value="{$info['idcard']}" />
                    </li>
                    <li>
                    <span>联系电话：</span>
                    <input type="text" name="mobile" value="{$info['mobile']}" />
                    </li>
                </ul>
            </ul>
          </div><!--修改联系人-->
        <div class="error_txt" id="error_txt"></div>
        </form>
    </div>
  </section>

  <div class="bom_link_box">
  	<div class="bom_fixed">
        {if !isset($info['isadd'])}
    	<a  href="javascript:" id="delete" data="{$cmsurl}member/linkman/update?action=delete&id={$info['id']}">删除联系人</a>
        {/if}
      <a class="on cursor" id="submit">确定</a>
    </div>
  </div>
</body>
<script type="text/javascript">
    $(document).ready(function(){
        //性别
        $("body").delegate(".radio a", 'click', function () {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $("#sex").val($(this).attr('data-value'));
        });

        $('#form').validate({
            rules:{
                linkman:{
                    required:true,
                    minlength:2
                },
                idcard:{
                    required:true,
                    isIdCardNo:true
                    {if empty($info['id'])}
                    //远程验证
                        ,remote:{
                             url:SITEURL+"member/linkman/ajax_check",  
                             type:"post",  
                             dataType:"html",  
                             dataFilter: function(data, type) {  
                                  if (data == "true")  
                                      return true;  
                                  else  
                                      return false;  
                            }  
                        }  
                    {/if}
                },
                mobile:{
                    required:true,
                    mobile: true
                    {if empty($info['id'])}
                        //远程验证
                    ,remote:{
                        url:SITEURL+"member/linkman/ajax_check",
                            type:"post",
                            dataType:"html",
                            dataFilter: function(data, type) {
                            if (data == "true")
                                return true;
                            else
                                return false;
                        }
                    }
                        {/if}
                }
            },
            messages:{
                'linkman':{
                    required:'{__("error_linkman_not_empty")}',
                    minlength:'{__("error_linktel_name_min")}'
                },
                'idcard':{
                    required:'{__("error_linktel_id_not_empty")}',
                    isIdCardNo:'{__("error_linktel_id")}'
                    {if empty($info['id'])}
                        ,remote:'{__('error_linktel_id_exist')}'
                    {/if}
                    },
                'mobile':{
                    required:'{__("error_linktel_not_empty")}',
                    mobile: '{__("error_linktel_phone")}'
                    {if empty($info['id'])}
                        ,remote:'{__('error_linktel_mobile_exist')}'
                    {/if}
                }
            },
            errorElement: "em",
            errorPlacement: function(error, element) {
                var content=$('#error_txt').html();
                if(content==''){
                    $('#error_txt').html('<i></i>');
                    error.appendTo($('#error_txt'));
                }
            },
            showErrors:function(errorMap,errorList){
                if(errorList.length<1){
                    $('#error_txt').html('');
                }else{
                    this.defaultShowErrors();
                }
            },
            submitHandler:function(form){
                var data={}
                $('#form').find('input').each(function(){
                    data[$(this).attr('name')]=$(this).val();
                });
               $.post(SITEURL+'member/linkman/update?action=save',data,function(rs){
                   show_msg(rs)
                },'json');
            }
        });
       $('#submit').click(function(){
           $('#form').submit();
       });
       $('#delete').click(function(){
           $.post($(this).attr('data'),{},function(rs){
               show_msg(rs)
           },'json');
       });

       function show_msg(rs){
           if(parseInt(rs.status)<1){
               layer.open({
                   content: rs.msg,
                   time: 2
               });
           }else{
               layer.open({
                   content: rs.msg,
                   time: 2,
                   end:function(){
                       window.location.href=rs.url;
                   }
               });
           }
       }

    });

</script>
</html>
