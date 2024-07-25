<?php

namespace Chen\Xhgui\Lib\Request;

use Chen\Xhgui\Contract\RequestContext;

class PhpFpmContext implements RequestContext
{
    use CommonContext;
    
    protected function get($key)
    {
        return array_key_exists($key, $_SERVER) ? $_SERVER[$key] : null;
    }
    
    public function getUri()
    {
        return $this->get('REQUEST_URI');
    }
    
    
    public function getReqTime()
    {
        return $this->get('REQUEST_TIME');
        // TODO: Implement getReqTime() method.
    }
    
    public function getReqTimeFloat()
    {
        return $this->get('REQUEST_TIME_FLOAT') ?? '';
        // TODO: Implement getReqTimeFloat() method.
    }
    
    public function getQueryParams()
    {
        return $_GET ? $_GET : [];
        // TODO: Implement getQueryParams() method.
    }
    
    public function getEnv()
    {
        return $_ENV ? $_ENV : [];
    }
    
    public function getServerInfo()
    {
        return $_SERVER ? $_SERVER : [];
    }
    
    public function getPostParams()
    {
        return $_POST ? $_POST : [];
        
    }
    
    public function getDocRoot()
    {
        return $this->get('DOCUMENT_ROOT');
        // TODO: Implement getDocRoot() method.
    }
    
    
    public function getArgv()
    {
        return $_SERVER['argv'];
        // TODO: Implement getArgv() method.
    }
    
}