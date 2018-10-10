<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>点评</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
 {Common::css('amazeui.css,style.css')}
 {Common::js('jquery.min.js,amazeui.js,template.js')}
</head>
<body>

  {request "pub/header/typeid/$typeid/isplpage/1"}
  <section>
    
  	<div class="mid_content">
     
		<div class="dp_top_data">
      	<span class="dp_xz_pic"></span>
        <span><b>{$info['score']}%</b>满意度</span>
        <span><b>{$info['commentnum']}</b>人点评</span>
      </div><!--满意数据-->
      
      <div class="dp_con_box">
      	<h3 class="tit">用户点评</h3>
        <ul class="dp_list" id="dp_list">


        </ul>
      </div><!--点评列表-->

    </div>
    
  </section>
  <input type="hidden" id="articleid" value="{$info['id']}"/>
  <input type="hidden" id="typeid" value="{$typeid}"/>
  <input type="hidden" id="page" value="1"/>
  <script type="text/html" id="tpl_comment">
      {{each list as value i}}
      <li>
          <dl>
              <dt>
                  <span class="name"><img src="{{value.litpic}}" />{{value.nickname}}</span>
                  <span class="myd">满意度：<b>{{value.score}}</b></span>
              </dt>
              <dd>{{value.content}}</dd>
          </dl>
      </li>
      {{/each}}
  </script>
 {request "pub/footer"}
 <script>
     $(function(){

         get_pinlun();
         //滚动加载内容
         $(window).scroll(function(){
             // 当滚动到最底部以上100像素时， 加载新内容
             if ($(document).height() - $(this).scrollTop() - $(this).height()<100){
                 get_pinlun();
             }
         });
     })

     function get_pinlun()
     {
         var articleid = $("#articleid").val();
         var typeid = $("#typeid").val();
         var page = Number($("#page").val());
         var url = SITEURL+'pub/ajax_comment'
         $.getJSON(url,{articleid:articleid,typeid:typeid,page:page},function(data){
             if(data){
                 var html = template("tpl_comment",data);
                 $("#dp_list").append(html);
                 $("#page").val(page+1)
             }
         })
     }
 </script>
</body>
</html>
