<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp\Consumer;

use Slick\Amqp\Consumer;
use Slick\Amqp\Producer;

/**
 * FanOutConsumer
 *
 * @package Slick\Amqp\Consumer
 */
abstract class FanOutConsumer extends BasicConsumer implements Consumer
{
    /**
     * @inheritDoc
     */
    protected function declareQueue(): void
    {
        $this->mergeExchangeOptions();
        $args = array_values($this->exchangeOptions());
        array_unshift($args, Producer::TYPE_FANOUT);
        array_unshift($args, $this->exchange);
        call_user_func_array([$this->channel(), 'exchange_declare'], $args);
        parent::declareQueue();
    }
}
