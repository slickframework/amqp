<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    "amqp" => [
        "server" => $_ENV["AMQP_SERVER"] ?? 'localhost',
        "port" => $_ENV["AMQP_PORT"] ?? 5672,
        "user" => $_ENV["AMQP_USER"] ?? 'guest',
        "password" => $_ENV["AMQP_PASSWORD"] ?? 'guest',
    ]
];
