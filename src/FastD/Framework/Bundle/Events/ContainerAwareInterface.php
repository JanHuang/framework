<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/23
 * Time: 下午6:43
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Events;

use FastD\Container\Container;

/**
 * Interface ContainerAwareInterface
 *
 * @package FastD\Framework\Bundle\Events\Http
 */
interface ContainerAwareInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function setContainer(Container $container);

    /**
     * @return Container
     */
    public function getContainer();
}