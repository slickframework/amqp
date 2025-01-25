<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Integration\Slick\Amqp\PublishSubscribe;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Producer\FanOutProducer;

/**
 * LogsProducer
 *
 * @package integration\PublishSubscribe
 */
final class LogsProducer extends FanOutProducer
{
    public function __construct(AMQPStreamConnection $connection, string $name)
    {
        $this->exchange = $name;
        parent::__construct($connection);
    }
}
