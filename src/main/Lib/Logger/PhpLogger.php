<?php

namespace Chen\Xhgui\Lib\Logger;

use Chen\Xhgui\Contract\Config;
use Chen\Xhgui\Contract\Logger;
use Chen\Xhgui\Lib\Container;

class PhpLogger implements Logger
{
    public $isDebug = 0;
    
    /**
     * @param int $isDebug
     */
    public function __construct(Config $config)
    {
        $isDebug = $config->get('XHGUI_CONFIG_DEBUG');
        if ($isDebug) {
            ini_set('display_errors', 1);
        }
        $this->isDebug = $isDebug;
    }
    
    public function debug($msg)
    {
        if($this->isDebug){
            error_log($msg);
        }
    }
}