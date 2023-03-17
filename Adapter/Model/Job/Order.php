<?php
namespace Productflow\Adapter\Model\Job;

class Order extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prductflow_order_shipment_queue';

	protected $_cacheTag = 'prductflow_order_shipment_queue';

	protected $_eventPrefix = 'prductflow_order_shipment_queue';

	protected function _construct()
	{
		$this->_init('Productflow\Adapter\Model\ResourceModel\Job\Order');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}