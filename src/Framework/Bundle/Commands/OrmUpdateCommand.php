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

use FastD\Console\Input\Input;
use FastD\Console\Output\Output;
use FastD\Database\Builder\AutoBuilding;
use FastD\Database\Builder\Table;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Bundle\Controllers\Controller;

/**
 * Class OrmUpdateCommand
 *
 * @package FastD\Framework\Bundle\Commands
 */
class OrmUpdateCommand extends CommandAware
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'orm:update';
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this
            ->setArgument('connection')
            ->setOption('bundle')
        ;
    }

    /**
     * @param Input  $input
     * @param Output $output
     * @return int
     */
    public function execute(Input $input, Output $output)
    {
        $connection = $input->getArgument('connection');
        if (empty($connection)) {
            $connection = 'read';
        }

        $type = Table::TABLE_CHANGE;

        $debug = false;
        if ($input->hasOption('debug')) {
            $debug = true;
        }

        $controller = new Controller();

        $controller->setContainer($this->getContainer());

        $driver = $controller->getDriver($connection);

        $bundles = $this->getContainer()->singleton('kernel')->getBundles();

        foreach ($bundles as $bundle) {
            $path = $bundle->getRootPath() . '/Resources/orm';

            if (!is_dir($path)) {
                continue;
            }

            $builder = new AutoBuilding($driver, $path, $debug);
            if ($input->has('bundle')) {
                if ($bundle->getShortName() == $input->get('bundle')) {
                    $this->building($builder, $bundle, $type, $output);
                    break;
                }
            } else {
                $this->building($builder, $bundle, $type, $output);
            }
        }
    }

    /**
     * @param AutoBuilding $builder
     * @param Bundle $bundle
     * @param $type
     * @param Output $output
     */
    protected function building(AutoBuilding $builder, Bundle $bundle, $type, Output $output)
    {
        $builder->ymlToTable($bundle->getRootPath() . '/Orm', $bundle->getNamespace() . '\\Orm', true, $type);

        $output->write('Building from bundle: ');
        $output->write("\t" . '<success>' . $bundle->getName() . '</success>');
        $output->writeln("\t" . '<success>["Resources/orm"]</success>');
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return '更新数据库结构';
    }
}