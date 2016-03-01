<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/1/6
 * Time: 下午2:18
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
use FastD\Database\Builder\AutoBuilding;
use FastD\Database\Builder\Table;
use FastD\Finder\Finder;
use FastD\Framework\Bundle\Controllers\Controller;

class OrmUpdateCommand extends Command
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'db:update';
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this->setArgument('connection');
        $this->setOption('create', Input::ARG_NONE);
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

        $type = Table::TABLE_CHANGE;
        if ($input->has('create')) {
            $type = Table::TABLE_CREATE;
        }


        $controller = new Controller();

        $controller->setContainer($this->getApplication()->getContainer());

        $driver = $controller->getDriver($connection);

        $bundles = $this->getApplication()->getKernel()->getBundles();

        foreach ($bundles as $bundle) {
            $path = $bundle->getRootPath() . '/Resources/orm';

            $builder = new AutoBuilding($driver, $path, true);

            $builder->ymlToTable($bundle->getRootPath() . '/Orm', $bundle->getNamespace() . '\\Orm', true, $type);

            $output->writeln("\t" . $bundle->getNamespace() . '/Orm/Entity', Output::STYLE_SUCCESS);
            $output->writeln("\t" . $bundle->getNamespace() . '/Orm/Repository', Output::STYLE_SUCCESS);
            $output->writeln("\t" . $bundle->getNamespace() . '/Orm/Field', Output::STYLE_SUCCESS);
        }
    }
}