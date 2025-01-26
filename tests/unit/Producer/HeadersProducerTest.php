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
use Slick\Amqp\Producer;
use Slick\Amqp\Producer\HeadersProducer;
use PHPUnit\Framework\TestCase;

class HeadersProducerTest extends TestCase {

    public function test_declareExchange()
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);
        $connection->method('channel')->willReturn($channel);

        $channel->expects($this->once())
            ->method('exchange_declare')
            ->with('test_exchange', Producer::TYPE_HEADERS)
            ->willReturn(null);


        $producer = new class($connection) extends HeadersProducer
        {
            public function __construct($connection)
            {
                $this->exchange = 'test_exchange';
                parent::__construct($connection);
                $this->declareExchange();
            }
        };

        self::assertInstanceOf(HeadersProducer::class, $producer);
    }
}
