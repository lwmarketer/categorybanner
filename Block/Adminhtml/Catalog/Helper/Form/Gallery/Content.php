<?php


namespace Lovevox\CatalogAttributes\Block\Adminhtml\Catalog\Helper\Form\Gallery;


use Magento\Backend\Block\DataProviders\ImageUploadConfig as ImageUploadConfigDataProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\MediaStorage\Helper\File\Storage\Database;

class Content extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{

    /**
     * @var string
     */
    protected $_template = 'Lovevox_CatalogAttributes::helper/gallery.phtml';

    /**
     * @var ImageUploadConfigDataProvider
     */
    private $imageUploadConfigDataProvider;

    /**
     * @var Database
     */
    private $fileStorageDatabase;

    /**
     * Content constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param array $data
     * @param ImageUploadConfigDataProvider|null $imageUploadConfigDataProvider
     * @param Database|null $fileStorageDatabase
     * @param JsonHelper|null $jsonHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        array $data = [],
        ImageUploadConfigDataProvider $imageUploadConfigDataProvider = null,
        Database $fileStorageDatabase = null,
        ?JsonHelper $jsonHelper = null
    )
    {
        parent::__construct($context, $jsonEncoder, $mediaConfig, $data, $imageUploadConfigDataProvider, $fileStorageDatabase, $jsonHelper);
        $data['jsonHelper'] = $jsonHelper ?? ObjectManager::getInstance()->get(JsonHelper::class);
        $this->imageUploadConfigDataProvider = $imageUploadConfigDataProvider
            ?: ObjectManager::getInstance()->get(ImageUploadConfigDataProvider::class);
        $this->fileStorageDatabase = $fileStorageDatabase
            ?: ObjectManager::getInstance()->get(Database::class);
    }

    protected function _prepareLayout()
    {
        $this->addChild(
            'uploader',
            \Magento\Backend\Block\Media\Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );


        /* here set you upload Controller */
        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('catalogattributes/category_banner/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );
    }


    public function getImageTypes()
    {
        return [];
    }


    /**
     * Retrieve media attributes
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        return [];
    }
}
