<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>会员资料-{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,real-name.css'); }
    {php echo Common::getScript("choose.js"); }
</head>
<body>

<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td">

            <div class="list-top-set">
                <div class="list-web-pad"></div>
                <div class="list-web-ct">
                    <table class="list-head-tb">
                        <tr>
                            <td class="head-td-rt">
                                <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="real-name-step">
                <ul class="step-item clearfix">
                    <li class="item-child item-first step-on">
                <span class="speed">
                    <i class="num-icon">1</i>
                    <em class="txt-label">填写资料</em>
                </span>
                    </li>
                    <li class="item-child item-second step-on">
                <span class="speed">
                    <i class="num-icon">2</i>
                    <em class="txt-label">资料审核</em>
                </span>
                    </li>
                 <li id="check_li" class="item-child item-third  {if $info['verifystatus']>1}step-on {/if}">
                <span class="speed">
                    <i class="num-icon">3</i>
                    <em class="txt-label">{if $info['verifystatus']==3}审核未通过{else}审核通过{/if}</em>
                </span>
                    </li>
                </ul>
            </div>

            <div class="rn-info-box">
                <ul class="rn-info-block">
                    <li>
                        <strong class="item-hd">真实姓名：</strong>
                        <div class="item-bd">
                            <span class="item-label">{$info['truename']}</span>
                        </div>
                    </li>
                    <li>
                        <strong class="item-hd">身份证号码：</strong>
                        <div class="item-bd">
                            <span class="item-label">{$info['cardid']}</span>
                        </div>
                    </li>
                    <li>
                        <strong class="item-hd">身份证正面：</strong>
                        <div class="item-bd">
                            <div class="update-box">
                        <span class="update-before-area">
                            <img src="{$info['idcard_pic']['front_pic']}"  onclick="show_big_pic('{$info['idcard_pic']['front_pic']}')" width="198" height="123">
                        </span>
                                <span class="sm-txt">*点击查看大图，并可支持右键保存到本地</span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <strong class="item-hd">身份证背面：</strong>
                        <div class="item-bd">
                            <div class="update-box">
                        <span class="update-after-area">
                            <img src="{$info['idcard_pic']['verso_pic']}" onclick="show_big_pic('{$info['idcard_pic']['verso_pic']}')" width="198" height="123">
                        </span>
                                <span class="sm-txt">*点击查看大图，并可支持右键保存到本地</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            {if $info['verifystatus']==1}
            <div class="st-btn-block clearfix">
                <div class="pass-console-bar">
                    <a class="yes-pass"  onclick="do_check(1)" href="javascript:;">通过</a>
                    <a class="not-pass" onclick="do_check(2)" href="javascript:;">不通过</a>
                </div>
            </div>
            {else}
            <div class="st-btn-block clearfix">
                <div class="pass-console-bar">
                    <a class="yes-pass"  onclick="toShow('{$info['mid']}')" href="javascript:;">查看详情</a>
                </div>
            </div>

            {/if}


        </td>
    </tr>
</table>




</body>
</html>

<script>

    //查看
    function toShow(id)
    {
        var url=SITEURL+"member/verifystatus_list/action/show/parentkey/member/menuid/{$meunid}/itemid/1/mid/"+id;
     //   var record=window.product_store.getById(id.toString());

         parent.window.addTab('实名认证详情',url,1);
        ST.Util.removeTab(SITEURL+'member/verifystatus_list/action/check/parentkey/member/menuid/{$meunid}/itemid/1/mid/9');


    }


    function show_big_pic(pic)
    {
        var url=pic;
        ST.Util.showBox('图片查看',url,1200,800,function(){window.product_store.load()});
    }

    function do_check(status)
    {
        var url=SITEURL+"member/verifystatus_list/action/do_check/parentkey/member/menuid/{$meunid}/itemid/1/mid/{$info['mid']}";
        $.ajax({
            type:'post',
            dataType:'json',
            data:{status:status},
            url:url,
            success:function(data)
            {
                if(data.status==1)
                {
                    ST.Util.showMsg('保存成功!',4,2000);
                    $('#check_li').addClass('step-on');
                    if(data.type==2)
                    {
                        $('#check_li').find('.txt-label').text('审核通过')
                    }
                    else if(data.type==3)
                    {
                        $('#check_li').find('.txt-label').text('审核未通过')
                    }

                }
                else
                {
                    ST.Util.showMsg('身份证格式错误!',5,2000)
                }
            }
        })
    }


</script><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0312&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
