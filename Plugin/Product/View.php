<?php
/**
 * Copyright © Bluethinkinc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bluethinkinc\ProductAvailability\Plugin\Product;

use Exception;
use Bluethinkinc\ProductAvailability\ViewModel\ProductData;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Controller\Product\View as MagentoView;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class View
{
    /**
     * Constructor
     *
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param ProductData $viewModel
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ProductData $viewModel,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager,
        LoggerInterface $logger
    ) {
        $this->viewModel = $viewModel;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * Disable product which exceeded availability of product
     *
     * @param MagentoView $subject
     * @param mixed $result
     * @return Redirect|mixed $resultRedirect|$result
     */
    public function afterExecute(MagentoView $subject, $result)
    {
        $isModuleEnabled = $this->viewModel->moduleStatus();

        if ($isModuleEnabled) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            $productId = (int) $subject->getRequest()->getParam('id');

            // Check whether product is available or not
            $currentDate = $this->viewModel->getCurrentDate();
            $product = $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());
            $sku = $product->getSku();
            $availabilityOfProduct = $product->getData('product_availability');
            if ($availabilityOfProduct !== null) {
                $availabilityOfProduct = date("Y-m-d", strtotime($availabilityOfProduct));
                if ($currentDate > $availabilityOfProduct) {
                    $product = $this->productRepository->get($sku, true, 0, true);
                    try {
                        $product->setStatus(Status::STATUS_DISABLED);
                        $this->productRepository->save($product);
                        $this->messageManager->addNotice(__("This product is no longer available."));
                        $resultRedirect->setPath('/');
                        return $resultRedirect;
                    } catch (Exception $e) {
                        $this->logger->warning("ProductAvailability Module : " . $e->getMessage());
                    }
                }
            }
        }
        return $result;
    }
}
