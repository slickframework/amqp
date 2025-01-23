<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Amqp;

use JsonSerializable;
use OutOfBoundsException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use ReflectionClass;
use ReflectionException;
use Stringable;

/**
 * Message
 *
 * @package Slick\Amqp
 */
class Message
{

    const DELIVERY_MODE    = 'delivery_mode';
    const TYPE             = 'type';
    const HEADERS          = 'headers';
    const CONTENT_TYPE     = 'content_type';
    const CONTENT_ENCODING = 'content_encoding';
    const MESSAGE_ID       = 'message_id';
    const CORRELATION_ID   = 'correlation_id';
    const REPLY_TO         = 'reply_to';
    const EXPIRATION       = 'expiration';
    const TIMESTAMP        = 'timestamp';
    const USER_ID          = 'user_id';
    const APP_ID           = 'app_id';

    const DELIVERY_MODE_PERSISTENT = AMQPMessage::DELIVERY_MODE_PERSISTENT;
    const DELIVERY_MODE_TRANSIENT  = AMQPMessage::DELIVERY_MODE_NON_PERSISTENT;

    /**
     * @var mixed
     */
    private mixed $body;

    private AMQPMessage $message;

    /**
     * @var mixed
     */
    private mixed $parsedContent = null;

    /**
     * Creates a Message
     *
     * @param mixed $body
     * @param array<string, mixed> $properties
     */
    public function __construct(mixed $body, array $properties = [])
    {
        $this->body = $body;
        $baseProps = [];
        if ($contentType = $this->detectContentType($body)) {
            $baseProps[self::CONTENT_TYPE] = $contentType;
        }
        $this->message = new AMQPMessage($this->body, array_merge_recursive($baseProps, $properties));
    }

    /**
     * Creates a message from an AMQP message
     *
     * @param AMQPMessage $amqpMessage
     * @return Message
     * @throws ReflectionException
     */
    public static function fromAMQPMessage(AMQPMessage $amqpMessage): Message
    {
        $reflection = new ReflectionClass(static::class);
        /** @var Message $message */
        $message = $reflection->newInstanceWithoutConstructor();
        $message->message = $amqpMessage;
        $message->body = $amqpMessage->getBody();
        $message->parseContent();
        return $message;
    }

    /**
     * payload
     *
     * @return mixed
     */
    public function body(): mixed
    {
        return $this->body;
    }

    /**
     * AMQP source Message
     *
     * @return AMQPMessage
     */
    public function sourceMessage(): AMQPMessage
    {
        return $this->message;
    }

    /**
     * Look for additional properties in the 'properties' dictionary,
     * and if present - the 'delivery_info' dictionary.
     *
     * @param string $property
     * @return mixed|AMQPChannel
     */
    public function get(string $property): mixed
    {
        try {
            return $this->message->get($property);
        } catch (OutOfBoundsException) {
            return null;
        }
    }

    /**
     * Sets a property value
     *
     * @param string $name The property name (one of the property definition)
     * @param mixed $value The property value
     * @return Message
     */
    public function set(string $name, mixed $value): self
    {
        $this->message->set($name, $value);
        return $this;
    }

    /**
     * Check whether a property exists in the 'properties' dictionary
     * or if present - in the 'delivery_info' dictionary.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->message->has($name);
    }

    /**
     * Used channel
     *
     * @return AMQPChannel|null
     */
    public function channel(): ?AMQPChannel
    {
        return $this->message->getChannel();
    }

    /**
     * Check if message is redelivered
     *
     * @return bool
     */
    public function isRedelivered(): bool
    {
        return (bool) $this->message->isRedelivered();
    }

    /**
     * The exchange name that routed this message
     *
     * @return string|null
     */
    public function exchange(): ?string
    {
        return $this->message->getExchange();
    }

    /**
     * Routing key string used on topic exchanges
     *
     * @return string|null
     */
    public function routingKey(): ?string
    {
        return $this->message->getRoutingKey();
    }

    /**
     * consumerTag
     *
     * @return string|null
     */
    public function consumerTag(): ?string
    {
        return $this->message->getConsumerTag();
    }

    /**
     * Sets message with consumer tag
     *
     * @param string $consumerTag
     * @return $this
     */
    public function withConsumerTag(string $consumerTag): self
    {
        $this->message->setConsumerTag($consumerTag);
        return $this;
    }

    /**
     * parsedBody
     *
     * @return mixed|object|string
     */
    public function parsedBody(): mixed
    {
        return $this->parsedContent;
    }

    /**
     * Detects content type
     *
     * @param mixed $content
     * @return string|null
     */
    private function detectContentType(mixed $content): ?string
    {
        if ($content instanceof JsonSerializable) {
            $encodeJson = json_encode($content);
            $this->parsedContent = is_string($encodeJson) ? json_decode($encodeJson) : null;
            $this->body = json_encode($content);
            return 'application/json';
        }

        if ($content instanceof Stringable) {
            $this->parsedContent = (string) $content;
            $this->body = $this->parsedContent;
            return 'text/plain';
        }

        return null;
    }

    protected function parseContent(): void
    {
        if (!$this->has(self::CONTENT_TYPE)) {
            $this->parsedContent = $this->body;
            return;
        }

        $regex = '/^.*(json).*$/i';
        if (!preg_match($regex, $this->get(self::CONTENT_TYPE))) {
            return;
        }

        $this->parsedContent = json_decode($this->message->getBody());
    }
}
