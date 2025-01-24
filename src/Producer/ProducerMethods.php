<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp\Producer;

use Slick\Amqp\Producer;

/**
 * ProducerMethods
 *
 * @package Slick\Amqp\Producer
 */
trait ProducerMethods
{
    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    /**
     * @var array<string, mixed>
     */
    protected static array $defaultOptions = [
        Producer::OPT_PASSIVE => false,
        Producer::OPT_DURABLE => false,
        Producer::OPT_AUTO_DELETE => true,
        Producer::OPT_INTERNAL => false,
        Producer::OPT_NOWAIT => false,
        Producer::OPT_ARGUMENTS => [],
        Producer::OPT_TICKET => null
    ];

    /**
     * Merges provided options
     *
     * @param array<string, mixed>|null $options
     * @return self
     */
    protected function mergeOptions(?array $options = []): self
    {
        $this->options = array_merge(self::$defaultOptions, $this->options, $options ?? []);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPassive(): bool
    {
        return (bool) $this->options[Producer::OPT_PASSIVE];
    }

    /**
     * @inheritDoc
     */
    public function isDurable(): bool
    {
        return (bool) $this->options[Producer::OPT_DURABLE];
    }

    /**
     * @inheritDoc
     */
    public function isAutoDelete(): bool
    {
        return (bool) $this->options[Producer::OPT_AUTO_DELETE];
    }

    /**
     * Exchange/Producer options
     *
     * @return array<string, mixed>
     */
    public function options(): array
    {
        return $this->options;
    }
}
