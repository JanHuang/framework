<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/11
 * Time: 上午11:07
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Common;

use FastD\Container\Aware;
use FastD\Database\DriverInterface;
use FastD\Storage\StorageManager;

trait Common
{
    use Aware;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var StorageManager
     */
    protected $storage;

    /**
     * Get custom defined helper obj.
     *
     * @param string $name
     * @param array $parameters
     * @param bool  $flag
     * @return mixed
     */
    public function get($name, array $parameters = [], $flag = false)
    {
        return $flag ? $this->getContainer()->instance($name, $parameters) : $this->getContainer()->singleton($name, $parameters);
    }

    /**
     * @param null  $connection
     * @return DriverInterface
     */
    public function getDriver($connection = null)
    {
        if (null === $this->driver) {
            $this->driver = $this->get('kernel.database', [$this->getParameters('database')]);
        }

        return $this->driver->getDriver($connection);
    }

    /**
     * @param       $connection
     * @param array $options
     * @return \FastD\Storage\StorageInterface
     */
    public function getStorage($connection, array $options = [])
    {
        if (null === $this->storage) {
            $this->storage = $this->get('kernel.storage', [$this->getParameters('storage')]);
        }

        return $this->storage->getConnection($connection);
    }

    /**
     * Get custom config parameters.
     *
     * @param string $name
     * @return mixed
     */
    public function getParameters($name = null)
    {
        return $this->get('kernel.config')->get($name);
    }
}