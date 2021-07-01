<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lovevox\CatalogAttributes\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Employ additional authorization logic when a category is saved.
 */
class CategoryMediaGallery implements ObserverInterface
{


    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $objectManager->get('\Psr\Log\LoggerInterface');
        /** @var CategoryInterface $category */
        $category = $observer->getEvent()->getData('category');
        if ($category) {
            /** @var \Magento\Framework\App\Request\Http $request */
            $request = $observer->getEvent()->getData('request');
            $galleries = $request->getParam('catalog');
            if (isset($galleries['images'])) {
                $data = [];
                foreach ($galleries['images'] as $gallery) {
                    if ($gallery['removed'] != 1 && !empty($gallery['file'])) {
                        $data[$gallery['position']] = $gallery['file'];
                    }
                }
                ksort($data);
                $category->setData('catalog_banner_images', json_encode($data));
                $logger->info("execute ==> info: " . json_encode($data));
            }
        }
    }
}
