<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS{$coreVersion}</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getCss('style.css,base.css,base_new.css'); }
    {php echo Common::getScript('ZeroClipboard.js');}

</head>
<body>

    <table class="content-tab">
        <tr>
            <td width="119px" class="content-lt-td" valign="top">
                {template 'stourtravel/public/leftnav'}
                <!--右侧内容区-->
            </td>
            <td valign="top" class="content-rt-td" style="overflow:hidden">
            	
                <div class="cfg-header-bar">
                    <a href="javascript:;" class="fr btn btn-primary radius mt-6 mr-10" onclick="window.location.reload()">刷新</a>
                </div>

                <div class="clear pd-20">
                    <h5 class="c-primary">404死链地图{Common::get_help_icon('sitemap_errorlink')}</h5>
                    <div class="clear mt-10">
                        <span class="item-text">生成状态:<span class="gentxt">{$errinfo['text']}</span></span>
                        <span class="item-text ml-20">生成日期:<span class="gentxt">{$errinfo['time']}</span></span>
                        <span class="item-text ml-20">生成数量:<span class="gentxt">{$errinfo['number']}</span></span>
                    </div>
                    <div class="mt-5">
                        <a class="btn btn-primary radius" onclick="gen404Map()">生成死链</a>
                        <a class="btn btn-primary radius ml-5" target="_blank" href="{$cfg_basehost}/404Sitemap.txt">查看死链</a>
                        <a class="btn btn-primary radius ml-5" id="errbtn" href="javascript:;" data-value="404Sitemap.txt">复制链接地址</a>
                    </div>
                    <div class="item-section c-999 mt-10">建议:建议复制这些地址提交到百度站长工具的 死链工具 可以达到阻止百度抓取这些错误信息的作用，提高在百度中的排名。</div>
                </div>
                <div class="line"></div>
            </td>
        </tr>
    </table>



<script language="JavaScript">
    ZeroClipboard.setMoviePath(SITEURL+'public/js/ZeroClipboard.swf');

    $(function(){

        initObj('errbtn');


    })

    function initObj(id)
    {
        var url = "{$GLOBALS['cfg_basehost']}";
        var v =url +'/'+ $('#'+id).attr('data-value');

        var clip = new ZeroClipboard.Client(); // 新建一个对象
        clip.setHandCursor( true );
        clip.setText(v); // 设置要复制的文本。
        clip.addEventListener( "mouseUp", function(client) {
            ST.Util.showMsg('复制成功',4,1000);
        });
        clip.glue(id);


    }
    function copyToClipBoardtxt(linkurl)
    {
        var clip = new ZeroClipboard.Client(); // 新建一个对象
        clip.setHandCursor( true );
        clip.setText('Sitemap.xml'); // 设置要复制的文本。
        clip.addEventListener( "mouseUp", function(client) {
            alert("复制成功！");
        });
        clip.glue("clip_button");



    }

    //生成Xml地图
    function genXmlMap()
    {


        $.ajax(
            {
                type: "post",
                dataType:"json",
                url: SITEURL+"sitemap/ajax_xmlmap",
                beforeSend: function(){
                    ST.Util.showMsg('网站xml地图正在生成中,请稍后...',6,20000);
                },
                success: function(data)
                {
                    console.log(data.status);
                    if(data.status=='1')
                    {
                        ST.Util.hideMsgBox();
                        ST.Util.showMsg('生成成功',4,2000);

                    }
                }

            }
        );



    }
    //生成html地图
    function genHtmlMap()
    {


        $.ajax(
            {
                type: "post",
                dataType:"json",
                url: SITEURL+"sitemap/ajax_htmlmap",
                beforeSend: function(){
                    ST.Util.showMsg('网站html地图正在生成中,请稍后...',6,20000);
                },
                success: function(data)
                {
                    console.log(data.status);
                    if(data.status=='1')
                    {
                        ST.Util.hideMsgBox();
                        ST.Util.showMsg('生成成功',4,2000);

                    }
                }

            }
        );



    }
    //生成404地图
    function gen404Map()
    {


        $.ajax(
            {
                type: "post",
                dataType:"json",
                url: SITEURL+"sitemap/ajax_404map",
                beforeSend: function(){
                    ST.Util.showMsg('死链地图正在生成中,请稍后...',6,2000000);
                },
                success: function(data)
                {

                    if(data.status=='1')
                    {
                        ST.Util.hideMsgBox();
                        ST.Util.showMsg('生成成功',4,2000);

                    }
                }

            }
        );



    }

</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201711.0102&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
