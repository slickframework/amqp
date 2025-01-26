<?php
/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Slick\Amqp\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{

    private Message $subject;
    private $payload;
    private $properties;

    protected function setUp(): void
    {
        $this->payload = 'Some text';
        $this->properties = [Message::DELIVERY_MODE => Message::DELIVERY_MODE_TRANSIENT];
        $this->subject = new Message($this->payload, $this->properties);
    }

    public function test_payload(): void
    {
        self::assertEquals($this->payload, $this->subject->body());
    }

    public function test_propertyGetter(): void
    {
        self::assertEquals(Message::DELIVERY_MODE_TRANSIENT, $this->subject->get(Message::DELIVERY_MODE));
    }

    public function test_setAGivenProperty(): void
    {
        $appName = 'Test app';
        self::assertSame($this->subject, $this->subject->set(Message::APP_ID, $appName));
        self::assertEquals($appName, $this->subject->get(Message::APP_ID));
    }

    public function test_getNullWhenNoFound(): void
    {
        self::assertNull($this->subject->get(Message::APP_ID));
    }

    public function test_checkMessageHasProperty()
    {
        self::assertFalse($this->subject->has(Message::REPLY_TO));
        self::assertTrue($this->subject->has(Message::DELIVERY_MODE));
    }

    public function test_itMayHaveAChannel(): void
    {
        self::assertNull($this->subject->channel());
    }

    public function test_checkRedelivery(): void
    {
        self::assertFalse($this->subject->isRedelivered());
    }

    public function test_itCanHaveAnExchange()
    {
        self::assertNull($this->subject->exchange());
    }

    public function test_hasARoutingKey(): void
    {
        self::assertNull($this->subject->routingKey());
    }

    public function test_consumerTag()
    {
        self::assertNull($this->subject->consumerTag());
        $tag = "some tag";
        self::assertSame($this->subject, $this->subject->withConsumerTag($tag));
        self::assertSame($tag, $this->subject->consumerTag());
    }

    public function test_detectJsonSerializable()
    {
        $content = new class implements \JsonSerializable {

            public function jsonSerialize(): mixed
            {
                return ["foo" => "bar"];
            }
        };

        $message = new Message($content);
        self::assertEquals('application/json', $message->get(Message::CONTENT_TYPE));
    }

    public function test_deserializeJsonContent()
    {
        $content = json_encode(new class implements \JsonSerializable {

            public function jsonSerialize(): mixed
            {
                return ["foo" => "bar"];
            }
        });

        $message = new AMQPMessage($content, [Message::CONTENT_TYPE => 'application/json']);
        $message->set(Message::HEADERS, new AMQPTable(['foo' => 'bar']));
        $sub = Message::fromAMQPMessage($message);
        self::assertEquals('application/json', $sub->get(Message::CONTENT_TYPE));
        self::assertEquals(['foo' => 'bar'], $sub->headers());
    }

    public function test_itCanBeParsedAsAStringMessage()
    {
        $content = new class implements \Stringable
        {
            public function __toString(): string
            {
                return "test";
            }
        };

        $message = new Message($content);
        self::assertEquals('text/plain', $message->get(Message::CONTENT_TYPE));
    }

    public function test_itHasItsEawMessage()
    {
        self::assertInstanceOf(AMQPMessage::class, $this->subject->sourceMessage());
    }

    public function test_parseContent()
    {
        $content = json_encode(new class implements \JsonSerializable {
            public function jsonSerialize(): mixed
            {
                return ["foo" => "bar"];
            }
        });

        $message = new AMQPMessage($content, [Message::CONTENT_TYPE => 'application/json']);
        $sub = Message::fromAMQPMessage($message);
        self::assertEquals($content, $sub->body());
        self::assertInstanceOf(\stdClass::class, $sub->parsedBody());
    }

    public function test_simpleTextMessage()
    {
        $message = Message::fromAMQPMessage(new AMQPMessage('test'));
        self::assertFalse($message->has(Message::CONTENT_TYPE));
        self::assertEquals('test', $message->parsedBody());
    }

    public function test_nonJsonMessage()
    {
        $message = Message::fromAMQPMessage(new AMQPMessage('test', [Message::CONTENT_TYPE => 'application/yaml']));
        self::assertTrue($message->has(Message::CONTENT_TYPE));
        self::assertNull($message->parsedBody());
    }

    public function test_headers()
    {
        self::assertSame($this->subject, $this->subject->withHeader('type', "mail"));
        self::assertEquals("mail", $this->subject->headers()['type']);
        self::assertEquals("mail", $this->subject->get(Message::HEADERS)['type']);
    }

    public function test_withoutHeader()
    {
        $this->subject->withHeader('type', "mail");
        $this->subject->withHeader('priority', "low");
        self::assertArrayHasKey("type", $this->subject->headers());
        self::assertArrayHasKey("priority", $this->subject->headers());

        $this->subject->withoutHeader('type');
        self::assertArrayNotHasKey("type", $this->subject->headers());
        self::assertArrayHasKey("priority", $this->subject->headers());
    }

    public function test_withHeadres()
    {
        self::assertSame($this->subject, $this->subject->withHeaders(['type' => "mail"]));
        self::assertEquals("mail", $this->subject->headers()['type']);
        self::assertEquals("mail", $this->subject->get(Message::HEADERS)['type']);
    }

    public function test_removeUnexistingHeader(): void
    {
        $this->subject->withHeader('priority', "low");
        $this->subject->withoutHeader('type');
        self::assertArrayHasKey("priority", $this->subject->headers());
    }
}
