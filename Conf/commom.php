<?php
//开发环境
if ('development' == APP_ENV) {
    return array(
        //'配置项'=>'配置值'
        'PIC_URL'=> 'http://local.pic.com',
        'ALLOW_HOST'=> array('http://local.erp.com'),
        'ERP_HTTP_URL' => 'http://local.erp.com',
        'CUSTOM_ATTR_IMAGE_PATH'=>'/customAttrImage/',
        'ALLOW_IP_ARRAY'=>array( // 允许登陆的IP
            '0.0.0.0',
            '192.168.0.0',
            '127.0.0.1',
        ),
        'IS_OPEN_PROXY' => false,
    );
} else {
    //正式环境
    return array(
        //'配置项'=>'配置值'
        'PIC_URL'   => 'http://img.erp.spocoo.com',
        'ALLOW_HOST' =>  array('http://47.90.38.119','http://47.89.27.165','http://erp.spocoo.com'),
        'ERP_HTTP_URL' => 'http://47.90.38.119',
        'CUSTOM_ATTR_IMAGE_PATH'=>'/customAttrImage/',
        'ALLOW_IP_ARRAY'=>array( // 允许登陆的IP
            //'0.0.0.0',
            '192.168.0.0',
            '127.0.0.1',
        ),
        'IS_OPEN_PROXY' => true,
        'PROXY_HOST' => '47.90.38.119',
        'PROXY_PORT' => 8889
    );
}
