<?php

namespace Productflow\Adapter\Model\Data\Order;

class Item extends \Magento\Framework\Model\AbstractModel implements
    \Productflow\Adapter\Api\Data\OrderItemInterface 
{
    const KEY_NAME = 'id';


     public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    public function getId()
    {
        return $this->_getData(self::KEY_NAME);
    }


    /**
     * Set Id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::KEY_NAME, $id);
    }


}