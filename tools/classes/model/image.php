<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Image extends ORM
{
    public static function change_open_status($current_file)
    {
        $config_files = Kohana::find_file('config', 'image');
        foreach ($config_files as $file)
        {
            if ($file == $current_file)
            {
                continue;
            }
            $data = Kohana::load($file);
            foreach ($data as $k => &$item)
            {
                if (is_array($item))
                {
                    if (isset($item['is_open']))
                    {
                        $item['is_open'] = 0;
                        break;
                    }
                }
                else
                {
                    if ($k == 'remote_image')
                    {
                        $item = false;
                        break;
                    }
                }
            }
            file_put_contents($file, '<?php defined(\'SYSPATH\') or die(\'No direct script access.\');' . "\r\n return " . var_export($data, true) . ';');
        }
    }
} 