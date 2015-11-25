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

use FastD\Container\Container;
use FastD\Framework\Container\ContainerAware;
use FastD\Framework\Dispatcher\Handle\HttpHandler;
use FastD\Framework\Dispatcher\Handle\TplHandler;

/**
 * Class Dispatcher
 *
 * @package FastD\Framework\Dispatcher
 */
class Dispatcher extends ContainerAware
{
    /**
     * @var Dispatch[]
     */
    protected $dispatchArray = [];

    /**
     * Dispatcher constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->setContainer($container);
        $this->dispatchArray['http.handle'] = new HttpHandler();
        $this->dispatchArray['event.tpl'] = new TplHandler();
    }

    /**
     * @param DispatchInterface $dispatchInterface
     */
    public function setDispatch(DispatchInterface $dispatchInterface)
    {
        $this->dispatchArray[] = $dispatchInterface;
    }

    /**
     * @param $name
     * @return Dispatch
     */
    public function getDispatch($name)
    {
        return $this->dispatchArray[$name]->setContainer($this->getContainer());
    }

    /**
     * @param       $name
     * @param array $paramters
     * @return mixed
     */
    public function dispatch($name, array $paramters = [])
    {
        return $this->getDispatch($name)->dispatch($paramters);
    }
}