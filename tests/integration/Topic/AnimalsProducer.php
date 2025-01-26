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
use Slick\Amqp\Producer;
use Slick\Amqp\Producer\TopicProducer;

/**
 * AnimalsProducer
 *
 * @package Integration\Slick\Amqp\Topic
 */
final class AnimalsProducer extends TopicProducer implements Producer
{
    public function __construct(AMQPStreamConnection $connection, string $name)
    {
        $this->exchange = $name;
        parent::__construct($connection);
        $this->mergeOptions([self::OPT_AUTO_DELETE => false]);
    }

}
