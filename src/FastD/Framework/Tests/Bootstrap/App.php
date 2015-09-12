<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/18
 * Time: 上午1:07
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Tests\Bootstrap;

use FastD\Config\Config;
use FastD\Framework\Bundle;
use FastD\Framework\Kernel\AppKernel;

class App extends AppKernel
{
    /**
     * Register project bundle.
     *
     * @return Bundle
     */
    public function registerBundles()
    {
        return [];
    }

    /**
     * Register application plugins.
     *
     * @return array
     */
    public function registerService()
    {
        return [];
    }

    /**
     * Register application configuration
     *
     * @param Config $config
     * @return void
     */
    public function registerConfiguration(Config $config)
    {
        // TODO: Implement registerConfiguration() method.
    }

    /**
     * @return array
     */
    public function registerConfigVariable()
    {
        return [];
    }
}