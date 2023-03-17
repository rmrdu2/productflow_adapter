<?php

namespace Productflow\Adapter\Model\FormatJson;

use Magento\Store\Model\StoreManagerInterface;

/**
 * @api
 *
 * @since 100.0.2
 */
class Order
{
    protected $_helper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    protected $json;

    /**
     * @var array
     */
    private $requiredAttributes;

    public function __construct(
    StoreManagerInterface $storeManager,
    \Productflow\Adapter\Helper\Data $helper)
    {
        $this->storeManager = $storeManager;

        $this->_helper = $helper;
    }

    public function formatJson($payload)
    {
        $convertedArray = json_decode($payload, true);

        return $convertedArray;
    }
}
