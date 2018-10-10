<script>
    var isLoginOrder="{$GLOBALS['cfg_login_order']}";
    var isLogin=false;
    //登陆下订单
    function is_login_order(){
        var bool=true;
        var text=$('#loginstatus').text();
        var reg=/免费注册/g;
        isLogin=!reg.test(text);
        if(isLoginOrder==1 && !isLogin){
            $('#is_login_order').removeClass('hide');
            bool=false;
        }
        return bool;
    }
</script>
{if !empty($GLOBALS['cfg_login_order']) && strpos(Common::get_current_url(),'/member/')===false}
    {Common::js('jquery.validate.js,jquery.md5.js')}
    {include "member/login_fast"}
{/if}