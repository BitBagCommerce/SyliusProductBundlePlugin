<?php

/*
 * This file is part of the Sylius ProductBundle Plugin package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use DMore\ChromeExtension\Behat\ServiceContainer\ChromeExtension;
use FriendsOfBehat\MinkDebugExtension\ServiceContainer\MinkDebugExtension;
use FriendsOfBehat\SuiteSettingsExtension\ServiceContainer\SuiteSettingsExtension;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use FriendsOfBehat\VariadicExtension\ServiceContainer\VariadicExtension;
use SyliusLabs\SuiteTagsExtension\ServiceContainer\SuiteTagsExtension;

$imports = ['tests/Behat/Resources/suites.php'];

// Import Sylius suites if available as PHP config (Behat 4.x requires PHP files, not YAML)
$syliusSuitesPath = 'vendor/sylius/sylius/src/Sylius/Behat/Resources/config/suites.php';
if (is_file(__DIR__ . '/' . $syliusSuitesPath)) {
    array_unshift($imports, $syliusSuitesPath);
}

return (new Config())
    ->import($imports)
    ->withProfile(
        (new Profile('default'))
            ->withExtension(new Extension(ChromeExtension::class))
            ->withExtension(new Extension(MinkDebugExtension::class, [
                'directory' => 'etc/build',
                'clean_start' => false,
                'screenshot' => true,
            ]))
            ->withExtension(new Extension(MinkExtension::class, [
                'files_path' => '%paths.base%/vendor/sylius/sylius/src/Sylius/Behat/Resources/fixtures/',
                'base_url' => 'http://127.0.0.1:8080/',
                'default_session' => 'symfony',
                'javascript_session' => 'chrome',
                'sessions' => [
                    'symfony' => [
                        'symfony' => null,
                    ],
                    'chrome' => [
                        'chrome' => [
                            'api_url' => 'http://127.0.0.1:9222',
                            'validate_certificate' => false,
                            'dom_wait_timeout' => 600,
                            'socket_timeout' => 600,
                        ],
                    ],
                ],
                'show_auto' => false,
            ]))
            ->withExtension(new Extension(SymfonyExtension::class, [
                'bootstrap' => 'vendor/sylius/test-application/config/bootstrap.php',
                'kernel' => [
                    'class' => 'Sylius\TestApplication\Kernel',
                    'environment' => 'test',
                ],
            ]))
            ->withExtension(new Extension(VariadicExtension::class))
            ->withExtension(new Extension(SuiteTagsExtension::class))
            ->withExtension(new Extension(SuiteSettingsExtension::class, [
                'paths' => ['features'],
            ])),
    )
;
