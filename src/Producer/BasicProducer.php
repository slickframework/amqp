<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp\Producer;

use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Message;
use Slick\Amqp\Producer;

/**
 * BasicProducer
 *
 * @package Slick\Amqp\Producer
 */
abstract class BasicProducer implements Producer
{
    use ProducerMethods;

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
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
    }

    /**
     * @inheritDoc
     */
    public function publish(Message $message, ?string $routingKey = ""): void
    {
        if (!$this->isDeclared()) {
            $this->declareExchange();
        }

        $this->channel()->basic_publish(
            $message->sourceMessage(),
            $this->exchange,
            $routingKey ?? ""
        );
    }

    /**
     * Exchange default options
     *
     * @return array<string, mixed>
     */
    public static function exchangeDefaultOptions(): array
    {
        return self::$defaultOptions;
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
     * AMQP channel (Session)
     *
     * @return AMQPChannel
     */
    protected function channel(): AMQPChannel
    {
        return $this->channel;
    }

    /**
     * Declares the exchange to be used
     *
     * This method SHOULD set up de the exchange and MUST set the declared bit accordingly
     */
    protected function declareExchange(): void
    {
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
