<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10 0010
 * Time: 13:44
 */
class Plugin_Core_Factory
{
    private $_listeners=array();
    public static function factory()
    {
        return new self;
    }
    public function add_listener($func,$params)
    {
        $this->_listeners[]=array('func'=>$func,'params'=>$params);
        return $this;
    }
    public function execute()
    {
        $classArr=self::get_plugin_classes();
        foreach($classArr as $className)
        {
                $object = new $className;
                foreach($this->_listeners as $row) {
                    $func = $row['func'];
                    $params = $row['params'];
                    if (method_exists($object, $row['func'])) {
                        $object->$func($params);
                    }
                }
         }
        return $this;

    }
    public function remove_listener()
    {
        $this->_listeners=array();
        return $this;
    }
    private static function  get_plugin_classes()
    {
        $classArr=array();
        if ($dh = opendir(APPPATH.'classes/plugin')) {
            while (($file = readdir($dh)) !== false) {
                if($file=='..' || $file=='.')
                    continue;

                $fileName=basename($file,'.php');
                $className='Plugin_'.ucfirst($fileName);
                if(class_exists($className))
                {
                    $classArr[]=$className;
                }
            }
        }
        return $classArr;
    }

}