<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp\Consumer;

use Slick\Amqp\Consumer;
use Slick\Amqp\Producer\BasicProducer;

/**
 * ConsumerMethods
 *
 * @package Slick\Amqp\Consumer
 */
trait ConsumerMethods
{
    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    /** @var array<string, mixed> */
    protected array $consumeOptions = [];

    /** @var array<string, mixed> */
    protected array $exchangeOptions = [];

    /**
     * @var array<string, mixed>
     */
    protected static array $defaultOptions = [
        Consumer::OPT_PASSIVE => false,
        Consumer::OPT_DURABLE => false,
        Consumer::OPT_EXCLUSIVE => false,
        Consumer::OPT_AUTO_DELETE => true,
        Consumer::OPT_NOWAIT => false,
        Consumer::OPT_ARGUMENTS => [],
        Consumer::OPT_TICKET => null
    ];

    /** @var array<string, mixed>  */
    protected static array $defaultConsumeOpt = [
        Consumer::CONSUME_OPT_CONSUMER_TAG => '',
        Consumer::CONSUME_OPT_NO_LOCAL => false,
        Consumer::CONSUME_OPT_NO_ACK => true,
        Consumer::CONSUME_OPT_EXCLUSIVE => false,
        Consumer::CONSUME_OPT_NOWAIT => false,
        'callback' => null,
        Consumer::CONSUME_OPT_TICKET => null,
        Consumer::CONSUME_OPT_ARGUMENTS=> [],
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
     * Merges consume options
     *
     * @param array<string, mixed> $options
     * @return self|$this
     */
    protected function mergeConsumeOptions(array $options): self
    {
        $this->consumeOptions = array_merge(self::$defaultConsumeOpt, $this->consumeOptions, $options);
        return $this;
    }

    /**
     * Merges exchange options
     *
     * @param array<string, mixed>|null $options
     * @return self
     */
    protected function mergeExchangeOptions(?array $options = []): self
    {
        $this->exchangeOptions = array_merge(
            BasicProducer::exchangeDefaultOptions(),
            $this->exchangeOptions,
            $options ?? []
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPassive(): bool
    {
        return (bool) $this->options[Consumer::OPT_PASSIVE];
    }

    /**
     * @inheritDoc
     */
    public function isDurable(): bool
    {
        return (bool) $this->options[Consumer::OPT_DURABLE];
    }

    /**
     * @inheritDoc
     */
    public function isExclusive(): bool
    {
        return (bool) $this->options[Consumer::OPT_EXCLUSIVE];
    }

    /**
     * @inheritDoc
     */
    public function isAutoDelete(): bool
    {
        return (bool) $this->options[Consumer::OPT_AUTO_DELETE];
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

    /**
     * Options used to declare exchange
     *
     * @return array<string, mixed>
     */
    public function exchangeOptions(): array
    {
        return $this->exchangeOptions;
    }
}
