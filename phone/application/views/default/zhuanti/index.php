<?php defined('SYSPATH') or die('');?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('style-new.css,sys-theme.css,swiper.min.css,extend.css')}
    {Common::js('jquery.min.js,lib-flexible.js,swiper.min.js,delayLoading.min.js')}
</head>

<body>
{request "pub/header_new/definetitle/".urlencode($info['title'])}
{if !empty($info['logo'])}
<div class="theme-img-block"><img src="{$info['logo']}" alt="{$info['title']}" /></div>
{/if}
<!-- 图片切换 -->
{st:channel action="getchannel" row="100"}
{loop $data $row}
{if $row['m_typeid']< 14 && $row['m_issystem'] && in_array($row['m_typeid'],array(1,2,3,5))}
    {php $pinyin=Model_Model::all_model($row['m_typeid'],'pinyin');}
    {include 'zhuanti/column/'.$pinyin}
{/if}
{/loop}
</body>
</html>