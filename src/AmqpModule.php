<?php

/**
 * This file is part of amqp
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);

namespace Slick\Amqp;

use Dotenv\Dotenv;
use Slick\ModuleApi\Infrastructure\AbstractModule;
use Slick\ModuleApi\Infrastructure\Console\ConsoleModuleInterface;
use Slick\ModuleApi\Infrastructure\FrontController\WebModuleInterface;
use function Slick\ModuleApi\importSettingsFile;

/**
 * AmqpModule
 *
 * @package Slick\Amqp
 */
final class AmqpModule extends AbstractModule implements ConsoleModuleInterface, WebModuleInterface
{
    public static string $amqpCnfFile = APP_ROOT . '/config/modules/amqp.php';

    /**
     * Returns a description of enabling message exchange using AMQP and advanced producer/consumer configurations.
     *
     * @return string
     */
    public function description(): string
    {
        return "Enable message exchange using AMQP and advanced producer/consumer configurations.";
    }

    /**
     * @inheritdoc
     */
    public function settings(Dotenv $dotenv): array
    {
        $settingsFile = APP_ROOT .'/config/modules/amqp.php';
        $defaultSettings = [
            'amqp' => [
                'server' => "localhost",
                'port' => 5672,
                'user' => 'guest',
                'password' => 'guest'
            ]
        ];
        return importSettingsFile($settingsFile, $defaultSettings);
    }

    public function services(): array
    {
        return importSettingsFile(dirname(__DIR__) . '/config/services.php');
    }

    /**
     * @inheritDoc
     */
    public function onEnable(array $context = []): void
    {
        if (is_file(self::$amqpCnfFile)) {
            return;
        }

        file_put_contents(self::$amqpCnfFile, file_get_contents(dirname(__DIR__) . '/config/default_settings.php'));
        $this->verifyEnvironment();
    }

    private function verifyEnvironment(): void
    {
        $file = APP_ROOT . '/.env';
        $append = [
            '# AMQP ENVIRONMENT.',
            '# This will be used in the config/modules/amqp.php settings file.',
            '',
            '# AMQP_SERVER=localhost',
            '# AMQP_PORT=5672',
            '# AMQP_USER=guest',
            '# AMQP_PASSWORD=guest',
        ];

        if (!file_exists($file)) {
            file_put_contents($file, implode("\n", $append));
            return;
        }

        $content = file_get_contents($file);
        if (is_string($content) && str_contains($content, 'AMQP_SERVER=')) {
            return;
        }

        file_put_contents($file, $content . "\n" . implode("\n", $append));
    }
}
