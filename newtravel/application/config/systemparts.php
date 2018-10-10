<?php
return array
(
    Model_SystemParts::$coreSystemPartCode => array
    (
        array
        (
            'id' => '0',
            'name' => '内核系统',
            'version_path' => "/{$GLOBALS['cfg_backdir']}/application/data/version.php",
            'status' => '1'
        )
    ),
    Model_SystemParts::$pcSystemPartCode => array
    (
      
        array
        (
            'id' => '5',
            'name' => '6.X版（最新版）',
            'version_path' => '/v5/data/version.php',
            'status' => '1'
        )
    ),
    Model_SystemParts::$mobileSystemPartCode => array
    (
       
        array
        (
            'id' => '1',
            'name' => '6.X版（最新版）',
            'version_path' => '/phone/application/data/version.php',
            'status' => '1'
        )
    )
);