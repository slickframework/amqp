<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Integration\Slick\Amqp\PublishSubscribe\LogsConsumer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Message;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('0.0.0.0', 5672, 'user', 'secret');
$consumer = new LogsConsumer($connection, 'logs');
$consumer->bind();

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function (Message $msg) {
    echo ' [x] ', $msg->parsedBody(), "\n";
};

$consumer->consume($callback);
