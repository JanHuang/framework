<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/9
 * Time: 下午10:51
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Command\Command;
use FastD\Container\Aware;
use FastD\Framework\Bundle\Common\Common;
use FastD\Framework\Bundle\Common\CommonInterface;

abstract class CommandAware extends Command implements CommonInterface
{
    use Aware, Common;
}