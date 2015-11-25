<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/25
 * Time: 下午11:40
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Dispatcher;

use FastD\Framework\Container\ContainerAware;
use FastD\Framework\Kernel\Handle\HttpHandler;
use FastD\Framework\Kernel\Handle\TplHandler;

class Dispatcher extends ContainerAware
{
    protected $dispatchArray = [];

    public function __construct()
    {
        $this->dispatchArray['http.handle'] = new HttpHandler();
        $this->dispatchArray['event.tpl'] = new TplHandler();
    }

    public function setDispatch(DispatchInterface $dispatchInterface)
    {
        $this->dispatchArray[] = $dispatchInterface;
    }

    public function getDispatch($name)
    {
        return $this->dispatchArray[$name];
    }
}