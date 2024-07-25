<?php

namespace Chen\Xhgui\Lib\Request;

trait CommonContext
{
    public function formatUrl($url)
    {
        return preg_replace('/\=\d+/', '', $url);
        // TODO: Implement formatUrl() method.
    }
}