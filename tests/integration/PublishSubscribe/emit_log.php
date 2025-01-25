<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Integration\Slick\Amqp\PublishSubscribe\LogsProducer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Message;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('0.0.0.0', 5672, 'user', 'secret');
$producer = new LogsProducer($connection, 'logs');

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "info: Hello World!";
}

$message = new Message($data);

$producer->publish($message);

echo ' [x] Sent ', $data, "\n";
