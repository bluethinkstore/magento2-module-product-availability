<?php
/**
 * Copyright © Bluethinkinc All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bluethinkinc\ProductAvailability\Plugin\Category;

use Exception;
use Bluethinkinc\ProductAvailability\ViewModel\ProductData;
use Bluethinkinc\ProductAvailability\Model\ProductCollection;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Controller\Category\View as MagentoCategoryView;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class View
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
     * Set product status disabled
     *
     * @param MagentoCategoryView $subject
     * @return void
     */
    public function beforeExecute(MagentoCategoryView $subject)
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
