<?php

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BitBagSyliusProductBundlePlugin extends Bundle
{
    use SyliusPluginTrait;
}
