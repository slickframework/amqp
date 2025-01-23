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
 * Producer
 *
 * @package Slick\Amqp
 */
interface Producer {

    const TYPE_DEFAULT = 'direct';
    const TYPE_DIRECT  = 'direct';
    const TYPE_FANOUT  = 'fanout';
    const TYPE_TOPIC   = 'topic';
    const TYPE_HEADERS = 'headers';

    const OPT_PASSIVE     = 'passive';
    const OPT_DURABLE     = 'durable';
    const OPT_AUTO_DELETE = 'auto_delete';
    const OPT_INTERNAL    = 'internal';
    const OPT_NOWAIT      = 'nowait';
    const OPT_ARGUMENTS   = 'arguments';
    const OPT_TICKET      = 'ticket';

    /**
     * Publish provided message to the AMQP Server
     *
     * @param Message $message
     * @param string|null $routingKey
     */
    public function publish(Message $message, ?string $routingKey = null): void;

    /**
     * Exchange passive
     *
     * If set, the server will reply with Declare-Ok if the exchange already exists
     * with the same name, and raise an error if not. The client can use this to check
     * whether an exchange exists without modifying the server state. When set, all
     * other method fields except name and no-wait are ignored. A declared with both
     * passive and no-wait has no effect. Arguments are compared for semantic equivalence.
     *
     * If set, and the exchange does not already exist, the server MUST raise a channel
     * exception with reply code 404 (not found).
     *
     * If not set and the exchange exists, the server MUST check that the existing
     * exchange has the same values for type, durable, and arguments fields. The
     * server MUST respond with Declare-Ok if the requested exchange matches these
     * fields, and MUST raise a channel exception if not.
     *
     * @return bool
     */
    public function isPassive(): bool;

    /**
     * Durability (exchanges survive broker restart)
     *
     * If set when creating a new exchange, the exchange will be marked as durable.
     * Durable exchanges remain active when a server restarts. Non-durable exchanges
     * (transient exchanges) are purged if/when a server restarts.
     *
     * @return bool
     */
    public function isDurable(): bool;

    /**
     * Auto-delete (exchange is deleted when last queue is unbound from it)
     *
     * If set, the exchange is deleted when all queues have finished using it.
     *
     * The server SHOULD allow for a reasonable delay between the point when it determines
     * that an exchange is not being used (or no longer used), and the point when it deletes
     * the exchange. At the least it must allow a client to create an exchange and then bind
     * queue to it, with a small but non-zero delay between these two actions.
     *
     * The server MUST ignore the auto-delete field if the exchange already exists.
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
}
