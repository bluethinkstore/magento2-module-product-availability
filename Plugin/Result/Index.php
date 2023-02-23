<?php
/**
 * Copyright © Bluethinkinc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bluethinkinc\ProductAvailability\Plugin\Result;

use Exception;
use Bluethinkinc\ProductAvailability\ViewModel\ProductData;
use Bluethinkinc\ProductAvailability\Model\ProductCollection;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\CatalogSearch\Controller\Result\Index as SearchIndex;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Index
{
    /**
     * Constructor
     *
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param ProductCollection $model
     * @param ProductData $viewModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ProductCollection $model,
        ProductData $viewModel,
        LoggerInterface $logger
    ) {
        $this->viewModel = $viewModel;
        $this->model = $model;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Before plugin on execute method for disabling the product
     *
     * @param SearchIndex $subject
     * @return void
     */
    public function beforeExecute(SearchIndex $subject)
    {
        $moduleEnabled = $this->viewModel->moduleStatus();

        if ($moduleEnabled) {
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
}
