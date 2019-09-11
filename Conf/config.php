<?php
return array(
    //'配置项'=>'配置值'
    'URL_MODEL'	=>	3,
    'DB_TYPE'	=>	'mysql',
    'DB_HOST'	=>	'192.168.1.91',
    'DB_NAME'	=>	'images',
    'DB_USER'	=>	'www',
    'DB_PWD'	=>	'123456',
    'DB_PORT'	=>	'3306',
    'DB_PREFIX'	=>	'',
    'SHOW_PAGE_TRACE'=>true,
    //'DATA_CACHE_TYPE'=>'Xcache',
    //"LOAD_EXT_FILE"=>"eBaySession,aliExpressSession,amazonClient",// 自动载入的公共文件
    'DB_CONFIG1' => 'mysql://www:123456@192.168.1.91:3306/images',
    'DB_CONFIG2' => 'mysql://www:123456@192.168.1.91:3306/v3-all',
    // 'PIC_URL'=>'http://img.wisstone.com:8088',
    'PIC_URL'=>'http://local.pic.com',
    'ALLOW_HOST'=>'http://local.erp.com',
    'CUSTOM_ATTR_IMAGE_PATH'=> '/customAttrImage/',
    'LOAD_EXT_CONFIG' => 'commom',
);