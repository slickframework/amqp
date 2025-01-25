<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Integration\Slick\Amqp\Routing\LogsDirectProducer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Message;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('localhost', 5672, 'user', 'secret');
$producer = new LogsDirectProducer($connection, 'direct_logs');

$severity = $argv[1] ?? 'info'; // Routing key
$messageBody = $argv[2] ?? 'Default log message';

$message = new Message($messageBody);
$producer->publish($message, $severity);

echo " [x] Sent: {$severity} - {$messageBody}\n";
 