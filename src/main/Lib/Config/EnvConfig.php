<?php

namespace Chen\Xhgui\Lib\Config;

use Chen\Xhgui\Contract\Config;

class EnvConfig implements Config
{
    protected $env = [];
    
    /**
     * @param array $env
     */
    public function __construct(array $env)
    {
        $this->env = $env;
    }
    
    public function get($name, $default = null)
    {
        
        return  isset($this->env[$name]) ? $this->env[$name] : $default;
    }
    
}