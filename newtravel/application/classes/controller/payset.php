<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Payset extends Stourweb_Controller
{

    private $_pay_types;
    private $_certs_arr=array(
        //支付宝
        1 => array(
            'dir'   => '/payment/application/vendor/pc/alipay_cash/',
            'files' => array(
                'rsa_private_key.pem',
                'rsa_private_key_pkcs8.pem',
                'rsa_public_key.pem',
            ),
        ),
        //快钱
        2=>array('dir'=>'/payment/application/vendor/pc/bill/cert/','files'=>array('public-rsa.cer')),
        //银联
        4=>array('dir'=>'/payment/application/vendor/pc/chinabank/certs/','files'=>array('zhengshu.pfx')),
       //微信
        8=>array('dir'=>'/payment/application/vendor/pc/wxpay/cert/','files'=>array('apiclient_cert.p12', 'apiclient_cert.pem', 'apiclient_key.pem', 'rootca.pem'))
    );

    public function before()
    {
        parent::before();
        $action = $this->request->action();

        $cfg_pay_type = Model_Sysconfig::get_configs(0,'cfg_pay_type',true);
        $this->_pay_types = explode(',',$cfg_pay_type);
        $this->assign('pay_types', $this->_pay_types);
    }
    /*
     * 支付宝
     */
    public function action_alipay()
    {
        $payid = 1;
        $fields = array('cfg_alipay_account','cfg_alipay_pid','cfg_alipay_key','cfg_alipay_appid');
        $configs = Model_Sysconfig::get_configs(0,$fields);


        $cert_info = $this->_certs_arr[$payid];
        $is_uploaded = $this->is_certs_uploaded($cert_info);

        $this->assign('is_uploaded',$is_uploaded);
        $this->assign('payid',$payid);
        $this->assign('configs',$configs);
        $this->assign('displayorder',Model_Payset::get_displayorder($payid));
        $this->display('stourtravel/payset/alipay');
    }

    /*
     * 快钱
     */
    public function action_bill()
    {
        $payid = 2;
        $fields = array('cfg_bill_account','cfg_bill_key');

        $cert_info = $this->_certs_arr[$payid];
        $is_uploaded = $this->is_certs_uploaded($cert_info);

        $this->assign('is_uploaded',$is_uploaded);
        $configs = Model_Sysconfig::get_configs(0,$fields);
        $this->assign('payid',$payid);
        $this->assign('configs',$configs);
        $this->assign('displayorder',Model_Payset::get_displayorder($payid));
        $this->display('stourtravel/payset/bill');
    }
    /*
     *微信
     */
    public function action_wxpay()
    {
        $payid = 8;
        $fields = array('cfg_wxpay_appid', 'cfg_wxpay_mchid','cfg_wxpay_key','cfg_wxpay_appsecret');
        $cert_info = $this->_certs_arr[$payid];
        $is_uploaded = $this->is_certs_uploaded($cert_info);

        $configs = Model_Sysconfig::get_configs(0,$fields);
        $this->assign('is_uploaded',$is_uploaded);
        $this->assign('payid',$payid);
        $this->assign('configs',$configs);
        $this->assign('displayorder',Model_Payset::get_displayorder($payid));
        $this->display('stourtravel/payset/wxpay');
    }
    /*
     * 银联
     */
    public function action_chinabank()
    {
        $payid = 4;
        $fields = array('cfg_yinlian_new_name','cfg_yinlian_new_securitykey');
        $configs = Model_Sysconfig::get_configs(0,$fields);

        $cert_info = $this->_certs_arr[$payid];
        $is_uploaded = $this->is_certs_uploaded($cert_info);

        $this->assign('is_uploaded',$is_uploaded);
        $this->assign('payid',$payid);
        $this->assign('configs',$configs);
        $this->assign('displayorder',Model_Payset::get_displayorder($payid));
        $this->display('stourtravel/payset/chinabank');
    }
    /*
     * 贝宝
     */
    public function action_paypal()
    {
        $payid = 7;
        $fields = array('cfg_paypal_key','cfg_paypal_currency');

        $configs = Model_Sysconfig::get_configs(0,$fields);
        $this->assign('payid',$payid);
        $this->assign('configs',$configs);
        $this->assign('displayorder',Model_Payset::get_displayorder($payid));
        $this->display('stourtravel/payset/paypal');
    }
    /*
     * 汇潮
     */
    public function action_huicao()
    {
        $payid = 3;
        $fields = array('cfg_huicao_account','cfg_huicao_key');
        // $configinfo['certs'] = Model_Config::is_exists_certs($configinfo);
        $configs = Model_Sysconfig::get_configs(0,$fields);
        $this->assign('configs',$configs);
        $this->assign('payid',$payid);
        $this->assign('displayorder',Model_Payset::get_displayorder($payid));
        $this->display('stourtravel/payset/huicao');
    }

    /*
    * 线下支付
    */
    public function action_offline()
    {
        $payid = 6;
        $fields = array('cfg_pay_xianxia');

        $configs = Model_Sysconfig::get_configs(0,$fields);
        $this->assign('configs',$configs);
        $this->assign('payid',$payid);
        $this->assign('displayorder',Model_Payset::get_displayorder($payid));
        $this->display('stourtravel/payset/offline');
    }

    /*
     * 支付宝信息保存
     */
    public function action_ajax_alipay_save()
    {
        $alipay_payids=array('11','12','13','14');
        $payids = $_POST['payids'];
        $webid = $_POST['webid'];
        $webid = empty($webid)?0:$webid;

        $result_paytypes = array_diff($this->_pay_types,$alipay_payids);
        if(!empty($payids))
        {
            $result_paytypes = array_merge($result_paytypes,$payids);
            $result_paytypes[] = 1;
        }
        else
        {
            $result_paytypes = array_diff($result_paytypes,1);
        }
        $result_paytypes = array_unique(array_filter($result_paytypes));
        $cfg_pay_type = implode(',',$result_paytypes);
        $configs = array('webid'=>$webid,'cfg_pay_type'=>$cfg_pay_type);
        foreach($_POST as $k=>$v)
        {
            if(strpos($k,'cfg_')===0)
            {
                $configs[$k] = $v;
            }
        }

        Model_Sysconfig::save_config($configs);
        $displayorder = intval($_POST['displayorder']);

        //新版本开启和关闭
        foreach($alipay_payids as $v)
        {
            Model_Payset::set_displayorder($v,$displayorder);
            if(in_array($v,$payids))
            {
                Model_Payset::set_open_status($v,1);
            }
            else
            {
                Model_Payset::set_open_status($v,0);
            }

        }
        //排序存储()
        Model_Payset::set_displayorder(1,$displayorder);
        echo json_encode(array('status'=>true,'msg'=>'保存成功'));
    }

    /*
     * 通用支付信息保存
     */
    public function action_ajax_save()
    {
        $payid = $_POST['payid'];
        $isopen = $_POST['isopen'];
        $webid = $_POST['webid'];
        $webid = empty($webid)?0:$webid;

        $result_paytypes = $this->_pay_types;

        if($isopen==1)
        {
            if(!in_array($payid,$result_paytypes))
            {
                array_push($result_paytypes,$payid);
            }
            //$result_paytypes = array_merge($this->_pay_types,array($payid));
        }
        else
        {
            if(in_array($payid,$result_paytypes))
            {
                $key = array_search($payid, $result_paytypes);

                array_splice($result_paytypes, $key, 1);
            }
            //$result_paytypes = array_diff($this->_pay_types,array($payid));
        }
        $result_paytypes = array_unique(array_filter($result_paytypes));
        $cfg_pay_type = implode(',',$result_paytypes);
        $configs = array('webid'=>$webid,'cfg_pay_type'=>$cfg_pay_type);
        foreach($_POST as $k=>$v)
        {
            if(strpos($k,'cfg_')===0)
            {
                $configs[$k] = $v;
            }
        }
        Model_Sysconfig::save_config($configs);
        //新版开启和关闭
        Model_Payset::set_open_status($payid,$isopen);

        //排序
        $displayorder = intval($_POST['displayorder']);
        Model_Payset::set_displayorder($payid,$displayorder);


        echo json_encode(array('status'=>true,'msg'=>'保存成功'));
    }
    /*
     * 上传证书
     */
    public function action_upload_certs()
    {
        $payid = $_POST['payid'];
        $basefolder = BASEPATH . '/uploads/main/certs/';
        $filedata = ARR::get($_FILES, 'Filedata');
        $path_info = pathinfo($filedata['name']);
        $filename = date('YmdHis');
        $filepath = $basefolder . $filename . '.' . $path_info['extension'];//文件上传路径
        if (!file_exists($basefolder))
        {
            mkdir($basefolder, 0777, true);
        }
        $out = array('status' => false);
        if ($rs = move_uploaded_file($filedata['tmp_name'], $filepath))
        {
            $dir = $this->_certs_arr[$payid]['dir'];
            //解压文件
            include(PUBLICPATH . '/vendor/zipfolder.php');
            $archive = new ZipFolder();
            $archive->setLoadPath(dirname($filepath) . '/');
            $archive->setFile(basename($filepath));

            $unzippath = BASEPATH . $dir;
            if (file_exists($unzippath) || substr_count($dir, 'payment'))
            {
                //payment下没有证书目录，则生成目录
                if (!file_exists($unzippath))
                {
                    mkdir($unzippath, 0777, true);
                }
                $archive->setSavePath($basefolder);
                $extractResult = $archive->openZip();
                if (!$extractResult || Common::isEmptyDir(dirname($filepath)))
                {

                    $out['msg'] = '文件损坏或网站目录及子目录无写权限'; //目录无写权限
                    exit(json_encode($out));
                }
                else
                {
                    $moveResult = Common::xCopy($basefolder . "{$filename}", $unzippath, true);
                    if ($moveResult['success'] == false)
                    {
                        $out['msg'] = '证书文件移动失败,' . $moveResult['errormsg'];
                        exit(json_encode($out));
                    }
                }
                $out['status'] = true;
            }

            //删除上传的文件
            Common::rrmdir($basefolder);
        }
        else
        {
            $out['msg'] = '证书上传错误';
        }
        echo json_encode($out);
    }

    /*
     * 判断证书是否存在
     */
    private function is_certs_uploaded($info)
    {
        $basepath = rtrim(BASEPATH.'/','\\../');
        foreach($info['files'] as $file)
        {
            $full_path = $basepath.$info['dir'].$file;
            if(!file_exists($full_path))
            {
                return false;
            }
        }
        return true;
    }



}