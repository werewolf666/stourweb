<?php defined('SYSPATH') or die('No direct script access.');

abstract class Pay_Platform
{
    /**
     * 根据不同平台显示不同的支付方式
     * @return mixed
     */
    public abstract function pay_method();

    /**
     * 信息提示
     * @param $data
     * @return string
     */
    public function status($data)
    {
        //状态模板
        $view = View::factory(dirname($this->template) . '/status');
        //提示信息
        $view->info = $data;
        //渲染模板
        return $view->render();
    }

    /**
     * 获取远程数据
     * @param $url
     * @return mixed
     */
    public function file_get_content($url) {
        $ch=  curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
}