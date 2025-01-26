<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Integration\Slick\Amqp\Headers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Producer;
use Slick\Amqp\Producer\HeadersProducer;

/**
 * NotificationsProducer
 *
 * @package Integration\Slick\Amqp\Headers
 */
final class NotificationsProducer extends HeadersProducer implements Producer
{
    public function __construct(AMQPStreamConnection $connection, string $exchangeName)
    {
        $this->exchange = $exchangeName;
        parent::__construct($connection);
    }
}
