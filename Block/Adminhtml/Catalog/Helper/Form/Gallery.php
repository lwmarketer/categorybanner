<?php


namespace Lovevox\CatalogAttributes\Block\Adminhtml\Catalog\Helper\Form;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Registry;

class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var here you set your ui form
     */
    protected $formName = 'category_form';

    protected $_filePath = 'media/catalog/banner';

    /**
     * Gallery constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param \Magento\Framework\Data\Form $form
     * @param array $data
     * @param DataPersistorInterface|null $dataPersistor
     * @throws FileSystemException
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Registry $registry,
        \Magento\Framework\Data\Form $form,
        $data = [],
        DataPersistorInterface $dataPersistor = null
    )
    {
        parent::__construct($context, $storeManager, $registry, $form, $data, $dataPersistor);

        $this->dataPersistor = $dataPersistor ?: ObjectManager::getInstance()->get(DataPersistorInterface::class);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    /**
     * Get product images
     *
     * @return array|null
     */
    public function getImages()
    {
        $catalog_banner_images = $this->getDataObject()->getData('catalog_banner_images') ?: null;
        $data = [];
        $images = json_decode($catalog_banner_images, true);
        if ($images) {
            foreach ($images as $key => $image) {
                try {
                    $fileHandler = $this->mediaDirectory->stat('catalog/banner' . $image);
                    $size = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    $size = 0;
                }

                $data[] = [
                    'position' => $key,
                    'file' => $image,
                    'media_type' => 'image',
                    'value_id' => $key,
                    'size' => $size,
                    'url' => $this->storeManager->getStore()->getBaseUrl() . $this->_filePath . $image
                ];
            }
        }

        return json_encode($data);
    }


    /**
     * Retrieve data object related with form
     *
     * @return ProductInterface|Product
     */
    public function getDataObject()
    {
        return $this->registry->registry('current_category');
    }

}
