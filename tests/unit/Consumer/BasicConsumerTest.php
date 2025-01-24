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
use PHPUnit\Framework\MockObject\MockObject;
use Slick\Amqp\Consumer;
use Slick\Amqp\Consumer\BasicConsumer;
use PHPUnit\Framework\TestCase;
use Slick\Amqp\Message;
use Slick\Amqp\Producer;

class BasicConsumerTest extends TestCase {

    private AMQPChannel&MockObject $channel;
    private AMQPStreamConnection&MockObject $connection;

    private BasicConsumer $subject;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(AMQPChannel::class);
        $this->connection = $this->createMock(AMQPStreamConnection::class);
        $this->connection->method('channel')->willReturn($this->channel);

        $this->subject = new class($this->connection ) extends BasicConsumer{
            public function __construct(AMQPStreamConnection $connection)
            {
                $this->queue = 'test_queue';
                parent::__construct($connection);
            }
        };
    }

    public function test_consume()
    {
        $changedCallback = null;
        $this->channel->expects($this->any())
            ->method('is_consuming')
            ->willReturn(true, false);
        $this->channel->expects($this->once())
            ->method('queue_declare')
            ->with('test_queue', false, false, false, true, false, [], null)
            ->willReturn(['test_queue', null, null]);
        $consumeCallable = function (...$args) use (&$changedCallback) {
            $changedCallback = $args[6];
            return true;
        };
        $this->channel->expects($this->once())
            ->method('basic_consume')
            ->with($this->callback($consumeCallable))->willReturn(1);
        $effectiveCallback = function () use (&$changedCallback) {
            $changedCallback((new Message('TEST'))->sourceMessage());
            return true;
        };
        $this->channel->expects($this->any())
            ->method('wait')
            ->with($this->callback($effectiveCallback))
            ->willReturn(null);

        $this->subject->consume(fn(Message $message) => true);
    }

    public function test_acknowledge()
    {
        $this->channel->expects($this->once())
            ->method('basic_ack')->with(1);

        $message = new Message('test');
        $message->sourceMessage()->setDeliveryTag(1);
        $message->sourceMessage()->setChannel($this->channel);
        $this->subject->acknowledge($message);
    }

    public function test_acknowledgeUndelivered()
    {
        $this->channel->expects($this->once())
            ->method('basic_ack')->with(-1);

        $message = new Message('test');
        $message->sourceMessage()->setChannel($this->channel);

        $this->subject->acknowledge($message);
    }


    public function test_bind()
    {
        $this->channel->expects($this->once())
            ->method('queue_bind')
            ->with('test_queue', '', '');
        $this->channel->expects($this->once())
            ->method('queue_declare')
            ->with('test_queue', false, false, false, true, false, [], null)
            ->willReturn(['test_queue', null, null]);

        $this->subject->bind();
    }

    public function test_destruct()
    {
        $this->channel->expects($this->once())->method('close');
        $this->connection->expects($this->once())->method('close');
        unset($this->subject);
    }

    public function test_destructException()
    {
        $this->channel->expects($this->once())->method('close')->willThrowException(new \Exception("test"));
        unset($this->subject);
    }

    public function test_isPassive(): void
    {
        self::assertFalse($this->subject->isPassive());
    }

    public function test_isDurable(): void
    {
        self::assertFalse($this->subject->isDurable());
    }

    public function test_isAutoDelete(): void
    {
        self::assertTrue($this->subject->isAutoDelete());
    }

    public function test_isExclusive(): void
    {
        self::assertFalse($this->subject->isExclusive());
    }

    public function test_options(): void
    {
        self::assertEquals([
            Consumer::OPT_PASSIVE => false,
            Consumer::OPT_DURABLE => false,
            Consumer::OPT_EXCLUSIVE => false,
            Consumer::OPT_AUTO_DELETE => true,
            Consumer::OPT_NOWAIT => false,
            Consumer::OPT_ARGUMENTS => [],
            Consumer::OPT_TICKET => null
        ], $this->subject->options());
    }

    public function test_exchangeOptions(): void
    {
        self::assertEquals([
            Producer::OPT_PASSIVE => false,
            Producer::OPT_DURABLE => false,
            Producer::OPT_AUTO_DELETE => true,
            Producer::OPT_INTERNAL => false,
            Producer::OPT_NOWAIT => false,
            Producer::OPT_ARGUMENTS => [],
            Producer::OPT_TICKET => null
        ], $this->subject->exchangeOptions());
    }

}
