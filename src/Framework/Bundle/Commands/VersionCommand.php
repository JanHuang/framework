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

use FastD\Console\IO\Input;
use FastD\Console\IO\Output;
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
        $output->writeln(sprintf('FastD Kernel version %s', $output->format(AppKernel::VERSION, Output::STYLE_INFO)));
        $output->writeln(sprintf('Environment %s', $output->format($this->getContainer()->get('kernel')->getEnvironment(), Output::STYLE_INFO)));
    }
}