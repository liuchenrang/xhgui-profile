<?php

namespace Chen\Xhgui\Lib\Logger;

use Chen\Xhgui\Contract\Config;
use Chen\Xhgui\Contract\Logger;
use Chen\Xhgui\Lib\Container;
use Psr\Log\LoggerInterface;

class PsrLogger implements Logger
{
    protected $isDebug = 0;
    protected $logger ;
    
    /**
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(Config $config, $logger)
    {
        $isDebug = $config->get('XHGUI_CONFIG_DEBUG');
        if ($isDebug) {
            ini_set('display_errors', 1);
        }
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }
    
    public function debug($msg)
    {
        if($this->isDebug){
            $this->logger->debug($msg);
        }
    }
}