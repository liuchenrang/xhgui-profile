<?php

namespace Chen\Xhgui\Lib\Request;

use Chen\Xhgui\Contract\RequestContext;
use Hyperf\HttpServer\Contract\RequestInterface;

class HyperfContext implements RequestContext
{
    use CommonContext;
    
    protected $req;
    
    /**
     * @param $req RequestInterface
     */
    public function __construct($req)
    {
        $this->req = $req;
    }
    
    public function getUri()
    {
        return $this->req->getRequestUri();
        // TODO: Implement getUri() method.
    }
    
    public function getReqTime()
    {
        $serverParams = $this->req->getServerParams();
        return isset($serverParams['request_time']) ? $serverParams['request_time'] : 0;
        // TODO: Implement getReqTime() method.
    }
    
    public function getReqTimeFloat()
    {
        $serverParams = $this->req->getServerParams();
        return isset($serverParams['request_time_float']) ? strval($serverParams['request_time_float']) : '0.0';
        
        // TODO: Implement getReqTimeFloat() method.
    }
    
    public function getQueryParams()
    {
        return $this->req->getQueryParams();
        // TODO: Implement getQueryParams() method.
    }
    
    public function getPostParams()
    {
        return $this->req->getQueryParams();
        
        // TODO: Implement getPostParams() method.
    }
    
    public function getDocRoot()
    {
        return __DIR__;
        // TODO: Implement getDocRoot() method.
    }
    
    
    public function getArgv()
    {
        return $_SERVER;
        // TODO: Implement getArgv() method.
    }
    
    public function getEnv()
    {
        return $_ENV;
        // TODO: Implement getEnv() method.
    }
    
    public function getServerInfo()
    {
        return $this->req->getServerParams();
        
        // TODO: Implement getServerInfo() method.
    }
    
}