<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/18
 * Time: 下午11:27
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Tests;

use FastD\Framework\Kernel\AppKernel;

abstract class FrameworkTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $env
     * @return AppKernel
     */
    public static function kernelBootstrap($env = 'dev')
    {

    }

    public static function createFastDClient($env = 'dev')
    {

    }
}