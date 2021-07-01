<?php
/**
 * Copyright © 2016 SW-THEMES. All rights reserved.
 */

namespace Lovevox\CatalogAttributes\Setup;

use Magento\Catalog\Model\Category\AttributeRepository;
use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;
    private $eavSetupFactory;

    private $objectManager;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory, EavSetupFactory $eavSetupFactory, \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
//        if (version_compare($context->getVersion(), '1.0.0', '<=')) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        /* cataglog attributes */
        $settingAttributes = [
            'delivery_from_days' => [
                'type' => 'int',
                'label' => 'Delivery From Days',
                'input' => 'select',
                'required' => false,
                'sort_order' => 10,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ],
            'delivery_to_days' => [
                'type' => 'int',
                'label' => 'Delivery To Days',
                'input' => 'select',
                'required' => false,
                'sort_order' => 20,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ],
            'is_show_hover' => [
                'type' => 'int',
                'label' => 'Is Show Hover',
                'input' => 'select',
                'source' => Boolean::class,
                'required' => false,
                'sort_order' => 30,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ],
            'is_show_colors' => [
                'type' => 'int',
                'label' => 'Is Show Colors',
                'input' => 'select',
                'source' => Boolean::class,
                'required' => false,
                'sort_order' => 30,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ],
            'color_sync' => [
                'type' => 'int',
                'label' => 'Relate Product Color Sync',
                'input' => 'select',
                'source' => Boolean::class,
                'required' => false,
                'sort_order' => 40,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ],
            'catalog_banner_images' => [
                'type' => 'text',
                'label' => 'catalog banner images',
                'required' => false,
                'sort_order' => 50,
                'used_in_product_listing' => 1,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            ],
        ];

        foreach ($settingAttributes as $item => $data) {
            $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, $item, $data);
        }
//        }

        /** @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository */
        $attributeRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductAttributeRepositoryInterface::class);
        //产品创建日期字段排序设置
        $attributeCodes = ['created_at'];
        foreach ($attributeCodes as $attributeCode) {
            $model = $attributeRepository->get($attributeCode);
            if ($model->getAttributeId()) {
                $model->setUsedForSortBy(true);
                $attributeRepository->save($model);
            }
        }
        $setup->endSetup();
    }
}
