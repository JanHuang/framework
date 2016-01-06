<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/12/24
 * Time: 下午7:01
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Framework\Bundle\Commands;

use FastD\Config\Loader\YmlFileLoader;
use FastD\Console\Command\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;
use FastD\Database\ORM\Generator\Mapping;
use FastD\Finder\Finder;
use FastD\Framework\Bundle\Controllers\Controller;

class ORMRevertCommand extends Command
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'db:revert';
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this->setArgument('connection');
    }

    /**
     * @param Input  $input
     * @param Output $output
     * @return int
     */
    public function execute(Input $input, Output $output)
    {
        $connection = $input->get('connection');
        if (empty($connection)) {
            $connection = 'read';
        }

        $kernel = $this->getApplication()->getKernel();

        $kernel->boot();

        $controller = new Controller();
        $controller->setContainer($kernel->getContainer());

        $driver = $controller->getDriver($connection);

        $finder = new Finder();

        foreach ($kernel->getBundles() as $bundle) {
            $builder = new Mapping($driver);
            $path = $bundle->getRootPath() . '/Resources/orm';
            $files = $finder->in($path)->depth(0)->files();
            $builder->buildEntity($bundle->getNamespace(), $bundle->getRootPath());
            $output->write('Generate into dir: ');
            $output->writeln($bundle->getName(), Output::STYLE_BG_SUCCESS);
            $output->writeln("\t" . $bundle->getNamespace() . '/Entity', Output::STYLE_SUCCESS);
            $output->writeln("\t" . $bundle->getNamespace() . '/Repository', Output::STYLE_SUCCESS);
            $output->writeln("\t" . $bundle->getNamespace() . '/Fields', Output::STYLE_SUCCESS);
        }
    }
}