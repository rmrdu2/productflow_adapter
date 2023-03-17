<?php

declare(strict_types=1);

namespace Productflow\Adapter\Model;
use Productflow\Adapter\Model\Data\Order;
use Productflow\Adapter\Model\Data\OrderData as OrderDetails; 
use Productflow\Adapter\Model\Data\Order\OrderData;
use Productflow\Adapter\Model\Data\Order\Address;
use Productflow\Adapter\Model\Data\Order\OrderItem;
use Productflow\Adapter\Model\FormatJson\Order as FormatJson;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;

class OrderManagement implements \Productflow\Adapter\Api\OrderManagementInterface
{

    protected $formatFactory;
    protected $itemFactory;
    protected $objectManager;
    protected $productModel;
    protected $taxHelper;
    private $productRepository;

    public function __construct(
    \Productflow\Adapter\Api\Data\OrderItemInterfaceFactory $itemFactory,// Instance of object manager
    FormatJson $formatFactory,
    ProductRepositoryInterface $productRepository
    )
    {
        $this->itemFactory = $itemFactory;
        $this->objectManager = ObjectManager::getInstance();
        $this->formatFactory = $formatFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOrders($storeId)
    {
        
        
        $orderCollectionFactory = $this->objectManager->get("\Magento\Sales\Model\ResourceModel\Order\CollectionFactory");
        $orderCollection = $orderCollectionFactory->create()
        ->addAttributeToSelect('entity_id')
        ->addAttributeToSelect('increment_id');
        //->addFieldToFilter(['method','status'],[['eq'=>'banktransfer'],['eq'=>'processing']]);

        if ($storeId) {
            $orderCollection = $orderCollection->addFieldToFilter('store_id', $storeId);
        }

        $orderCollection->getSelect()
        ->join(
            ['soa' => 'sales_order_address'],
            'main_table.entity_id = soa.parent_id',
            ['country_id', 'city', 'postcode']
        )->join(
            ['sop' => 'sales_order_payment'],
            'main_table.entity_id = sop.parent_id',
            ['method']
        )
        ->where(
        'soa.address_type=?',
        'billing');

        $orderCollection = $orderCollection->setOrder(
            'created_at',
            'desc'
        );

        $orders = [];
        $orderData = [];
        if ($orderCollection) {

            foreach ($orderCollection as $order) {
                $payment = $order->getPayment();
                $method = $payment->getMethodInstance();
                $methodTitle = $method->getTitle();
                foreach ($order->getAllVisibleItems() as $_item) {
                
                    if ($_item->isDeleted() || $_item->getParentItemId() ) {
                        continue;
                    }
                    $productId =  $_item->getProductId();  
                    //echo $productId;exit;
                    $product = $this->productRepository->getById($_item->getProductId());
                    if(!$product->getShopReference()){
                        continue;
                    }
                    $orderData[] = [
                        'id' => $product->getShopReference().'-'.$order->getIncrementId(),
                    ];
                } 
            }

            $order = new Order;
            $order->data = $orderData;
            return $order;
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOrders($storeId, $page = 1)
    {
        
        
        $orderCollectionFactory = $this->objectManager->get("\Magento\Sales\Model\ResourceModel\Order\CollectionFactory");
        $orderCollection = $orderCollectionFactory->create()
        ->addAttributeToSelect('entity_id')
        ->addAttributeToSelect('increment_id')
        ->addFieldToFilter(['method','status'],[['eq'=>'banktransfer'],['eq'=>'processing']]);

        if ($storeId) {
            $orderCollection = $orderCollection->addFieldToFilter('store_id', $storeId);
        }

        $orderCollection->getSelect()
        ->join(
            ['soa' => 'sales_order_address'],
            'main_table.entity_id = soa.parent_id',
            ['country_id', 'city', 'postcode']
        )
        ->join(
            ['sop' => 'sales_order_payment'],
            'main_table.entity_id = sop.parent_id',
            []
        )
        ->where(
        'soa.address_type=?',
        'billing');

        $orderCollection = $orderCollection->setOrder(
            'created_at',
            'desc'
        );

        $orderCollection->setPageSize(20);
        $orderCollection->setCurPage($page);
        $orderData = [];
        if ($orderCollection) {
            foreach ($orderCollection as $order) {

                $payment = $order->getPayment();
                $method = $payment->getMethodInstance();
                $methodTitle = $method->getCode();
                foreach ($order->getAllVisibleItems() as $_item) {
                
                    if ($_item->isDeleted() || $_item->getParentItemId() ) {
                        continue;
                    }
                    $productId =  $_item->getProductId();  
                    //echo $productId;exit;
                    $product = $this->productRepository->getById($_item->getProductId());
                    if(!$product->getShopReference()){
                        continue;
                    }
                    $orderData[] = [
                        'id' => $product->getShopReference().'-'.$order->getIncrementId(),
                    ];
                }    
            }

            $resultJson = $this->objectManager->get('\Magento\Framework\Controller\Result\JsonFactory')->create();
            $order = new Order;
            $order->data = $orderData;
            return $order;
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder($storeId, $orderId)
    {
        if ($orderId == '') {
            return [];
        }
        $subOrderReference = explode('-',$orderId);
        
        $orderId = $subOrderReference[1];
        $shopReference = $subOrderReference[0];

        
        $orderCollectionFactory = $this->objectManager->get("\Magento\Sales\Model\ResourceModel\Order\CollectionFactory");
        $orderCollection = $orderCollectionFactory->create()
        ->addAttributeToSelect('*')
        ->addFieldToFilter('increment_id', $orderId);

        $order = new OrderDetails;
        $order->data = $this->extractOrderDetails($storeId,$shopReference, $orderCollection);
        return $order;
    }
    
    /**
     * {@inheritdoc}
     */
    public function load($storeId, $orderId)
    {
        if (!$orderId) {
            return [];
        }

        
        $orderCollectionFactory = $this->objectManager->get("\Magento\Sales\Model\ResourceModel\Order\CollectionFactory");
        $orderCollection = $orderCollectionFactory->create()
        ->addAttributeToSelect('*')
        ->addFieldToFilter('increment_id', $orderId);

        $order = new OrderDetails;
        $order->data = $this->extractOrderDetails($storeId, $orderCollection);
        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOrderStatus($storeId = null)
    {
        
        $request = $this->objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $orderQueue = $this->objectManager->get("\Productflow\Adapter\Model\Job\OrderFactory")->create();
        $orderData = $this->formatFactory->formatJson($request->getContent());
        $subOrder = explode("-",$orderData['order']['order_id']);
        $orderId  =  $subOrder[1];
        $shopReference = $subOrder[0];
            
        $orderQueue->addData([
            'order_id' => $orderId,
            'sub_order_id' => $orderData['order']['order_id'],
            'payload' => $request->getContent(),
            'messages' => '',
        ]);
        $saveData = $orderQueue->save();
        if ($saveData) {
            $order = $this->objectManager->create("\Productflow\Adapter\Model\Order", ['storeManager' => $this->storeManager, 'storeId' => $storeId]);
            $order->updateOrderStatus($orderQueue);
            $this->response[] = ['status' => 1, 'message' => 'Successfully added'];

            return $this->response;
        }

        $this->response[] = ['status' => 1, 'message' => 'Successfully added'];

        return $this->response;
    }
    
    /**
     * {@inheritdoc}
     */
    public function updateShipment($storeId = null)
    {
        
        $request = $this->objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $orderQueue = $this->objectManager->get("\Productflow\Adapter\Model\Job\OrderFactory")->create();
        $orderData = $this->formatFactory->formatJson($request->getContent());
        $subOrder = explode("-",$orderData['order']['external_identifiers']);
        $orderId  =  $subOrder[1];
        $shopReference = $subOrder[0];
            
        $orderQueue->addData([
            'order_id' => $orderId,
            'sub_order_id' => $orderData['order']['external_identifiers'],
            'payload' => $request->getContent(),
            'messages' => '',
        ]);
        $saveData = $orderQueue->save();
       
        if ($saveData) {
            $order = $this->objectManager->create("\Productflow\Adapter\Model\Order", ['storeId' => $storeId]);
            $order->updateShipment($orderQueue);
            $this->response[] = ['status' => 1, 'message' => 'Successfully added'];

            return $this->response;
        }

        $this->response[] = ['status' => 1, 'message' => 'Successfully added'];

        return $this->response;
    }
    
    private function extractOrderDetails($storeId,$shopReference,$orderCollection)
    {
        if ($storeId) {
            $orderCollection->addFieldToFilter('store_id', $storeId);
        }
        $_product = $this->objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');
        $orderCollection->getSelect()
        ->join(
            ['soa' => 'sales_order_address'],
            'main_table.entity_id = soa.parent_id',
            [
                'firstname as billing_firstname',
                'lastname as billing_secondname',
                'telephone as billing_phonenumber',
                'country_id as billing_country',
                'street as billing_street',
                'city as billing_city',
                'postcode as billing_postcode',
                'company as billing_company',
                'vat_id as billing_vatid',
             ]
        )
        ->where(
        'soa.address_type=?',
        'billing');

        $orderCollection->getSelect()
        ->join(
            ['sob' => 'sales_order_address'],
            'main_table.entity_id = sob.parent_id',
            [
                'firstname as shipping_firstname',
                'lastname as shipping_secondname',
                'telephone as shipping_phonenumber',
                'country_id as shipping_country',
                'street as shipping_street',
                'city as shipping_city',
                'postcode as shipping_postcode',
                'company as shipping_company',
                'vat_id as shipping_vatid',
             ]
        )
        ->where(
        'sob.address_type=?',
        'shipping');
        $order = $orderCollection->getFirstItem();

        if (!$order->getId()) {
            return [];
        }
        $orderData = new OrderData;
        $orderData->currency_code = $order->getOrderCurrencyCode();
        $orderData->placed_at = $order->getCreatedAt();
        $orderData->email = $order->getCustomerEmail();
        $orderData->phone_number = "";
        $billingAddress = new Address;
        
        $billingAddress->first_name= $order->getBillingFirstname();
        $billingAddress->last_name= $order->getBillingSecondname();
        $billingAddress->street_name= $order->getBillingStreet();
        $billingAddress->house_number= '';
        $billingAddress->house_number_addition= '';
        $billingAddress->zip_code= $order->getBillingPostcode();
        $billingAddress->city= $order->getBillingCity();
        $billingAddress->country_code= $order->getBillingCountry();
        $billingAddress->company_name= $order->getBillingCompany();
        $billingAddress->vat_number= $order->getBillingVatid();
        
        $orderData->billing_customer = $billingAddress;
        $shippingAddress = new Address;
        
        $shippingAddress->first_name= $order->getShippingFirstname();
        $shippingAddress->last_name= $order->getShippingSecondname();
        $shippingAddress->street_name= $order->getShippingStreet();
        $shippingAddress->house_number= '';
        $shippingAddress->house_number_addition= '';
        $shippingAddress->zip_code= $order->getShippingPostcode();
        $shippingAddress->city= $order->getShippingCity();
        $shippingAddress->country_code= $order->getShippingCountry();
        $shippingAddress->company_name= $order->getShippingCompany();
        $shippingAddress->vat_number= $order->getShippingVatid();

        $orderData->shipping_customer = $shippingAddress;
        
        foreach ($order->getAllItems() as $item) {

            if ($item->isDeleted() || $item->getParentItemId() ) {
                continue;
            }
            $productId =  $item->getProductId();  
            //echo $productId;exit;
            $product = $this->productRepository->getById($item->getProductId());

            if($product->getShopReference() != $shopReference ){
                continue;
            }
            $orderitem = new OrderItem;
            

            $orderitem->external_identifier = $item->getProductId();
            $orderitem->title = $item->getName();
            $orderitem->sku = $item->getSku();
            $orderitem->quantity = $item->getQtyOrdered();
            $orderitem->price = ($item->getPriceInclTax()*$item->getQtyOrdered())*100;
            //$orderitem->price_including_tax = $item->getPriceInclTax()*100;
            $orderitem->fee_fixed = 50;
            $orderitem->fbm = false;
            $orderitem->sbm = false;

            $lines[] = $orderitem;
        }

        $orderData->lines = $lines;

        return $orderData;
    }
}
