<?php
/**
 * Copyright © Bluethinkinc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bluethinkinc\ProductAvailability\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Provider to fetch config value
 */
class Provider
{
    public const XML_PATH_CONFIG_MSG_PDP = 'outofstock/options/message_pdp';

    public const XML_PATH_CONFIG_MSG_PLP = 'outofstock/options/message_plp';

    public const XML_PATH_ENABLE_DISABLE = 'outofstock/options/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Provider Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get Module Status from configuration
     *
     * @return bool
     */
    public function getModuleStatus(): bool
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_DISABLE, $storeScope);
    }

    /**
     * Get Config Message (which is displayed on PDP) from configuration
     *
     * @return string
     */
    public function getConfigMessagePDP(): string
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CONFIG_MSG_PDP, $storeScope);
    }

    /**
     * Get Config Message (which is displayed on PLP) from configuration
     *
     * @return string
     */
    public function getConfigMessagePLP(): string
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_CONFIG_MSG_PLP, $storeScope);
    }
}
