<?php
namespace Chen\Xhgui\Contract;
interface RequestContext {
     /**
     * @return string
     */   
    public function getUri();
     /**
     * @return float
     */    
    public function getReqTime();
    
    /**
     * @notes: 1111.111
     * @author: chen
     * @date: 2024/7/25
     * @return string
     */
    public function getReqTimeFloat();
    /**
     * @return array
     */
    public function getQueryParams();
    public function getEnv();
    public function getServerInfo();
    /**
     * @return array
     */
    public function getPostParams();
    public function getDocRoot();
    
    public function formatUrl($url);
    
    /**
     * @notes:
     * @author: chen
     * @date: 2024/7/25
     * @return array
     */
    public function getArgv();
}