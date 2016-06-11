<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/10
 * Time: 下午9:57
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
use FastD\Framework\Kernel\AppKernel;

class VersionCommand extends CommandAware
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'kernel:version';
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
        $output->writeln(sprintf('Running (%s) with PHP %s on %s / %s', date('Y-m-d H:i:s'), PHP_VERSION, PHP_OS, php_uname('r')));
        $output->writeln(sprintf('FastD Kernel version <info>%s</info>', AppKernel::VERSION));
        $output->writeln(sprintf('Environment <info>"%s"</info>', $this->getContainer()->get('kernel')->getEnvironment()));
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        // TODO: Implement getHelp() method.
    }
}