<?php
/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Tests\Slick\Amqp\Producer;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Amqp\Producer\DirectProducer;
use PHPUnit\Framework\TestCase;

class DirectProducerTest extends TestCase {

    public function test_declareExchange()
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);
        $connection->method('channel')->willReturn($channel);

        $channel->expects($this->once())
            ->method('exchange_declare')
            ->with('test_exchange')
            ->willReturn(null);


        $producer = new class($connection) extends DirectProducer
        {
            public function __construct($connection)
            {
                $this->exchange = 'test_exchange';
                parent::__construct($connection);
                $this->declareExchange();
            }
        };

        self::assertInstanceOf(DirectProducer::class, $producer);
    }
}
