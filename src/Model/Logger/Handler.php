<?php

namespace SajidPatel\OrderEmail\Model\Logger;

use Magento\Framework\App\Filesystem\DirectoryList;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    const LOG_FILE_NAME = 'returns.log';

    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = self::LOG_FILE_NAME;

    public function __construct(
        \Magento\Framework\Filesystem\DriverInterface $filesystem,
        $filePath = null
    ) {
        $logDir = DIRECTORY_SEPARATOR . DirectoryList::getDefaultConfig()[DirectoryList::LOG][DirectoryList::PATH];
        $this->fileName = $logDir . DIRECTORY_SEPARATOR . self::LOG_FILE_NAME;

        parent::__construct($filesystem, $filePath, $this->fileName);
    }
}
