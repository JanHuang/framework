<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/25
 * Time: 下午11:07
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Extensions;

use FastD\Container\Container;
use FastD\Framework\Bundle\Events\ContainerAwareInterface;
use FastD\Template\Extension;

/**
 * Class TplExtension
 *
 * @package FastD\Framework\Extensions
 */
abstract class TplExtension extends Extension implements ContainerAwareInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}