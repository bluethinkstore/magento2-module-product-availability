<?php
/**
 * Copyright © Bluethinkinc All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Bluethinkinc\ProductAvailability\Model;

use Bluethinkinc\ProductAvailability\ViewModel\ProductData;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductCollection
{
    /**
     * Constructor
     *
     * @param CollectionFactory $productCollection
     * @param ProductData $viewModel
     */
    public function __construct(
        CollectionFactory $productCollection,
        ProductData $viewModel
    ) {
        $this->viewModel = $viewModel;
        $this->productCollection = $productCollection;
    }

    /**
     * Getting filtered product collection
     */
    public function getProductCollection()
    {
        $currentDate = $this->viewModel->getCurrentDate();

        $categoryProducts = $this->productCollection->create();
        $categoryProducts->addAttributeToSelect('*')
            ->addAttributeToFilter('product_availability', ['lteq' => $currentDate])
            ->addAttributeToFilter('status', ['eq' => 1]);
        return $categoryProducts->getData();
    }
}
