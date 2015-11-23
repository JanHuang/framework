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

use FastD\Http\Request;
use FastD\Routing\Route;
use FastD\Routing\Router;

class HttpHandler implements HandlerInterface
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

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
                return call_user_func_array([$controller, $action], $route->getParameters());
        }
    }

    public function handleHttpRequest(Request $request)
    {
        $route = $this->router->match($request->getPathInfo());

        return $this->dispatchEventCallback($route);
    }
}