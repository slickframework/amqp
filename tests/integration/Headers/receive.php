<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Integration\Slick\Amqp\Headers\NotificationsConsumer;
use PhpAmqpLib\Connection\AMQPStreamConnection;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('0.0.0.0', 5672, 'user', 'secret');

$consumer = new NotificationsConsumer($connection, 'notifications_headers');

// Bind headers to receive only high-priority emails for the US region.
$consumer->bindHeaders([
    'type' => 'email',
    'priority' => 'high',
    'region' => 'US',
], 'all');

$consumer->consume(function ($message) {
    echo " [x] Received: {$message->parsedBody()}\n";
});
