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
 * @package FastD\Framework
 */
class Bundle extends \ReflectionClass implements BundleInterface
{
    /**
     * @var string
     */
    protected $rootPath;

    protected $namespace;

    protected $shortname;

    protected $fullname;

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
            $this->rootPath = dirname((new \ReflectionClass($this))->getFileName());
        }

        return $this->rootPath;
    }

    public function getNamespace()
    {
        if (null === $this->namespace) {
            $this->namespace = (new \ReflectionClass($this))->getNamespaceName();
        }

        return $this->namespace;
    }

    public function getShortName()
    {
        if (null === $this->shortname) {
            $this->shortname = (new \ReflectionClass($this))->getShortName();
        }

        return $this->shortname;
    }

    public function getName()
    {
        if (null === $this->fullname) {
            $this->fullname = (new \ReflectionClass($this))->getName();
        }

        return $this->fullname;
    }

    public function registerRouting(Router $router)
    {
        // TODO: Implement registerRouting() method.
    }

    public function registerConfiguration(Config $config)
    {
        // TODO: Implement registerConfiguration() method.
    }
}