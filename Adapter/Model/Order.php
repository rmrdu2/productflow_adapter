<?php

namespace Productflow\Adapter\Model;

use Magento\Store\Model\StoreManagerInterface;
use Productflow\Adapter\Model\FormatJson\Order as FormatJson;

/**
 * @api
 *
 * @since 100.0.2
 */
class Order
{
    /**
     * @var FormatJson
     */
    private $formatFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var string
     */
    protected $accessToken = '';

    /**
     * @var string
     */
    protected $baseUrl = '';

    /**
     * @var string
     */
    protected $apiUrl = '';

    /**
     * @var string
     */
    protected $configurableProductApiUrl = '';

    /**
     * @var string
     */
    protected $storeId;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
    string $storeId,
    StoreManagerInterface $storeManager,
    FormatJson $formatFactory)
    {
        $this->storeManager = $storeManager;
        $this->formatFactory = $formatFactory;
        $storeId = $storeId ? $storeId : $this->storeManager->getStore()->getStoreId();
        $store = $this->storeManager->getStore($storeId)->getCode();
    }

    /**
     * Gets partners json.
     *
     * @return array
     */
    public function updateOrderStatus($job)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $orderObjectmanager = $objectManager->create('Magento\Sales\Model\Order');

        try {
            $orderData = $this->formatFactory->formatJson($job->getPayload());
            $incrementId = $orderData['order']['order_id'];
            $order = $orderObjectmanager->loadByIncrementId($incrementId);
            $tableName = "prductflow_order_shipment_queue";
            
            $sql = "Select * FROM " . $tableName." WHERE order_id = ".$incrementId;
            $result = $connection->fetchAll($sql);

            if(count($result) !== count($order->getAllItems())){
                return ['status' => 3, 'message' => 'Sub order added in queue'];
            }
            if (!$order->getId()) {
                return ['status' => 3, 'message' => 'Order not found'];
            }

            if ($orderData['order']['order_id'] == 'completed' && $order->canShip()) {
                $convertOrder = $objectManager->create('Magento\Sales\Model\Convert\Order');
                $shipment = $convertOrder->toShipment($order);

                // Loop through order items
                foreach ($order->getAllItems() as $orderItem) {
                    // Check if order item has qty to ship or is virtual
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }
                    $qtyShipped = $orderItem->getQtyToShip();
                    // Create shipment item with qty
                    $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                    // Add shipment item to shipment
                    $shipment->addItem($shipmentItem);
                }

                // Register shipment
                $shipment->register();

                $shipment->getOrder()->setIsInProcess(true);

                // Save created shipment and order
                $shipment->save();
                $shipment->getOrder()->save();

                // Send email
                $objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                            ->notify($shipment);
                $trackingNumber = "Track00000013";    
                $trackingdata = array(
                    'carrier_code' => 'custom',
                    'title' => 'Nedis',
                    'number' => $trackingNumber,
                );
                $track = $objectManager->create('Magento\Sales\Model\Order\Shipment\TrackFactory')->create()->addData($trackingdata);
                $shipment->addTrack($track);
                $shipment->save();
            }
        } catch (\Exception $e) {
            $return = ['status' => 3, 'message' => $e->getMessage()];
        }

        $job->setStatus($return['status']);
        $job->setMessages($return['message']);
        $job->save();
    }
    
    /**
     * Gets partners json.
     *
     * @return array
     */
    public function updateShipment($job)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $orderObjectmanager = $objectManager->create('Magento\Sales\Model\Order');
        $return['status'] = 3;
        $return['message'] = "Error";
        try {
            $orderData = $this->formatFactory->formatJson($job->getPayload());

            $lineItems = array_column($orderData['order']['lines'], 'external_identifier');

            $incrementId = $orderData['order']['external_identifiers'];
            $subOrder    = explode("-",$orderData['order']['external_identifiers']);
            $incrementId =  $subOrder[1];
            $shopReference = $subOrder[0];
            $trackingNumber = $orderData['track_and_trace'];
            $order = $orderObjectmanager->loadByIncrementId($incrementId);
            $tableName = "prductflow_order_shipment_queue";
            
            $sql = "Select * FROM " . $tableName." WHERE order_id = '".$incrementId."'";
            $result = $connection->fetchAll($sql);
            
            if(count($result) !== count($order->getAllItems())){
                return ['status' => 3, 'message' => 'Sub order added in queue'];
            }

            if (!$order->getId()) {
                $return = ['status' => 3, 'message' => 'Order not found'];
                $job->setStatus($return['status']);
                $job->setMessages($return['message']);
                $job->save();

                return true;
            }

            if ($order->canShip()) {
                $convertOrder = $objectManager->create('Magento\Sales\Model\Convert\Order');
                $shipment = $convertOrder->toShipment($order);

                // Loop through order items
                foreach ($order->getAllItems() as $orderItem) {
                    // Check if order item has qty to ship or is virtual
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }
                    
                    $qtyShipped = $orderItem->getQtyToShip();
                    // Create shipment item with qty
                    $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                    // Add shipment item to shipment
                    $shipment->addItem($shipmentItem);
                }

                // Register shipment
                $shipment->register();

                $shipment->getOrder()->setIsInProcess(true);

                // Save created shipment and order
                $shipment->save();
                $shipment->getOrder()->save();

                // Send email
                $objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                            ->notify($shipment);
                $trackingdata = array(
                    'carrier_code' => 'custom',
                    'title' => 'Nedis',
                    'number' => $trackingNumber,
                );
                $track = $objectManager->create('Magento\Sales\Model\Order\Shipment\TrackFactory')->create()->addData($trackingdata);
                
                $shipment->addTrack($track);
                $shipment->save();
                $return['status'] = 2;
                $return['message'] = "Sucess";
            }else{
                $return['status'] = 3;
                $return['message'] = "This order is already shipped";
            } 
        } catch (\Exception $e) {
            $return['status'] = 3;
            $return['message'] = $e->getMessage();
        }

        
        $job->setStatus($return['status']);
        $job->setMessages($return['message']);
        $job->save();
    }
}
