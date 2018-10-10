<?php defined('SYSPATH') or die('No direct script access.');

return array(

    'First' => '首页',
    'Previous' => '上一页',
    'Next' => '下一页',
    'Last' => '末页',
    'OK' => '确定',
    'norightmsg' => '你没有权限进行此项操作，请联系管理员！',
    'onlysys' => '只有系统管理员能进行权限设置,请联系管理员!',
    //error

    'error_login' => '登录失败',
    'error_find_pwd' => '已过有效验证时间，请重新找回密码',
    'error_404' => '页面不存在',
    'success_logout' => '退出成功',
    /*++++++++++++++++++++++++++++++++++*/
    //验证码
    'error_code_not_empty' => '验证码不能为空',
    'error_msg_not_empty' => '短信动态码不能为空',
    'error_code' => '验证码错误',
    'error_msgcode' => '短信动态码错误',
    'error_frmcode' => '安全检验码错误',
    'error_code_Length' => '验证码位数不正确',
    //账号
    'error_user_not_empty' => '账号不能为空',
    'error_pwd_not_empty' => '密码不能为空',
    'error_pwd_min_length' => '密码长度不能低于6个字符',
    'error_user_noexists' => '账号不存在',
    'error_user_exists' => '账号已存在',
    'error_user_phone' => '手机号不合法',
    'error_user_email' => '邮箱格式不正确',
    'success_login' => '登录成功',
    'error_login' => '登录失败:账号或密码不正确',
    'success_member_insert' => '注册成功',
    'error_member_insert' => '注册失败',
    'error_member_nickname_not_empty' => '昵称不能为空',

    //酒店提示
    'error_linkman_not_empty' => '联系人姓名不能为空',
    'error_linktel_not_empty' => '联系人手机不能为空',
    'error_linktel_phone' => '手机格式不正确',
    'error_no_product' => '当前产品不可预订',

    //结伴提示
    'success_jieban_join_insert' => '加入结伴成功',
    'error_jieban_join_insert' => '加入结伴失败',
    'success_jieban_add_insert' => '我要结伴发布成功',
    'error_jieban_add_insert' => '我要结伴发布失败',
    'error_repeat_join' => '您已经加入该结伴，不用重复加入',
    //结伴验证
    'error_adultnum_digit' => '成人数错误',
    'error_childnum_digit' => '儿童数错误',
    'error_kindlist_not_empty' => '您准备去什么地方旅游不能为空',
    'error_dest_mainid_not_empty' => '您准备去什么地方旅游不能为空',
    'error_userdest_not_empty' => '要去这个区域的目的地不能为空',
    'error_title_not_empty' => '活动标题不能为空',
    'error_startdate_not_empty' => '出发日期不能为空',
    'error_vartime_digit' => '出发日期可以早或晚多少天格式错误',
    'error_day_digit' => '出行天数格式错误',
    'error_usertheme_not_empty' => '您的行程需求不能为空',
    'error_date' => '日期格式错误',

    //私人定制
    'success_customize_add_insert' => '私人定制成功',
    'error_customize_add_insert' => '私人定制失败',
    //私人定制验证
    'error_customize_dest_not_empty' => '目的地不能为空',
    'error_customize_startplace_not_empty' => '出发地点不能为空',
    'error_customize_starttime_not_empty' => '出发时间不能为空',
    'error_customize_days_digit' => '出行时长错误',
    'error_customize_planerank_not_empty' => '交通方式不能为空',
    'error_customize_hotelrank_not_empty' => '酒店星级不能为空',
    'error_customize_room_not_empty' => '需要房型不能为空',
    'error_customize_food_not_empty' => '用餐形式不能为空',
    'error_customize_contactname_not_empty' => '您的称呼不能为空',
    'error_customize_sex_not_empty' => '性别不能为空',
    'error_customize_address_not_empty' => '所在地点不能为空',
    'error_customize_contacttime_not_empty' => '合适的联系时间不能为空',

    //签证
    'error_dingnum_regex' => '订单数量必须大于1',

 
	   //关键词为空
    'error_keyword_not_empty'=>'关键词不能为空',
    'error_no_storage'=>'该产品余量不足,请检查预订数量',

    //评论
    'success_comment' => '评论成功',
    'error_comment' => '评论失败',

    //问答
    'error_question_title_not_empty' => '问题标题不能为空',
    'error_question_content_not_empty' => '问题内容不能为空',
    'error_question_success_add' => '提问成功',
    'error_question_error_add' => '提问失败',

    //通用
    'success_delete' => '删除成功',
    'success_add' => '添加成功',
    'success_edit' => '修改成功',
    'error_delete' => '删除失败',
    'error_add' => '添加失败',
    'error_edit' => '修改失败',
    'unlogin' => '您没有权限，请先登陆',
    'noallow' => '非法操作',
    'third_exception'=>'第三方登录异常',
    
    //常用联系人
    'error_linktel_id_not_empty'=>'身份证号不能为空',
    'error_linktel_id'=>'身份证号错误',
    'error_linktel_id_exist'=>'联系人身份证号码已存在',
    'error_linktel_mobile_exist'=>'联系电话已存在已存在',
    'error_linktel_name_min'=>'姓名长度不得低于2个字',
    'error_linktel_not_login'=>'您没有登陆，请先登陆再选择？',

);
