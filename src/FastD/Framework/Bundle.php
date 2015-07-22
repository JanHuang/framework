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

namespace FastD\Framework;

/**
 * Class Bundle
 *
 * @package FastD\Framework
 */
class Bundle
{
    /**
     * @var string
     */
    protected $rootPath;

    protected $namespace;

    protected $shortname;

    protected $fullname;

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

    public function getShortname()
    {
        if (null === $this->shortname) {
            $this->shortname = (new \ReflectionClass($this))->getShortName();
        }

        return $this->shortname;
    }

    public function getFullname()
    {
        if (null === $this->fullname) {
            $this->fullname = (new \ReflectionClass($this))->getName();
        }

        return $this->fullname;
    }
}