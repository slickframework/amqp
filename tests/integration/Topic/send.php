<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Integration\Slick\Amqp\Topic\AnimalsProducer;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Message;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('localhost', 5672, 'user', 'secret');
$producer = new AnimalsProducer($connection, 'animal_topics');

$routingKey = $argv[1] ?? 'animal.unknown';
$messageBody = $argv[2] ?? 'Default animal message';

$message = new Message($messageBody);
$producer->publish($message, $routingKey);

echo " [x] Sent: {$routingKey} - {$messageBody}\n";
 