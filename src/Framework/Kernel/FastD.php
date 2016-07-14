<?php
/**
 *
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace Framework\Kernel;

use FastD\Config\Config;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Kernel\AppKernel;

class FastD
{
    public function __construct($bootstrap)
    {
        
    }

    public static function run($bootstrap)
    {
        $app = new static($bootstrap);

        $app->start();
    }

    public function start()
    {

    }

    /**
     * Register project bundle.
     *
     * @return Bundle
     */
    public function registerBundles()
    {
        // TODO: Implement registerBundles() method.
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
     * Register application configuration dynamic variable.
     *
     * @param Config $config
     * @return void
     */
    public function registerConfigurationVariable(Config $config)
    {
        // TODO: Implement registerConfigurationVariable() method.
    }
}