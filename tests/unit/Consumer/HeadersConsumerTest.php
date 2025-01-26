<?php
/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Tests\Slick\Amqp\Consumer;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;
use Slick\Amqp\Consumer\HeadersConsumer;
use PHPUnit\Framework\TestCase;
use Slick\Amqp\Producer;

class HeadersConsumerTest extends TestCase {

    public function test_declareExchange(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);
        $connection->method('channel')->willReturn($channel);

        $channel->expects($this->once())
            ->method('exchange_declare')
            ->with('test_exchange', Producer::TYPE_HEADERS)
            ->willReturn(['test_exchange.queue']);

        $channel->expects($this->once())
            ->method('queue_declare')
            ->with('')
            ->willReturn(['test_exchange.queue']);

        $consumer = new class ($connection) extends HeadersConsumer
        {
            public function __construct($connection)
            {
                $this->exchange = 'test_exchange';
                parent::__construct($connection);
                $this->declareQueue();
            }

        };

        self::assertInstanceOf(HeadersConsumer::class, $consumer);
    }

    public function test_bindHeaders(): void
    {
        $connection = $this->createMock(AMQPStreamConnection::class);
        $channel = $this->createMock(AMQPChannel::class);
        $connection->method('channel')->willReturn($channel);

        $channel->expects($this->once())
            ->method('exchange_declare')
            ->with('test_exchange', Producer::TYPE_HEADERS)
            ->willReturn(['test_exchange.queue']);

        $channel->expects($this->once())
            ->method('queue_declare')
            ->with('')
            ->willReturn(['test_exchange.queue']);

        $channel->expects($this->once())
            ->method('queue_bind')
            ->with('test_exchange.queue', 'test_exchange', '', false, $this->isInstanceOf(AMQPTable::class));

        $consumer = new class ($connection) extends HeadersConsumer
        {
            public function __construct($connection)
            {
                $this->exchange = 'test_exchange';
                parent::__construct($connection);
            }

        };

        self::assertNull($consumer->bindHeaders(['foo' => 'bar']));
    }
}
