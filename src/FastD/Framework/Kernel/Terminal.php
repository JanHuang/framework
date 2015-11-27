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

use FastD\Framework\Bundle;

/**
 * Class Terminal
 *
 * @package FastD\Framework\Kernel
 */
abstract class Terminal implements TerminalInterface, AppKernelInterface
{
    /**
     * Application process shutdown.
     *
     * @param AppKernel $appKernel
     * @return void
     */
    public function shutdown(AppKernel $appKernel)
    {
        $this->getContainer()->singleton('kernel.dispatch')->dispatch('handle.shutdown');
    }
}