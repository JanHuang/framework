<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/4/29
 * Time: 下午4:15
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Input\Input;
use FastD\Console\Output\Output;

class ProdCommand extends CommandAware
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'production:init';
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
        $this->getApplication()->getCommand('config:cache')->execute($input, $output);
        $this->getApplication()->getCommand('route:cache')->execute($input, $output);
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        // TODO: Implement getHelp() method.
    }
}