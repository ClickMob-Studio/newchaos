<?php

require __DIR__ . '/vendor/autoload.php';
use React\EventLoop\Loop;

$http = new React\Http\HttpServer(function (Psr\Http\Message\ServerRequestInterface $request) {
    return React\Http\Message\Response::plaintext(
        "Hello World!\n"
    );
});

$timer = Loop::addPeriodicTimer(0.1, function () {
    echo 'Tick' . PHP_EOL;
});

Loop::addTimer(5.0, function () use ($timer) {
    Loop::cancelTimer($timer);
    echo 'Done' . PHP_EOL;
});

$socket = new React\Socket\SocketServer('176.9.41.104:8085');
$http->listen($socket);

echo "Server running at http://127.0.0.1:8085" . PHP_EOL;
