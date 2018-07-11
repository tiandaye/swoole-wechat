<?php
$config = require 'config.php';
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Swoole Wechat 扫描登录</title>

    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-6 col-xs-offset-3">
            <img id="qrcode" src="" class="img-responsive">
        </div>
        <div class="col-xs-6 col-xs-offset-3">
            <div class="alert alert-warning" role="alert">
                <strong>Note!</strong> 扫描后需要关注公众平台测试号，可以过后自行取消关注。
            </div>
        </div>
        <div class="col-xs-6 col-xs-offset-3">
            <div class="alert alert-info hide" role="alert" id="connect-info">
                <strong>Well done!</strong> 连接成功.
            </div>
        </div>
        <div class="col-xs-6 col-xs-offset-3">
            <div class="alert alert-success hide" role="alert" id="scan-info">
                <strong id="username"></strong> 登录成功. 欢迎关注我的公众号～
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    const socket = new WebSocket('<?php echo $config['ws']?>');
    socket.addEventListener('open', function (event) {
        $('#connect-info').removeClass('hide');
    });
    socket.addEventListener('message', function (event) {
        var data = JSON.parse(event.data);
        console.log(data);
        if (data.message_type == 'qrcode_url'){
            $('#qrcode').attr('src', data.url);
        }
        if (data.message_type == 'scan_success'){
            $('#scan-info').removeClass('hide');
            $('#username').text(data.user);
            $('#qrcode').attr('src', 'https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq/it/u=3726321181,3910107498&fm=173&app=25&f=JPEG?w=640&h=677&s=8BB0C40257EA66B81C2A196C03000060');
        }
    });
</script>
</body>
</html>