<?php

namespace Chen\Xhgui\Contract;
interface Config
{
    /**
     * @return mixed
     */
    public function get($name,$default=null);
}