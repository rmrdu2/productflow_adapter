<?php
namespace Productflow\Adapter\Model\Config\Job\Source;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                 'value' => $value,
                 'label' => $label,
             ];
        }

        return $result;
    }

    public function getOptions()
    {
        return [
            0 => 'Pending',
            1 => 'Processing',
            2 => 'Success',
            3 => 'Error'
        ];
    }
}
