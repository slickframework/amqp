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
$worker = new class($connection, 'task_queue') extends BasicConsumer {
    public function __construct(AMQPStreamConnection $connection, string $name)
    {
        $this->queue = $name;
        parent::__construct($connection);
        $this->options[self::OPT_DURABLE] = true;
        $this->consumeOptions[self::CONSUME_OPT_NO_ACK] = false;
    }

    protected function declareQueue(): void
    {
        parent::declareQueue();
        $this->channel()->basic_qos(0, 1, null);
    }
};

$callback = function (Message $message) use ($worker) {
    echo ' [x] Received ', $message->parsedBody(), "\n";
    sleep(substr_count($message->parsedBody(), '.'));
    $worker->acknowledge($message);
    echo " [x] Done\n";
};

$worker->consume($callback);

 