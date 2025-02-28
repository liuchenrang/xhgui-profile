<?php

use Chen\Xhgui\Lib\Container;

/**
 * @param  $container Container
 */
function profile_start_func($container)
{
    
    $configer = $container->getConfig();
    $logger = $container->getLogger();
    $req = $container->getRequest();

    if (!$configer->get('XHGUI_CONFIG_SHOULD_RUN')) {
        $logger->debug('xhgui 关闭状态，不采集！ ');
        return false;
    }
    
    $extension = $configer->get('XHGUI_CONFIG_EXTENSION');
    if (!$extension) {
        $logger->debug('xhgui 环境初始化错误，没有设置要使用的扩展！ ');
        return false;
    }
    $percent = $configer->get('XHGUI_CONFIG_PERCENT');
    if (rand(1, 100) > $percent) {
        $logger->debug('xhgui 百分比采集忽略！ ');
        return false;
    }
    
    
    if (!extension_loaded('xhprof') && !extension_loaded('uprofiler') && !extension_loaded('tideways') && !extension_loaded('tideways_xhprof')) {
        $logger->debug('xhgui - either extension xhprof, uprofiler or tideways must be loaded');
        return false;
    }
    
    $filterPath = $configer->get('XHGUI_CONFIG_FILTER_PATH') ? explode(',', $configer->get('XHGUI_CONFIG_FILTER_PATH')) : [];
    if (is_array($filterPath) && in_array($req->getDocRoot(), $filterPath)) {
        return false;
    }
    
    
    $extension = $configer->get('XHGUI_CONFIG_EXTENSION');
    if ($extension == 'uprofiler' && extension_loaded('uprofiler')) {
        uprofiler_enable(UPROFILER_FLAGS_CPU | UPROFILER_FLAGS_MEMORY);
    } else if ($extension == 'tideways_xhprof' && extension_loaded('tideways_xhprof')) {
        tideways_xhprof_enable(TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_MEMORY_MU | TIDEWAYS_XHPROF_FLAGS_MEMORY_PMU | TIDEWAYS_XHPROF_FLAGS_CPU);
    } else if ($extension == 'tideways' && extension_loaded('tideways')) {
        tideways_enable(TIDEWAYS_FLAGS_CPU | TIDEWAYS_FLAGS_MEMORY);
        tideways_span_create('sql');
    } else if (function_exists('xhprof_enable')) {
        if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION > 4) {
            xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS);
        } else {
            xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
        }
    } else {
        $logger->debug('xhgui 你指定扩展不支持！ ');
        return false;
    }
    return true;
    
}

/**
 * @param  $container Container
 */
function profile_end_func($container)
{
    $config = $container->getConfig();
    $logger = $container->getLogger();
    $req = $container->getRequest();
    
    $extension = $config->get('XHGUI_CONFIG_EXTENSION');
    if ($extension == 'uprofiler' && extension_loaded('uprofiler')) {
        $data['profile'] = uprofiler_disable();
    } else if ($extension == 'tideways_xhprof' && extension_loaded('tideways_xhprof')) {
        $data['profile'] = tideways_xhprof_disable();
    } else if ($extension == 'tideways' && extension_loaded('tideways')) {
        $data['profile'] = tideways_disable();
        $sqlData = tideways_get_spans();
        $data['sql'] = array();
        if (isset($sqlData[1])) {
            foreach ($sqlData as $val) {
                if (isset($val['n']) && $val['n'] === 'sql' && isset($val['a']) && isset($val['a']['sql'])) {
                    $_time_tmp = (isset($val['b'][0]) && isset($val['e'][0])) ? ($val['e'][0] - $val['b'][0]) : 0;
                    if (!empty($val['a']['sql'])) {
                        $data['sql'][] = [
                            'time' => $_time_tmp,
                            'sql' => $val['a']['sql']
                        ];
                    }
                }
            }
        }
    } else {
        $data['profile'] = xhprof_disable();
    }
    
    // ignore_user_abort(true) allows your PHP script to continue executing, even if the user has terminated their request.
    // Further Reading: http://blog.preinheimer.com/index.php?/archives/248-When-does-a-user-abort.html
    // flush() asks PHP to send any data remaining in the output buffers. This is normally done when the script completes, but
    // since we're delaying that a bit by dealing with the xhprof stuff, we'll do it now to avoid making the user wait.
    ignore_user_abort(true);
    flush();
    
    
    $uri = $req->getUri() ? $req->getUri() : null;
    if (empty($uri) && $req->getArgv()) {
        $argv = $req->getArgv();
        $cmd = basename($argv[0]);
        $uri = $cmd . ' ' . implode(' ', array_slice($argv, 1));
    }
    
    $time = $req->getReqTime() ? $req->getReqTime() : time();
    $requestTimeFloat = $req->getReqTimeFloat() ? explode('.', $req->getReqTimeFloat()) : [];
    if (!isset($requestTimeFloat[1])) {
        $requestTimeFloat[1] = 0;
    }
    
    $requestTs = array('sec' => $time, 'usec' => 0);
    $requestTsMicro = array('sec' => $requestTimeFloat[0], 'usec' => $requestTimeFloat[1]);
    $data['meta'] = array(
        'url' => $uri,
        'SERVER' => $req->getServerInfo(),
        'get' => $req->getQueryParams(),
        'env' => $req->getEnv(),
        'simple_url' => $req->formatUrl($uri),
        'request_ts' => $requestTs,
        'request_ts_micro' => $requestTsMicro,
        'request_date' => date('Y-m-d', $time),
    );
    $saver = $container->getSaver();
    try {
        $result = $saver->save($data);
        $logger->debug("saver result: " . json_encode($result));
        
    } catch (Exception $e) {
        $logger->debug('xhgui - ' . $e->getMessage());
    }
}

/**
 * @param  $profile Container
 */
function profile_function($profile, $func)
{
    $startStatus = false;
    try {
        $startStatus = profile_start_func($profile);
    } catch (Exception $e) {
        $profile->getLogger()->debug($e->getMessage() . "\r\n" . $e->getTraceAsString());
    }
    $profile->getLogger()->debug('profile_function '.($startStatus?"enable":"disable"));
    
    $func();
    if ($startStatus) {
        try {
            profile_end_func($profile);
        } catch (Exception $e) {
            $profile->getLogger()->debug($e->getMessage() . "\r\n" . $e->getTraceAsString());
        }
    }
}