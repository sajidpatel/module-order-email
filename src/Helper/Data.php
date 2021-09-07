<?php

namespace SajidPatel\OrderEmail\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_PRECISION = 2;
    const FORM_DATA_PARAM = 'rma_form_data';

    /*** @var \Magento\Customer\Model\Session */
    protected $session;

    /*** @var \Magento\Backend\Model\UrlInterface */
    protected $backendUrl;

    /*** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /*** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $priceCurrency;

    /*** @var \Magento\Framework\App\State */
    protected $state;

    /*** @var \Magento\Framework\App\ResourceConnection */
    protected $resourceConnection;

    /*** @var \Magento\Config\Model\Config */
    protected $config;

    /**
     * Storage of form data
     *
     * @var mixed
     */
    protected $formData = null;

    /*** @var \Ruroc\Rma\Helper\Config */
    protected $configHelper;

    /*** @var \Magento\Framework\Pricing\Helper\Data */
    protected \Magento\Framework\Pricing\Helper\Data $pricingHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Ruroc\Rma\Helper\Config $configHelper
     * @param \Magento\Config\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Ruroc\Rma\Helper\Config $configHelper,
        \Magento\Config\Model\Config $config
    ) {
        $this->session = $session;
        $this->backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->pricingHelper = $pricingHelper;
        $this->state = $state;
        $this->resourceConnection = $resourceConnection;
        $this->configHelper = $configHelper;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Get store name
     *
     * @return string
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Retrieve stored form data
     *
     * @param  null|string $key
     * @return mixed
     */
    public function getFormData($key = null)
    {
        if ($this->formData  === null) {
            $this->formData = $this->session
                ->getData(self::FORM_DATA_PARAM, false);
        }

        if ($key !== null) {
            return isset($this->formData[$key]) ? $this->formData[$key] : null;
        }

        return $this->formData;
    }

    /**
     * @return \Ruroc\Rma\Helper\Config
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * @param $item
     * @return float
     */
    public function calculateDifferenceInDays($item)
    {
        $now = time();
        $createdDate = strtotime($item->getCreatedAt());
        $datediff = $now - $createdDate;

        return ceil(abs($datediff / 86400));
    }

    /**
     * @param $price
     * @return float
     */
    public function convertToStorePrice($price)
    {
        return $this->priceCurrency->convert($price);
    }

    /**
     * Round price with precision
     *
     * @param float $price
     * @return float
     */
    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }
}
