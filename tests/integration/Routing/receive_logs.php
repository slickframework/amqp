<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Integration\Slick\Amqp\Routing\LogsDirectConsumer;
use PhpAmqpLib\Connection\AMQPStreamConnection;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('localhost', 5672, 'user', 'secret');
$consumer = new LogsDirectConsumer($connection, 'direct_logs');

$routingKey = $argv[1] ?? 'info';
$consumer->bind($routingKey); // Bind the queue to the exchange with the routing key

$consumer->consume(function ($message) {
    echo " [x] Received: {$message->parsedBody()}\n";
});
 