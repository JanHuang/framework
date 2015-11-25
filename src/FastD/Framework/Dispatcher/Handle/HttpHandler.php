<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/19
 * Time: 下午11:39
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Kernel\Handle;

use FastD\Framework\Bundle\Events\Http\Event;
use FastD\Framework\Dispatcher\Dispatch;
use FastD\Http\Request;
use FastD\Routing\Route;

class HttpHandler extends Dispatch
{
    protected $container;

    public function dispatchEventCallback(Route $route)
    {
        $callback = $route->getCallback();
        switch (gettype($callback)) {
            case 'object':
            case 'closure':
                return $callback();
            case 'array':
                return call_user_func_array($callback, $route->getParameters());
            case 'string':
            default:
                list($controller, $action) = explode('@', $callback);
                $controller = str_replace(':', '\\', $controller);
                $controller = $this->container->set('request_callback', $controller)->singleton('request_callback');
                if ($controller instanceof Event) {
                    $controller->setContainer($this->container);
                }
                return call_user_func_array([$controller, $action], $route->getParameters());
        }
    }

    public function handleHttpRequest(Request $request)
    {
        $route = $this->container->singleton('kernel.routing')->match($request->getPathInfo());

        return $this->dispatchEventCallback($route);
    }

    public function getName()
    {
        return 'http.handle';
    }

    public function dispatch()
    {
        // TODO: Implement dispatch() method.
    }
}