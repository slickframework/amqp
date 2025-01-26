<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp\Producer;

use Slick\Amqp\Producer;
use Slick\Amqp\Producer\BasicProducer;

/**
 * TopicProducer
 *
 * @package Slick\Amqp\Producer
 */
class TopicProducer extends BasicProducer implements Producer
{
    /**
     * @inheritDoc
     */
    protected function declareExchange(): void
    {
        $this->mergeOptions();
        $args = array_values($this->options());
        array_unshift($args, self::TYPE_TOPIC);
        array_unshift($args, $this->exchange);
        call_user_func_array([$this->channel(), 'exchange_declare'], $args);
        $this->declared = true;
    }
}
