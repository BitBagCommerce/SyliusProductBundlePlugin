<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Provider;

use BitBag\SyliusProductBundlePlugin\Command\AddProductBundleItemToCartCommand;
use Doctrine\Common\Collections\Collection;

interface AddProductBundleItemToCartCommandProviderInterface
{
    /** @return Collection<int, AddProductBundleItemToCartCommand> */
    public function provide(string $bundleCode, array $overwrittenVariants): Collection;
}
