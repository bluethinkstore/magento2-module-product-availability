<?php
/**
 * Copyright © Bluethinkinc All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bluethinkinc\ProductAvailability\ViewModel;
 
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Bluethinkinc\ProductAvailability\Model\Config\Provider;
use Magento\Framework\Stdlib\DateTime\DateTime;
 
class ProductData implements ArgumentInterface
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
     * Get module status
     *
     * @return bool
     */
    public function moduleStatus(): bool
    {
        return $this->provider->getModuleStatus();
    }

    /**
     * Get config value of PDP message field
     *
     * @return string
     */
    public function configMessagePDP(): string
    {
        return $this->provider->getConfigMessagePDP();
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
     * Getting Current Date
     *
     * @return string
     */
    public function getCurrentDate(): string
    {
        return $this->dateTime->gmtDate('Y-m-d');
    }
}
