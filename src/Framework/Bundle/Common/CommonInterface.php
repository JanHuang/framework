<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/11
 * Time: 上午11:11
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Common;

use FastD\Database\Drivers\DriverInterface;
use FastD\Storage\StorageInterface;

/**
 * Interface CommonInterface
 *
 * @package Framework\Bundle\Common
 */
interface CommonInterface
{
    /**
     * @param            $name
     * @param array      $parameters
     * @param bool       $flag
     * @return mixed
     */
    public function get($name, array $parameters = [], $flag = false);

    /**
     * @param $name
     * @return mixed
     */
    public function getParameters($name);

    /**
     * @param            $connection
     * @return DriverInterface
     */
    public function getDriver($connection);

    /**
     * @param            $connection
     * @return StorageInterface
     */
    public function getStorage($connection);
}