<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/27
 * Time: 下午12:18
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Dispatcher\Handle;

use FastD\Framework\Dispatcher\Dispatch;

/**
 * 注释处理调度任务
 *
 * Class AnnotationHandle
 *
 * @package FastD\Framework\Dispatcher\Handle
 */
class AnnotationHandle extends Dispatch
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'handle.annotation';
    }

    /**
     * @param array|null $parameters
     * @return mixed
     */
    public function dispatch(array $parameters = null)
    {
        // TODO: Implement dispatch() method.
    }
}