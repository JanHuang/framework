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
use FastD\Database\Fdb;
use FastD\Storage\CacheInterface;
use FastD\Storage\StorageInterface;
use FastD\Storage\Storage;

/**
 * Class Common
 *
 * @package FastD\Framework\Bundle\Common
 */
trait Common
{
    use Aware;

    /**
     * @var Fdb
     */
    protected $fdb;

    /**
     * @var Storage
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
        if (null === $this->fdb) {
            $this->fdb = $this->get('kernel.database', [$this->getParameters('database')]);
            if ($this->get('kernel')->isDebug()) {
                $this->get('kernel.debug')->getBar()->addFdb($this->fdb);
            }
        }

        return $this->fdb->getDriver($connection);
    }

    /**
     * @param       $connection
     * @return StorageInterface|CacheInterface
     */
    public function getStorage($connection)
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