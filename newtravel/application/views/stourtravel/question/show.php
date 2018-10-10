<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>问答查看</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
</head>

<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" >
                <div class="cfg-header-bar">
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>
                <form method="post" name="product_frm" id="product_frm">
                    <ul class="info-item-block">
                        <li>
                            <span class="item-hd">产品/标题：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['productname']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">提问人：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['nickname']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">提问时间：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['addtime']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">电话：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['phone']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">邮箱：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['email']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">QQ：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['qq']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">微信：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['weixin']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">内容：</span>
                            <div class="item-bd">
                                <span class="item-text">{$info['content']}</span>
                            </div>
                        </li>
                        <li>
                            <span class="item-hd">回复内容：</span>
                            <div class="item-bd">
                                <div>{php echo Common::getEditor('replycontent',$info['replycontent'],900,300);}</div>
                            </div>
                        </li>
                    </ul>
                    <div class="clear clearfix mt-5">
                        <input type="hidden" name="questionid" id="questionid" value="{$info['id']}"/>
                        <a class="btn btn-primary radius size-L ml-115" id="btn_save" href="javascript:;">保存</a>
                    </div>
                </form>
            </td>
        </tr>
    </table>

    <script>
        var id="{$info['id']}";
        $(document).ready(function(){

            //保存
            $("#btn_save").click(function(){

                $.ajaxform({
                    url   :  SITEURL+"question/ajax_save",
                    method  :  "POST",
                    form  : "#product_frm",
                    dataType:'json',
                    success  :  function(data)
                    {
                        if(data.status)
                        {

                            ST.Util.showMsg('保存成功!','4',2000);
                            setTimeout(function(){ST.Util.responseDialog({id:id},true)},1000);

                        }
                        else{
                            ST.Util.showMsg("{__('norightmsg')}",5,1000);
                        }
                    }
                });
            });

        });

    </script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.1.201711.0719&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
