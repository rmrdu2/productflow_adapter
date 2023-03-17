<?php

namespace Productflow\Adapter\Model\Data;

class OrderData
{
    
  public $data;

  /**
   * Gets partners array.
   *
   * @return void
   */
  function setData($data) {
    $this->data = $data;
  }

  /**
   * Gets partners array.
   *
   * @return \Productflow\Adapter\Model\Data\Order\OrderData
   */
  function getData() {
    return $this->data;
  }    
}