<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>ucenter设置</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,gallery.css,base_new.css'); }
    {php echo Common::getScript('config.js');}
</head>
<body>
	<table class="content-tab" body_html=hyvz8B >
    <tr>
    <td width="119px" class="content-lt-td"  valign="top">
     {template 'stourtravel/public/leftnav'}
    <!--右侧内容区-->
    </td>
     <td valign="top" class="content-rt-td">


        <form id="configfrm">
         <div class="w-set-con">
        	<div class="w-set-tit bom-arrow"><a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a></div>
          <div class="w-set-nr">
              <div class="gallery-server" id="content">
                  <ul class="info-item-block">
                      <li>
                          <span class="item-hd">FTP图片存储开关：</span>
                          <div class="item-bd">
                              <label class="radio-label"><input type="radio" checked="checked" name="is_open" value="1" {if $config['is_open']==1}checked{/if}>开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
                              <label class="radio-label"><input type="radio" name="is_open" value="0" {if $config['is_open']!=1}checked{/if}>关闭</label>
                          </div>
                      </li>
                      <li>
                          <span class="item-hd"><span class="pr-5 c-red">*</span>FTP地址：</span>
                          <div class="item-bd">
                              <input type="text" name="address" class="input-text" value="{$config['address']}">
                          </div>
                      </li>
                      <li>
                          <span class="item-hd"><span class="pr-5 c-red">*</span>FTP端口：</span>
                          <div class="item-bd">
                              <input type="text" name="port" class="input-text is_numeric" value="{$config['port']}">
                              <span class="error-span c-red hide">*请填写数字</span>
                          </div>
                      </li>
                      <li>
                          <span class="item-hd"><span class="pr-5 c-red">*</span>FTP账号：</span>
                          <div class="item-bd">
                              <input type="text" name="username" class="input-text" value="{$config['username']}">
                          </div>
                      </li>
                      <li>
                          <span class="item-hd"><span class="pr-5 c-red">*</span>FTP密码：</span>
                          <div class="item-bd">
                              <input type="text" name="password" class="input-text" value="{$config['password']}">
                          </div>
                      </li>
                      <li>
                          <span class="item-hd"><span class="pr-5 c-red">*</span>图片域名：</span>
                          <div class="item-bd">
                              <input type="text" name="domain" class="input-text" value="{$config['domain']}">
                          </div>
                      </li>
                  </ul>
                  <div class="gallery-server-bc mt-5">
                      <a class="btn btn-primary radius size-L ml-115" href="javascript:void(0)" id="btn_save">保存</a>
                  </div>

              </div>
          </div>
        </div>
        </form>
  </td>
  </tr>
  </table>

<script>

    $(document).ready(function () {
        $('#content').find('input[type="text"]').keyup(function () {
            if ($(this).val().length < 1) {
                $(this).addClass('error-text');
            } else {
                $(this).removeClass('error-text');
            }
        });
        $('.is_numeric').blur(function(){
            var isNumber = new RegExp(/^\d+$/);
            if(!isNumber.test($(this).val().replace(/\s/g,''))){
                $(this).addClass('error-text').next().removeClass('hide');
            }else{
                $(this).removeClass('error-text').next().addClass('hide');
            }
        });

        //配置信息保存
        $("#btn_save").click(function () {
            var bool=true;
            var numberic = new RegExp(/^\d+$/);
            $('#content').find('input[type="text"]').each(function(){
                if($(this).val().length<1){
                    bool=false;
                    return false;
                }
            });
            $('.is_numeric').each(function(){
                if(!numberic.test($(this).val().replace(/\s/g,''))){
                   $(this).addClass('error-text').next().removeClass('hide');
                   bool=false;
                   return false;
                }
            });
            if(!bool){
              return;
            }
            var url = SITEURL + 'ftp/admin/ftp/ajax_save_config';
            var frmdata = $("#configfrm").serialize();
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: frmdata,
                success: function (data) {
                    if (data.status) {
                        ST.Util.showMsg('保存成功', 4);
                    } else {
                        ST.Util.showMsg('保存失败', 5);
                    }
                }
            })
        })
    });
</script>
</body>
</html>
