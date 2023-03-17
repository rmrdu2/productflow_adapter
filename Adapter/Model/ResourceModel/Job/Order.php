<?php
namespace Productflow\Adapter\Model\ResourceModel\Job;


class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('prductflow_order_shipment_queue', 'id');
	}
	
}