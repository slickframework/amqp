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
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use Slick\Amqp\Message;
use Slick\Amqp\Producer;
use Slick\Amqp\Producer\BasicProducer;
use PHPUnit\Framework\TestCase;

class BasicProducerTest extends TestCase
{

    private BasicProducer $subject;
    private AMQPChannel&MockObject $channel;
    private AMQPStreamConnection&MockObject $connection;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(AMQPChannel::class);
        $this->connection = $this->createMock(AMQPStreamConnection::class);
        $this->connection->method('channel')->willReturn($this->channel);

        $this->subject = new class($this->connection) extends BasicProducer {};
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

    public function test_options(): void
    {
        self::assertEquals([
        Producer::OPT_PASSIVE => false,
        Producer::OPT_DURABLE => false,
        Producer::OPT_AUTO_DELETE => true,
        Producer::OPT_INTERNAL => false,
        Producer::OPT_NOWAIT => false,
        Producer::OPT_ARGUMENTS => [],
        Producer::OPT_TICKET => null
    ], $this->subject->options());
    }

    public function test_defaultOptions(): void
    {
        self::assertEquals([
            Producer::OPT_PASSIVE => false,
            Producer::OPT_DURABLE => false,
            Producer::OPT_AUTO_DELETE => true,
            Producer::OPT_INTERNAL => false,
            Producer::OPT_NOWAIT => false,
            Producer::OPT_ARGUMENTS => [],
            Producer::OPT_TICKET => null
        ], BasicProducer::exchangeDefaultOptions());
    }

    public function test_publish(): void
    {
        $message = new Message("test message");
        $this->channel->expects($this->once())
            ->method('basic_publish')
            ->with($this->isInstanceOf(AMQPMessage::class), $this->isString(), "");
        $this->subject->publish($message);
    }

    public function test_destruct(): void
    {
        $this->channel->expects($this->once())->method('close');
        $this->connection->expects($this->once())->method('close');
        unset($this->subject);
    }

    public function test_destructException(): void
    {
        $this->channel->expects($this->once())->method('close')->willThrowException(new \Exception('test'));
        unset($this->subject);
    }

}
