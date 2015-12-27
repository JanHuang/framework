<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/26
 * Time: 下午11:16
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * sf: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

namespace FastD\Framework\Bundle;

use FastD\Config\Config;
use FastD\Routing\Router;

/**
 * Class Bundle
 *
 * @package FastD\Framework\Bundle
 */
class Bundle extends \ReflectionClass implements BundleInterface
{
    /**
     * @var string
     */
    protected $rootPath;

    /**
     * Constructs a ReflectionClass
     *
     * @link  http://php.net/manual/en/reflectionclass.construct.php
     * @since 5.0
     */
    public function __construct()
    {
        parent::__construct($this);
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        if (null === $this->rootPath) {
            $this->rootPath = dirname($this->getFileName());
        }

        return $this->rootPath;
    }

    /**
     * Get namespace. Alias getNamespaceName
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->getNamespaceName();
    }

    /**
     * Register bundle routing list.
     *
     * @param Router $router
     * @param string $env
     * @return void
     */
    public function registerRouting(Router $router, $env)
    {
        include $this->getRootPath() . '/Resources/config/routes.php';
    }

    /**
     * Register bundle configuration.
     *
     * @param Config $config
     * @param string $env
     * @return void
     */
    public function registerConfiguration(Config $config, $env)
    {
        include $this->getRootPath() . '/Resources/config/config.php';
    }

    /**
     * @return array
     */
    public function registerExtensions()
    {
        return [];
    }
}