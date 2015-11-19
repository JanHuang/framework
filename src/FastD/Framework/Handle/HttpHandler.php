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

namespace FastD\Framework\Handle;

use FastD\Http\Request;
use FastD\Routing\Router;

class HttpHandler
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getEvent(Request $request)
    {
//        $this->router->match($request->getPathInfo());
    }

    public function handle(Request $request)
    {
        //$event = $this->getEvent($request);

        return 'hello world';
    }
}