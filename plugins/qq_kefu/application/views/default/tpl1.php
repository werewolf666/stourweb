{Common::css('/plugins/qq_kefu/public/js/layer/skin/layer.css',true,false)}
{if $conf['pos']=='right'}
{Common::css('/plugins/qq_kefu/public/kf1/css/right.css',true,false)}
{else}
{Common::css('/plugins/qq_kefu/public/kf1/css/left.css',true,false)}
{/if}
{Common::js('freekefu.js')}
{Common::js("layer/layer.js")}

<style>
    .st-kfBox{
        width:125px;
        position:absolute;
        {$conf['pos']}:{$conf['posh']};
        top:{$conf['post']};
        border-radius:5px;
        box-shadow:3px 3px 9px rgba(0,0,0,.25),-3px -3px 9px rgba(0,0,0,.25);
        background:#fff;
        z-index: 99999
    }
</style>
<script>
	$(function(){
        //获取顶部位置
        var post='{$conf['post']}';
        var postVal=0;
        if(post.indexOf('%')==-1)
        {
            postVal=parseFloat(post);
        }
        else
        {
            postVal=$(window).height()*parseFloat(post)/100;
        }


        //滚动
		$(window).scroll(function () {
            var offsetTop = $(window).scrollTop() + postVal + "px";
            $('.st-kfBox,.sm-kfBox').animate({top: offsetTop}, {duration: 500, queue: false});
          });

		//关闭客服
        $('#kf-close').click(function(){
            $('.st-kfBox').fadeOut(100,function(){
                $('.sm-kfBox').show();
            });
        });
        //展开客服
        $('.sm-showKf').click(function(){
            $('.sm-kfBox').fadeOut(100,function(){
                $('.st-kfBox').show();
            });
        });
		//返回顶部
		$('#kf-backTop,#sm-backTop').click(function(){
			$('body,html').animate({scrollTop:0},500); 
			return false; 
		}); 
		//获取焦点、失去焦点提示
		$('.num-text').focus(function(){
			$('.txtCon').show()
		})
		$('.num-text').blur(function(){
			$('.txtCon').hide()
		})

        $("#freekefu_btn").click(function(){
              Freekefu.send_freekefu(function(result){
                   layer.msg(result.msg,{time:3000})
              });
        });

	})
</script>
	<div class="st-kfBox">
		<div class="online-tit"><span>在线咨询</span><i id="kf-close"></i></div>
		<ul class="kf-menuList">
			<li>
				<div class="show-lm" onclick="window.open('http://wpa.qq.com/msgrd?v=3&uin={$group[0]['qq'][0]['qqnum']}&site=qq&menu=yes','_blank')"><i class="ico01"></i>{$group[0]['qqname']}</div>
		  	</li>
			<li>
				<div class="show-lm" onclick="window.open('http://wpa.qq.com/msgrd?v=3&uin={$group[1]['qq'][0]['qqnum']}&site=qq&menu=yes','_blank')"><i class="ico02"></i>{$group[1]['qqname']}</div>
		  	</li>
			<li>
				<div class="show-lm"><i class="ico03"></i>免费通话</div>
				<div class="show-con">
					<div class="toll-freeCall">
						<input type="text" class="num-text" id="freekefu_phone" placeholder="请输入您的电话号码" />
						<input type="button" class="call-btn" id="freekefu_btn" value="立即免费通话" />
						<div class="txtCon">输入电话号码，点击免费通话、稍后我们将与您联系，此次通话将不收取您任何费用，请注意接听。</div>
				  	</div>
				</div>
		  	</li>
            {if !empty($Glb['cfg_weixin_logo'])}
			<li>
				<div class="show-lm"><i class="ico04"></i>官方微信</div>
				<div class="show-con"><p class="wechat-pic"><img src="{$Glb['cfg_weixin_logo']}" /></p></div>
		  	</li>
            {/if}
            {if !empty($Glb['cfg_phone'])}
			<li>
				<div class="show-lm"><i class="ico05"></i>客服电话</div>
				<div class="show-con"><p class="phone-num">{$Glb['cfg_phone']}</p></div>
		  	</li>
            {/if}
		</ul>
		<div class="kf-backTop" id="kf-backTop"><i></i>返回顶部</div>
  	</div>

<div class="sm-kfBox" style="{$conf['pos']}:{$conf['posh']};top:{$conf['post']};">
    <ul class="clearfix">
        <li class="sm-showKf"><img src="{$GLOBALS['cfg_res_url']}/kf1/images/point.gif" /></li>
        <li class="sm-backTop" id="sm-backTop"></li>
    </ul>
</div>

