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
use FastD\Framework\Dispatcher\Handle\AssetHandler;
use FastD\Framework\Dispatcher\Handle\ErrorHandler;
use FastD\Framework\Dispatcher\Handle\ForwardHandler;
use FastD\Framework\Dispatcher\Handle\HttpHandler;
use FastD\Framework\Dispatcher\Handle\LogHandler;
use FastD\Framework\Dispatcher\Handle\ShutdownHandler;
use FastD\Framework\Dispatcher\Handle\TestCaseHandle;
use FastD\Framework\Dispatcher\Handle\TplHandler;
use FastD\Framework\Dispatcher\Handle\UrlHandler;

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
        $this->setDispatch(new HttpHandler());
        $this->setDispatch(new TplHandler());
        $this->setDispatch(new AssetHandler());
        $this->setDispatch(new UrlHandler());
        $this->setDispatch(new ForwardHandler());
        $this->setDispatch(new TestCaseHandle());
        $this->setDispatch(new ErrorHandler());
        $this->setDispatch(new LogHandler());
        $this->setDispatch(new ShutdownHandler());
    }

    /**
     * @param DispatchInterface $dispatchInterface
     */
    public function setDispatch(DispatchInterface $dispatchInterface)
    {
        $this->dispatchArray[$dispatchInterface->getName()] = $dispatchInterface;
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
     * @param array $parameters
     * @return mixed
     */
    public function dispatch($name, array $parameters = [])
    {
        return $this->getDispatch($name)->dispatch($parameters);
    }
}