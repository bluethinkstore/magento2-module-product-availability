<?php
/**
 * Copyright © Bluethinkinc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bluethinkinc\ProductAvailability\ViewModel\Product;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Bluethinkinc\ProductAvailability\Model\Config\Provider;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Model\Product;

/**
 * Product options data view model
 */
class OptionsData implements ArgumentInterface
{
    /**
     * Constructor
     *
     * @param Provider $provider
     * @param DateTime $dateTime
     */
    public function __construct(
        Provider $provider,
        DateTime $dateTime
    ) {
        $this->provider = $provider;
        $this->dateTime = $dateTime;
    }

    /**
     * Returns options data array
     *
     * @param Product $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOptionsData(Product $product) : array
    {
        return [];
    }

    /**
     * Get module status
     *
     * @return bool
     */
    public function moduleStatus(): bool
    {
        return $this->provider->getModuleStatus();
    }

    /**
     * Getting Current Date
     *
     * @return string
     */
    public function getCurrentDate(): string
    {
        return $this->dateTime->gmtDate('Y-m-d');
    }

    /**
     * Get config value of PLP message field
     *
     * @return string
     */
    public function configMessagePLP(): string
    {
        return $this->provider->getConfigMessagePLP();
    }

    /**
     * Format date of availibility of the product
     *
     * @param string $availabilityOfProduct
     * @return null|string $availabilityOfProduct
     */
    public function getAvailabilityOfProduct($availabilityOfProduct): ?string
    {
        if ($availabilityOfProduct !== null) {
            $availabilityOfProduct = date("Y-m-d", strtotime($availabilityOfProduct));
        }
        if (($this->getCurrentDate() <= $availabilityOfProduct) && $availabilityOfProduct !== null) {
            $availabilityOfProduct = date("d-M-Y", strtotime($availabilityOfProduct));
            return $availabilityOfProduct;
        }
        return null;
    }
}
