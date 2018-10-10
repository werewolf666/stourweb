<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}短信平台</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,plist.css,sms_sms.css'); }
    {php echo Common::getScript('common.js,config.js,DatePicker/WdatePicker.js');}
</head>

<body>
<table class="content-tab">
<tr>
<td width="119px" class="content-lt-td"  valign="top">
    {template 'stourtravel/public/leftnav'}
    <!--右侧内容区-->
</td>
<td valign="top" class="content-rt-td">
<!--面包屑-->
    <div class="list-top-set">
        <div class="list-web-pad"></div>
        <div class="list-web-ct">
            <table class="list-head-tb">
                <tbody><tr>
                    <td class="head-td-lt">

                    </td>
                    <td class="head-td-rt">
                        <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>

                </tr>
                </tbody></table>
        </div>
    </div>
    <div class="manage-nr">

        <div class="sms-set">
            <div class="msg-switcher">
            {template 'stourtravel/noticemanager/sms/'.$file.'_notice'}
            </div>

        </div>
    </div>
</td>
</tr>
</table>
<script>
   var typeid = "{$condition}";
   $(document).ready(function(){


         $('.set-one .short-cut').click(function(){
                 var ele=$(this).parents('.set-one:first').find('.box-con textarea');
                 var value=$(this).attr('data');
                 ST.Util.insertContent(value,ele);

         })

       $(".msg-bar span").click(function(){
               var index=$(".msg-bar span").index(this);
                $(".msg-bar span.on").removeClass('on');
               $(this).addClass('on');
               $(".msg-switcher .info-one").hide();
               $(".msg-switcher .info-one:eq("+index+")").show();
       });

       if(typeid)
       {
           var index=-1;
           $('#msg-bar').find('span').each(function(i){
               var _if=$(this).attr('data-if');
               var _condition=$(this).attr('data-condition');
               if(_if==_condition){
                   index=i;
                   $(this).trigger('click');
               }
           });
           if(index==-1){
               $("#tongyong").trigger('click');
           }
       }
       else
       {
           $(".msg-bar span:first").trigger('click');
       }

   })
</script>


</body>
</html>
