<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Amqp;

/**
 * Consumer
 *
 * @package Slick\Amqp
 */
interface Consumer
{
    const  OPT_PASSIVE     = 'passive';
    const  OPT_DURABLE     = 'durable';
    const  OPT_EXCLUSIVE   = 'exclusive';
    const  OPT_AUTO_DELETE = 'auto_delete';
    const  OPT_NOWAIT      = 'nowait';
    const  OPT_ARGUMENTS   = 'arguments';
    const  OPT_TICKET      = 'ticket';

    const  CONSUME_OPT_CONSUMER_TAG = 'consumer_tag';
    const  CONSUME_OPT_NO_LOCAL     = 'no_local';
    const  CONSUME_OPT_NO_ACK       = 'no_ack';
    const  CONSUME_OPT_EXCLUSIVE    = 'exclusive';
    const  CONSUME_OPT_NOWAIT       = 'nowait';
    const  CONSUME_OPT_TICKET       = 'ticket';
    const  CONSUME_OPT_ARGUMENTS    = 'arguments';


    /**
     * Queue is passive
     *
     * If set, the server will reply with Declare-Ok if the queue already exists with the same,
     * and raise an error if not. The client can use this to check whether a queue exists
     * without modifying the server state. When set, all other method fields except name and no-wait
     * are ignored. A declared with both passive and no-wait has no effect. Arguments are compared
     * for semantic equivalence.
     *
     * The client MAY ask the server to assert that a queue exists without creating the queue if not.
     * If the queue does not exist, the server treats this as a failure. Error code: not-found
     *
     * If not set and the queue exists, the server MUST check that the existing queue has the same
     * values for durable, exclusive, auto-delete, and arguments fields. The server MUST respond
     * with Declare-Ok if the requested queue matches these fields, and MUST raise a channel exception if not.
     *
     * @return bool
     */
    public function isPassive(): bool;

    /**
     * Durability (exchanges survive broker restart)
     *
     * If set when creating a new queue, the queue will be marked as durable. Durable queues
     * remain active when a server restarts. Non-durable queues (transient queues) are purged
     * if/when a server restarts. Note that durable queues do not necessarily hold persistent
     * messages, although it does not make sense to send persistent messages to a transient
     * queue.
     *
     * @return bool
     */
    public function isDurable(): bool;

    /**
     * Used only by current connection
     *
     * Exclusive queues may only be accessed by the current connection, and are deleted when
     * that connection closes. Passive declaration of an exclusive queue by other connections
     * are not allowed.
     *
     * The server MUST support both exclusive (private) and non-exclusive (shared) queues.
     * The client MAY NOT attempt to use a queue that was declared as exclusive by another
     * still-open connection. Error code: resource-locked
     *
     * @return bool
     */
    public function isExclusive(): bool;

    /**
     * Auto-delete (queue is deleted when last message is processed)
     *
     * If set, the queue is deleted when all consumers have finished using it. The last
     * consumer can be cancelled either explicitly or because its channel is closed. If
     * there was no consumer ever on the queue, it won't be deleted. Applications can
     * explicitly delete auto-delete queues using the Delete method as normal.
     *
     * The server MUST ignore the auto-delete field if the queue already exists.
     *
     * @return bool
     */
    public function isAutoDelete(): bool;

    /**
     * List of exchange options
     *
     * @return array<string, mixed>
     */
    public function options(): array;

    /**
     * Options used to declare exchange
     *
     * @return array<string, mixed>
     */
    public function exchangeOptions(): array;

    /**
     * Adds an exchange binding rule
     *
     * @param string|null $routingKey
     * @return mixed
     */
    public function bind(?string $routingKey = ''): mixed;

    /**
     * Handles message consuming
     *
     * @param callable $callable
     * @param array<string,mixed>|null $options
     */
    public function consume(callable $callable, ?array $options = []): void;

    /**
     * Sends an acknowledgment back to the AMQP server
     *
     * @param Message $message
     */
    public function acknowledge(Message $message): void;
}
