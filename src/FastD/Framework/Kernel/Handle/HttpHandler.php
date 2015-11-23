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

use FastD\Container\Container;
use FastD\Http\Request;
use FastD\Http\Response;
use FastD\Routing\Route;

class HttpHandler implements HandlerInterface
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function dispatchRoute(Route $route)
    {
        return new HandleContext($route);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleHttpRequest(Request $request)
    {
        $route = $this->container->get('kernel.routing')->match($request->getPathInfo());

        $handlerContext = $this->dispatchRoute($route);

        return $handlerContext->getResponse($this->container);
    }
}