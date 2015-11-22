<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/22
 * Time: 上午11:23
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle;

use FastD\Config\Config;
use FastD\Routing\Router;

interface BundleInterface
{
    public function registerRouting(Router $router);

    public function registerConfiguration(Config $config);
}