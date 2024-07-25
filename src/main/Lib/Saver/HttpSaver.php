<?php

namespace Chen\Xhgui\Lib\Saver;

use Chen\Xhgui\Contract\Config;
use Chen\Xhgui\Contract\Saver;
use Chen\Xhgui\Lib\Container;

class HttpSaver implements Saver
{
    
    protected $config;
    protected $logger;
    
    public function __construct($config, $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }
    
    public function save($data)
    {
        $saveUrl = $this->config->get('XHGUI_CONFIG_SAVER_URL');
        $timeout = $this->config->get('XHGUI_CONFIG_SAVER_URL_TIME_OUT');
        if ($saveUrl) {
            $options = array(
                'http' => array(
                    'header' => "Content-type: application/json",
                    'method' => 'POST',
                    'content' => json_encode($data, true),
                    'timeout' => $timeout ? $timeout : 4,
                ),
            );
            $context = stream_context_create($options);
            $result = file_get_contents($saveUrl, false, $context);
            if ($result === false) {
                return [];
            }
            return json_decode($result, true);
        } else {
            $this->logger->debug('xhgui 没有配置采集地址，请配置环境变量 ');
        }
    }
    
}