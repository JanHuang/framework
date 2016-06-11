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

use FastD\Console\Input\Input;
use FastD\Console\Output\Output;
use FastD\Database\Builder\AutoBuilding;
use FastD\Framework\Bundle\Bundle;
use FastD\Framework\Bundle\Controllers\Controller;

/**
 * Class OrmRevertCommand
 *
 * @package FastD\Framework\Bundle\Commands
 */
class OrmEntityCommand extends CommandAware
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'orm:entity';
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

        $controller = new Controller();
        $controller->setContainer($this->getContainer());

        $driver = $controller->getDriver($connection);

        $bundles = $this->getContainer()->singleton('kernel')->getBundles();

        foreach ($bundles as $bundle) {
            $builder = new AutoBuilding($driver);

            if ($input->hasOption('bundle')) {
                if ($bundle->getShortName() == $input->getOption('bundle')) {
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
        $path = $bundle->getRootPath() . '/Orm';

        $builder->saveYmlTo($path, true);
        $builder->saveTo($path, $bundle->getNamespace() . '\\Orm', true);

        $output->write('Generate into bundle: ');
        $output->writeln(sprintf('<success>%s\\Orm</success>', $bundle->getName()));
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '反射数据库结构到实体对象, 生成 ORM 目录';
    }
}