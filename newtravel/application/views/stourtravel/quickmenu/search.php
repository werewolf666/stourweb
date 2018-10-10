<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>菜单搜索-思途CMS{$coreVersion}</title>
 {template 'stourtravel/public/public_js'}
 {php echo Common::getCss('base.css,home.css'); }

</head>
<body>
<div class="cms-main-box">
	<!--左侧内容-->
	<div class="cms-content-box">
		<!-- 功能搜索 -->
		<div class="search-block">
			<div class="search-box">
				<input type="text" id="keyword" class="search-text" value="{$keyword}" />
				<input type="button" id="submit" class="search-btn" />
			</div>
			<div class="search-tit">使用<span class="tag" id="keyword_view">{$keyword}</span>关键词，共查询到<span class="num" id="page_total"></span>个相关结果，若查询其他功能，请在上方再次搜索。</div>
			<div class="search-list">
				<ul id="search_content">

				</ul>
			</div>
			<div class="pm-btm-box">
				<div class="pm-btm-msg" id="page">
				</div>
			</div>
		</div>

	</div>

	<!--右侧内容-->
	<div class="cms-sidle-box">
		<!-- 应用商城 -->
		<div class="sidle-module">
			<div class="sidle-tit"><s class="bgs11"></s>应用商城<a class="more" href="{$app['more_url']}" target="_blank">更多&gt;</a></div>
			<div class="mall-product">
				<ul>
                    {loop $app['list'] $v}
                    <li>
                        <a class="info" href="{$v['url']}" target="_blank">
                            <img src="{$v['litpic']}" alt="{$v['title']}" width="158" height="96" />
                            <p>{$v['title']}</p>
                        </a>
                    </li>
                    {/loop}
				</ul>
			</div>
		</div>

		<!-- 模板商城 -->
		<div class="sidle-module">
			<div class="sidle-tit"><s class="bgs12"></s>模板商城<a class="more" href="{$template['more_url']}" target="_blank">更多&gt;</a></div>
			<div class="mall-product">
				<ul>
                    {loop $template['list'] $v}
                    <li>
                        <a class="info" href="{$v['url']}" target="_blank">
                            <img src="{$v['litpic']}" alt="{$v['title']}" width="158" height="96" />
                            <p>{$v['title']}</p>
                        </a>
                    </li>
                    {/loop}
				</ul>
			</div>
		</div>

		<!-- 营销推广 -->
		<div class="sidle-module">
			<div class="sidle-tit"><s class="bgs13"></s>营销推广<a class="more" href="{$stourzx['more_url']}" target="_blank">更多&gt;</a></div>
			<div class="mall-product">
				<ul>
                    {loop $stourzx['list'] $v}
					<li>
						<a class="info" href="{$v['url']}" target="_blank">
							<img src="{$v['litpic']}" alt="{$v['title']}" width="158" height="96" />
							<p>{$v['title']}</p>
						</a>
					</li>
                    {/loop}
				</ul>
			</div>
		</div>

	</div>

</div>
<script>
	$(function () {
		//获取宽度
		function setDivAttr() {
			//var cmsMainHeight = $(window).height();
			var cmsMainWidth = $(window).width() - 450;
			//$(".cms-main-box").height(cmsMainHeight);
			$(".cms-content-box").width(cmsMainWidth);
		}

		setDivAttr();

		//窗口改变重新获取宽度
		$(window).resize(function () {
			setDivAttr();
			var cmsMainWidth = $(window).width() - 450;
			$(".cms-content-box").width(cmsMainWidth - 20);
		});

		//滚动公告
		function marginTop() {
			$(".scorll-list > ul").animate({
				marginTop: "-32px"
			}, 500, function () {
				$(this).css("marginTop", "0").find("li:first").appendTo(this);
			});
		}
		setInterval(marginTop, 5000);
        //ajax
        loadData(1)
        function loadData(page){
            var _keyword=$('#keyword').val();
            $.post(SITEURL +'quickmenu/ajax_search',{keyword:_keyword,page:page},function(data){
                var html='';
                for (var i in data['list']){
                    html+='<li>';
                    html+='<a onclick="ST.Util.addTab(\''+data['list'][i]['title']+'\',\''+data['list'][i]['url']+'\');" href="javascript:;" target="_blank">'+data['list'][i]['searchtitle']+'</a><p>'+data['list'][i]['path']+'</p>';
                    html+='</li>';
                }
                $('#search_content').html(html);
                var pageHtml = ST.Util.page(data.page.size, data.page.current, data.page.total, 10);
                $("#page").html(pageHtml);
                $('#page_total').text(data.page.total);
            },'json');
        }
        $('#submit').click(function(){
                var _keyword=($('#keyword').val()).trim();
                if(_keyword.length>0){
                    $('#keyword_view').text(_keyword);
                    loadData(1);
                }else{
                    ST.Util.showMsg('请输入您的搜索关键词',5,1000)
                }
        });
        //分页点击
        $('#page a').live('click',function(){
            var page=$(this).attr('page');
            loadData(page);
        });
	})
</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=stourwebcms&Version=7.0.201710.2614&DomainName=&ServerIP=unknown&SerialNumber=50551355" ></script>
