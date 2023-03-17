<?php
namespace Productflow\Adapter\Model\ResourceModel\Job\Order;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'prductflow_order_shipment_queue_collection';
	protected $_eventObject = 'order_shipment_queue_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Productflow\Adapter\Model\Job\Order', 'Productflow\Adapter\Model\ResourceModel\Job\Order');
	}

}