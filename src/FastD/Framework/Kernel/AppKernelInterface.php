<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/6/30
 * Time: 下午5:15
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Kernel;

use FastD\Config\Config;
use FastD\Container\Container;
use FastD\Framework\Bundle\Bundle;

/**
 * Interface AppKernelInterface
 *
 * @package FastD\Framework\Kernel
 */
interface AppKernelInterface
{
    /**
     * @return Bundle[]
     */
    public function getBundles();

    /**
     * @return Container
     */
    public function getContainer();

    /**
     * Register project bundle.
     *
     * @return Bundle
     */
    public function registerBundles();

    /**
     * Register application plugins.
     *
     * @param Container $container
     * @return void
     */
    public function registerService(Container $container);

    /**
     * Register application configuration
     *
     * @param Config $config
     * @return void
     */
    public function registerConfiguration(Config $config);

    /**
     * Register application configuration dynamic variable.
     *
     * @param Config $config
     * @return void
     */
    public function registerConfigurationVariable(Config $config);
}