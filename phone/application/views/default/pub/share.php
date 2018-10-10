
{if $info['litpic']}
<img id="wx_litpic" src="{Common::img($info['litpic'],80,80)}" style="display:none" width="0" height="0">
{/if}
{if $info['title']}
<input type="hidden" id="wx_title" value="{$info['title']}"/>
{/if}
{if $info['sellpoint']}
<input type="hidden" id="wx_sellpoint" value="{$info['sellpoint']}"/>
{/if}
<script type="text/javascript">
    window.share_product_litpic = $("#wx_litpic").attr('src');
    window.share_product_title = $('#wx_title').val();
    window.share_product_sellpoint = $("#wx_sellpoint").val();
    window.share_product_summary = "{$info['summary']}";
    window.cfg_df_img = "{$GLOBALS['cfg_df_img']}";
    $(function(){
        if(typeof(wx) == 'undefined')
        {
            $.getScript('https://res.wx.qq.com/open/js/jweixin-1.2.0.js',
                function (response, status) {
                    pre_weixin_share_data();
            });
        }
        else
        {
            pre_weixin_share_data();
        }
    });

    function pre_weixin_share_data()
    {
        var typeid = "{$typeid}";
        var href = SITEURL + 'share/wxclient/ajax_share_wx_info';
        var url = location.href.split("#")[0];
        $.post(href, {typeid: typeid, url: url}, function(data){
            if(data && data.status)
            {
                weixin_share(data.data);
            }
        },'json');
    }

    function weixin_share(wx_data)
    {
        var link    = wx_data.url;
        var title   = share_product_title;
        var img_url = share_product_litpic ;
        var desc   ='';// =  share_product_title?share_product_sellpoint:$("meta[name=description]").attr('content');
        if(share_product_title)
        {
            desc = share_product_sellpoint?share_product_sellpoint:share_product_summary;
            desc = desc?desc:share_product_title;
        }
        else
        {
            desc = $("meta[name=description]").attr('content');
        }


        //  无值时调用网站基础信息
        title   = title   ? title : $("title:eq(0)").text();
        title   = title   ? title : wx_data.default.title;
        desc    = desc    ? desc : wx_data.default.desc;
        /*if( !img_url )
        {
            img_url = $('section img[original-src]:eq(0)').attr('original-src');
            if(!(img_url && img_url.length>0))
            {
                img_url = $('section img[st-src]:eq(0)').attr('st-src');
                if(!(img_url && img_url.length>0))
                {
                    img_url = $('section img[src!="/public/images/grey.gif"]:eq(0)').attr('st-src');
                }
            }
        }*/
        img_url = (img_url && img_url.length>0) ? img_url : wx_data.default.litpic;
        img_url = (img_url && img_url.length>0) ? img_url : cfg_df_img;

        if( img_url && img_url.length>0 )
        {
            img_url = img_url.indexOf("://")>-1 ? img_url : location.origin + img_url;
        }

        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: wx_data.appid, // 必填，公众号的唯一标识
            timestamp: wx_data.timestamp, // 必填，生成签名的时间戳
            nonceStr: wx_data.noncestr, // 必填，生成签名的随机串
            signature: wx_data.signature,// 必填，签名，见附录1
            jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
        wx.ready(function(){
            wx.onMenuShareTimeline({
                title: title, // 分享标题
                link: link, // 分享链接
                imgUrl: img_url, // 分享图标
                success: function () {

                },
                cancel: function () {
                },
                trigger:function(){
                }
            });
            wx.onMenuShareAppMessage({
                title: title, // 分享标题
                desc: desc, // 分享描述
                link: link, // 分享链接
                imgUrl: img_url, // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                },
                trigger:function(){
                }
            });
            wx.onMenuShareQQ({
                title: title, // 分享标题
                desc: desc, // 分享描述
                link: link, // 分享链接
                imgUrl: img_url, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareWeibo({
                title: title, // 分享标题
                desc: desc, // 分享描述
                link: link, // 分享链接
                imgUrl: img_url, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareQZone({
                title: title, // 分享标题
                desc: desc, // 分享描述
                link: link, // 分享链接
                imgUrl: img_url, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        })
        wx.error(function(res){
            console.info(res);
        });

    };
</script>
