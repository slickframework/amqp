<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Integration\Slick\Amqp\Topic\AnimalsConsumer;
use PhpAmqpLib\Connection\AMQPStreamConnection;

require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

$connection = new AMQPStreamConnection('localhost', 5672, 'user', 'secret');

$consumer = new AnimalsConsumer($connection, 'animal_topics');

$pattern = $argv[1] ?? 'animal.*';
$consumer->bind($pattern); // Bind the queue to the exchange with the given pattern

$consumer->consume(function ($message) {
    echo " [x] Received: {$message->parsedBody()}\n";
});
