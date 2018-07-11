<?php
require 'vendor/autoload.php';

use EasyWeChat\Factory;
use Predis\Client;
$config = require 'config.php';

class Wechat {
    private $config;

    private $app;

    public function __construct($config) {
        $this->config = $config;

        $this->app = Factory::officialAccount($config['wechat']);
    }

    public function init(){
        try {
            $this->app->server->push([$this, 'handler']);
            $this->app->server->serve()->send();
        }catch (Exception $exception){

        }
    }

    public function handler($message){
        if ($message['MsgType'] == 'event'){
            if ($message['Event'] == 'subscribe'){  //关注
                return $this->subscribe($message);
            }
            if ($message['Event'] == 'unsubscribe') {  //关注
                return $this->unsubscribe($message);
            }
            if ($message['Event'] == 'SCAN'){
                return $this->scan($message);
            }
        }else{
            return "您好！欢迎使用 SwooleWechat 扫描登录";
        }
    }

    public function subscribe($message){
        $eventKey = intval(str_replace('qrscene_', '', $message['EventKey']));
        $openId = $message['FromUserName'];
        $user = $this->app->user->get($openId);
        $this->notify(json_encode([
            'type'  =>  'scan',
            'fd'    =>  $eventKey,
            'nickname'  =>  $user['nickname']
        ]));
        $count = $this->count($openId);
        $msgTemp = "%s，登录成功！\n这是你第%s次登录，玩的开心！";
        return sprintf($msgTemp, $user['nickname'], $count);
    }

    public function unsubscribe($message){
        $openId = $message['FromUserName'];
        $client = new Client();
        $client->del(['SWOOLE::WECHAT::'.$openId]);
    }

    public function scan($message){
        $eventKey = $message['EventKey'];
        $openId = $message['FromUserName'];

        $user = $this->app->user->get($openId);
        $this->notify(json_encode([
            'type'  =>  'scan',
            'fd'    =>  $eventKey,
            'nickname'  =>  $user['nickname']
        ]));
        $count = $this->count($openId);

        $msgTemp = "%s，欢迎回来！\n这是你第%s次登录，玩的开心！";
        return sprintf($msgTemp, $user['nickname'], $count);
    }

    public function notify($message){
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', $this->config['notify_port'], -1)) {
            return "connect failed. Error: {$client->errCode}\n";
        }
        $ret = $client->send($message);
        $client->close();

        return $ret;
    }

    public function count($openId){
        $client = new Client();
        return $client->incr('SWOOLE::WECHAT::'.$openId);
    }
}

$wechat = new Wechat($config);
$wechat->init();