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

use FastD\Console\Command\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;
use FastD\Database\Builder\AutoBuilding;
use FastD\Database\Builder\Table;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Bundle\Controllers\Controller;

/**
 * Class OrmUpdateCommand
 *
 * @package FastD\Framework\Bundle\Commands
 */
class OrmUpdateCommand extends Command
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
            ->setOption('create', Input::ARG_NONE)
            ->setOption('bundle')
            ->setOption('debug', Input::ARG_NONE)
        ;
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

        $debug = false;
        if ($input->has('debug')) {
            $debug = true;
        }

        $controller = new Controller();

        $controller->setContainer($this->getApplication()->getContainer());

        $driver = $controller->getDriver($connection);

        $bundles = $this->getApplication()->getKernel()->getBundles();

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
        $output->write("\t" . $bundle->getName(), Output::STYLE_SUCCESS);
        $output->writeln("\t" . '["Resources/orm"]', Output::STYLE_SUCCESS);
    }
}