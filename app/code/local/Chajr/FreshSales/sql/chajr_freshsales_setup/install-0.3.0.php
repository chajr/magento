<?php

/** @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$entityTypeId = (int)$installer->getEntityTypeId('customer');
$attributeSetId = (int)$installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = (int)$installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute(
    $entityTypeId,
    'customer_freshsales_id',
    [
        'type' => 'int',
        'label' => 'Customer FreshSales ID',
        'input' => 'text',
        'forms' => ['adminhtml_customer'],
        'required' => false,
        'visible' => 1,
        'position' => 110,
        'default' => 'Inactive',
    ]
);

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'customer_freshsales_id',
    100
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'customer_freshsales_id');
$oAttribute->setData('used_in_forms', ['adminhtml_customer']);
$oAttribute->save();

$installer->endSetup();
