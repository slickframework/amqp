<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp\Consumer;

use PhpAmqpLib\Wire\AMQPTable;
use Slick\Amqp\Consumer;
use Slick\Amqp\Consumer\BasicConsumer;
use Slick\Amqp\Producer;

/**
 * HeadersConsumer
 *
 * @package Slick\Amqp\Consumer
 */
abstract class HeadersConsumer extends BasicConsumer implements Consumer
{
    const X_MATCH_ALL = 'all';
    const X_MATCH_ANY = 'any';

    /**
     * @inheritDoc
     */
    protected function declareQueue(): void
    {
        $this->mergeExchangeOptions();
        $args = array_values($this->exchangeOptions());
        array_unshift($args, Producer::TYPE_HEADERS);
        array_unshift($args, $this->exchange);
        call_user_func_array([$this->channel(), 'exchange_declare'], $args);
        parent::declareQueue();
    }

    /**
     * Binds the queue to the exchange based on headers.
     *
     * This method checks if the queue is already declared and declares it if not.
     *
     * @param array<string, mixed> $headers An array of header key-value pairs to bind the queue with
     * @return mixed The result of the queue bind operation
     */
    public function bindHeaders(array $headers, string $xMatch = self::X_MATCH_ALL): mixed
    {
        if (!$this->isDeclared()) {
            $this->declareQueue();
        }

        $headerParams = new AMQPTable(array_merge(['x-match' => $xMatch], $headers));
        return $this->channel()->queue_bind($this->queue, $this->exchange, '', false, $headerParams);
    }
}
