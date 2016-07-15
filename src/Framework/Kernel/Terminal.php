<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/6/30
 * Time: 下午3:58
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Kernel;

use FastD\Framework\Bundle\Controllers\Controller;
use FastD\Http\Request;

/**
 * Class Terminal
 *
 * @package FastD\Framework\Kernel
 */
abstract class Terminal implements TerminalInterface, AppKernelInterface
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function handleHttpRequest(Request $request)
    {
        $route = $this->getContainer()->singleton('kernel.routing')->match($request->getMethod(), $request->getPathInfo());

        $callback = $route->getCallback();

        list($controller, $action) = explode('@', $callback);

        $controller = str_replace(':', '\\', $controller);

        $service = $this->getContainer()->set('request_callback', $controller)->get('request_callback');

        if (method_exists($service->singleton(), 'setContainer')) {
            $service->singleton()->setContainer($this->getContainer());
        }
        
        try {
            $service->__initialize();
        } catch (\Exception $e) {}

        return call_user_func_array([$service, $action], $route->getParameters());
    }

    public function handleError()
    {

    }

    /**
     * Application process shutdown.
     *
     * @param AppKernelInterface $appKernel
     * @return void
     */
    public function shutdown(AppKernelInterface $appKernel)
    {
        $event = $this->getContainer()->singleton('kernel.event');
        
        $event->on('handle.shutdown', function () {

        });

        $event->trigger('handle.shutdown');
    }
}