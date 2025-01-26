<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Integration\Slick\Amqp\Headers\NotificationsProducer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Message;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('0.0.0.0', 5672, 'user', 'secret');
$producer = new NotificationsProducer($connection, 'notifications_headers');

$message1 = (new Message("High-priority email for US region"))
    ->withHeaders([
        'type' => 'email',
        'priority' => 'high',
        'region' => 'US',
    ]);

$message2 = (new Message("Low-priority SMS for EU region"))
    ->withHeaders([
        'type' => 'sms',
        'priority' => 'low',
        'region' => 'EU',
    ]);

$producer->publish($message1);
$producer->publish($message2);

echo "Messages published.\n";