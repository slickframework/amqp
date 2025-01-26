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
use Slick\Amqp\Consumer;
use Slick\Amqp\Consumer\HeadersConsumer;

/**
 * NotificationsConsumer
 *
 * @package Integration\Slick\Amqp\Headers
 */
final class NotificationsConsumer extends HeadersConsumer implements Consumer
{
    public function __construct(AMQPStreamConnection $connection, string $exchangeName)
    {
        $this->exchange = $exchangeName;
        $this->queue = "urgent-notifications";
        parent::__construct($connection);
    }
}
