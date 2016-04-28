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

use FastD\Console\Environment\ApplicationAbstract;

/**
 * Class AppConsole
 *
 * @package FastD\Framework\Kernel
 */
class AppConsole extends ApplicationAbstract
{
    /**
     * @var AppKernel
     */
    protected $application;

    /**
     * AppConsole constructor.
     *
     * @param AppKernel $appKernel
     */
    public function __construct(AppKernel $appKernel)
    {
        $this->application = $appKernel;

        $this->application->boot();

        parent::__construct();

        $this->init();
    }

    /**
     * @return AppKernel
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