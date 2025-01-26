<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp;

use PhpAmqpLib\Wire\AMQPTable;

/**
 * MessageHeadersMethods
 *
 * @package Slick\Amqp
 */
trait MessageHeadersMethods
{

    /**
     * Retrieves the headers of the AMQP message.
     *
     * @return array<string, mixed> The headers of the AMQP message.
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Adds a header to the message.
     *
     * @param string $name The name of the header.
     * @param mixed $value The value of the header.
     * @return self Returns an instance of the class for method chaining.
     */
    public function withHeader(string $name, mixed $value): self
    {
        $this->headers[$name] = $value;
        $this->message->set(self::HEADERS, new AMQPTable($this->headers));
        return $this;
    }

    /**
     * Removes a header from the message if it exists.
     *
     * @param string $string
     * @return self Returns an instance of the class for method chaining.
     */
    public function withoutHeader(string $string): self
    {
        if (!array_key_exists($string, $this->headers)) {
            return $this;
        }

        unset($this->headers[$string]);
        $this->message->set(self::HEADERS, new AMQPTable($this->headers));
        return $this;
    }

    /**
     * Set multiple headers for the message.
     *
     * @param array<string, mixed> $headers An array of headers to set for the message.
     * @return self Returns an instance of the class for method chaining.
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        $this->message->set(self::HEADERS, new AMQPTable($this->headers));
        return $this;
    }
}
