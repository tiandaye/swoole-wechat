<?php
use EasyWeChat\Factory;
require 'vendor/autoload.php';
$config = require 'config.php';

$server = new swoole_websocket_server("0.0.0.0", $config['ws_port']);
$server->set([
    'daemonize' => 1
]);
$server->on('open', function (swoole_websocket_server $server, $request) use ($config){
    $app = Factory::officialAccount($config['wechat']);
    $result = $app->qrcode->temporary($request->fd, 6 * 24 * 3600);
    $url = $app->qrcode->url($result['ticket']);
    $server->push($request->fd, json_encode([
        'message_type'    =>  'qrcode_url',
        'url'       =>  $url
    ]));

    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) {

});

$tcp_server = $server->addListener('0.0.0.0', $config['notify_port'], SWOOLE_SOCK_TCP);
$tcp_server->set([]);
$tcp_server->on('receive', function ($serv, $fd, $threadId, $data) {
    $data = json_decode($data, true);
    if ($data['type'] == 'scan'){
        $serv->push($data['fd'], json_encode([
            'message_type'    =>  'scan_success',
            'user'  =>  $data['nickname']
        ]));
    }
    $serv->close($fd);
});

$server->start();