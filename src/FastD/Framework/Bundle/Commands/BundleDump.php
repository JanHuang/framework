<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/7/22
 * Time: 下午11:08
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Console\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;

class BundleDump extends Command
{
    public function getName()
    {
        return 'bundle:dump';
    }

    public function configure()
    {
        // TODO: Implement configure() method.
    }

    public function execute(Input $input, Output $output)
    {
        $bundles = $this->getContainer()->get('kernel')->getBundles();
        $output->write('Bundle length: ');
        $output->writeln(count($bundles), Output::STYLE_SUCCESS);
        $output->writeln('');
        foreach ($bundles as $bundle) {
            $output->write('Bundle: ');
            $output->writeln($bundle->getFullname(), Output::STYLE_SUCCESS);
            $output->write("Path to: ");
            $output->writeln($bundle->getRootPath(), Output::STYLE_SUCCESS);
            $output->writeln('');
        }
    }
}