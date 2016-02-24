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
use FastD\Debug\Debug;

class ErrorHandler extends Dispatch
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.error';
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        list($isDebug) = $parameters;

        $logger = null;
        if (!$isDebug) {
            $logger = $this->getContainer()->singleton('kernel.dispatch')->dispatch('handle.log', [LogHandler::LOG_ERROR]);
        }

        Debug::enable($isDebug, $logger);

        unset($isDebug, $logger);
    }
}