<?php
namespace Interactivated\Customerreview\Logger;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger as MonologLogger;

/**
 * Class ErrorHandler
 */
class Handler extends BaseHandler
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = MonologLogger::INFO;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName = '/var/log/kiyoh.log';
}
