(function($){
    /*
    * 思途手机端弹层插件
    * */
    $.layer = function(options) {
        var defaults = {
            type : 1, //1,信息框,2,tip框,3,confirm框,4,loading状态
            icon:1, //成功 or 失败状态
            text : '弹出提示', //展示文本
            time : -1, //自动关闭时间,默认不自动关闭
            target:'',//loading 显示容器,不传此参数则全局
            ok:function(){}, //当type 为3时,点击"确认"的回调
            cancel:function(){} //当type 为3时,点击"取消"的回调

        };
        var options = $.extend(defaults,options);
        _create_box();
        if(options.time!=-1){
            setTimeout(function(){
                _close();
            },options.time)
        }
        //创建对象
        function _create_box(){
           var html = '';
            switch(options.type){
                case 1:
                    html += '<div class="layer-content">';
                    if(options.icon == 1)
                    $cls = "layer-pass-icon";
                    else
                    $cls = "layer-error-icon";
                    html+='<div class="layer-out-txt"><i class="'+$cls+'"></i>'+options.text+'</div>';
                    html+='</div>';
                    break;
                case 2:
                    html += '<div class="layer-content">';
                    html+='<div class="layer-hint-txt">'+options.text+'</div>';
                    html+='</div>';
                    html+='</div>';
                    break;
                case 3:

                    html += '<div class="layer-content">';
                    html+='<div class="layer-confirm">';
                    html+='<div class="confirm-info">'+options.text+'</div>';
                    html+='<div class="confirm-bar">';
                    html+='<a class="cancel btn" href="javascript:;">取消</a>';
                    html+='<a class="confirm btn" href="javascript:;">确认</a>';
                    html+='</div>';
                    html+='</div>';
                    html+='</div>';
                    break;
                case 4:
                    html+='<div class="layer-mask">';
                    html+='<i class="layer-loading"></i>';
                    if(options.text && options.text!='弹出提示'){
                        html+='<span class="layer-loading-text">'+options.text+'</span>';
                    }
                    html+='</div>';
                    break;


            }
            if(options.type!=4){
                $('body').append(html);
            }else{
                if(options.target){
                    $(options.target).append(html);
                }else{
                    $('body').append(html);
                }
            }

            _bind_event();
          }
          //绑定事件
          function _bind_event(){
              if(typeof (options.cancel) == 'function'){
                  $('body').delegate('.confirm-bar .cancel','click',function(){
                      options.cancel();
                      _close();
                  })
              }
              if(typeof (options.ok) == 'function'){
                  $('body').delegate('.confirm-bar .confirm','click',function(){
                      options.ok();
                      _close();
                  })
              }

          }

          //关闭
         function _close(){
             $('.layer-content').remove();
             $('.layer-mask').remove();
         }
        //对外关闭对话框方法
        $.layer.close = function(){
             _close();
         }

    };



})(jQuery)
