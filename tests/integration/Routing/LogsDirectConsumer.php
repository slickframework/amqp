<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Integration\Slick\Amqp\Routing;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Consumer\DirectConsumer;

/**
 * LogsDirectConsumer
 *
 * @package Integration\Slick\Amqp\Routing
 */
final class LogsDirectConsumer extends DirectConsumer
{

    public function __construct(AMQPStreamConnection $connection, string $name)
    {
        $this->exchange = $name;
        parent::__construct($connection);
        $this->exchangeOptions[self::OPT_AUTO_DELETE] = false;
        $this->options[self::OPT_AUTO_DELETE] = false;
        $this->options[self::OPT_EXCLUSIVE] = true;
    }
}
