<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp\Consumer;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Slick\Amqp\Consumer;
use Slick\Amqp\Message;

/**
 * BasicConsumer
 *
 * @package Slick\Amqp\Consumer
 */
abstract class BasicConsumer implements Consumer
{
    use ConsumerMethods;

    /**
     * @var string
     */
    protected string $queue = '';

    /**
     * @var string
     */
    protected string $exchange = '';

    /**
     * @var bool
     */
    protected bool $declared = false;

    /**
     * @var AMQPStreamConnection
     */
    protected AMQPStreamConnection $connection;

    /**
     * @var AMQPChannel
     */
    private AMQPChannel $channel;

    /**
     * Creates a BasicProducer
     *
     * @param AMQPStreamConnection $connection
     */
    public function __construct(AMQPStreamConnection $connection)
    {
        $this->mergeOptions();
        $this->mergeExchangeOptions();
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
    }

    /**
     * @inheritDoc
     */
    public function bind(?string $routingKey = ''): mixed
    {
        if (!$this->isDeclared()) {
            $this->declareQueue();
        }

        return $this->channel()->queue_bind($this->queue, $this->exchange, $routingKey ?? "");
    }

    /**
     * @inheritDoc
     * @param array<string, mixed> $options
     */
    public function consume(callable $callable, array $options = []): void
    {
        if (!$this->isDeclared()) {
            $this->declareQueue();
        }

        $callback = function (AMQPMessage $message) use ($callable) {
            return $callable(Message::fromAMQPMessage($message));
        };

        $this->mergeConsumeOptions(array_merge($options, ['callback' => $callback]));
        $args = $this->consumeOptions;
        call_user_func_array([$this->channel(), 'basic_consume'], $args);

        while ($this->channel()->is_consuming()) {
            $this->channel()->wait();
        }
    }

    /**
     * @inheritDoc
     */
    public function acknowledge(Message $message): void
    {
        if ($message->channel()) {
            $message->channel()->basic_ack($message->deliveryTag());
        }
    }

    /**
     * AMQP channel (Session)
     *
     * @return AMQPChannel
     */
    protected function channel(): AMQPChannel
    {
        return $this->channel;
    }

    /**
     * Check if this producer has a declared exchange
     *
     * @return bool
     */
    protected function isDeclared(): bool
    {
        return $this->declared;
    }

    /**
     * Declares the exchange to be used
     *
     * This method SHOULD set up de the exchange and MUST set the declared bit accordingly
     */
    protected function declareQueue(): void
    {
        $args = array_values($this->options());
        array_unshift($args, $this->queue);
        list($queueName, ,) = call_user_func_array([$this->channel(), 'queue_declare'], $args);
        $this->queue = $queueName;
        $this->declared = true;
    }

    public function __destruct()
    {
        try {
            $this->channel->close();
            $this->connection->close();
        } catch (Exception) {
        }
    }
}
