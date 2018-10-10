<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css'); }
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
                    <div class="w-set-tit bom-arrow" id="nav">
                        <a href="javascript:;" class="refresh-btn mt-3" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>
                        <div class="product-add-div" >
                            <div class="add-class">
                            <dl>
                                <dt>提现方式：</dt>
                                <dd>
                                        {$info['way_name']}
                                 </dd>
                            </dl>
                            <dl>
                                <dt>提现账号：</dt>
                                <dd>
                                   {$info['account']}
                                </dd>
                            </dl>
                            <dl>
                                <dt>用户昵称：</dt>
                                <dd>
                                   {$info['nickname']}
                                </dd>
                            </dl>
                                <dl class="list_dl">
                                    <dt class="wid_90">提现金额：</dt>
                                    <dd>
                                        {$info['withdrawamount']}
                                    </dd>
                                </dl>
                             {if $info['way']=='bank'}
                            <dl class="list_dl">
                                <dt class="wid_90">开户行名称：</dt>
                                <dd>
                                   {$info['bankname']}
                                </dd>
                            </dl>
                                {/if}
                            <dl class="list_dl">
                                <dt class="wid_90">帐户姓名：</dt>
                                <dd>
                                    {$info['bankaccountname']}
                                </dd>
                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">{$info['way_name']}账号：</dt>
                                <dd>
                                    {$info['bankcardnumber']}
                                </dd>
                            </dl>

                            <dl class="list_dl">
                                <dt class="wid_90">申请说明：</dt>
                                <dd>{$info['description']}</dd>
                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">申请时间：</dt>
                                <dd>{date('Y-m-d H:i:s',$info['addtime'])}</dd>
                            </dl>

                            <dl class="list_dl">
                                <dt class="wid_90">申请状态：</dt>
                                <dd>
                                    <span style="color:#999"><input type="radio" name="status" value="0" disabled="disabled" {if $info['status']==0}checked="checked"{/if} />申请中</span>
                                    <span class="ml-10 " style="{if $info['status']!=0}color:#999{/if}"><input type="radio" name="status" value="1" {if $info['status']!=0}disabled="disabled"{/if}  {if $info['status']==1}checked="checked"{/if}/>已完成</span>
                                    <span class="ml-10" style="{if $info['status']!=0}color:#999{/if}"><input type="radio" name="status" value="2" {if $info['status']!=0}disabled="disabled"{/if}  {if $info['status']==2}checked="checked"{/if}/>未通过</span>
                                </dd>
                            </dl>

                                <dl>
                                 <dt class="wid_90">审核说明：</dt>
                                 <dd>
                                     {if $info['status']==0}
                                     <textarea name="audit_description" class="set-area wid_695">{$info['audit_description']}</textarea>
                                     {else}
                                       {$info['audit_description']}
                                     {/if}
                                 </dd>
                                </dl>

                        </div>
                        <div class="opn-btn">
                            <input type="hidden" id="id" name="id" value="{$info['id']}">
                            <a class="normal-btn" id="btn_save" href="javascript:;">保存</a>
                        </div>

            </div>
            </form>
        </td>
    </tr>
</table>


<script language="JavaScript">
var old_status="{$info['status']}";
 $(function(){

     $("#btn_save").click(function(){
         var cur_status=$("input[name=status]:checked").val();
         if(cur_status!=old_status)
         {
             ST.Util.confirmBox("提示", "审核状态有改动，确定保存？", function () {
                 $.ajaxform({
                     url   :  SITEURL+"member/ajax_withdraw_save",
                     method  :  "POST",
                     form  : "#frm",
                     dataType:'json',
                     success  :  function(data)
                     {
                         if(data.status)
                         {
                             ST.Util.showMsg(data.msg,'4',2000);
                         }
                         else
                         {
                             ST.Util.showMsg(data.msg,'5',2000);
                         }

                     }});
             })
         }



     });


 })





</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=6.0.201706.2603&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
