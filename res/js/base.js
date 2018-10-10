//全站顶部滚动订单信息
function AutoScroll(obj) {
	$(obj).find("ul:first").animate({marginTop: "-35px"}, 500, function(){
		$(this).css({ marginTop: "0px" }).find("li:first").appendTo(this);
	});
}

$(document).ready(function() {
	var myar = setInterval('AutoScroll(".scroll-order")', 5000)
	$(".scroll-order").hover(function(){
			clearInterval(myar)
		},function(){
			myar = setInterval('AutoScroll(".scroll-order")', 5000)
		}); //当鼠标放上去的时候，滚动停止，鼠标离开的时候滚动开始
})

$(function(){

	//顶部网站导航显示隐藏
	var topNavToggle = $('.top-login dl');
	topNavToggle.hover(function(){
		$(this).css({background:'#fff',borderLeft:'1px solid #f9f7f6',borderRight:'1px solid #f9f7f6'});
		$(this).children('dd').slideDown(100)
	},function(){
		$(this).css({background:'none',borderLeft:'1px solid #f9f7f6',borderRight:'1px solid #f9f7f6'});
		$(this).children('dd').slideUp(100)
	});
	
	//线路首页分类导航
	$('.st-dh-con').hover(function(){
		$(this).children('h3').addClass('hover').next('.st-dh-item').show()
	},function(){
		$(this).children('h3').removeClass('hover').next('.st-dh-item').hide()
	})
	
	
	
})