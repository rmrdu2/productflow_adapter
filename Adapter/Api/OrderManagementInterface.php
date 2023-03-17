<?php

declare(strict_types=1);

namespace Productflow\Adapter\Api;

interface OrderManagementInterface
{
    /**
    * Get all orders for a store.
    *
    * @param string $storeId The store ID.
    *
    * @return Productflow\Adapter\Model\Data\Order The orders data.
    */
    public function getAllOrders($storeId);

    /**
     * Get paginated orders for a store.
     *
     * @param string $storeId The store ID.
     * @param int $page The page number.
     *
     * @return Productflow\Adapter\Model\Data\Order The orders data.
     */
    public function getOrders($storeId, $page);

    /**
     * Get an order by ID for a store.
     *
     * @param string $storeId The store ID.
     * @param string $orderId The order ID.
     *
     * @return Productflow\Adapter\Model\Data\OrderData The order data.
     */
    public function getOrder($storeId, $orderId);

    /**
     * Load an order by ID and store ID.
     *
     * @param string $storeId The store ID.
     * @param string $orderId The order ID.
     *
     * @return Productflow\Adapter\Model\Data\OrderData The order data.
     */
    public function load($storeId, $orderId);


    /**
     * POST for Order Update api.
     *
     * @param string
     *
     * @return string
     */
    public function updateOrderStatus($storeId = null);
    
    /**
     * POST for Order Shipment Update api.
     *
     * @param string
     *
     * @return string
     */
    public function updateShipment($storeId = null);
}
