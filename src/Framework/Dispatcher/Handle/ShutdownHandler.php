<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/27
 * Time: 下午3:25
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Dispatcher\Handle;

use FastD\Framework\Dispatcher\Dispatch;

class ShutdownHandler extends Dispatch
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.shutdown';
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        if (!$this->getContainer()->singleton('kernel')->isDebug()) {
            $request = $this->getContainer()->singleton('kernel.request');

            $parameters['ip'] = $request->getClientIp();
            $parameters['query'] = $request->query->all();
            $parameters['request'] = $request->request->all();
            $parameters['ua'] = $request->getUserAgent();

            $logger = $this->getContainer()->singleton('kernel.dispatch')->dispatch('handle.log', [LogHandler::LOG_ACCESS]);

            $logger->addInfo($request->getPathInfo(), $parameters);
            unset($request);
        }
    }
}