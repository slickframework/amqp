<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Message;
use Slick\Amqp\Producer\BasicProducer;

$connection = new AMQPStreamConnection('0.0.0.0', 5672, 'user', 'secret');
$producer = new class ($connection) extends BasicProducer {};

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "Hello World!";
}

$message = new Message(
    $data,
    [
        Message::DELIVERY_MODE => Message::DELIVERY_MODE_PERSISTENT
    ]
);

$producer->publish($message, 'task_queue');

echo ' [x] Sent ', $data, "\n";
