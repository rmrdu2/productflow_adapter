<?php

namespace Productflow\Adapter\Model\Data\Order;

class OrderData
{
    public $currency_code;

    public $placed_at;

    public $email;

    public $phone_number;

    public $billing_customer;

    public $shipping_customer;

    public $lines;

    /**
     * Sets currency_code.
     *
     * @return void
     */
    function setCurrencyCode($code) {
      $this->currency_code = $code;
    }
  
    /**
     * Gets currency_code.
     *
     * @return string
     */
    function getCurrencyCode() {
      return $this->currency_code;
    }

    /**
     * Sets Placed At.
     *
     * @return void
     */
    function setPalcedAt($date) {
        $this->placed_at = $date;
    }
    
    /**
     * Gets placed at.
     *
     * @return string
     */
    function getPalcedAt() {
        return $this->placed_at;
    }

    /**
     * Sets email.
     *
     * @return void
     */
    function setEmail($email) {
        $this->email = $email;
    }
    
    /**
     * Gets email.
     *
     * @return string
     */
    function getEmail() {
        return $this->email;
    }

    /**
     * Sets phone_number.
     *
     * @return void
     */
    function setPhoneNumber($phone_number) {
        $this->phone_number = $phone_number;
    }
    
    /**
     * Gets phone_number.
     *
     * @return string
     */
    function getPhoneNumber() {
    return $this->phone_number;
    }

    /**
     * Sets billing_customer.
     *
     * @return void
     */
    function setBillingCustomer($billing_customer) {
        $this->billing_customer = $billing_customer;
    }
    
    /**
     * Gets billing_customer.
     *
     * @return Productflow\Adapter\Model\Data\Order\Address
     */
    function getBillingCustomer() {
    return $this->billing_customer;
    }

    /**
     * Sets shipping_customer.
     *
     * @return void
     */
    function setShippingCustomer($shipping_customer) {
        $this->shipping_customer = $shipping_customer;
    }
    
    /**
     * Gets shipping_customer.
     *
     * @return Productflow\Adapter\Model\Data\Order\Address
     */
    function getShippingCustomer() {
        return $this->shipping_customer;
    }
      
    /**
     * Sets lines.
     *
     * @return void
     */
    function setLines($lines) {
        $this->lines = $lines;
    }
    
    /**
     * Gets lines.
     *
     * @return Productflow\Adapter\Model\Data\Order\OrderItem[]
     */
    function getLines() {
        return $this->lines;
    }
}