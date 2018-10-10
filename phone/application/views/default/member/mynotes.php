<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>我的游记{$webname}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js,template.js,delayLoading.min.js')}
    {Common::js('layer/layer.m.js')}
</head>
<body>
{request "pub/header/typeid/$typeid/isshowpage/1/definetitle/".urlencode('我的游记')}
  	<section>

  		<div class="mid_content">

            {if !empty($list)}
			<div class="travel-diary-content">
				<ul id="list_container">
                    {loop $list $row}
					<li>
						<div class="pic"><a href="{$row['url']}"><img src="{$row['litpic']}" align="{$row['title']}" /></a>{if $row['status']==0} <span class="attr-ing">正在审核</span>
                            {elseif $row['status']==1}
                               <span class="attr-pass">已经发布</span>
                            {elseif $row['status']==-1}
                                <span class="attr-ing">审核未通过</span>
                            {/if}
                        </div>
						<div class="bt"><a href="{$row['url']}">{$row['title']}</a></div>
						<div class="data"><span class="sf">{date('Y/m/d',$row['modtime'])}</span><span class="sr">{$row['shownum']}</span></div>
					</li>
                    {/loop}
				</ul>
                <div class="list_more"><a href="javascript:;" id="btn_more">查看更多</a></div>
			</div>
            {else}
            <div class="no-content">
                <img src="{$GLOBALS['cfg_public_url']}/images/nocon.png"/>
                <p>亲，您还没有发表游记哦！</p>
            </div>
            {/if}
  		</div>
  	</section>
<script>
    var pagesize="{$pagesize}";
    var sorttype="{$sorttype}";
    var current_page=1;
    $(function(){
        $("#btn_more").click(function(){
            get_data();
        });
        if($("#list_container li").length<pagesize)
        {
            $(".list_more").hide();
        }
    });

    function get_data()
    {
        layer.open({
            type: 2,
            content: '正在加载数据...',
            time :20

        });
        var url=SITEURL+'member/mynotes/ajax_get_more';
        var nextpage=current_page+1;
        var data={'page':nextpage,'sorttype':sorttype,'pagesize':pagesize};
        $.ajax({
            type: 'POST',
            url: url ,
            data: data ,
            dataType: 'json',
            success:function(result){

                var html='';
                for(var i in result['list'])
                {

                    var row=result['list'][i];

                    var statusHtml='';
                    if(row['status']==0)
                    {
                        statusHtml='<span class="attr-ing">正在审核</span>';
                    }else if(row['status']==1)
                    {
                        statusHtml='<span class="attr-pass">已经发布</span>';
                    }else if(row['status']==-1) {
                        statusHtml='<span class="attr-ing">审核未通过</span>';
                    }

                    html+='<li> <div class="pic"><a href="'+row['url']+'"><img src="'+row['litpic']+'" align="'+row['title']+'"/></a>' +
                        statusHtml+
                    '</div> <div class="bt"><a href="'+row['url']+'">'+row['title']+'</a></div> ' +
                    '<div class="data"><span class="sf">'+row['modtime']+'</span>' +
                    '<span class="sr">'+row['shownum']+'</span></div></li>';
                }
                $("#list_container").append(html);
                if(result['page']==-1)
                {
                    $(".list_more").hide();
                }
                else {
                    current_page = result['page']-1;
                }
                layer.closeAll();
            }
        });
    }
</script>
	</body>
</html>
