<?php

namespace Productflow\Adapter\Api\Data;

/**
 * @api
 */
interface OrderItemInterface
{

    /**
     * Get Id
     *
     * @return string
     */
    public function getId();

    /**
     * Set Id
     *
     * @param string $id
     * @return $this
    */
    public function setId($id);

}