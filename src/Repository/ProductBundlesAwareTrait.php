<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * @method QueryBuilder createListQueryBuilder(string $locale, $taxonId = null)
 */
trait ProductBundlesAwareTrait
{
    public function createSearchListQueryBuilder(string $locale, $taxonId = null): QueryBuilder
    {
        return $this->createListQueryBuilder($locale, $taxonId)
            ->leftJoin('o.productBundle', 'productBundle')
            ->leftJoin('productBundle.productBundleItems', 'productBundleItems');
    }
}
