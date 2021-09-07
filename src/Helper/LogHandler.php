<?php

namespace SajidPatel\OrderEmail\Helper;

class LogHandler extends \Magento\Framework\App\Helper\AbstractHelper
{
    /*** @var ConfigurationHelper */
    protected $configHelper;

    /*** @var \Ruroc\Rma\Model\Logger\Logger */
    protected $logger;

    /**
     * LogHandler constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ruroc\Rma\Helper\Config $helper
     * @param \Ruroc\Rma\Model\Logger\Logger $logger
     * @param \Ruroc\Rma\Model\Logger\Handler $handler
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Config $helper,
        \Ruroc\Rma\Model\Logger\Logger $logger,
        \Ruroc\Rma\Model\Logger\Handler $handler
    ) {
        $this->configHelper = $helper;
        $this->logger = $logger;
        $this->logger->setHandlers([$handler]);

        parent::__construct($context);
    }

    /**
     * @param $message
     */
    public function info($message)
    {
        if ($this->configHelper->isDebugEnabled()) {
            $this->logger->info($message);
        }
    }

    /**
     * @param $message
     */
    public function error($message)
    {
        $this->logger->error($message);
    }

    /**
     * @param $message
     */
    public function crit($message)
    {
        $this->logger->crit($message);
    }
}
