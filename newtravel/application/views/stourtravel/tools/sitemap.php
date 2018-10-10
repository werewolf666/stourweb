<!doctype html>
<html>
<head float_size=zSlDTk >
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
                    <h5 class="c-primary">XML格式sitemap</h5>
                    <div class="mt-10">
                        <span class="item-text">生成状态:<span class="gentxt">{$xmlinfo['text']}</span></span>
                        <span class="item-text ml-20">生成日期:<span class="gentxt">{$xmlinfo['time']}</span></span>
                        <span class="item-text ml-20">生成数量:<span class="gentxt">{$xmlinfo['number']}</span></span>
                    </div>
                    <div class="mt-5">
                        <a class="btn btn-primary radius" onclick="genXmlMap()">生成XML{Common::get_help_icon('sitemap_index_genxml')}</a>
                        <a class="btn btn-primary radius ml-5" id="xml_sitemap_btn" target="_blank" href="javascript:;" data-ok="{$isxml}" data-url="{$GLOBALS['cfg_basehost']}/Sitemap.xml">查看SiteMap{Common::get_help_icon('sitemap_index_loopup_xml')}</a>
                        <a class="btn btn-primary radius ml-5" id="xmlbtn" href="javascript:;" data-value="Sitemap.xml">复制链接地址</a>
                    </div>
                    <div class="item-section mt-10 c-999">建议:创建、提交并更新站点地图有助于确保搜索引擎了解您网站上的重要网页和信息。</div>
                </div>
                <div class="line"></div>
                <div class="clear pd-20">
                    <h5 class="c-primary">HTML格式sitemap</h5>
                    <div class="mt-10">
                        <span class="item-text">生成状态:<span class="gentxt">{$htmlinfo['text']}</span></span>
                        <span class="item-text ml-20">生成日期:<span class="gentxt">{$htmlinfo['time']}</span></span>
                        <span class="item-text ml-20">生成数量:<span class="gentxt">{$htmlinfo['number']}</span></span>
                    </div>
                    <div class="mt-5">
                        <a class="btn btn-primary radius" onclick="genHtmlMap()">生成HTML{Common::get_help_icon('sitemap_index_genhtml')}</a>
                        <a class="btn btn-primary radius ml-5" target="_blank" id="html_sitemap_btn"  href="javascript:;" data-ok="{$ishtml}" data-url="{$GLOBALS['cfg_basehost']}/Sitemap.html">查看地图{Common::get_help_icon('sitemap_index_loopup_html')}</a>
                        <a class="btn btn-primary radius ml-5" id="htmlbtn" href="javascript:;" data-value="Sitemap.html">复制链接地址</a>
                    </div>
                    <div class="item-section mt-10 c-999">建议:创建、提交并更新站点地图有助于确保搜索引擎了解您网站上的重要网页和信息。</div>
                </div>

            </td>
        </tr>
    </table>



<script language="JavaScript">
    $("#xml_sitemap_btn,#html_sitemap_btn").click(function(){
        var jqueryObj=$(this);
        var isok=jqueryObj.attr('data-ok');
        var url=jqueryObj.attr('data-url');
        if(isok==0)
        {
            ST.Util.showMsg('请先生成',4,1000);
            return;
        }
        window.open(url,'_blank');
    });
    $(function(){
        initObj('xmlbtn');
        initObj('htmlbtn');
        initObj('errbtn');
    });

    ZeroClipboard.setMoviePath(SITEURL+'public/js/ZeroClipboard.swf');

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

                    if(data.status=='1')
                    {
                        ST.Util.hideMsgBox();
                        ST.Util.showMsg('生成成功',4,2000);
                        $("#xml_sitemap_btn").attr("data-ok",1);


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

                    if(data.status=='1')
                    {
                        ST.Util.hideMsgBox();
                        ST.Util.showMsg('生成成功',4,2000);
                        $("#html_sitemap_btn").attr("data-ok",1);

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
</html>