<?php

namespace Productflow\Adapter\Model\Data;

class Order 
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
   * @return void
   */
  function getData() {
    return $this->data;
  }    
}