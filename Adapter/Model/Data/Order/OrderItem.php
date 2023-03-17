<?php

namespace Productflow\Adapter\Model\Data\Order;

class OrderItem
{
    public $external_identifier;

    public $title;

    public $sku;

    public $quantity;

    public $price;

    public $fee_fixed;

    public $fbm;

    public $sbm;

    /**
     * Sets external_identifier.
     *
     * @return void
     */
    function setExternalIdentifier($external_identifier) {
      $this->external_identifier = $external_identifier;
    }
  
    /**
     * Gets external_identifier.
     *
     * @return string
     */
    function getExternalIdentifier() {
      return $this->external_identifier;
    }

    /**
     * Sets title.
     *
     * @return void
     */
    function setTitle($title) {
        $this->title = $title;
    }
    
    /**
     * Gets title.
     *
     * @return string
     */
    function getTitle() {
        return $this->title;
    }

    /**
     * Sets sku.
     *
     * @return void
     */
    function setSku($sku) {
        $this->sku = $sku;
    }
    
    /**
     * Gets sku.
     *
     * @return string
     */
    function getSku() {
        return $this->sku;
    }

    /**
     * Sets quantity.
     *
     * @return void
     */
    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
    
    /**
     * Gets quantity.
     *
     * @return string
     */
    function getQuantity() {
        return $this->quantity;
        
    }

    /**
     * Sets price.
     *
     * @return void
     */
    function setPrice($price) {
        $this->price = $price;
    }
    
    /**
     * Gets price.
     *
     * @return string
     */
    function getPrice() {
        return $this->price;
    }

    /**
     * Sets fee_fixed.
     *
     * @return void
     */
    function setFeeFixed($fee_fixed) {
        $this->fee_fixed = $fee_fixed;
    }
    
    /**
     * Gets fee_fixed.
     *
     * @return object
     */
    function getFeeFixed() {
        return $this->fee_fixed;
    }

    /**
     * Sets fbm.
     *
     * @return void
     */
    function setFbm($fbm) {
        $this->fbm = $fbm;
    }
    
    /**
     * Gets fbm.
     *
     * @return object
     */
    function getFbm() {
        return $this->fbm;
    }

    /**
     * Sets sbm.
     *
     * @return void
     */
    function setSbm($sbm) {
        $this->sbm = $sbm;
    }
    
    /**
     * Gets sbm.
     *
     * @return object
     */
    function getSbm() {
        return $this->sbm;
    }

    
}