<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title clear_body=lyvz8B >会员资料-{$coreVersion}</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css,member-info.css,base_new.css'); }
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

            <div class="cfg-header-bar">
                <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
            </div>
            <form id="frm">
            <div class="member-info-container">

                <div class="st-info-block">
                    <ul>
                        {if $action=="edit"}
                        <li>
                            <span class="item-hd">会员ID：</span>
                            <div class="item-bd">
                                <span class="member-name">{$info['mid']}</span>
                            </div>
                        </li>
                        {/if}
                        <li>
                            <span class="item-hd">登录密码：</span>
                            <div class="item-bd">
                                <a class="hy-default-btn ml-5 mt-3" id="default-pwd" href="javascript:;">使用默认密码</a>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">确认密码：</span>
                            <div class="item-bd">
                                <input type="text"      name="password" class="default-text" >
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- 资料信息 -->
            <div class="member-info-container">
                <div class="st-info-block">
                    <ul>
                        <li>
                            <span class="item-hd">会员头像：</span>
                            <div class="item-bd">
                                <span class="member-hd-img"><img src="{$info['litpic']}" width="90" height="90" /></span>

                            </div>
                        </li>
                        <li>
                            <span class="item-hd">手机号码：</span>
                            <div class="item-bd">
                                <span class="msg-item-txt">
                                    {$info['mobile']}
                                </span>

                            </div>
                        </li>
                        <li>
                            <span class="item-hd">电子邮箱：</span>
                            <div class="item-bd">
                                <span class="msg-item-txt">
                                    {$info['email']}
                                </span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">会员昵称：</span>
                            <div class="item-bd">
                                <input type="text" class="default-text" name="nickname" value="{$info['nickname']}">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">性别：</span>
                            <div class="item-bd">
                                <label class="radio-label mr-30"><input type="radio" value="男" name="sex" {if $info['sex']=='男'}checked="checked" {/if}>男</label>
                                <label class="radio-label mr-30"><input type="radio" value="女" name="sex" {if $info['sex']=='女'}checked="checked" {/if}>女</label>
                                <label class="radio-label mr-30"><input type="radio" value="保密"  name="sex" {if $info['sex']=='保密'}checked="checked" {/if}>保密</label>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">出生日期：</span>
                            <div class="item-bd">
                                <select name="bairth_year" class="drop-down wid_100" onChange="YYYYDD(this.value)">
                                    <option value="0" >选择年</option>
                                </select>
                                <select name="bairth_month" class="drop-down wid_100" onChange="MMDD(this.value,this)">
                                    <option value="0">选择月</option>
                                </select>
                                <select name="bairth_day" class="drop-down wid_100">
                                    <option value="0">选择日</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">星座：</span>
                            <div class="item-bd">
                                <select name="constellation" class="drop-down wid_100">
                                    <option value="请选择">请选择</option>
                                    <option value="水瓶座">水瓶座</option>
                                    <option value="双鱼座">双鱼座</option>
                                    <option value="白羊座">白羊座</option>
                                    <option value="金牛座">金牛座</option>
                                    <option value="双子座">双子座</option>
                                    <option value="巨蟹座">巨蟹座</option>
                                    <option value="狮子座">狮子座</option>
                                    <option value="处女座">处女座</option>
                                    <option value="天秤座">天秤座</option>
                                    <option value="天蝎座">天蝎座</option>
                                    <option value="射手座">射手座</option>
                                    <option value="魔羯座">魔羯座</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">籍贯：</span>
                            <div class="item-bd">
                                <input type="text" class="default-text" name="native_place" value="{$info['native_place']}">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">QQ：</span>
                            <div class="item-bd">
                                <input type="text" name="qq" value="{$info['qq']}" class="default-text">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">微信号：</span>
                            <div class="item-bd">
                                <input type="text" class="default-text" name="wechat" value="{$info['wechat']}">
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">个性签名：</span>
                            <div class="item-bd">
                                <textarea name="signature"  class="default-textarea">{$info['signature']}</textarea>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
                <input type="hidden" value="{$action}" name="action">
                <input type="hidden" value="{$info['mid']}" name="mid">
            </form>
            <!-- 资料信息 -->

            {if $action="edit"}
            <div class="member-info-container">
                <div class="st-info-block">
                    <ul>
                        <li>
                            <span class="item-hd">会员等级：</span>
                            <div class="item-bd">
                                <span class="member-level">{$info['grade']}</span>
                            </div>
                        </li>
                        <!--
                        <li>
                            <span class="item-hd">会员状态：</span>
                            <div class="item-bd">
                                <span class="msg-item-txt">正常</span>
                                <a class="hy-default-btn ml-5 mt-3" href="#">冻结</a>
                            </div>
                        </li>-->
                        <li>
                            <span class="item-hd">实名状态：</span>
                            <div class="item-bd">
                                <span class="msg-item-txt">
                                    {if $info['verifystatus']==0}未认证
                                    {elseif $info['verifystatus']==1}待审核
                                    {elseif $info['verifystatus']==2}已实名
                                    {elseif $info['verifystatus']==3}认证失败{/if}</span>
                                {if $info['verifystatus']>0}
                                <a class="msg-item-link" href="javascript:;" onclick="toShowVerify('{$info['mid']}')">查看实名信息&gt;</a>
                                {/if}
                            </div>
                        </li>
                        {if $info['is_guide']}
                        <li>
                            <span class="item-hd">是否导游：</span>
                            <div class="item-bd">
                                {if $info['is_guide']==1}
                                <span class="msg-item-txt">是</span>
                                <a class="msg-item-link" href="javascript:;" onclick="toShowGuide('{$info['guide_id']}')">查看导游信息&gt;</a>
                                {elseif $info['is_guide']==2}
                                <span class="msg-item-txt">否</span>
                                {/if}
                            </div>
                        </li>
                        {/if}
                        <li>
                            <span class="item-hd">最近登录：</span>
                            <div class="item-bd">
                                <span class="msg-item-txt">{date('Y-m-d H:i:s',$info['logintime'])}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            {/if}
            <!-- 资料信息 -->
            <div class="st-btn-block clearfix">
                <a class="btn btn-primary radius size-L ml-115" id="save_btn" href="javascript:;">保存</a>
            </div>

        </td>
    </tr>
</table>




</body>
</html>

<script>

    //查看实名信息
    function toShowVerify(id)
    {
        var url=SITEURL+"member/verifystatus_list/action/show/parentkey/member/menuid/{$meunid}/itemid/1/mid/"+id;
       // var record=window.product_store.getById(id.toString());
        parent.window.addTab('实名认证详情',url,1);
    }

    //查看导游信息
    function toShowGuide(id)
    {
        var url=SITEURL+"guide/admin/guide/info/id/"+id+"/menuid/{$guide_menuid}/";
        parent.window.addTab('实名认证详情',url,1);
    }
    $(function()
    {

        YYYYMMDDstart();
        var year =  '{$info['birth_date'][0]}';
        var month =  '{$info['birth_date'][1]}';
        var day =  '{$info['birth_date'][2]}';
        if(year)
        {
            $('select[name=bairth_year]').val(year);
        }
        if(month)
        {
            month = parseInt(month);
            var n = MonHead[month-1];
            if (month==2&&IsPinYear(year)==true)
            {
                n++;
            }
            writeDay(n);
            $('select[name=bairth_month]').val(month);
        }
        if(day)
        {
            day = parseInt(day)
            $('select[name=bairth_day]').val(day);
        }

        var constellation = '{$info['constellation']}';
        $('select[name=constellation]').val(constellation);//星座选中
        //密码重置,8位随机密码
        $('#default-pwd').click(function()
        {
            var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
            var maxPos = chars.length;
            var pwd = '';
            for (i = 0; i < 8; i++) {
                pwd += chars.charAt(Math.floor(Math.random() * maxPos));
            }
            $('input[name=password]').val(pwd)
        })

        //提交
        $('#save_btn').click(function()
        {
            var pwd =  $('input[name=password]').val();
            if(pwd)
            {
                ST.Util.confirmBox("提示","确定要重置密码？",function(){
                    submit_frm();
                })
            }
            else
            {
                submit_frm();
            }

        })
    });


    //提交
    function submit_frm()
    {
        $.ajax({
            type:'post',
            dataType:'json',
            url:SITEURL+'member/ajax_save',
            data:$('#frm').serialize(),
            success:function(data)
            {
                if(data.status)
                {
                    ST.Util.showMsg('保存成功!','4',2000);
                }
                else
                {
                    ST.Util.showMsg('保存失败!','5',2000);
                }

            }

        })

    }

    function YYYYMMDDstart()
    {
        MonHead = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        //先给年下拉框赋内容
        var y = new Date().getFullYear();
        for (var i = (y - 100); i <= y ; i++) //以今年为准，前100年
        {
            $('select[name=bairth_year]').append(new Option(" " + i + " 年", i));
        }
        //赋月份的下拉框
        for (var i = 1; i < 13; i++)
        {
            $('select[name=bairth_month]').append(new Option(" " + i + " 月", i));
        }

    }
    if (document.attachEvent)
    {
        window.attachEvent("onload", YYYYMMDDstart);
    }
    else
    {
        window.addEventListener('load', YYYYMMDDstart, false);
    }

    function YYYYDD(year) //年发生变化时日期发生变化(主要是判断闰平年)
    {
        $('select[name=bairth_month]').attr('year',year);
        $('select[name=bairth_month]').val(0);
        $('select[name=bairth_day]').val(0);
    }
    function MMDD(month,obj) //月发生变化时日期联动
    {
        $('select[name=bairth_day]').val(0);
        var n = MonHead[month-1];
        var year = $(obj).attr('year');
        if (month==2&&IsPinYear(year)==true)
        {
            n++;
        }
        writeDay(n);
    }
    function writeDay(n) //据条件写日期的下拉框
    {
        var html = '<option value="0">请选择日</option>';
        for (var i = 1; i < (n + 1); i++)
        {
            html +='<option value="'+i+'">'+i+'日</option>'
        }
        $('select[name=bairth_day]').html(html);
    }

    function IsPinYear(year) //判断是否闰平年
    {
        return (0 == year % 4 && (year % 100 != 0 || year % 400 == 0));
    }



</script>