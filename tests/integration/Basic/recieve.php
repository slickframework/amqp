<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


require dirname(dirname(__DIR__), 2).'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Consumer\BasicConsumer;
use Slick\Amqp\Message;

$connection = new AMQPStreamConnection('0.0.0.0', 5672, 'user', 'secret');
$consumer = new class($connection) extends BasicConsumer {
    public function __construct($connection)
    {
        $this->queue =  'hello';
        parent::__construct($connection);
    }
};

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function (Message $msg) {
    echo ' [x] Received ', $msg->parsedBody(), "\n";
};

$consumer->consume($callback);
