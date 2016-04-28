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
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class LogHandler
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
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

    /**
     * @param $type
     * @return string
     */
    public function getLogger($type)
    {
        $log = $this->getContainer()->singleton('kernel')->getRootPath() . '/storage/logs/' . date('Ymd');

        switch ($type) {
            case self::LOG_ACCESS:
                $name = 'access';
                $log .= '/access.log';
                break;
            case self::LOG_ERROR:
                $name = 'error';
                $log .= '/error.log';
                break;
            default:
                $name = 'log';
                $log .= '/log.log';
        }

        $logger = new Logger($name);
        $stream = new StreamHandler($log);

        return $logger->pushHandler($stream);
    }
}