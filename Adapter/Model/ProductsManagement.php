<?php

declare(strict_types=1);

namespace Productflow\Adapter\Model;

class ProductsManagement implements \Productflow\Adapter\Api\ProductsManagementInterface
{
    /**
     * {@inheritdoc}
     */
    public function postProducts($storeId = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $model = $objectManager->get("\Productflow\Adapter\Model\Job\ScheduleFactory")->create();
        $resultJson = $objectManager->get('\Magento\Framework\Controller\Result\JsonFactory')->create();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');

        $product = $request->getBodyParams();
        $model->addData([
            'job_code' => $this->getJobcode($product),
            'payload' => $request->getContent(),
            'status' => 'pending',
        ]);
        $saveData = $model->save();
        if ($saveData) {
            $product = $objectManager->create("\Productflow\Adapter\Model\Product", ['storeId' => $storeId]);
            $product->postProducts($model);
            $this->response[] = ['status' => 1, 'message' => 'Successfully added'];

            return $this->response;
        }

        $this->response[] = ['status' => 1, 'message' => 'Successfully added'];

        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function getJobcode($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productFactory = $objectManager->get('\Magento\Catalog\Model\ProductFactory')->create();

        if (!isset($product['identifiers']['sku'])) {
            return 'product_create';
        }

        $sku = $product['identifiers']['sku'];
        if ($productFactory->getIdBySku($sku)) {
            return 'product_update';
        }

        return 'product_create';
    }
}
