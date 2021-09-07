<?php

namespace SajidPatel\OrderEmail\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Configuration path to enable module
     */
    const MODULE_ENABLED_PATH = 'sales/magento_rma/enabled';
    const UPLOAD_DIRECTORY = 'sales/magento_rma/upload_directory';
    const UPLOAD_FILE_TYPE = 'sales/magento_rma/type';
    const UPLOAD_FILE_SIZE = 'sales/magento_rma/size';
    const UPLOAD_FILE_COUNT = 'sales/magento_rma/count';
    const RETURN_AUTOMATE_PROCESS_IN = 'sales/magento_rma/automate_in';
    const RETURN_EXPIRES_IN = 'sales/magento_rma/expiry';
    const ALLOW_RETURN_ON_FRONTEND = 'sales/dhl_settings/allow_on_front';
    const RETURN_REFERENCE_SUFFIX = 'sales/dhl_settings/return_reference_suffix';
    const RETURN_TERMS_BLOCK = 'sales/dhl_settings/return_terms';
    const IS_DHL_RWS_ENABLED = 'sales/dhl_settings/enabled_dhl_rws';
    const SELLER_ID = 'sales/dhl_settings/seller_id';
    const ENCRYPTION_KEY = 'sales/dhl_settings/encryption_key';
    const DHL_RWS_URL = 'sales/dhl_settings/dhl_rws_url';
    const MYDHL_DEBUG_ENABLED = 'sales/my_dhl_settings/debug_enabled';
    const MYDHL_BASE_PATH = 'sales/my_dhl_settings/base_path';
    const MYDHL_USERNAME = 'sales/my_dhl_settings/username';
    const MYDHL_PASSWORD = 'sales/my_dhl_settings/password';
    const MYDHL_ACCOUNT_ID = 'sales/my_dhl_settings/account_id';
    const SHIPPING_RATE_INSTRUCTIONS_BLOCK = 'sales/my_dhl_settings/rma_rate_instructions';
    const SHIPPING_PAID_BLOCK = 'sales/my_dhl_settings/rma_shipping_paid';

    /*** @var \Magento\Framework\File\Size */
    protected $fileSize;

    /*** @var \Magento\Framework\Filesystem */
    protected $filesystem;

    /*** @var \Magento\Framework\Filesystem\Io\File */
    protected $fileConnection;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $fileConnection
     * @param \Magento\Framework\File\Size $fileSize
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $fileConnection,
        \Magento\Framework\File\Size $fileSize
    ) {
        $this->fileSize = $fileSize;
        $this->filesystem = $filesystem;
        $this->fileConnection = $fileConnection;

        parent::__construct($context);
    }

    /**
     * Is module enabled
     *
     * @param  int $store store id
     * @return boolean
     */
    public function moduleEnabled($store = null)
    {
        return (bool) $this->getConfig(self::MODULE_ENABLED_PATH, $store);
    }

    /**
     * Is module enabled
     *
     * @param  int $store store id
     * @return boolean
     */
    public function isDebugEnabled($store = null)
    {
        return (bool) $this->getConfig(self::MYDHL_DEBUG_ENABLED, $store);
    }

    /**
     * Check possibility to creating return on frontend
     *
     * @return bool
     */
    public function allowCreateOnFrontend()
    {
        return (bool) $this->getConfig(self::ALLOW_RETURN_ON_FRONTEND);
    }

    /**
     * Get return reference suffix
     *
     * @return string
     */
    public function getReturnReferenceSuffix()
    {
        return $this->getConfig(self::RETURN_REFERENCE_SUFFIX);
    }

    /**
     * Get return instruction message block id
     *
     * @return string
     */
    public function getReturnTermsBlock()
    {
        return $this->getConfig(self::RETURN_TERMS_BLOCK);
    }

    /**
     * @return string
     */
    public function getSellerId()
    {
        return trim($this->getConfig(self::SELLER_ID));
    }

    /**
     * @return string
     */
    public function getEncryptionKey()
    {
        return trim($this->getConfig(self::ENCRYPTION_KEY));
    }

    /**
     * @return string
     */
    public function isDhlRwsEnabled()
    {
        return trim($this->getConfig(self::IS_DHL_RWS_ENABLED));
    }

    /**
     * @return string
     */
    public function getDhlRwsUrl()
    {
        return trim($this->getConfig(self::DHL_RWS_URL));
    }

    /**
     * @return string
     */
    public function getUploadDirectory()
    {
        return $this->getConfig(self::UPLOAD_DIRECTORY);
    }

    /**
     * Get max size of file
     *
     * @return int
     */
    public function getFileMaxSize($inBytes = false)
    {
        $size = (int)$this->getConfig(self::UPLOAD_FILE_SIZE);
        $size *= 1024 * 1024;
        $size = min($size, $this->fileSize->getMaxFileSize());

        if (!$inBytes) {
            // $size /= (1024 * 1024);
            $size = $this->fileSize->getFileSizeInMb($size);
        }

        return $size;
    }

    /**
     * Get max count of files
     *
     * @return int
     */
    public function getFileMaxCount()
    {
        $count = (int)$this->getConfig(self::UPLOAD_FILE_COUNT);
        return max(1, $count);
    }

    /**
     * Get max days before returns are not acceptable
     *
     * @return int
     */
    public function getReturnsExpireInDays()
    {
        return (int) $this->getConfig(self::RETURN_EXPIRES_IN);
    }

    /**
     * Get max days before returns are not acceptable
     *
     * @return int
     */
    public function getAutomaticReturnsInDays()
    {
        return (int) $this->getConfig(self::RETURN_AUTOMATE_PROCESS_IN);
    }

    /**
     * Get max count of files
     *
     * @return int
     */
    public function getAcceptableFileType()
    {
        return $this->getConfig(self::UPLOAD_FILE_TYPE);
    }

    /**
     * @return string
     */
    public function getMediaUrl()
    {
        $baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
        return $baseUrl . $this->getUploadDirectory() . '/';
    }

    /**
     * @param $imageUrl
     * @return false|int
     */
    public function getImageSize($imageUrl)
    {
        return filesize($this->getDir(). $imageUrl);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getDir()
    {
        $rmaDirectory = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath($this->getUploadDirectory());

        return $this->createDestinationFolder($rmaDirectory) . '/';
    }

    /**
     * Create destination folder
     *
     * @param string $source
     * @return \Magento\Framework\File\Uploader
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function createDestinationFolder(string $source)
    {
        if (!is_dir($source)) {
            $result = $this->fileConnection->mkdir($source);

            if (!$result) {
                $message = __('Folder could not be created %1', $source);
                throw new \Magento\Framework\Exception\FileSystemException(__($message));
            }
        }

        return $source;
    }

    /**
     * Get return instruction message block id
     *
     * @return string
     */
    public function getShippingRateInstructionsBlock()
    {
        return $this->getConfig(self::SHIPPING_RATE_INSTRUCTIONS_BLOCK);
    }

    /**
     * Get return instruction message block id
     *
     * @return string
     */
    public function getShippingRatePaidBlock()
    {
        return $this->getConfig(
            self::SHIPPING_PAID_BLOCK
        );
    }

    /**
     * @return string
     */
    public function getRmaBasePath()
    {
        return trim($this->getConfig(
            self::MYDHL_BASE_PATH
        ));
    }

    /**
     * @return string
     */
    public function getMyDhlUsername()
    {
        return trim($this->getConfig(
            self::MYDHL_USERNAME
        ));
    }

    /**
     * @return string
     */
    public function getMyDhlPassword()
    {
        return trim($this->getConfig(
            self::MYDHL_PASSWORD
        ));
    }

    /**
     * @return string
     */
    public function getMyDhlAccountId()
    {
        return trim($this->getConfig(
            self::MYDHL_ACCOUNT_ID
        ));
    }

    /**
     * Retrieve magento config value
     *
     * @param  string                                     $path
     * @param  string | int                               $store
     * @param  \Magento\Store\Model\ScopeInterface | null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }
}
