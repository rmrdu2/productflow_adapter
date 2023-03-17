<?php

namespace Productflow\Adapter\Model\Data\Order;

class Address
{
    public $first_name;

    public $last_name;

    public $street_name;

    public $house_number;

    public $house_number_addition;

    public $zip_code;

    public $city;

    public $country_code;

    public $company_name;

    /**
     * Sets first_name.
     *
     * @return void
     */
    function setFirstName($first_name) {
      $this->first_name = $first_name;
    }
  
    /**
     * Gets currency_code.
     *
     * @return string
     */
    function getFirstName() {
      return $this->first_name;
    }

    /**
     * Sets last_name.
     *
     * @return void
     */
    function setLastName($last_name) {
        $this->last_name = $last_name;
    }
    
    /**
     * Gets last_name.
     *
     * @return string
     */
    function getLastName() {
        return $this->last_name;
    }

    /**
     * Sets street_name.
     *
     * @return void
     */
    function setStreetName($street_name) {
        $this->street_name = $street_name;
    }
    
    /**
     * Gets street_name.
     *
     * @return string
     */
    function getStreetName() {
        return $this->street_name;
    }

    /**
     * Sets house_number.
     *
     * @return void
     */
    function setHousenumber($house_number) {
        $this->house_number = $house_number;
    }
    
    /**
     * Gets house_number.
     *
     * @return string
     */
    function getHousenumber() {
        return $this->house_number;
        
    }

    /**
     * Sets house_number_addition.
     *
     * @return void
     */
    function setHousenumberAddition($house_number_addition) {
        $this->house_number_addition = $house_number_addition;
    }
    
    /**
     * Gets house_number_addition.
     *
     * @return string
     */
    function getHousenumberAddition() {
        return $this->house_number_addition;
    }

    /**
     * Sets zip_code.
     *
     * @return void
     */
    function setZipCode($zip_code) {
        $this->zip_code = $zip_code;
    }
    
    /**
     * Gets zip_code.
     *
     * @return object
     */
    function getZipCode() {
        return $this->zip_code;
    }

    /**
     * Sets city.
     *
     * @return void
     */
    function setCity($city) {
        $this->city = $city;
    }
    
    /**
     * Gets city.
     *
     * @return object
     */
    function getCity() {
        return $this->city;
    }

    /**
     * Sets country_code.
     *
     * @return void
     */
    function setCountryCode($country_code) {
        $this->country_code = $country_code;
    }
    
    /**
     * Gets country_code.
     *
     * @return object
     */
    function getCountryCode() {
        return $this->country_code;
    }

    /**
     * Sets company_name.
     *
     * @return void
     */
    function setCompanyName($company_name) {
        $this->company_name = $company_name;
    }
    
    /**
     * Gets company_name.
     *
     * @return object
     */
    function getCompanyName() {
        return $this->company_name;
    }

    
      
}