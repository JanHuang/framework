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

class LogHandler extends Dispatch
{
    const LOG_ACCESS = 1;
    const LOG_ERROR = 2;

    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.log';
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        return $this->getLogger($parameters[0]);
    }

    public function getLogger($type)
    {
        $logger = $this->getContainer()->singleton('kernel.logger');

        $log = $this->getContainer()->singleton('kernel')->getRootPath() . '/logs/' . date('Ymd');

        switch ($type) {
            case self::LOG_ACCESS:
                $log .= '/access.log';
                break;
            case self::LOG_ERROR:
                $log .= '/error.log';
                break;
            default:
                $log .= '/log.log';
        }

        return $logger->createLogger($log);
    }
}