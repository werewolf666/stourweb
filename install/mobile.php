<?php
return array (
    'domain' =>
        array (
            'mobile' => '~domainname~',
            'main' => '~domainname~',
        ),
    'delimiterLeft' => '#mobile start',
    'delimiterRight' => '#mobile end',
    'rules' => '{PHP_EOL}RewriteCond %{HTTP_HOST} ^{host}${PHP_EOL}RewriteCond %{REQUEST_URI} !^/uploads/ {PHP_EOL} RewriteRule (.*) {path}/$1 [L]{PHP_EOL}',
    'rulesReplace' => false,
    'version' =>
        array (
            1 =>
                array (
                    'no' => '6.0',
                    'path' => '/phone/',
                ),
        ),
);