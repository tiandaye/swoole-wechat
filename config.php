<?php

return [
    'wechat'    =>  [   //微信配置
        'app_id' => 'xxxxxxxxxx',
        'secret' => 'xxxxxxxxx',
        'token'     =>  'xxxxxxxx',
        'response_type' => 'array',
        'log' => [
            'level' => 'debug',
            'file' => __DIR__.'/wechat.log',
        ],
    ],
    'ws_port'    =>  1099,  // websocket 监听端口号
    'ws'    =>  'ws://127.0.0.1:1099',  // websocket连接地址,服务器ip地址
    'notify_port'   =>  9999 // 消息通知端口号
];