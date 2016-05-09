<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/9
 * Time: 下午11:08
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\IO\Input;
use FastD\Console\IO\Output;

class ControllerInitCommand extends CommandAware
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'controller:init';
    }

    /**
     * @return void
     */
    public function configure()
    {
        // TODO: Implement configure() method.
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int
     */
    public function execute(Input $input, Output $output)
    {
        // TODO: Implement execute() method.
    }
}