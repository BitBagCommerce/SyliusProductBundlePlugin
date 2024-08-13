<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Grid\Filter;

use BitBag\SyliusProductBundlePlugin\Repository\ProductBundleRepositoryInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filter\BooleanFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

final class IsBundleFilter implements FilterInterface
{
    public function __construct(private ProductBundleRepositoryInterface $productBundleRepository)
    {
    }

    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $productBundleIds = $this->productBundleRepository->getProductIds();

        switch ($data) {
            case BooleanFilter::TRUE:
                $dataSource->restrict($dataSource->getExpressionBuilder()->in('id', $productBundleIds));

                break;
            case BooleanFilter::FALSE:
                $dataSource->restrict($dataSource->getExpressionBuilder()->notIn('id', $productBundleIds));

                break;
        }
    }
}
