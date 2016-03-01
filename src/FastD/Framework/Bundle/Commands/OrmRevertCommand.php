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

use FastD\Console\Command\Command;
use FastD\Console\IO\Input;
use FastD\Console\IO\Output;
use FastD\Database\Builder\AutoBuilding;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Bundle\Controllers\Controller;

/**
 * Class OrmRevertCommand
 *
 * @package FastD\Framework\Bundle\Commands
 */
class OrmRevertCommand extends Command
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
        $this->setOption('bundle');
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

        $controller = new Controller();
        $controller->setContainer($this->getApplication()->getContainer());

        $driver = $controller->getDriver($connection);

        $bundles = $this->getApplication()->getKernel()->getBundles();

        foreach ($bundles as $bundle) {
            $builder = new AutoBuilding($driver);

            if ($input->has('bundle')) {
                if ($bundle->getShortName() == $input->get('bundle')) {
                    $this->building($builder, $bundle, $output);
                    break;
                }
            } else {
                $this->building($builder, $bundle, $output);
            }
        }
    }

    /**
     * @param AutoBuilding $builder
     * @param Bundle $bundle
     * @param Output $output
     */
    protected function building(AutoBuilding $builder, Bundle $bundle, Output $output)
    {
        $path = $bundle->getRootPath() . '/Resources/orm';

        $builder->saveYmlTo($path, true);
        $builder->saveTo($bundle->getRootPath() . '/Orm', $bundle->getNamespace() . '\\Orm', true);

        $output->write('Generate into bundle: ');
        $output->writeln($bundle->getName() . '\\Orm', Output::STYLE_SUCCESS);
    }
}