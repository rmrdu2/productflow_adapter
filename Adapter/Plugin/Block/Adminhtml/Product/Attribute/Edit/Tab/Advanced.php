<?php

namespace Productflow\Adapter\Plugin\Block\Adminhtml\Product\Attribute\Edit\Tab;

class Advanced
{/**
     * @var Yesno
     */
    protected $_yesNo;

    protected $_coreRegistry;

    /**
     * @param Magento\Config\Model\Config\Source\Yesno $yesNo
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Magento\Framework\Registry $registry
    ) {
        $this->_yesNo = $yesNo;
        $this->_coreRegistry = $registry;
    }

    /**
     * Get form HTML.
     *
     * @return string
     */
    public function aroundGetFormHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Advanced $subject,
        \Closure $proceed
    ) {
        $attributeObject = $this->_coreRegistry->registry('entity_attribute');
        $yesnoSource = $this->_yesNo->toOptionArray();
        $form = $subject->getForm();
        $fieldset = $form->getElement('advanced_fieldset');
        $fieldset->addField(
            'include_in_datamodel',
            'select',
            [
                'name' => 'include_in_datamodel',
                'label' => 'Include In Productflow Datamodel',
                'title' => 'Include In Datamodel',
                'note' => 'Choose "Yes" for allowing this field in Productflow datamodel.',
                'values' => $yesnoSource,
            ]
        );
        $form->setValues($attributeObject->getData());

        return $proceed();
    }
}
