<?php

return array(
    'controller' => array(
        'default' => 'index',
        'action'  => 'index'
    ),
    'view'       => array(
        'path'    => 'Tpl/',
        'compile' => array(
            'path'   => 'Tmp/Compile/',
            'expire' => 3600
        ),
        'cache'   => 'view'
    )
);