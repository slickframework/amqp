<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Integration\Slick\Amqp\Topic;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Consumer;
use Slick\Amqp\Consumer\TopicConsumer;

/**
 * AnimalsConsumer
 *
 * @package Integration\Slick\Amqp\Topic
 */
final class AnimalsConsumer extends TopicConsumer implements Consumer
{
    public function __construct(AMQPStreamConnection $connection, string $name)
    {
        $this->exchange = $name;
        parent::__construct($connection);
        $this->exchangeOptions[self::OPT_AUTO_DELETE] = false;
        $this->mergeOptions([
            self::OPT_AUTO_DELETE => false,
            self::OPT_EXCLUSIVE => true
        ]);
    }
}
