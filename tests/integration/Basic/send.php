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
$producer = new class($connection) extends BasicProducer {};

$message = new Message('Hello World!!');

$producer->publish($message, 'hello');