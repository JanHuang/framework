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

namespace FastD\Framework\Dispatcher\Handle;

use FastD\Framework\Bundle\Events\Http\Event;
use FastD\Framework\Dispatcher\Dispatch;
use FastD\Http\Request;
use FastD\Routing\Route;

/**
 * Framework kernel boot http handle.
 * Handle every time user request.
 *
 * Class HttpHandler
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
class HttpHandler extends Dispatch
{
    public function getName()
    {
        return 'handle.http';
    }

    public function handleRoute(Route $route)
    {
        $callback = $route->getCallback();

        list($controller, $action) = explode('@', $callback);
        $controller = str_replace(':', '\\', $controller);
        $service = $this->container->set('request_callback', $controller)->get('request_callback');
        try {
            $service->setContainer($this->container);
            $service->__initialize();
        } catch (\Exception $e) {}

        return $service->$action($route->getParameters());
    }

    public function dispatch(array $parameters = null)
    {
        $route = $this->getContainer()->singleton('kernel.routing')->match($parameters[0]->getPathInfo());

        return $this->handleRoute($route);
    }
}