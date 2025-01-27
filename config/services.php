<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Amqp\Config\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slick\Di\Container;

$services = [];

$services[AMQPStreamConnection::class] = function (Container $container) {
    $settings = $container->get('settings');
    return new AMQPStreamConnection(
        $settings->get('amqp.server'),
        $settings->get('amqp.port'),
        $settings->get('amqp.user'),
        $settings->get('amqp.password')
    );
};

return $services;
