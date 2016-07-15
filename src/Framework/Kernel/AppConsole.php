<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/12/11
 * Time: 下午2:58
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Kernel;

use FastD\Console\Console;

/**
 * Class AppConsole
 *
 * @package FastD\Framework\Kernel
 */
class AppConsole extends Console
{
    /**
     * @var AppKernelInterface
     */
    protected $application;

    /**
     * AppConsole constructor.
     *
     * @param AppKernelInterface $appKernel
     */
    public function __construct(AppKernelInterface $appKernel)
    {
        $this->application = $appKernel;

        $this->application->bootstrap();

        parent::__construct();

        $this->init();
    }

    /**
     * @return AppKernelInterface
     */
    public function getKernel()
    {
        return $this->application;
    }

    /**
     * @return \FastD\Container\Container
     */
    public function getContainer()
    {
        return $this->application->getContainer();
    }

    /**
     * @return int 0 or 1.
     */
    public function init()
    {
        $this->application->getContainer()->singleton('kernel.dispatch')->dispatch('handle.scan.commands', [$this]);
    }
}