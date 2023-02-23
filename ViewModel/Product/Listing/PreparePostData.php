<?php

declare(strict_types=1);

namespace Bluethinkinc\ProductAvailability\ViewModel\Product\Listing;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Bluethinkinc\ProductAvailability\Model\Config\Provider;
use Bluethinkinc\ProductAvailability\Model\ProductCollection;

/**
 * Check is available add to compare.
 */
class PreparePostData implements ArgumentInterface
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @param Provider $provider
     * @param DateTime $dateTime
     * @param UrlHelper $urlHelper
     * @param LoggerInterface $logger
     * @param ProductCollection $model
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Provider $provider,
        DateTime $dateTime,
        UrlHelper $urlHelper,
        LoggerInterface $logger,
        ProductCollection $model,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->model = $model;
        $this->logger = $logger;
        $this->provider = $provider;
        $this->dateTime = $dateTime;
        $this->urlHelper = $urlHelper;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * Wrapper for the PostHelper::getPostData()
     *
     * @param string $url
     * @param array $data
     * @return array
     */
    public function getPostData(string $url, array $data = []): array
    {
        if (!isset($data[ActionInterface::PARAM_NAME_URL_ENCODED])) {
            $data[ActionInterface::PARAM_NAME_URL_ENCODED] = $this->urlHelper->getEncodedUrl();
        }
        return ['action' => $url, 'data' => $data];
    }

    /**
     * Load product and return the product_availability attribute
     *
     * @param String $sku
     * @return Product $product
     */
    public function getProductAttribute($sku)
    {
        return $this->productRepository->get($sku)->getProductAvailability();
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

    /**
     * Set products status to disabled
     *
     * @return void
     */
    public function disableProducts()
    {
        $productCollection = $this->model->getProductCollection();
        foreach ($productCollection as $collection) {
            try {
                $sku = $collection['sku'];
                $product = $this->productRepository->get($sku);
                $product->setStoreId(0);
                $product->setStatus(Status::STATUS_DISABLED);
                $product->save();
            } catch (Exception $e) {
                $this->logger->warning("ProductAvailability Module : " . $e->getMessage());
            }
        }
    }
}
