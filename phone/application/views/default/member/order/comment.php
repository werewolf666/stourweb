  <div class="header_top bar-nav">
        <a class="back-link-icon" href="javascript:;" data-rel="back" data-preventdefault="check_comment"></a>
        <h1 class="page-title-bar">我要评价</h1>
    </div>
    <!-- 公用顶部 -->
    <div class="page-content">
        <form id="commentfrm">
        <div class="publish-dp">
            <div class="item-bt" style="overflow: hidden">{$info['productname']}</div>
            <div class="item-star">
                <em class="tit">评分：</em>
                    <span class="p_rate" id="p_rate">
                        <i title="1分"></i>
                        <i title="2分"></i>
                        <i title="3分"></i>
                        <i title="4分"></i>
                        <i title="5分"></i>
                    </span>
                <strong class="snum" id="snum"></strong>
                <input type="hidden" id="score" name="score" value="3"/>
            </div>
            <div class="item-edit"><textarea id="content" name="content" class="edit-textarea" placeholder="发表点评，记录您的消费体验，不少于5个字"></textarea></div>
            <div class="item-upload">
                <ul class="clearfix" id="upload_list">
                    <!--<li class="show-pic"><a href="#myCommentPic"><img src="{$tpl}/mobilPhone3.0/images/pic/03.jpg" /></a></li>-->
                    <li class="add-btn"></li>
                </ul>
                <div style="display: none" id="addfile"></div>
            </div>
        </div>
            <a class="submit-info-btn save-comment" href="javascript:;">提交</a>
            <input type="hidden" name="orderid" value="{$info['id']}">
            <input type="hidden" name="articleid" value="{$info['productautoid']}">
            <input type="hidden" name="typeid" value="{$info['typeid']}">
       </form>
    </div>

      <div class="original-show-page hide">
          <div class="original-info-bar">
              <span class="back-page goback"><a href="javascript:;" class="ico"></a><em class="page-num"><span id="current_image_index">1</span> / <span id="total_image_num">2</span></em></span>
              <a class="delete-icon" href="javascript:;"></a>
          </div>
          <div class="original-show-block">
              <div class="swiper-container">
                  <div class="swiper-wrapper" id="temp_image_list">

                  </div>

              </div>
          </div>
      </div>


  <script>
      function get_str_length(str){
          var realLength = 0, len = str.length, charCode = -1;
          for (var i = 0; i < len; i++) {
              charCode = str.charCodeAt(i);
              if (charCode >= 0 && charCode <= 128) realLength += 1;
              else realLength += 2;
          }
          return realLength;
      }

      var is_save = false;

      //点评
      var pRate = function(box,callBack){
          this.Index = null;
          var B = $("#"+box),
              rate = B.children("i"),
              me = this;
          rate.click(function(){
              $(this).addClass("select");
              $(this).prevAll().addClass("select");
              $(this).nextAll().removeClass("select");
              me.Index = $(this).index() + 1;
              if(callBack){callBack();}
          })
      };
      var Rate = new pRate("p_rate",function(){
          document.getElementById('snum').innerHTML=Rate.Index+'分'
          $('#score').val(Rate.Index);
      });



      // 初始化Web Uploader
      var uploader = WebUploader.create({
          // 选完文件后，是否自动上传。
          auto: true,

          // swf文件路径
          swf: 'http://{$GLOBALS['main_host']}'+'/res/js/webuploader/Uploader.swf',

          // 文件接收服务端。
          server: SITEURL+'member/comment/uploadfile',

          // 选择文件的按钮。可选。
          // 内部根据当前运行是创建，可能是input元素，也可能是flash.
          pick: '#addfile',

          fileVal: 'Filedata',
          // 只允许选择图片文件。
          accept: {
              title: 'Images',
              extensions: 'gif,jpg,jpeg,bmp,png',
              mimeTypes: 'image/*'
          }
      });

      uploader.on('uploadProgress',function(){
          $.layer({
              type:4
          })
      })
      uploader.on('uploadComplete',function(){
          $.layer.close();
      })

      uploader.on( 'uploadSuccess', function( file,response ) {
          if(response.success=='true') {

              var html = '<li class="show-pic" id="img_'+file.id+'"><a href="javascript:;"><img  src="'+response.litpic+'" /></a><input type="hidden" data-id="'+file.id+'" name="pic[]" value="'+response.litpic+'"/></li>';
              $(html).insertBefore(".add-btn");
          }

      });



      //上传图片
      $('.add-btn').click(function(){
          $('input[type="file"]').trigger('click');
      })

      //显示图片
      $('body').delegate('.show-pic','click',function(){
          var pic = [];
          var html = '';
          var total = 0;
            $("input[name^='pic']").each(function(i,obj){
                var imgid = $(obj).attr('data-id');
                html+='<div class="swiper-slide" data-imgid="'+imgid+'"><img src="'+$(obj).val()+'"  /></div>';
                total++;
            })
          $('#temp_image_list').html(html);
          $('#total_image_num').html(total);

          $('.original-show-page').show();

      })

      var mySwiper = new Swiper ('.swiper-container',{
          loop: true,
          onSlideChangeEnd: function(swiper){

              $('#current_image_index').html(parseInt(swiper.activeIndex)+1);
          }
      });

      //关闭图库浏览
      $('.goback').click(function(){
          $('.original-show-page').hide();
      })

      //删除图片
      $('.delete-icon').click(function(){
          if(confirm('确定删除图片?')){
              if($('.swiper-slide-active').length>0){
                  var imgid = $('.swiper-slide-active').attr('data-imgid');
                  $('.swiper-slide-active').remove();
              }else{
                  var imgid = $('.swiper-slide').first().attr('data-imgid');
                  $('.swiper-slide').first().remove();
              }
              $('#img_'+imgid).remove();
          }


      })

      //提交评论
      $('.save-comment').click(function(){
            var content = $('#content').val();
          if(get_str_length(content)<10){
              $.layer({
                  type:2,
                  text:'请留下你的评论,至少5个汉字',
                  time:1000
              })
              return false;
          }
          if(is_save==true)
          {
              $.layer({
                  type:2,
                  text:'请勿重复提交!',
                  time:1000
              })
              return false;
          }
          is_save = true;
          $.ajax({
              type:'POST',
              url:SITEURL+'member/comment/save',
              data:$('#commentfrm').serialize(),
              dataType:'json',
              success:function(data){
                  if(data.status){
                      $.layer({
                          type:1,
                          icon:1,
                          text:'点评成功',
                          time:1000
                      });
                      setTimeout(function(){
                          history.go(-1);
                      },1000)
                  }
                  else
                  {
                      $.layer({
                          type:2,
                          text:data.msg,
                          time:1000
                      })
                      is_save = false;
                  }
              }
          })
      })



      function check_comment(){

            var comment = $('#content').val();
            if(comment.length>0 && !is_save){
                $.layer({
                    type:3,
                    icon:1,
                    text:'你的点评还未提交，确认返回？',
                    ok:function(){
                        $('#content').val('');
                        history.go(-1);
                    },
                    cancel:function(){

                    }
                })
                return true;
            }else{
                return false;
            }
      }
  </script>
