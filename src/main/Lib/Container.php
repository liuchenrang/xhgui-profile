<?php

namespace Chen\Xhgui\Lib;

use Chen\Xhgui\Contract\Config;
use Chen\Xhgui\Contract\Logger;
use Chen\Xhgui\Contract\RequestContext;
use Chen\Xhgui\Contract\Saver;
use Chen\Xhgui\Lib\Config\EnvConfig;
use Chen\Xhgui\Lib\Logger\PhpLogger;
use Chen\Xhgui\Lib\Logger\PsrLogger;
use Chen\Xhgui\Lib\Request\HyperfContext;
use Chen\Xhgui\Lib\Request\PhpFpmContext;
use Chen\Xhgui\Lib\Saver\HttpSaver;
use Psr\Log\LoggerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

class Container
{
    protected $logger;
    protected $request;
    protected $config;
    protected $saver;
    
    /**
     * @param $logger
     * @param $request
     * @param $config
     * @param $saver
     */
    public function __construct($logger, $request, $config, $saver)
    {
        $this->logger = $logger;
        $this->request = $request;
        $this->config = $config;
        $this->saver = $saver;
    }
    
    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * @return RequestContext
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * @return Saver
     */
    public function getSaver()
    {
        return $this->saver;
    }
    
    /**
     * @notes:
     * @return Container
     * @author: chen
     * @date: 2024/7/25
     */
    public static function buildFPM()
    {
        $envConfig = new EnvConfig($_ENV);
        $phpLogger = new PhpLogger($envConfig);
        $saver = new HttpSaver($envConfig, $phpLogger);
        $req = new PhpFpmContext($envConfig, $phpLogger);
        return new Container($phpLogger, $req, $envConfig, $saver);
    }
    
    /**
     * @notes:
     * @author: chen
     * @date: 2024/7/25
     * @param $configData
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @return Container
     */
    public static function buildHyperf($configData, $logger, $request)
    {
        $envConfig = new EnvConfig($configData);
        $phpLogger = new PsrLogger($envConfig, $logger);
        $saver = new HttpSaver($envConfig, $phpLogger);
        $req = new HyperfContext($request);
        return new Container($phpLogger, $req, $envConfig, $saver);
    }
}