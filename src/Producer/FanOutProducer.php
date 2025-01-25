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

/**
 * FanOutProducer
 *
 * @package Slick\Amqp\Producer
 */
abstract class FanOutProducer extends BasicProducer implements Producer
{

    protected function declareExchange(): void
    {
        $this->mergeOptions();
        $args = array_values($this->options());
        array_unshift($args, self::TYPE_FANOUT);
        array_unshift($args, $this->exchange);
        call_user_func_array([$this->channel(), 'exchange_declare'], $args);
        $this->declared = true;
    }
}
