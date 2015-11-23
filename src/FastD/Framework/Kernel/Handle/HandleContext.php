<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/23
 * Time: 下午10:08
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Kernel\Handle;

use FastD\Container\Container;
use FastD\Framework\Bundle\Events\Http\Event;
use FastD\Http\Response;
use FastD\Routing\Route;

class HandleContext
{
    protected $route;

    protected $eventName;

    protected $actionName;

    protected $callback;

    public function __construct(Route $route)
    {
        $this->route = $route;

        $callback = $route->getCallback();
        switch (gettype($callback)) {
            case 'object':
            case 'closure':
                $this->callback = $callback;
                break;
            case 'array':
                $this->eventName = $callback[0];
                $this->actionName = $callback[1];
                break;
            case 'string':
            default:
                list($controller, $action) = explode('@', $callback);
                $controller = str_replace(':', '\\', $controller);
                $this->eventName = $controller;
                $this->actionName = $action;
        }
    }

    public function getEventName()
    {
        return $this->eventName;
    }

    public function getActionName()
    {
        return $this->actionName;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getResponse(Container $container)
    {
        $event = $container->set('http_request_handle_callback', str_replace(':', '\\', $this->getEventName()))->get('http_request_handle_callback');
        if ($event instanceof Event) {
            $event->setContainer($container);
        }
        // Initialize assert.
        if (method_exists($event, '__initialize')) {
            $response = $container->getProvider()->callServiceMethod($event, '__initialize');
            if (null !== $response && $response instanceof Response) {
                return $response;
            }
        }
        $response = $container->getProvider()->callServiceMethod($event, $this->getActionName(), $this->route->getParameters());
        if ($response instanceof Response) {
            return $response;
        }

        return new Response($response);
    }
}